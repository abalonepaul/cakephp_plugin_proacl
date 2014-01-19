<?php
echo $this->element('design/header');
?>

<?php
echo $this->element('Acos/links');
?>

<?php
if ($run) {
    if (count($logs) > 0) {
        echo $this->Html->tag('p',__d('acl', 'The following actions ACOs have been pruned'));

        echo $this->Html->nestedList($logs);
    } else {
        echo $this->Html->tag('p', __d('acl', 'There was no actions ACOs to prune'));
    }
} else {
     echo $this->Html->tag('p',__d('acl', 'This page allows you to prune obsolete ACOs.'));


     if (count($nodesToPrune) > 0) {
         echo $this->Html->tag('h3', __d('acl', 'Obsolete ACO nodes'));

         echo $this->Html->nestedList($nodesToPrune);
         
         $this->Html->tag('p', __d('acl',
             'Clicking the link will not change or remove permissions for actions ACOs that are not obsolete.'));
         
         echo $this->Html->tag('p', $this->Html->link(
             $this->Html->image('/acl/img/design/clean.png') . ' ' . __d('acl',
                 'Prune'), '/admin/acl/acos/prune_acos/run',
             array(
                 'escape' => false
             )));
    } else {
        echo '<p style="font-style:italic;">';
        echo $this->Html->image('/acl/img/design/tick.png') . ' ' . __d('acl',
            'There is no ACO node to delete');
        echo '</p>';
    }
}

echo $this->element('design/footer');
?>