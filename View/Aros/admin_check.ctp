<?php
echo $this->element('design/header');
?>

<?php
echo $this->element('Aros/links');
?>

<?php
if (count($missingAros['roles']) > 0) {
    echo '<h3>' . __d('acl', 'Roles without corresponding Aro') . '</h3>';
    
    $list = array();
    foreach ($missingAros['roles'] as $missingAro) {
        $list[] = $missingAro[$roleModelName][$roleDisplayField];
    }
    
    echo $this->Html->nestedList($list);
}
?>

<?php
if (count($missingAros['users']) > 0) {
    echo '<h3>' . __d('acl', 'Users without corresponding Aro') . '</h3>';
    
    $list = array();
    foreach ($missingAros['users'] as $missingAro) {
        $list[] = $missingAro[$userModelName][$userDisplayField];
    }
    
    echo $this->Html->nestedList($list);
}
?>

<?php
if (count($missingAros['roles']) > 0 || count($missingAros['users']) > 0) {
    echo '<p>';
    echo $this->Html->link(__d('acl', 'Build'), '/admin/acl/aros/check/run');
    echo '</p>';
} else {
    echo '<p>';
    echo __d('acl', 'There is no missing ARO.');
    echo '</p>';
}
?>

<?php
echo $this->element('design/footer');
?>