<?php
/**
 *
 * @author Paul Marshall
 * @author Nicolas Rod <nico@alaxos.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://www.protelligence.com
 * @property AclReflectorComponent $AclReflector
 */
class ArosController extends AclAppController {

    public $name = 'Aros';

    public $uses = array(
        'Aro'
    );

    public $helpers = array(
        'Js' => array(
            'Jquery'
        )
    );

    public $paginate = array(
        'limit' => 20
    // 'order' => array('display_name' => 'asc')
        );

    /**
     * (non-PHPdoc)
     * @see AclAppController::beforeFilter()
     */
    public function beforeFilter() {

        $this->loadModel(Configure::read('acl.aro.role.model'));
        $this->loadModel(Configure::read('acl.aro.user.model'));

        parent::beforeFilter();
    }

    /**
     * List the Access Request Objects
     */
    public function admin_index() {

    }

    /**
     * Check for missing AROs or build new AROs
     * @param string $run
     */
    public function admin_check($run = null) {

        $userModelName = Configure::read('acl.aro.user.model');
        $roleModelName = Configure::read('acl.aro.role.model');

        $userDisplayField = $this->AclManager->setDisplayName($userModelName, Configure::read('acl.user.display_name'));
        $roleDisplayField = $this->AclManager->setDisplayName($roleModelName, Configure::read('acl.aro.role.display_field'));

        $roles = $this->{$roleModelName}->find('all',
            array(
                'order' => $roleDisplayField,
                'contain' => false,
                'recursive' => - 1
            ));

        $missingAros = array(
            'roles' => array(),
            'users' => array()
        );

        foreach ($roles as $role) {
            /* Check if ARO for role exist */
            $aro = $this->Aro->find('first',
                array(
                    'conditions' => array(
                        'model' => $roleModelName,
                        'foreign_key' => $role[$roleModelName][$this->getRolePrimaryKeyName()]
                    )
                ));
            //If there is no ARO, then add the missing ARO to the missingAros array.
            if (empty($aro)) {
                $missingAros['roles'][] = $role;
            }
        }

        $users = $this->{$userModelName}->find('all',
            array(
                'order' => $userDisplayField,
                'contain' => false,
                'recursive' => - 1
            ));
        foreach ($users as $user) {
            /* Check if ARO for user exist */
            $aro = $this->Aro->find('first',
                array(
                    'conditions' => array(
                        'model' => $userModelName,
                        'foreign_key' => $user[$userModelName][$this->_getUserPrimaryKeyName()]
                    )
                ));

            if (empty($aro)) {
                $missingAros['users'][] = $user;
            }
        }

        //Add missing AROs
        if (isset($run)) {
            $this->set('run', true);

            // Add roles AROs
            if (count($missingAros['roles']) > 0) {
                foreach ($missingAros['roles'] as $k => $role) {
                    $this->Aro->create(
                        array(
                            'parent_id' => null,
                            'model' => $roleModelName,
                            'foreign_key' => $role[$roleModelName][$this->getRolePrimaryKeyName()],
                            'alias' => $role[$roleModelName][$roleDisplayField]
                        ));

                    if ($this->Aro->save()) {
                        unset($missingAros['roles'][$k]);
                    }
                }
            }

            /* Add User AROs */
            if (count($missingAros['users']) > 0) {
                foreach ($missingAros['users'] as $k => $user) {
                    /* Find ARO parent for user ARO */
                    $parent_id = $this->Aro->field('id',
                        array(
                            'model' => $roleModelName,
                            'foreign_key' => $user[$userModelName][$this->_getRoleForeignKeyName()]
                        ));

                    if ($parent_id !== false) {
                        $this->Aro->create(
                            array(
                                'parent_id' => $parent_id,
                                'model' => $userModelName,
                                'foreign_key' => $user[$userModelName][$this->_getUserPrimaryKeyName()],
                                'alias' => $user[$userModelName][$userDisplayField]
                            ));

                        if ($this->Aro->save()) {
                            unset($missingAros['users'][$k]);
                        }
                    }
                }
            }
        } else {
            $this->set('run', false);
        }

        $this->set(compact('userDisplayField','roleDisplayField','missingAros'));

    }

