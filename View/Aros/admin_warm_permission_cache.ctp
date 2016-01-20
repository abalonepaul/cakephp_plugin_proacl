<?php
echo $this->element('design/header');
?>

<?php
echo $this->element('Aros/links');
?>

    <h1><?php echo  __d('acl', 'Warm the Permission Cache') ?></h1>

    <p>Select a role to update the Permission Cache for only one role. Otherwise all users will have their cache warmed.</p>
<?php
$run = true;
    echo $this->Form->create('Aros', array('action' => 'warm_permission_cache'));
    echo $this->Form->hidden('run',array('value' => 'run'));
    echo $this->Form->input('role_id',array('label' => 'Warm Cache for a selected Role', 'empty' => ''));
    echo $this->form->end('Warm Cache');

?>
<?php
echo $this->element('design/footer');
?>