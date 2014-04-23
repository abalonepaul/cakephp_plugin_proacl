<?php
App::uses('ArosController', 'Acl.Controller');
App::uses('Controller', 'Controller');
App::uses('AclAppController', 'Acl.Controller');
App::uses('SessionComponent', 'Controller.Component');
App::uses('AclHtmlHelper', 'Acl.View/Helper');

class TestArosController extends ArosController {
    var $name = 'Aros';

    var $autoRender = false;

    function redirect($url, $status = null, $exit = true) {
        $this->redirectUrl = $url;
    }

    function render($action = null, $layout = null, $file = null) {
        $this->renderedAction = $action;
    }

}
/**
 * ArosController Test Case
 *
 */
class ArosControllerTest extends ControllerTestCase {


/**
 * Fixtures
 *
 * @var array
 */
    public $fixtures = array(
        'plugin.acl.aco',
        'plugin.acl.aro',
        'plugin.acl.aros_acos',
        //'plugin.acl.permission',
        'plugin.acl.user',
        'plugin.acl.role',
            );

    public function setUp() {
        parent::setUp();
        //$this->Controller = new ArosController(new CakeRequest(), new CakeResponse());
        $this->Controller = $this->generate('Acl.Aros', array(
            'methods' => array(
                'redirect',
                '_returnToReferer'
            ),
            'components' => array(
                'Auth' => array('user'),
                //'Session',

            ),
        ));
        //$this->Controller->constructClasses();
        //$this->Controller->startupProcess();
        $this->Controller->Auth
        ->staticExpects($this->any())
        ->method('user')
        ->will($this->returnValue( array(
            'id' => 1,
            'role_id' => 1,
            'email' => 'admin@test.loc',
                )));
        $this->Controller->Auth
        ->staticExpects($this->any())
        ->method('isAuthorized')
        ->will($this->returnValue( true));

        $this->Controller->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'email' => 'admin@test.loc',
        ));
        $this->_testAction('/admin/acl/aros/grant_all_controllers/1');

    }



/**
 * testAdminIndex method
 *
 * @return void
 */
    public function testAdminIndex() {
        $view = $this->_testAction('/admin/acl/aros/index',array('return' => 'view'));
        $this->assertNoErrors();
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
       //$this->assertEquals($this->controller,$this->Controller);
    }

/**
 * testAdminCheck method
 *
 * @return void
 */
    public function testAdminCheck() {
        $view = $this->_testAction('/admin/acl/aros/check',array('return' => 'view'));
        $this->assertNoErrors();
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
        $this->assertTextContains('There is no missing ARO.', $view);
        //debug($this->Controller->AclManager->Acl->Aro->find('all'));
        $vars = $this->_testAction('/admin/acl/aros/check/run',array('return' => 'vars'));
        //$view = $this->_testAction('/admin/acl/aros/check',array('return' => 'view'));
        $this->assertNoErrors();
        $this->assertTrue($vars['run']);
    }

/**
 * testAdminUsers method
 *
 * @return void
 */
    public function testAdminUsers() {
        $view = $this->_testAction('/admin/acl/aros/users/index',array('return' => 'view'));
        $this->assertTextContains('Update the user role', $view);
        $this->assertNoErrors();
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
    }

/**
 * testAdminUpdateUserRole method
 *
 * @return void
 */
    public function testAdminUpdateUserRole() {
        //$this->Controller->Session = new SessionComponent(new ComponentCollection());
        $result = $this->_testAction('/admin/acl/aros/update_user_role/user:1/role:2',array('return' => 'result'));
        $result = $this->_testAction('/admin/acl/aros/update_user_role/user:1df/role:1',array('return' => 'result'));

        $this->assertEquals('The user role has been updated', $this->Controller->Session->read('Message.plugin_acl.message'));
        $this->assertNoErrors();
        $result = $this->_testAction('/admin/acl/aros/update_user_role/user:1/role:1',array('return' => 'result'));
    }

/**
 * testAdminAjaxRolePermissions method
 *
 * @return void
 */
    public function testAdminAjaxRolePermissions() {
        $view = $this->_testAction('/admin/acl/aros/ajax_role_permissions',array('return' => 'view'));
        $this->assertTextContains('/acl/img/design/tick.png', $view);
        $this->assertTextNotContains('acl/img/ajax/waiting16.gif', $view);
        $this->assertNoErrors();
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
    }

/**
 * testAdminRolePermissions method
 *
 * @return void
 */
    public function testAdminRolePermissions() {
        $view = $this->_testAction('/admin/acl/aros/role_permissions',array('return' => 'view'));
        $this->assertTextContains('acl/img/ajax/waiting16.gif', $view);
        $this->assertNoErrors();
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
    }

/**
 * testAdminUserPermissions method
 *
 * @return void
 */
    public function testAdminUserPermissions() {
        $view = $this->_testAction('/admin/acl/aros/user_permissions',array('return' => 'view'));
        $this->assertTextContains('Manage user specific rights', $view);
        $this->assertNoErrors();
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
        $view = $this->_testAction('/admin/acl/aros/user_permissions/1',array('return' => 'view'));
        $view = $this->_testAction('/admin/acl/aros/user_permissions/2',array('return' => 'view'));
        //debug($view);
        $this->assertTextContains('<th>authorization</th>', $view);
        $this->assertNoErrors();
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
    }