    /**
     * View Users list
     */
    public function admin_users() {

        $userModelName = Configure::read('acl.aro.user.model');
        $roleModelName = Configure::read('acl.aro.role.model');

        $userDisplayField = $this->AclManager->setDisplayName( $userModelName, Configure::read('acl.user.display_name'));
        $roleDisplayField = $this->AclManager->setDisplayName( $roleModelName, Configure::read('acl.aro.role.display_field'));

        $this->paginate['order'] = array(
            $userDisplayField => 'asc'
        );

        $this->{$roleModelName}->recursive = - 1;
        $roles = $this->{$roleModelName}->find('all',
            array(
                'order' => $roleDisplayField,
                'contain' => false,
                'recursive' => - 1
            ));

        $this->{$userModelName}->recursive = - 1;

        if (isset($this->request->data['User'][$userDisplayField]) || $this->Session->check('acl.aros.users.filter')) {
            if (! isset($this->request->data['User'][$userDisplayField])) {
                $this->request->data['User'][$userDisplayField] = $this->Session->read('acl.aros.users.filter');
            } else {
                $this->Session->write('acl.aros.users.filter', $this->request->data['User'][$userDisplayField]);
            }

            $filter = array(
                $userModelName . '.' . $userDisplayField . ' LIKE' => '%' . $this->request->data['User'][$userDisplayField] . '%'
            );
        } else {
            $filter = array();
        }

        $users = $this->paginate($userModelName, $filter);

        $missingAro = false;

        foreach ($users as &$user) {
            $aro = $this->Aro->find('first',
                array(
                    'conditions' => array(
                        'model' => $userModelName,
                        'foreign_key' => $user[$userModelName][$this->_getUserPrimaryKeyName()]
                    )
                ));

            if ($aro !== false) {
                $user['Aro'] = $aro['Aro'];
            } else {
                $missingAro = true;
            }
            unset($user);
        }
        $this->set(compact('userDisplayField','roleDisplayField','roles','users','missingAro'));
    }

    /**
     * Update a User's role
     */
   public function admin_update_user_role() {

        $userModelName = Configure::read('acl.aro.user.model');

        $data = array(
            $userModelName => array(
                $this->_getUserPrimaryKeyName() => $this->params['named']['user'],
                $this->_getRoleForeignKeyName() => $this->params['named']['role']
            )
        );

        if ($this->{$userModelName}->save($data)) {
            $this->Session->setFlash(
                __d('acl', 'The user role has been updated'), 'flash_message',
                null, 'plugin_acl');
        } else {
            $errors = array_merge(
                array(
                    __d('acl', 'The user role could not be updated')
                ), $this->{$userModelName}->validationErrors);
            $this->Session->setFlash($errors, 'flash_error', null, 'plugin_acl');
        }

        $this->_returnToReferer();
    }

    /**
     * View/Set the role based permissions.
     */
    public function admin_ajax_role_permissions() {

        $roleModelName = Configure::read('acl.aro.role.model');

        $roleDisplayField = $this->AclManager->setDisplayName($roleModelName, Configure::read('acl.aro.role.display_field'));

        $roles = $this->{$roleModelName}->find('all',
            array(
                'order' => $roleDisplayField,
                'contain' => false,
                'recursive' => - 1
            ));

        $actions = $this->AclReflector->getAllActions();

        $methods = array();
        foreach ($actions as $k => $fullAction) {
            $arr = String::tokenize($fullAction, '/');

            if (count($arr) == 2) {
                $pluginName = null;
                $controllerName = $arr[0];
                $action = $arr[1];
            } elseif (count($arr) == 3) {
                $pluginName = $arr[0];
                $controllerName = $arr[1];
                $action = $arr[2];
            }

            if ($controllerName == 'App') {
                unset($actions[$k]);
            } else {
                if (isset($pluginName)) {
                    $methods['plugin'][$pluginName][$controllerName][] = array('name' => $action);
                } else {
                    $methods['app'][$controllerName][] = array('name' => $action);
                }
            }
        }

        $this->set(compact('roleDisplayField','roles', 'actions'));
    }

