<?php
/**
 *
 * @author Nicolas Rod <nico@alaxos.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://www.alaxos.ch
 * @property AclManagerComponent $AclManager
 */
/**
 *
 * @author Nicolas Rod <nico@alaxos.com>
 */
class AclAppController extends AppController {

    public $components = array(
        'RequestHandler',
        'Acl.AclManager',
        'Acl.AclReflector'
    );

    /**
     * beforeFilter
     */
    public function beforeFilter() {

        parent::beforeFilter();

        $this->checkConfig();
        $this->checkFileUpdates();
    }

    /**
     * Check the configuration and set view vars
     */
    private function checkConfig() {

        $roleModelName = Configure::read('acl.aro.role.model');

        if (! empty($roleModelName)) {
            $this->set('roleModelName', $roleModelName);
            $this->set('userModelName', Configure::read('acl.aro.user.model'));
            $this->set('rolePkName', $this->getRolePrimaryKeyName());
            $this->set('userPkName', $this->_getUserPrimaryKeyName());
            $this->set('roleFkName', $this->_getRoleForeignKeyName());

            $this->authorizeAdmins();

            if (Configure::read('acl.check_act_as_requester')) {
                $is_requester = true;

                if (! $this->AclManager->checkAclRequester(
                    Configure::read('acl.aro.user.model'))) {
                    $this->set('model_is_not_requester', false);
                    $is_requester = false;
                }

                if (! $this->AclManager->checkAclRequester(
                    Configure::read('acl.aro.role.model'))) {
                    $this->set('role_is_not_requester', false);
                    $is_requester = false;
                }

                if (! $is_requester) {
                    $this->render('/Aros/admin_not_acl_requester');
                }
            }
        } else {
            $this->Session->setFlash(
                __d('acl',
                    'The role model name is unknown. The ACL plugin bootstrap.php file has to be loaded in order to work. (see the README file)'),
                'flash_error', null, 'plugin_acl');
        }
    }

    /**
     * check for for missing Acos or nodes to prune and update the Controller
     * Hash File
     */
    private function checkFileUpdates() {

        if ($this->request->params['controller'] != 'acos' || ($this->request->params['action'] != 'admin_synchronize' && $this->request->params['action'] != 'admin_prune_acos' && $this->request->params['action'] != 'admin_build_acl')) {
            if ($this->AclManager->isControllerHashFileOutOfSync()) {
                $missingAcoNodes = $this->AclManager->getMissingAcos();
                $nodesToPrune = $this->AclManager->getAcosToPrune();

                $hasUpdates = false;

                if (count($missingAcoNodes) > 0) {
                    $hasUpdates = true;
                }

                if (count($nodesToPrune) > 0) {
                    $hasUpdates = true;
                }

                $this->set('nodesToPrune', $nodesToPrune);
                $this->set('missingAcoNodes', $missingAcoNodes);

                if ($hasUpdates) {
                    $this->render('/Acos/admin_has_updates');
                    $this->response->send();
                    $this->AclManager->updateControllersHashFile();
                    exit();
                } else {
                    $this->AclManager->updateControllersHashFile();
                }
            }
        }
    }

    /**
     * Authorize Admins for access to the Admin area.
     */
    private function authorizeAdmins() {

        $authorizedRoleIds = Configure::read('acl.role.access_plugin_role_ids');
        $authorizedUserIds = Configure::read('acl.role.access_plugin_user_ids');

        $modelRoleFk = $this->_getRoleForeignKeyName();

        if (in_array($this->Auth->user($modelRoleFk), $authorizedRoleIds) || in_array(
            $this->Auth->user($this->_getUserPrimaryKeyName()),
            $authorizedUserIds)) {
            // Allow all actions. CakePHP 2.0
            $this->Auth->allow('*');

            // Allow all actions. CakePHP 2.1
            $this->Auth->allow();
        }
    }

    /**
     * Get the Aco passed as an url parameter
     *
     * @return string
     */
    protected function getPassedAcoPath() {

        $acoPath = '';
        if (isset($this->params['named']['plugin'])) {
            $acoPath = $this->params['named']['plugin'];
        }
        if (empty($acoPath)) {
            $acoPath .= $this->params['named']['controller'];
        } else {
            $acoPath .= '/' . $this->params['named']['controller'];
        }
        $acoPath .= '/' . $this->params['named']['action'];

        return $acoPath;
    }

    /**
     * Set the Aco Variables
     */
    protected function setAcoVariables() {

        $this->set('plugin',
            isset($this->params['named']['plugin']) ? $this->params['named']['plugin'] : '');
        $this->set('controller_name', $this->params['named']['controller']);
        $this->set('action', $this->params['named']['action']);
    }

    /**
     * Get the name of the Primary Key
     *
     * @return unknown string
     */
    protected function getRolePrimaryKeyName() {

        $forcedPkName = Configure::read('acl.aro.role.primary_key');
        if (! empty($forcedPkName)) {
            return $forcedPkName;
        } else {
            /* Return the primary key's name that follows the CakePHP
             * conventions */
            return 'id';
        }
    }

    /**
     * Get the Primary Key Name for the User Model
     *
     * @return unknown string
     */
    protected function _getUserPrimaryKeyName() {

        $forcedPkName = Configure::read('acl.aro.user.primary_key');
        if (! empty($forcedPkName)) {
            return $forcedPkName;
        } else {
            /* Return the primary key's name that follows the CakePHP
             * conventions */
            return 'id';
        }
    }

    /**
     * Get the Foreign Key Name for the Role Model
     *
     * @return unknown string
     */
    protected function _getRoleForeignKeyName() {

        $forcedFkName = Configure::read('acl.aro.role.foreign_key');
        if (! empty($forcedFkName)) {
            return $forcedFkName;
        } else {
            /* Return the foreign key's name that follows the CakePHP
             * conventions */
            return Inflector::underscore(Configure::read('acl.aro.role.model')) . '_id';
        }
    }

    /**
     * redirect to the referring page or the admin_index action.
     */
    protected function _returnToReferer() {

        $this->redirect(
            $this->referer(
                array(
                    'action' => 'admin_index'
                )));
    }
}
