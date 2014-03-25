<?php
App::uses('ArosController', 'Acl.Controller');
App::uses('Controller', 'Controller');

class AppController extends Controller {

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
        'plugin.acl.aro',
        'plugin.acl.aco',
        'plugin.acl.permission',
        'plugin.acl.user',
        'plugin.acl.role',
    );

/**
 * testAdminIndex method
 *
 * @return void
 */
    public function testAdminIndex() {

        debug($this->testAction('admin/acl/aros/index'));
    }

/**
 * testAdminCheck method
 *
 * @return void
 */
    public function testAdminCheck() {
    }

/**
 * testAdminUsers method
 *
 * @return void
 */
    public function testAdminUsers() {
    }

/**
 * testAdminUpdateUserRole method
 *
 * @return void
 */
    public function testAdminUpdateUserRole() {
    }

/**
 * testAdminAjaxRolePermissions method
 *
 * @return void
 */
    public function testAdminAjaxRolePermissions() {
    }

/**
 * testAdminRolePermissions method
 *
 * @return void
 */
    public function testAdminRolePermissions() {
    }

/**
 * testAdminUserPermissions method
 *
 * @return void
 */
    public function testAdminUserPermissions() {
    }

/**
 * testAdminEmptyPermissions method
 *
 * @return void
 */
    public function testAdminEmptyPermissions() {
    }

/**
 * testAdminClearUserSpecificPermissions method
 *
 * @return void
 */
    public function testAdminClearUserSpecificPermissions() {
    }

/**
 * testAdminGrantAllControllers method
 *
 * @return void
 */
    public function testAdminGrantAllControllers() {
    }

/**
 * testAdminDenyAllControllers method
 *
 * @return void
 */
    public function testAdminDenyAllControllers() {
    }

/**
 * testAdminGetRoleControllerPermission method
 *
 * @return void
 */
    public function testAdminGetRoleControllerPermission() {
    }

/**
 * testAdminGrantRolePermission method
 *
 * @return void
 */
    public function testAdminGrantRolePermission() {
    }

/**
 * testAdminDenyRolePermission method
 *
 * @return void
 */
    public function testAdminDenyRolePermission() {
    }

/**
 * testAdminGetUserControllerPermission method
 *
 * @return void
 */
    public function testAdminGetUserControllerPermission() {
    }

/**
 * testAdminGrantUserPermission method
 *
 * @return void
 */
    public function testAdminGrantUserPermission() {
    }

/**
 * testAdminDenyUserPermission method
 *
 * @return void
 */
    public function testAdminDenyUserPermission() {
    }

}