    /**
     * View/Set Role Based Permissions.
     */
    public function admin_role_permissions() {

        $roleModelName = Configure::read('acl.aro.role.model');

        $roleDisplayField = $this->AclManager->setDisplayName($roleModelName, Configure::read('acl.aro.role.display_field'));

        $roles = $this->{$roleModelName}->find('all',
            array(
                'order' => $roleDisplayField,
                'contain' => false,
                'recursive' => - 1
            ));

        $actions = $this->AclReflector->getAllActions();

        $permissions = array();
        $methods = array();

        foreach ($actions as $full_action) {
            $arr = String::tokenize($full_action, '/');

            if (count($arr) == 2) {
                $pluginName = null;
                $controllerName = $arr[0];
                $action = $arr[1];
            } elseif (count($arr) == 3) {
                $pluginName = $arr[0];
                $controllerName = $arr[1];
                $action = $arr[2];
            }

            if ($controllerName != 'App') {
                foreach ($roles as $role) {
                    $aroNode = $this->Acl->Aro->node($role);
                    if (! empty($aroNode)) {
                        $acoNode = $this->Acl->Aco->node('controllers/' . $full_action);
                        if (! empty($acoNode)) {
                            $authorized = $this->Acl->check($role, 'controllers/' . $full_action);

                            $permissions[$role[Configure::read('acl.aro.role.model')][$this->getRolePrimaryKeyName()]] = $authorized ? 1 : 0;
                        }
                    } else {
                        /* No check could be done as the ARO is missing */
                        $permissions[$role[Configure::read('acl.aro.role.model')][$this->getRolePrimaryKeyName()]] = - 1;
                    }
                }

                if (isset($pluginName)) {
                    $methods['plugin'][$pluginName][$controllerName][] = array(
                        'name' => $action,
                        'permissions' => $permissions
                    );
                } else {
                    $methods['app'][$controllerName][] = array(
                        'name' => $action,
                        'permissions' => $permissions
                    );
                }
            }
        }
        $this->set(compact('actions', 'roles', 'roleDisplayField'));
    }

    /**
     * Display the user permissions view
     * @param string $userId
     */
    public function admin_user_permissions($userId = null) {

        $userModelName = Configure::read('acl.aro.user.model');
        $roleModelName = Configure::read('acl.aro.role.model');

        $userDisplayField = $this->AclManager->setDisplayName( $userModelName, Configure::read('acl.user.displayName'));

        $this->paginate['order'] = array(
            $userDisplayField => 'asc'
        );

        if (empty($userId)) {
            if (isset($this->request->data['User'][$userDisplayField]) || $this->Session->check(
                'acl.aros.user_permissions.filter')) {
                if (! isset($this->request->data['User'][$userDisplayField])) {
                    $this->request->data['User'][$userDisplayField] = $this->Session->read(
                        'acl.aros.user_permissions.filter');
                } else {
                    $this->Session->write('acl.aros.user_permissions.filter',
                        $this->request->data['User'][$userDisplayField]);
                }

                $filter = array(
                    $userModelName . '.' . $userDisplayField . ' LIKE' => '%' . $this->request->data['User'][$userDisplayField] . '%'
                );
            } else {
                $filter = array();
            }

            $users = $this->paginate($userModelName, $filter);

            $this->set('users', $users);
        } else {
            $roleDisplayField = $this->AclManager->setDisplayName(
                $roleModelName,
                Configure::read('acl.aro.role.display_field'));

            $this->set('roleDisplayField', $roleDisplayField);

            $this->{$roleModelName}->recursive = - 1;
            $roles = $this->{$roleModelName}->find('all',
                array(
                    'order' => $roleDisplayField,
                    'contain' => false,
                    'recursive' => - 1
                ));

            $this->{$userModelName}->recursive = - 1;
            $user = $this->{$userModelName}->read(null, $userId);

            $permissions = array();
            $methods = array();

            /* Check if the user exists in the ARO table */
            $user_aro = $this->Acl->Aro->node($user);
            if (empty($user_aro)) {
                $display_user = $this->{$userModelName}->find('first',
                    array(
                        'conditions' => array(
                            $userModelName . '.id' => $userId,
                            'contain' => false,
                            'recursive' => - 1
                        )
                    ));
                $this->Session->setFlash(
                    sprintf(
                        __d('acl',
                            "The user '%s' does not exist in the ARO table"),
                        $display_user[$userModelName][$userDisplayField]),
                    'flash_error', null, 'plugin_acl');
            } else {
                $actions = $this->AclReflector->getAllActions();

                foreach ($actions as $full_action) {
                    $arr = String::tokenize($full_action, '/');

                    if (count($arr) == 2) {
                        $pluginName = null;
                        $controllerName = $arr[0];
                        $action = $arr[1];
                    } elseif (count($arr) == 3) {
                        $pluginName = $arr[0];
                        $controllerName = $arr[1];
                        $action = $arr[2];
                    }

                    if ($controllerName != 'App') {
                        if (! isset($this->params['named']['ajax'])) {
                            $acoNode = $this->Acl->Aco->node(
                                'controllers/' . $full_action);
                            if (! empty($acoNode)) {
                                $authorized = $this->Acl->check($user,
                                    'controllers/' . $full_action);

                                $permissions[$user[$userModelName][$this->_getUserPrimaryKeyName()]] = $authorized ? 1 : 0;
                            }
                        }

                        if (isset($pluginName)) {
                            $methods['plugin'][$pluginName][$controllerName][] = array(
                                'name' => $action,
                                'permissions' => $permissions
                            );
                        } else {
                            $methods['app'][$controllerName][] = array(
                                'name' => $action,
                                'permissions' => $permissions
                            );
                        }
                    }
                }

                /* Check if the user has specific permissions */
                $count = $this->Aro->Permission->find('count',
                    array(
                        'conditions' => array(
                            'Aro.id' => $user_aro[0]['Aro']['id']
                        )
                    ));
                if ($count != 0) {
                    $this->set('user_has_specific_permissions', true);
                } else {
                    $this->set('user_has_specific_permissions', false);
                }
            }

            $this->set(compact('actions', 'roles', 'user','userDisplayField'));

            if (isset($this->params['named']['ajax'])) {
                $this->render('admin_ajax_user_permissions');
            }
        }
    }

