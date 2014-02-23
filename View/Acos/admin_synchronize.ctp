<?php echo $this->element('design/header'); ?>
<?php echo $this->element('Acos/links');?>

<?php
if ($run) {
    echo '<h3>' . __d('acl', 'New ACOs') . '</h3>';
    
    if (count($createLogs) > 0) {
        // echo '<p>';
        // echo __d('acl', 'The following actions ACOs have been created');
        // echo '<p>';
        echo $this->Html->nestedList($createLogs);
    } else {
        echo $this->Html->tag('p',__d('acl', 'There was no new actions ACOs to create'));
    }
    
    echo $this->Html->tag('h3', __d('acl', 'Obsolete ACOs'));
    
    if (count($pruneLogs) > 0) {
        // echo '<p>';
        // echo __d('acl', 'The following actions ACOs have been deleted');
        // echo '<p>';
        echo $this->Html->nestedList($pruneLogs);
    } else {
        echo $this->Html->tag('h3', __d('acl', 'There was no action ACO to delete'));
    }
} else {

    echo $this->Html->tag('p', __d('acl', 
        'This page allows you to synchronize the existing controllers and actions with the ACO datatable.'),array('class'=>'lead'));

    $has_aco_to_sync = false;
    
    if (count($missingAcoNodes) > 0) {
        echo '<h3>' . __d('acl', 'Missing ACOs') . '</h3>';
        
        echo $this->Html->tag('div', $this->Html->nestedList($missingAcoNodes), array('class'=>'row'));
        
        $has_aco_to_sync = true;
    }
    
    if (count($nodesToPrune) > 0) {
        echo '<h3>' . __d('acl', 'Obsolete ACO nodes') . '</h3>';
        
        echo $this->Html->tag('div', $this->Html->nestedList($nodesToPrune), array('class'=>'row'));
        
        $has_aco_to_sync = true;
    }
    
    if ($has_aco_to_sync) {
        
        echo '<p>'.__d('acl', 
            'Clicking the link will not change or remove permissions for existing actions ACOs.')
            .'<br />';
        

        echo $this->Html->link(
            $this->Html->image('/acl/img/design/sync.png') . ' ' . __d('acl', 
                'Synchronize'), '/admin/acl/acos/synchronize/run', 
            array(
                'escape' => false
            ));
        echo '</p>';
    } else {
        echo '<p><em>';
        echo $this->Html->image('/acl/img/design/tick.png') . ' ' . __d('acl', 
            'The ACO datatable is already synchronized');
        echo '</em></p>';
    }
}

echo $this->element('design/footer');
?>