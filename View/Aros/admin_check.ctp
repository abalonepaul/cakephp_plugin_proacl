<?php
echo $this->element('design/header');
echo $this->element('Aros/links');

if (count($missingAros['roles']) > 0) {
    echo '<h3>' . __d('acl', 'Roles without corresponding Aro') . '</h3>';
    
    $list = '';
    foreach ($missingAros['roles'] as $missingAro) {
        $list .= $this->Html->tag('li', $missingAro[$roleModelName][$roleDisplayField], array('class'=>'list-group-item'));
    }
    
    echo $this->Html->tag('ul', $list, array('class'=>'list-group col-md-5'));
}

if (count($missingAros['users']) > 0) {
    echo $this->Html->tag('h3', __d('acl', 'Users without corresponding Aro'));
    
    $list = '';
    foreach ($missingAros['users'] as $missingAro) {
        $list .= $this->Html->tag('li', $missingAro[$userModelName][$userDisplayField], array('class'=>'list-group-item'));
    }   
}

if (count($missingAros['roles']) > 0 || count($missingAros['users']) > 0) {
    $list .= $this->Html->tag('li', $this->Html->link(__d('acl', 'Build'),
        array('admin'=>true, 'prefix'=>'admin', 'plugin'=>'acl','controller'=>'aros', 'action'=>'check', 'run'),
        array('class'=>'btn')),array('class'=>'list-group-item')
        );
    echo $this->Html->tag('ul', $list, array('class'=>'list-group col-md-6 col-xs-6'));
} else {
    echo $this->Html->tag('h4', __d('acl', 'There is no missing ARO.'));
}

echo $this->element('design/footer');
?>