    /**
     * Clear all the permissions
     */
    public function admin_empty_permissions() {

        if ($this->Aro->Permission->deleteAll(array('Permission.id > ' => 0))) {
            $this->Session->setFlash(
                __d('acl', 'The permissions have been cleared'), 'flash_message',
                null, 'plugin_acl');
        } else {
            $this->Session->setFlash(
                __d('acl', 'The permissions could not be cleared'),
                'flash_error', null, 'plugin_acl');
        }

        $this->_returnToReferer();
    }

    /**
     * Clear Permissions for a given user.
     * @param unknown $userId
     */
    public function admin_clear_user_specific_permissions($userId) {

        $User = & $this->{Configure::read('acl.aro.user.model')};
        $User->id = $userId;

        /* Check if the user exists in the ARO table */
        $node = $this->Acl->Aro->node($User);
        if (empty($node)) {
            $askedUser = $User->read(null, $userId);
            $this->Session->setFlash(
                sprintf(
                    __d('acl', "The user '%s' does not exist in the ARO table"),
                    $askedUser[Configure::read('acl.aro.user.model')][Configure::read('acl.user.display_name')]),
                'flash_error', null, 'plugin_acl');
        } else {
            if ($this->Aro->Permission->deleteAll(
                array(
                    'Aro.id' => $node[0]['Aro']['id']
                ))) {
                $this->Session->setFlash(
                    __d('acl', 'The specific permissions have been cleared'),
                    'flash_message', null, 'plugin_acl');
            } else {
                $this->Session->setFlash(
                    __d('acl', 'The specific permissions could not be cleared'),
                    'flash_error', null, 'plugin_acl');
            }
        }

        $this->_returnToReferer();
    }

    /**
     * Grant all permissions to a given role.
     * @param unknown $roleId
     */
    public function admin_grant_all_controllers($roleId) {

        $Role = & $this->{Configure::read('acl.aro.role.model')};
        $Role->id = $roleId;

        /* Check if the Role exists in the ARO table */
        $node = $this->Acl->Aro->node($Role);
        if (empty($node)) {
            $askedRole = $role->read(null, $roleId);
            $this->Session->setFlash(
                sprintf(
                    __d('acl', "The role '%s' does not exist in the ARO table"),
                    $askedRole[Configure::read('acl.aro.role.model')][Configure::read(
                        'acl.aro.role.display_field')]), 'flash_error', null,
                'plugin_acl');
        } else {
            // Grant permissions to the roll
            $this->Acl->allow($Role, 'controllers');
        }

        $this->_returnToReferer();
    }

