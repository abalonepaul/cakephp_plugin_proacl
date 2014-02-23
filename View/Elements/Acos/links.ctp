<div id="acos_link" class="acl_links navbar">
<?php
if(!isset($selected)){
    $selected = $this->params['action'];
}


$class=($selected == 'admin_synchronize') ? 'active' : null;
$list = $this->Html->tag('li', $this->Html->link(__d('acl', 'Synchronize actions ACOs'),
    array('plugin'=>'acl', 'controller'=>'acos', 'admin'=>true, 'prefix'=>'admin', 'action'=>'synchronize'),
    array(
        array(
            'confirm' => __d('acl', 'are you sure ?')
        )
    )),
    array('class' => $class));
    
$class=($selected == 'admin_empty_acos') ? 'active' : null;
$list .= $this->Html->tag('li', $this->Html->link(__d('acl', 'Clear actions ACOs'),
    array('plugin'=>'acl', 'controller'=>'acos', 'admin'=>true, 'prefix'=>'admin', 'action'=>'empty_acos'),
    array(
        array(
            'confirm' => __d('acl', 'are you sure ?')
        )
    )),
    array('class' => $class));

$class=($selected == 'admin_build_acl') ? 'active' : null;
$list .= $this->Html->tag('li', $this->Html->link(__d('acl', 'Build actions ACOs'),
    array('plugin'=>'acl', 'controller'=>'acos', 'admin'=>true, 'prefix'=>'admin', 'action'=>'build_acl')),
    array('class' => $class));
    
$class=($selected == 'admin_prune_acos') ? 'active' : null;
$list .= $this->Html->tag('li', $this->Html->link(__d('acl', 'Prune actions ACOs'),
    array('plugin'=>'acl', 'controller'=>'acos', 'admin'=>true, 'prefix'=>'admin', 'action'=>'prune_acos'),
    array(
        array(
            'confirm' => __d('acl', 'are you sure ?')
        )
    )),
    array('class' => $class));      

    echo $this->Html->tag('ul', $list, array('class'=>'nav nav-tabs'));
?>
</div>