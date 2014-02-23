<?php
App::uses('File', 'Utility');
/**
 *
 * @property AclManagerComponent $AclManager
 */
class AclManagerComponent extends Component {

    public $components = array(
        'Auth',
        'Acl',
        'Acl.AclReflector',
        'Session'
    );

    /**
     *
     * @var AclAppController
     */
    private $Controller = null;

    private $controllerHashFile;

    private $Aco;

    /**
     * Initialize the ACL Manager Component
     *
     * @param Controller $controller
     */
    public function initialize(Controller $controller) {

        $this->Controller = $controller;
        $this->controllerHashFile = CACHE . 'persistent' . DS . 'controller_hash.txt';
    }

    /**
     * Check if the file containing the stored controllers hashes can be
     * created, and create it if it does not exist
     *
     * @return boolean true if the file exists or could be created
     */
    private function checkControllerHashFile() {

        if (is_writable(dirname($this->controllerHashFile))) {
            $File = new File($this->controllerHashFile, true);
            return $File->exists();
        } else {
            $this->Session->setFlash(
                sprintf(__d('acl', 'the %s directory is not writable'),
                    dirname($this->controllerHashFile)), 'flash_error', null,
                'plugin_acl');
            return false;
        }
    }

    /**
     * Checks to see if the User model is set to act as an ACL requester or set to act as
     * both a requested and controlled object
     *
     * @param unknown $modelClassName
     * @return boolean
     */
    public function checkAclRequester($modelClassName) {

        $model = $this->getModelInstance($modelClassName);

        $behaviors = $model->actsAs;
        if (! empty($behaviors) && array_key_exists('Acl', $behaviors)) {
            $aclBehavior = $behaviors['Acl'];
            if ($aclBehavior == 'requester' || $aclBehavior == 'both') {
                return true;
            } elseif (is_array($aclBehavior) && isset($aclBehavior['type']) && ($aclBehavior['type'] == 'requester' || $aclBehavior['type'] == 'both')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a given fieldExpression is an existing fieldname for the given
     * model If it doesn't exist, a virtual field called
     * 'alaxos_acl_display_name' is created with the given expression
     *
     * @param string $modelClassName
     * @param string $fieldExpression
     * @return string The name of the field to use as display name
     */
    public function setDisplayName($modelClassName, $fieldExpression) {

        $modelInstance = $this->getModelInstance($modelClassName);

        $schema = $modelInstance->schema();

        if (array_key_exists($fieldExpression, $schema) || array_key_exists(
            str_replace($modelClassName . '.', '', $fieldExpression), $schema) || array_key_exists(
            $fieldExpression, $modelInstance->virtualFields)) {
            /* The field does not need to be created as it already exists in the
             * model as a datatable field, or a virtual field configured in the
             * model
             */

            /*
             * Remove the model name
             */
            if (strpos($fieldExpression, $modelClassName . '.') === 0) {
                $fieldExpression = str_replace($modelClassName . '.', '',
                    $fieldExpression);
            }

            return $fieldExpression;
        } else {
            /* The field does not exist in the model -> create a virtual field
             * with the given expression */

            $this->Controller->{$modelClassName}->virtualFields['alaxos_acl_display_name'] = $fieldExpression;

            return 'alaxos_acl_display_name';
        }
    }

    /**
     * Return an instance of the given model name
     *
     * @param string $modelClassName
     * @return Model
     */
    private function getModelInstance($modelClassName) {

        if (! isset($this->Controller->{$modelClassName})) {
            /* Do not use $this->Controller->loadModel, as calling it from a
             * plugin may prevent correct loading of behaviors */
            $modelInstance = ClassRegistry::init($modelClassName);
        } else {
            $modelInstance = $this->Controller->{$modelClassName};
        }

        return $modelInstance;
    }

    /**
     * return the stored array of controllers hashes
     *
     * @return array
     */
    public function getStoredControllerHash() {

        if ($this->checkControllerHashFile()) {
            $File = new File($this->controllerHashFile);
            $content = $File->read();

            if (! empty($content)) {
                $controllerHash = unserialize($content);
            } else {
                $controllerHash = array();
            }

            return $controllerHash;
        }
    }

    /**
     * return an array of all controllers hashes
     *
     * @return array
     */
    public function getCurrentControllerHash() {

        $controllers = $this->AclReflector->getAllControllers();

        $currentControllerHash = array();

        foreach ($controllers as $controller) {
            $File = new File($controller['file']);
            $currentControllerHash[$controller['name']] = $File->md5();
        }

        return $currentControllerHash;
    }

    /**
     * Return ACOs paths that should exist in the ACO datatable but do not exist
     */
    public function getMissingAcos() {

        $actions = $this->AclReflector->getAllActions();
        $controllers = $this->AclReflector->getAllControllers();

        $actionsAcoPaths = array();
        foreach ($actions as $action) {
            $actionPath = explode('/', $action);
            $controller = $action_infos[count($action_infos) - 2];

            if ($controller != 'App') {
                $actionsAcoPaths[] = 'controllers/' . $action;
            }
        }
        foreach ($controllers as $controller) {
            if ($controller['name'] != 'App') {
                $actionsAcoPaths[] = 'controllers/' . $controller['name'];
            }
        }
        $actionsAcoPaths[] = 'controllers';

        $aco =& $this->Acl->Aco;

        $acos = $aco->find('all',
            array(
                'recursive' => - 1
            ));

        $existingAcoPaths = array();
        foreach ($acos as $acoNode) {
            $pathNodes = $aco->getPath($acoNode['Aco']['id']);
            $path = '';
            foreach ($pathNodes as $pathNode) {
                $path .= '/' . $pathNode['Aco']['alias'];
            }

            $path = substr($path, 1);
            $existingAcoPaths[] = $path;
        }

        $missingAcos = array_diff($actionsAcoPaths, $existingAcoPaths);

        return $missingAcos;
    }

    /**
     * Store missing ACOs for all actions in the datasource If necessary, it
     * creates actions parent nodes (plugin and controller) as well
     */
    public function createAcos() {

        $this->Aco =& $this->Acl->Aco;

        $log = array();

        $controllers = $this->AclReflector->getAllControllers();


         // Create 'controllers' node if it does not exist

        $root = $this->Aco->node('controllers');
        if (empty($root)) {
            $root = $this->addRootNode($this->Aco->id);
            if(!empty($root)) {
                $log[] = __d('acl', 'Created Aco node for controllers');
            }

        } else {
            $root = $root[0];
        }

        foreach ($controllers as $controller) {
            $controllerName = $controller['name'];

            if ($controllerName !== 'App') {
                $pluginName = $this->AclReflector->getPluginName(
                    $controllerName);
                $pluginNode = null;

                if (! empty($pluginName)) {
                    /* Case of plugin controller */

                    $controllerName = $this->AclReflector->getPluginControllerName(
                        $controllerName);


                     //Check plugin node

                    $pluginNode = $this->Aco->node('controllers/' . $pluginName);
                    if (empty($pluginNode)) {
                        /* plugin node does not exist -> create it */
                        $pluginNode = $this->addPluginNode($root['Aco']['id'], $this->Aco->id, $pluginName);
                        if (!empty($pluginNode)) {
                            $log[] = sprintf(
                            __d('acl', 'Created Aco node for %s plugin'),
                            $pluginName);
                        }
                    }
                }

                /**
                 * Check controller node
                 */
                $controllerNode = $this->Aco->node(
                    'controllers/' . (! empty($pluginName) ? $pluginName . '/' : '') . $controllerName);
                if (empty($controllerNode)) {
                    /* controller node does not exist -> create it */
                    $controllerNode = $this->addControllerNode($controllerName, $root['Aco']['id'], $this->Aco->id, $pluginNode);
                    if (!empty($controllerNode)) {
                        $loggedController = $controllerName;
                        if(!empty($pluginName)) {
                            $loggedController = $pluginName . '/' . $controllerName;
                        }
                        $log[] = sprintf(__d('acl', 'Created Aco node for %s'),
                            $loggedController);

                    }
                } else {
                    $controllerNode = $controllerNode[0];
                }

                /**
                 * Check controller actions node
                 */
                $actions = $this->AclReflector->getControllerActions(
                    $controllerName);

                foreach ($actions as $action) {
                    $actionNode = $aco->node(
                        'controllers/' . (! empty($pluginName) ? $pluginName . '/' : '') . $controllerName . '/' . $action);

                    if (empty($actionNode)) {
                        /* action node does not exist -> create it */
                        $methodNode = $this->addActionNode($controllerNodId, $action);

                        if (!empty($methodNode)) {
                            $log[] = sprintf(__d('acl', 'Created Aco node for %s'),
                                (! empty($pluginName) ? $pluginName . '/' : '') . $controllerName . '/' . $action);
                        }
                    }
                }
            }
        }

        return $log;
    }

    /**
     * Add the root controllers node.
     * @param unknown $acoId
     * @return unknown
     */
    private function addRootNode($acoId) {
        /* root node does not exist -> create it */
        $this->Aco->create(
            array(
                'parent_id' => null,
                'model' => null,
                'alias' => 'controllers'
            ));
        $root = $this->Aco->save();
        $root['Aco']['id'] = $acoId;

        return $root;

    }

    /**
     * Add a node for a Plugin
     * @param unknown $rootAcoId
     * @param unknown $acoId
     * @param unknown $pluginName
     * @return unknown
     */
    private function addPluginNode($rootAcoId,$acoId,$pluginName) {
        $this->Aco->create(
            array(
                'parent_id' => $rootAcoId,
                'model' => null,
                'alias' => $pluginName
            ));
        $pluginNode = $this->Aco->save();
        $pluginNode['Aco']['id'] = $acoId;

        return $pluginNode;

    }

    /**
     * Add a Controller Node
     *
     * @param unknown $controllerName
     * @param unknown $rootAcoId
     * @param unknown $acoId
     * @param string $pluginNode
     * @return unknown
     */
    private function addControllerNode($controllerName, $rootAcoId, $acoId, $pluginNode = null) {
        /**
         * @todo make this a new method addControllerNode
         */

        if (isset($pluginNode)) {
            /* The controller belongs to a plugin */

            $pluginNodeAcoId = isset($pluginNode[0]) ? $pluginNode[0]['Aco']['id'] : $pluginNode['Aco']['id'];

            $this->Aco->create(
                array(
                    'parent_id' => $pluginNodeAcoId,
                    'model' => null,
                    'alias' => $controllerName
                ));
            $controllerNode = $this->Aco->save();
            $controllerNode['Aco']['id'] = $acoId;
        } else {
            /* The controller is an app controller */

            $this->Aco->create(
                array(
                    'parent_id' => $rootAcoId,
                    'model' => null,
                    'alias' => $controllerName
                ));
            $controllerNode = $this->Aco->save();
            $controllerNode['Aco']['id'] = $acoId;

        }

        return $controllerNode;

    }

    /**
     * Add an Action Node
     *
     * @param unknown $controllerNodId
     * @param unknown $action
     * @return unknown
     */
    private function addActionNode($controllerNodId,$action) {
                        $this->Aco->create(
                            array(
                                'parent_id' => $controllerNode['Aco']['id'],
                                'model' => null,
                                'alias' => $action
                            ));
                        $methodNode = $this->Aco->save();

        return $pluginNode;

    }

    /**
     * Update the Controller Hash File
     */
    public function updateControllerHashFile() {

        $currentControllerHash = $this->getCurrentControllerHash();

        $File = new File($this->controllerHashFile);
        return $File->write(serialize($currentControllerHashes));

    }

    /**
     * Check to see if the Controller Hash File is out of sync.
     * @return boolean
     */
    public function isControllerHashFileOutOfSync() {

        if ($this->checkControllerHashFile()) {
            $storedControllerHash = $this->getStoredControllerHash();
            $currentControllerHash = $this->getCurrentControllerHash();

            /* Check what controllers have changed */
            $updatedControllers = array_keys(
                Hash::diff($currentControllerHash,$storedControllerHash));
            if (!empty($updatedControllers)) {
                return true;
            }
            return false;
        }
    }

    /**
     * Get the array of Acos to prune.
     * @return multitype:
     */
    public function getAcosToPrune() {

        $actions = $this->AclReflector->getAllActions();
        $controllers = $this->AclReflector->getAllControllers();
        $plugins = $this->AclReflector->getAllPluginNames();

        $actionsAcoPaths = array();
        foreach ($actions as $action) {
            $actionsAcoPaths[] = 'controllers/' . $action;
        }
        foreach ($controllers as $controller) {
            $actionsAcoPaths[] = 'controllers/' . $controller['name'];
        }
        foreach ($plugins as $plugin) {
            $actionsAcoPaths[] = 'controllers/' . $plugin;
        }
        $actionsAcoPaths[] = 'controllers';

        $this->Aco = & $this->Acl->Aco;

        $acos = $this->Aco->find('all',
            array(
                'recursive' => - 1
            ));

        $existingAcoPaths = array();
        foreach ($acos as $aco_node) {
            $pathNodes = $this->Aco->getPath($acoNode['Aco']['id']);

            if (count($pathNodes) > 1 && $pathNodes[0]['Aco']['alias'] == 'controllers') {
                $path = '';
                foreach ($pathNodes as $pathNode) {
                    $path .= '/' . $pathNode['Aco']['alias'];
                }

                $path = substr($path, 1);
                $existingAcoPaths[] = $path;
            }
        }

        return array_diff($existingAcoPaths, $actionsAcoPaths);
    }

    /**
     * Remove all ACOs that don't have any corresponding controllers or actions.
     *
     * @return array log of removed ACO nodes
     */
    public function pruneAcos() {

        $this->Aco = & $this->Acl->Aco;

        $log = array();

        $pathsToPrune = $this->getAcosToPrune();

        foreach ($pathsToPrune as $pathToPrune) {
            $node = $this->Aco->node($pathToPrune);
            if (! empty($node)) {
                /* First element is the last part in path -> we delete it */
                if ($this->Aco->delete($node[0]['Aco']['id'])) {
                    $log[] = sprintf(
                        __d('acl', "Aco node '%s' has been deleted"),
                        $pathToPrune);
                } else {
                    $log[] = '<span class="error">' . sprintf(
                        __d('acl', "Aco node '%s' could not be deleted"),
                        $pathToPrune) . '</span>';
                }
            }
        }

        return $log;
    }

    /**
     *
     * @param AclNode $aroNodes The Aro model hierarchy
     * @param string $acoPath The Aco path to check for
     * @param string $permission_type 'deny' or 'allow', 'grant', depending on
     * what permission (grant or deny) is being set
     */
    public function savePermissions($aroNodes, $acoPath, $permission_type) {

        if (isset($aroNodes[0])) {
            $acoPath = 'controllers/' . $acoPath;

            $pkName = 'id';
            if ($aroNodes[0]['Aro']['model'] == Configure::read(
                'acl.aro.role.model')) {
                $pkName = $this->Controller->getRolePrimaryKeyName();
            } elseif ($aroNodes[0]['Aro']['model'] == Configure::read(
                'acl.aro.user.model')) {
                $pkName = $this->Controller->_getUserPrimaryKeyName();
            }

            $aroModelData = array(
                $aroNodes[0]['Aro']['model'] => array(
                    $pkName => $aroNodes[0]['Aro']['foreign_key']
                )
            );
            $aroId = $aroNodes[0]['Aro']['id'];

            $specificPermissionRight = $this->getSpecificPermissionRight(
                $aroNodes[0], $acoPath);
            $inheritedPermissionRight = $this->getFirstParentPermissionRight(
                $aroNodes[0], $acoPath);

            if (! isset($inheritedPermissionRight) && count($aroNodes) > 1) {
                /* Get the permission inherited by the parent ARO */
                $specificParentAroPermissionRight = $this->getSpecificPermissionRight(
                    $aroNodes[1], $acoPath);

                if (isset($specificParentAroPermissionRight)) {
                    /* If there is a specific permission for the parent ARO on
                     * the ACO, the child ARO inheritates this permission */
                    $inheritedPermissionRight = $specificParentAroPermissionRight;
                } else {
                    $inheritedPermissionRight = $this->getFirstParentPermissionRight(
                        $aroNodes[1], $acoPath);
                }
            }

            /* Check if the specific permission is necessary to get the correct
             * permission */
            if (! isset($inheritedPermissionRight)) {
                $specificPermissionNeeded = true;
            } else {
                if ($permissionType == 'allow' || $permissionType == 'grant') {
                    $specificPermissionNeeded = ($inheritedPermissionRight != 1);
                } else {
                    $specificPermissionNeeded = ($inheritedPermissionRight == 1);
                }
            }

            if ($specificPermissionNeeded) {
                if ($permissionType == 'allow' || $permissionType == 'grant') {
                    if ($this->Acl->allow($aroModelData, $acoPath)) {
                        return true;
                    } else {
                        trigger_error(
                            __d('acl',
                                'An error occured while saving the specific permission'),
                            E_USER_NOTICE);
                        return false;
                    }
                } else {
                    if ($this->Acl->deny($aroModelData, $acoPath)) {
                        return true;
                    } else {
                        trigger_error(
                            __d('acl',
                                'An error occured while saving the specific permission'),
                            E_USER_NOTICE);
                        return false;
                    }
                }
            } elseif (isset($specificPermissionRight)) {
                $acoNode = $this->Acl->Aco->node($acoPath);
                if (! empty($acoNode)) {
                    $acoId = $acoNode[0]['Aco']['id'];

                    $specificPermission = $this->Acl->Aro->Permission->find(
                        'first',
                        array(
                            'conditions' => array(
                                'aro_id' => $aroId,
                                'aco_id' => $acoId
                            )
                        ));

                    if ($specificPermission !== false) {
                        if ($this->Acl->Aro->Permission->delete(
                            array(
                                'Permission.id' => $specificPermission['Permission']['id']
                            ))) {
                            return true;
                        } else {
                            trigger_error(
                                __d('acl',
                                    'An error occured while deleting the specific permission'),
                                E_USER_NOTICE);
                            return false;
                        }
                    } else {
                        /* As $specific_permission_right has a value, we should
                         * never fall here, but who knows... ;-) */

                        trigger_error(
                            __d('acl',
                                'The specific permission id could not be retrieved'),
                            E_USER_NOTICE);
                        return false;
                    }
                } else {
                    /* As $specific_permission_right has a value, we should
                     * never fall here, but who knows... ;-) */
                    trigger_error(
                        __d('acl', 'The child ACO id could not be retrieved'),
                        E_USER_NOTICE);
                    return false;
                }
            } else {
                /* Right can be inherited, and no specific permission exists =>
                 * there is nothing to do... */
            }
        } else {
            trigger_error(__d('acl', 'Invalid ARO'), E_USER_NOTICE);
            return false;
        }
    }

    /**
     * Get the Permissions for a given node and aco path.
     * @param unknown $aroNode
     * @param unknown $acoPath
     * @return number|NULL
     */
    private function getSpecificPermissionRight($aroNode, $acoPath) {

        $pkName = 'id';
        if ($aroNode['Aro']['model'] == Configure::read('acl.aro.role.model')) {
            $pkName = $this->Controller->getRolePrimaryKeyName();
        } elseif ($aroNode['Aro']['model'] == Configure::read(
            'acl.aro.user.model')) {
            $pkName = $this->Controller->_getUserPrimaryKeyName();
        }

        $aroModelData = array(
            $aroNode['Aro']['model'] => array(
                $pkName => $aroNode['Aro']['foreign_key']
            )
        );
        $aroId = $aroNode['Aro']['id'];

        /* Check if a specific permission of the ARO's on the ACO already exists
         * in the datasource => 		1) the ACO node must exist in the ACO table
         * 		2) a record with the aro_id and aco_id must exist in the aros_acos
         * table */
        $acoId = null;
        $specificPermission = null;
        $specificPermissionRight = null;

        $acoNode = $this->Acl->Aco->node($acoPath);
        if (! empty($aco_node)) {
            $aco_id = $aco_node[0]['Aco']['id'];

            $specificPermission = $this->Acl->Aro->Permission->find('first',
                array(
                    'conditions' => array(
                        'aro_id' => $aroId,
                        'aco_id' => $acoId
                    )
                ));

            if ($specificPermission !== false) {
                /* Check the right (grant => true / deny => false) of this
                 * specific permission */
                $specificPermissionRight = $this->Acl->check($aroModelData,
                    $acoPath);

                if ($specificPermissioRight) {
                    return 1; // allowed
                } else {
                    return - 1; // denied
                }
            }
        }

        return null; // no specific permission found
    }

    /**
     * get the First Parent Permission for a given Aro Node and Path
     * @param unknown $aroNode
     * @param unknown $acoPath
     * @return number|NULL
     */
    private function getFirstParentPermissionRight($aroNode, $acoPath) {

        $pkName = 'id';
        if ($aroNode['Aro']['model'] == Configure::read('acl.aro.role.model')) {
            $pkName = $this->Controller->getRolePrimaryKeyName();
        } elseif ($aroNode['Aro']['model'] == Configure::read(
            'acl.aro.user.model')) {
            $pkName = $this->Controller->_getUserPrimaryKeyName();
        }

        $aroModelData = array(
            $aroNode['Aro']['model'] => array(
                $pkName => $aroNode['Aro']['foreign_key']
            )
        );
        $aroId = $aroNode['Aro']['id'];

        while (strpos($acoPath, '/') !== false && ! isset(
            $parentPermissionright)) {
            $acoPath = substr($acoPath, 0, strrpos($acoPath, '/'));

            $parentAcoNode = $this->Acl->Aco->node($acoPath);
            if (! empty($parentAcoNode)) {
                $parentAcoId = $parentAcoNode[0]['Aco']['id'];

                $parentPermission = $this->Acl->Aro->Permission->find('first',
                    array(
                        'conditions' => array(
                            'aro_id' => $aroId,
                            'aco_id' => $parentAcoId
                        )
                    ));

                if ($parentPermission !== false) {
                    /* Check the right (grant => true / deny => false) of this
                     * first parent permission */
                    $parentPermissionRight = $this->Acl->check(
                        $aroModelData, $acoPath);

                    if ($parentPermissionRight) {
                        return 1; // allowed
                    } else {
                        return - 1; // denied
                    }
                }
            }
        }

        return null; // no parent permission found
    }

    /**
     * Set the permissions of the authenticated user in Session The session
     * permissions are then used for instance by the AclHtmlHelper->link()
     * function
     */
    public function setSessionPermissions() {

        if (! $this->Session->check('Alaxos.Acl.permissions')) {
            $actions = $this->AclReflector->getAllActions();

            $user = $this->Auth->user();

            if (! empty($user)) {
                $user = array(
                    Configure::read('acl.aro.user.model') => $user
                );
                $permissions = array();

                foreach ($actions as $action) {
                    $acoPath = 'controllers/' . $action;

                    $permissions[$acoPath] = $this->Acl->check($user,
                        $acoPath);
                }

                $this->Session->write('Alaxos.Acl.permissions', $permissions);
            }
        }
    }
}