    /**
     * Deny permissions to all controllers to a given Role
     * @param unknown $roleId
     */
    public function admin_deny_all_controllers($roleId) {

        $Role = & $this->{Configure::read('acl.aro.role.model')};
        $Role->id = $roleId;

        /* Check if the Role exists in the ARO table */
        $node = $this->Acl->Aro->node($Role);
        if (empty($node)) {
            $askedRole = $Role->read(null, $roleId);
            $this->Session->setFlash(
                sprintf(
                    __d('acl', "The role '%s' does not exist in the ARO table"),
                    $askedRole[Configure::read('acl.aro.role.model')][Configure::read(
                        'acl.aro.role.display_field')]), 'flash_error', null,
                'plugin_acl');
        } else {
            // Deny permissions to the role
            $this->Acl->deny($Role, 'controllers');
        }

        $this->_returnToReferer();
    }

    /**
     * Get the permissions for a given role
     * @param unknown $roleId
     */
    public function admin_get_role_controller_permission($roleId) {

        $Role = & $this->{Configure::read('acl.aro.role.model')};

        $roleData = $Role->read(null, $roleId);

        $aroNode = $this->Acl->Aro->node($roleData);
        if (! empty($aroNode)) {
            $pluginName = '';
            if (isset($this->params['named']['plugin'])) {
                $pluginName = $this->params['named']['plugin'];
            }

            $controllerName = $this->params['named']['controller'];
            $controllerActions = $this->AclReflector->getControllerActions($controllerName);

            $roleControllerPermissions = array();

            foreach ($controllerActions as $actionName) {
                $acoPath = $pluginName;
                if (empty($acoPath)) {
                    $acoPath .= $controllerName;
                } else {
                    $acoPath .= '/' . $controllerName;
                }

                $acoPath .= '/' . $actionName;

                $acoNode = $this->Acl->Aco->node('controllers/' . $acoPath);
                if (! empty($acoNode)) {
                    $authorized = $this->Acl->check($roleData,
                        'controllers/' . $acoPath);
                    $roleControllerPermissions[$actionName] = $authorized;
                } else {
                    $roleControllerPermissions[$actionName] = - 1;
                }
            }
        } else {
            // $this->set('aclError', true);
            // $this->set('aclErrorAro', true);
        }

        if ($this->request->is('ajax')) {
            //Disable debug output.
            Configure::write('debug', 0);
            $this->autoRender = false;

            echo json_encode($roleControllerPermissions);
        } else {
            $this->_returnToReferer();
        }
    }

    /**
     * Grants permissions to a role for a passed ACO path
     * @param unknown $roleId
     */
    public function admin_grant_role_permission($roleId) {

        $Role =& $this->{Configure::read('acl.aro.role.model')};

        $Role->id = $roleId;

        $acoPath = $this->getPassedAcoPath();

        /* Check if the role exists in the ARO table */
        $aroNode = $this->Acl->Aro->node($Role);
        if (! empty($aroNode)) {
            if (! $this->AclManager->savePermissions($aroNode, $acoPath, 'grant')) {
                $this->set('aclError', true);
            }
        } else {
            $this->set('aclError', true);
            $this->set('aclErrorAro', true);
        }

        $this->set('roleId', $roleId);
        $this->setAcoVariables();

        if ($this->request->is('ajax')) {
            $this->render('ajax_role_granted');
        } else {
            $this->_returnToReferer();
        }
    }

    /**
     * Deny permissions to a given role.
     * @param unknown $roleId
     */
    public function admin_deny_role_permission($roleId) {

        $Role =& $this->{Configure::read('acl.aro.role.model')};

        $Role->id = $roleId;

        $acoPath = $this->getPassedAcoPath();

        $aroNode = $this->Acl->Aro->node($Role);
        if (! empty($aroNode)) {
            if (! $this->AclManager->savePermissions($aroNode, $acoPath, 'deny')) {
                $this->set('aclError', true);
            }
        } else {
            $this->set('aclError', true);
        }

        $this->set('roleId', $roleId);
        $this->setAcoVariables();

        if ($this->request->is('ajax')) {
            $this->render('ajax_role_denied');
        } else {
            $this->_returnToReferer();
        }
    }

