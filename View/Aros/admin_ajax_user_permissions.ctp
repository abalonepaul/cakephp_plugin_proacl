<?php
//echo $this->Html->script('/acl/js/jquery');
echo $this->Html->script('Acl.acl_plugin.js');

echo $this->element('design/header');
?>

<?php
echo $this->element('Aros/links');
?>

<h1><?php echo  __d('acl', 'User') . ' : ' . $user[$userModelName][$userDisplayField]; ?></h1>

<h2><?php echo __d('acl', 'Role'); ?></h2>

<table border="0">
    <tr>
        <?php
    foreach ($roles as $role) {
        echo '<td>';

        echo $role[$roleModelName][$roleDisplayField];
        if ($role[$roleModelName][$rolePkName] == $user[$userModelName][$roleFkName]) {
            echo $this->Html->image('/acl/img/design/tick.png');
        } else {
            $title = __d('acl', 'Update the user role');
            echo $this->Html->link(
                $this->Html->image('/acl/img/design/tick_disabled.png'),
                array(
                    'plugin' => 'acl',
                    'controller' => 'aros',
                    'action' => 'update_user_role',
                    'user' => $user[$userModelName][$userPkName],
                    'role' => $role[$roleModelName][$rolePkName]
                ), array(
                    'title' => $title,
                    'alt' => $title,
                    'escape' => false
                ));
        }

        echo '</td>';
    }
    ?>
    </tr>
</table>

<h2><?php echo __d('acl', 'Permissions'); ?></h2>

<?php
if ($user_has_specific_permissions) {
    echo '<div class="separator"></div>';
    echo $this->Html->image('/acl/img/design/bulb24.png') . __d('acl',
        'This user has specific permissions');
    echo ' (';
    echo $this->Html->link(
        $this->Html->image('/acl/img/design/cross2.png',
            array(
                'style' => 'vertical-align:middle;'
            )) . ' ' . __d('acl', 'Clear'),
        '/admin/acl/aros/clear_user_specific_permissions/' . $user[$userModelName][$userPkName],
        array(
            'confirm' => __d('acl',
                'Are you sure you want to clear the permissions specific to this user ?'),
            'escape' => false
        ));
    echo ')';
    echo '<div class="separator"></div>';
}
?>

<table border="0" cellpadding="5" cellspacing="2">
    <tr>
        <?php

    $column_count = 1;

    $headers = array(
        __d('acl', 'action'),
        __d('acl', 'authorization')
    );

    echo $this->Html->tableHeaders($headers);
    ?>
    </tr>

    <?php
$js_init_done = array();
$previous_ctrl_name = '';

if (isset($actions['app']) && is_array($actions['app'])) {
    foreach ($actions['app'] as $controller_name => $ctrl_infos) {
        if ($previous_ctrl_name != $controller_name) {
            $previous_ctrl_name = $controller_name;

            $color = (isset($color) && $color == 'color1') ? 'color2' : 'color1';
        }

        foreach ($ctrl_infos as $ctrl_info) {
            echo '<tr class="' . $color . '">';

            echo '<td>' . $controller_name . '->' . $ctrl_info['name'] . '</td>';

            echo '<td>';
            echo '<span id="right__' . $user[$userModelName][$userPkName] . '_' . $controller_name . '_' . $ctrl_info['name'] . '">';

            /* The right of the action for the role must still be loaded */
            echo $this->Html->image('/acl/img/ajax/waiting16.gif',
                array(
                    'title' => __d('acl', 'loading')
                ));

            if (! in_array(
                $controller_name . '_' . $user[$userModelName][$userPkName],
                $js_init_done)) {
                $js_init_done[] = $controller_name . '_' . $user[$userModelName][$userPkName];
                $this->Js->buffer(
                    'init_register_user_controller_toggle_right("' . $this->Html->url(
                        '/', true) . '", "' . $user[$userModelName][$userPkName] . '", "", "' . $controller_name . '", "' . __d(
                        'acl',
                        'The ACO node is probably missing. Please try to rebuild the ACOs first.') . '");');
            }

            echo '</span>';

            echo ' ';
            echo $this->Html->image('/acl/img/ajax/waiting16.gif',
                array(
                    'id' => 'right__' . $user[$userModelName][$userPkName] . '_' . $controller_name . '_' . $ctrl_info['name'] . '_spinner',
                    'style' => 'display:none;'
                ));

            echo '</td>';
            echo '</tr>';
        }
    }
}
?>
    <?php
if (isset($actions['plugin']) && is_array($actions['plugin'])) {
    foreach ($actions['plugin'] as $plugin_name => $plugin_ctrler_infos) {
        echo '<tr class="title"><td colspan="2">' . __d('acl', 'Plugin') . ' ' . $plugin_name . '</td></tr>';

        foreach ($plugin_ctrler_infos as $plugin_ctrler_name => $plugin_methods) {
            if ($previous_ctrl_name != $plugin_ctrler_name) {
                $previous_ctrl_name = $plugin_ctrler_name;

                $color = (isset($color) && $color == 'color1') ? 'color2' : 'color1';
            }

            foreach ($plugin_methods as $method) {
                echo '<tr class="' . $color . '">';

                echo '<td>' . $plugin_ctrler_name . '->' . $method['name'] . '</td>';

                echo '<td>';
                echo '<span id="right_' . $plugin_name . '_' . $user[$userModelName][$userPkName] . '_' . $plugin_ctrler_name . '_' . $method['name'] . '">';

                /* The right of the action for the role must still be loaded */
                echo $this->Html->image('/acl/img/ajax/waiting16.gif',
                    array(
                        'title' => __d('acl', 'loading')
                    ));

                if (! in_array(
                    $plugin_name . "_" . $plugin_ctrler_name . '_' . $user[$userModelName][$userPkName],
                    $js_init_done)) {
                    $js_init_done[] = $plugin_name . "_" . $plugin_ctrler_name . '_' . $user[$userModelName][$userPkName];
                    $this->Js->buffer(
                        'init_register_user_controller_toggle_right("' . $this->Html->url(
                            '/', true) . '", "' . $user[$userModelName][$userPkName] . '", "' . $plugin_name . '", "' . $plugin_ctrler_name . '", "' . __d(
                            'acl',
                            'The ACO node is probably missing. Please try to rebuild the ACOs first.') . '");');
                }

                echo '</span>';

                echo ' ';
                echo $this->Html->image('/acl/img/ajax/waiting16.gif',
                    array(
                        'id' => 'right_' . $plugin_name . '_' . $user[$userModelName][$userPkName] . '_' . $plugin_ctrler_name . '_' . $method['name'] . '_spinner',
                        'style' => 'display:none;'
                    ));

                echo '</td>';
                echo '</tr>';
            }
        }
    }
}
?>
    </table>
<?php
echo $this->Html->image('/acl/img/design/tick.png') . ' ' . __d('acl',
    'authorized');
echo '&nbsp;&nbsp;&nbsp;';
echo $this->Html->image('/acl/img/design/cross.png') . ' ' . __d('acl',
    'blocked');
?>
<?php

echo $this->element('design/footer');
?>