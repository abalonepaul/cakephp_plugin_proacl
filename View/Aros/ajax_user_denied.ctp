<?php
echo '<span id="right_' . $plugin . '_' . $userId . '_' . $controller_name . '_' . $action . '">';

if (isset($aclError)) {
    if (isset($aclErrorAro)) {
        $title = __d('acl', 'The user node does not exist in the ARO table');
    } elseif (isset($aclErrorAco)) {
        $title = __d('acl', 
            'The ACO node is probably missing. Please try to rebuild the ACOs first.');
    } else {
        $title = __d('acl', 
            'The ARO or the ACO node is probably missing. Please try to rebuild the ACOs first.');
    }
    
    echo $this->Html->image('/acl/img/design/important16.png', 
        array(
            'class' => 'pointer', 
            'alt' => $title, 
            'title' => $title
        ));
} else {
    echo $this->Html->image('/acl/img/design/cross.png', 
        array(
            'class' => 'pointer'
        ));
}

echo '</span>';
?>