    /**
     * Get Controller Permissions for a given user
     * @param unknown $userId
     */
    public function admin_get_user_controller_permission($userId) {

        $User =& $this->{Configure::read('acl.aro.user.model')};

        $userData = $user->read(null, $userId);

        $aroNode = $this->Acl->Aro->node($userData);
        if (! empty($aroNode)) {
            $pluginName = '';
            if (isset($this->params['named']['plugin'])){
                $pluginName =  $this->params['named']['plugin'];
            }

            $controllerName = $this->params['named']['controller'];
            $controllerActions = $this->AclReflector->getControllerActions($controllerName);

            $userControllerPermissions = array();

            foreach ($controllerActions as $actionName) {
                $acoPath = $pluginName;
                if (empty($acoPath)) {
                    $acoPath .= $controllerName;
                } else {
                    $acoPath .= '/' . $controllerName;
                }

                $acoPath .= '/' . $actionName;

                $acoNode = $this->Acl->Aco->node('controllers/' . $acoPath);
                if (! empty($acoNode)) {
                    $authorized = $this->Acl->check($userData, 'controllers/' . $acoPath);
                    $userControllerPermissions[$actionName] = $authorized;
                } else {
                    $userControllerPermissions[$actionName] = - 1;
                }
            }
        } else {
            if (!$this->request->is('ajax')) {
                 $this->set('aclError', true);
                 $this->set('aclErrorAro', true);
            }
        }

        if ($this->request->is('ajax')) {
            //Disable debug output.
            Configure::write('debug', 0);
            $this->autoRender = false;
            echo json_encode($userControllerPermissions);
        } else {
            $this->_returnToReferer();
        }
    }

    /**
     * Grant permissions to a given user
     * @param unknown $userId
     */
    public function admin_grant_user_permission($userId) {

        $User =& $this->{Configure::read('acl.aro.user.model')};

        $User->id = $userId;

        $acoPath = $this->getPassedAcoPath();

        /* Check if the user exists in the ARO table */
        $aroNode = $this->Acl->Aro->node($User);
        if (! empty($aroNode)) {
            $acoNode = $this->Acl->Aco->node('controllers/' . $acoPath);
            if (! empty($acoNode)) {
                if (! $this->AclManager->savePermissions($aroNode, $acoPath,  'grant')) {
                    $this->set('aclError', true);
                }
            } else {
                $this->set('aclError', true);
                $this->set('aclErrorAco', true);
            }
        } else {
            $this->set('aclError', true);
            $this->set('aclErrorAro', true);
        }

        $this->set('userId', $userId);
        $this->getPassedAcoPath();

        if ($this->request->is('ajax')) {
            $this->render('ajax_user_granted');
        } else {
            $this->_returnToReferer();
        }
    }

    /**
     * Deny permissions to a given user
     * @param unknown $userId
     */
    public function admin_deny_user_permission($userId) {

        $User =& $this->{Configure::read('acl.aro.user.model')};

        $User->id = $userId;

        $acoPath = $this->getPassedAcoPath();

        /* Check if the user exists in the ARO table */
        $aroNode = $this->Acl->Aro->node($user);
        if (! empty($aroNode)) {
            $acoNode = $this->Acl->Aco->node('controllers/' . $acoPath);
            if (! empty($acoNode)) {
                if (! $this->AclManager->savePermissions($aroNode, $acoPath, 'deny')) {
                    $this->set('aclError', true);
                }
            } else {
                $this->set('aclError', true);
                $this->set('aclErrorAco', true);
            }
        } else {
            $this->set('aclError', true);
            $this->set('aclErrorAro', true);
        }

        $this->set('userId', $userId);
        $this->getPassedAcoPath();

        if ($this->request->is('ajax')) {
            $this->render('ajax_user_denied');
        } else {
            $this->_returnToReferer();
        }
    }
}
