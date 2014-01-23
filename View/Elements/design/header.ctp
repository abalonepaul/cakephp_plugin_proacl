<?php
//echo $this->Html->css('/acl/css/acl.css');

    /* Latest compiled and minified CSS */ 
echo $this->Html->css('//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css'); 

    /* Optional theme */ 
echo $this->Html->css('//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css'); 
echo $this->Html->css('Acl.pro-acl'); 
    
?>

<div id="" class="">

<?php echo $this->Session->flash('plugin_acl'); ?>

<div class="page-header">
  <h1>PRO-ACL plugin <small>Updated for CakePHP 2.3 with bootstrap 3</small></h1>
</div>
<?php

if (!isset($no_acl_links)) {
    if(!isset($selected)){
        $selected = $this->params['controller'];
    }

$class =  ($selected == 'aros') ? 'active' : null;
$links = $this->Html->tag('li', $this->Html->link(__d('acl', 'Permissions'),
    array('plugin'=>'acl', 'controller'=>'aros', 'admin'=>true, 'prefix'=>'admin', 'action'=>'index')),
    array('class' => $class));

$class =  ($selected == 'acos') ? 'active' : null;
$links .= $this->Html->tag('li', $this->Html->link(__d('acl', 'Actions'),
    array('plugin'=>'acl', 'controller'=>'acos', 'admin'=>true, 'prefix'=>'admin', 'action'=>'index')),
    array('class' => $class));    

}   ?>    
<div id="aros_link" class="acl_links navbar">
<?php echo $this->Html->tag('ul', $links, array('class'=>'nav nav-pills'));?>
</div>
