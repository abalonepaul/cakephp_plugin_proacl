<?php
echo $this->element('design/header');

echo $this->element('Acos/links');
?>

<?php
if ($run) :
    if (count($logs) > 0) :
?>
    <?php /*@TODO replace with cake tag(p)*/ ?>
        <p>
        <?php echo __d('acl', 'The following actions ACOs have been created');?>
        </p>
        <p>
        <?php echo $this->Html->nestedList($logs); ?>
        </p>

    <?php  else : ?>
        <p>
        <?php echo __d('acl', 'There was no new actions ACOs to create'); ?>
        </p>
<?php     endif;
else:
?>
    <?php /*@TODO replace with cake tag(p)*/ ?>
    <p><div class="row">
    <?php 
    echo $this->Html->tag('p',  __d('acl',
        'This page allows you to build missing actions ACOs if any.'), array('class'=>'lead'));
        ?>

    </div>

<?php     if (count($missingAcoNodes) > 0) : ?>
        <h3><?php echo __d('acl', 'Missing ACOs'); ?></h3>

        <p>
        <?php echo $this->Html->nestedList($missingAcoNodes); ?>
        </p>

        <p>&nbsp;</p>

        <p>
        <?php echo __d('acl',
            'Clicking the link will not destroy existing actions ACOs.'); ?>
        </p>

        <p>
        <?php echo $this->Html->link(
            $this->Html->image('/acl/img/design/add.png') . ' ' . __d('acl',
                'Build'), '/admin/acl/acos/build_acl/run',
            array(
                'escape' => false
            )); ?>
        </p>
    <?php else : ?>
        <p style="font-style:italic;">
        <?php echo $this->Html->image('/acl/img/design/tick.png') . ' ' . __d('acl',
            'There is no ACO node to create'); ?>
        </p>
    <?php endif;
endif;
    ?>

<?php echo $this->element('design/footer'); ?>