/**
 * testAdminEmptyPermissions method
 *
 * @return void
 */
    public function testAdminEmptyPermissions() {
        $return = $this->_testAction('/admin/acl/aros/empty_permissions');
        $this->assertEquals('The permissions have been cleared', $this->Controller->Session->read('Message.plugin_acl.message'));
    }

/**
 * testAdminClearUserSpecificPermissions method
 *
 * @return void
 */
    public function testAdminClearUserSpecificPermissions() {
        $this->_testAction('/admin/acl/aros/clear_user_specific_permissions/2');
        $this->assertEquals('The specific permissions have been cleared', $this->Controller->Session->read('Message.plugin_acl.message'));
        $this->Controller->Session->delete('Message.plugin_acl');
  }

    /**
     * testAdminDenyAllControllers method
     *
     * @return void
     */
    public function testAdminDenyAllControllers() {
        $view = $this->_testAction('/admin/acl/aros/grant_all_controllers/2',array('return' => 'view'));
        $this->_testAction('/admin/acl/aros/deny_all_controllers/2',array('return' => 'view'));
        //$this->_testAction('/admin/acl/aros/deny_all_controllers/1',array('return' => 'view'));
        //debug($this->Controller->Session->read('Message'));
        /*$this->Controller
        ->expects($this->any())
        ->method('_returnToReferer');*/
        //$this->assertEquals('The specific permissions have been cleared', $this->Controller->Session->read('Message.plugin_acl.message'));
    }

/**
 * testAdminGrantAllControllers method
 *
 * @return void
 */
    public function testAdminGrantAllControllers() {
        $view = $this->_testAction('/admin/acl/aros/grant_all_controllers/1',array('return' => 'view'));
                $view = $this->_testAction('/admin/acl/aros/user_permissions/2',array('return' => 'view'));
        $this->assertTextContains('This user has specific permissions', $view);

    }


/**
 * testAdminGetRoleControllerPermission method
 *
 * @return void
 */
    public function testAdminGetRoleControllerPermission() {
        $result = $this->_testAction('/admin/acl/aros/get_role_controller_permission/1/controller:users',array('return' => 'result'));
        //debug($result);
        //debug($this->Controller->Session->read('Message.plugin_acl.message'));
        //$this->assertEquals('The specific permissions have been cleared', $this->Controller->Session->read('Message.plugin_acl.message'));
    }

/**
 * testAdminGrantRolePermission method
 *
 * @return void
 */
    public function testAdminGrantRolePermission() {
        $result = $this->_testAction('/admin/acl/aros/grant_role_permission/1/controller:users/action:add',array('return' => 'result'));
        //$this->assertEquals('The specific permissions have been cleared', $this->Controller->Session->read('Message.plugin_acl.message'));
    }

/**
 * testAdminDenyRolePermission method
 *
 * @return void
 */
    public function testAdminDenyRolePermission() {
        $result = $this->_testAction('/admin/acl/aros/deny_role_permission/1/controller:users/action:add',array('return' => 'result'));
        //$this->assertEquals('The specific permissions have been cleared', $this->Controller->Session->read('Message.plugin_acl.message'));
    }

/**
 * testAdminGetUserControllerPermission method
 *
 * @return void
 */
    public function testAdminGetUserControllerPermission() {
        $view = $this->_testAction('/admin/acl/aros/get_user_controller_permission/1/controller:users/action:add',array('return' => 'view'));
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $view = $this->_testAction('/admin/acl/aros/get_user_controller_permission/1/controller:users/action:add',array('return' => 'view'));
        $array = json_decode($view);
        debug($array);
        //$this->assertArrayHasKey('add', $array);
    }

/**
 * testAdminGrantUserPermission method
 *
 * @return void
 */
    public function testAdminGrantUserPermission() {
        $view = $this->_testAction('/admin/acl/aros/grant_user_permission/1/controller:users/action:add',array('return' => 'view'));
    }

/**
 * testAdminDenyUserPermission method
 *
 * @return void
 */
    public function testAdminDenyUserPermission() {
        $view = $this->_testAction('/admin/acl/aros/deny_user_permission/1/controller:users/action:add',array('return' => 'view'));
    }

    public function testHelperLink() {
        $view = $this->_testAction('/admin/acl/aros/grant_all_controllers/1',array('return' => 'view'));
        $view = $this->_testAction('/admin/acl/aros/grant_user_permission/1/controller:App/action:isAuthorized',array('return' => 'view'));
        $View = new View($this->Controller);
        $this->AclHtml = new AclHtmlHelper($View);
        $this->Controller->AclManager->setSessionPermissions();
        $this->assertEquals('<a href="/users/edit">TestLink</a>',$this->AclHtml->link('TestLink',array('admin' => false, 'prefix' => false,'plugin' => false, 'controller' => 'users', 'action' => 'edit')));
    }

}
