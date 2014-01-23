<?php
echo $this->element('design/header');

echo $this->element('Acos/links');

    echo $this->Html->tag('p',  __d('acl', 'This page allows you to clear all actions ACOs.'),array('class'=>'lead'));

    if ($actionsExist) : ?>
    <p>
    <?php echo $this->Html->tag('div',__d('acl',
        'Clicking the link will destroy all existing actions ACOs and associated permissions.'),array('class'=>'alert alert-warning'));?>

    <?php echo $this->Html->link(
        $this->Html->image('/acl/img/design/cross.png') . ' ' . __d('acl',
            'Clear ACOs'), '/admin/acl/acos/empty_acos/run',
        array(
            'confirm' => __d('acl',
                'Are you sure you want to destroy all existing ACOs ?'),
            'escape' => false
        )); ?>
    </p>
<?php else : ?>
    <p style="font-style:italic;">
   <?php echo __d('acl', 'There is no ACO node to delete'); ?>
    </p>
<?php endif; ?>

<?php echo $this->element('design/footer'); ?>