<?php
//echo '<span id="right_' . $plugin . '_' . $roleId . '_' . $controller_name . '_' . $action . '">';

if (isset($aclError)) {
    $title = isset($aclErrorAro) ? __d('acl',
        'The role node does not exist in the ARO table') : __d('acl',
        'The ACO node is probably missing. Please try to rebuild the ACOs first.');
    echo $this->Html->image('/acl/img/design/important16.png',
        array(
            'class' => 'pointer',
            'alt' => $title,
            'title' => $title
        ));
} else {
    echo $this->Html->image('/acl/img/design/cross.png',
        array(
            'class' => 'pointer',
            'alt' => 'denied'
        ));
}

//echo '</span>';
?>