<div id="aros_link" class="acl_links navbar">
<?php
if(!isset($selected)){
    $selected = $this->params['action'];
}


$class=($selected == 'admin_check') ? 'active' : null;
$list = $this->Html->tag('li', $this->Html->link(__d('acl', 'Build missing AROs'), 
    array('admin'=>true, 'prefix'=>'admin', 'plugin'=>'acl', 'controller'=>'aros', 'action'=>'check')),
    array('class' => $class));

$class = ($selected == 'admin_users') ? 'active' : null;
$list .= $this->Html->tag('li', $this->Html->link(__d('acl', 'Users roles'),
    array('admin'=>true, 'prefix'=>'admin', 'plugin'=>'acl', 'controller'=>'aros', 'action'=>'users')),
    array('class' => $class) );

if (Configure::read('acl.gui.roles_permissions.ajax') === true) {
    $class = ($selected == 'admin_role_permissions' || $selected == 'admin_ajax_role_permissions') ? 'active' : null;
    $list .= $this->Html->tag('li', $this->Html->link(__d('acl', 'Roles permissions'),  
        array('admin'=>true, 'prefix'=>'admin', 'plugin'=>'acl', 'controller'=>'aros', 'action'=>'ajax_role_permissions')),
        array('class' => $class));
} else {
    $class = ($selected == 'admin_role_permissions' || $selected == 'admin_ajax_role_permissions') ? 'active' : null;
    $list .= $this->Html->tag('li', $this->Html->link(__d('acl', 'Roles permissions'), 
        array('admin'=>true, 'prefix'=>'admin', 'plugin'=>'acl', 'controller'=>'aros', 'action'=>'role_permissions')),
        array('class' => $class));
}


$class = ($selected == 'admin_user_permissions') ? 'active' : null;
$list .= $this->Html->tag('li', $this->Html->link(__d('acl', 'Users permissions'), 
    array('admin'=>true, 'prefix'=>'admin', 'plugin'=>'acl', 'controller'=>'aros', 'action'=>'user_permissions')),
    array( 'class' => $class));
    
    echo $this->Html->tag('ul', $list, array('class'=>'nav nav-tabs'));
?>
</div>