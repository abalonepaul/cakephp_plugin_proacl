<?php
/**
 *
 * @author Nicolas Rod <nico@alaxos.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://www.alaxos.ch
 */
/**
 *
 * @author Nicolas Rod <nico@alaxos.com>
 *
 */
class AclController extends AclAppController {

    public $name = 'Acl';

    public $uses = null;

    public function index() {

        $this->redirect('/admin/acl/aros');
    }

    public function admin_index() {

        $this->redirect('/admin/acl/acos');
    }
}

