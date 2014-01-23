<?php
echo $this->element('design/header');
?>

<?php
echo $this->element('Aros/links');

echo $this->Form->create('User', 
    array('class'=>'form-inline',
        'role'=>'form',
        'url' => array(
            'plugin' => 'acl', 
            'controller' => 'aros', 
            'action' => 'admin_users'
        )
    ));
echo $this->Form->input($userDisplayField, 
    array(
        'label' => array('class'=>'sr-only'), 
        'div' => array('class'=>'form-group'),
        'placeholder'=>$userDisplayField
    ));
echo $this->Form->button(__d('acl','Filter'), array('class'=>'btn btn-default'));
echo $this->Form->end();
echo '<br/>';
?>
<div class="col-md-8 col-xs-8">
<table class="table table-striped table-striped">
    <tr>
	<?php
$column_count = 1;

$headers = array(
    $this->Paginator->sort($userDisplayField, __d('acl', 'name'))
);

foreach ($roles as $role) {
    $headers[] = $role[$roleModelName][$roleDisplayField];
    $column_count ++;
}

echo $this->Html->tableHeaders($headers);

?>
	
</tr>
<?php
foreach ($users as $user) {
    $style = isset($user['Aro']) ? '' : ' class="line_warning"';
    
    echo '<tr' . $style . '>';
    echo '  <td>' . $user[$userModelName][$userDisplayField] . '</td>';
    
    foreach ($roles as $role) {
        if (isset($user['Aro']) && $role[$roleModelName][$rolePkName] == $user[$userModelName][$roleFkName]) {
            echo '  <td>' . $this->Html->image('/acl/img/design/tick.png') . '</td>';
        } else {
            $title = __d('acl', 'Update the user role');
            echo '  <td>' . $this->Html->link(
                $this->Html->image('/acl/img/design/tick_disabled.png'), 
                '/admin/acl/aros/update_user_role/user:' . $user[$userModelName][$userPkName] . '/role:' . $role[$roleModelName][$rolePkName], 
                array(
                    'title' => $title, 
                    'alt' => $title, 
                    'escape' => false
                )) . '</td>';
        }
    }
   
    echo '</tr>';
}
?>

</table>
        <?php echo $this->element('paginator_links');?>
</div>

<?php
if ($missingAro) {
echo $this->Html->tag('div', __d('acl', 'Some users AROS are missing. Click on a role to assign one to a user.'), array('class'=>'alert alert-warning'));

}
?>

<?php
echo $this->element('design/footer');
?>