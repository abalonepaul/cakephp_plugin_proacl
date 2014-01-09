<?php
App::uses('HtmlHelper', 'View/Helper');
class AclHtmlHelper extends HtmlHelper {

    var $helpers = array(
        'Session'
    );

    function link($title, $url = null, $options = array(), $confirmMessage = false) {

        $permissions = $this->Session->read('Alaxos.Acl.permissions');
        if (! isset($permissions)) {
            $permissions = array();
        }
        
        $acoPath = AclRouter::acoPath($url);
        
        if (isset($permissions[$acoPath]) && $permissions[$acoPath] == 1) {
            return parent::link($title, $url, $options, $confirmMessage);
        } else {
            return null;
        }
    }
}