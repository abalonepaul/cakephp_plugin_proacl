<?php
App::uses('AcosController', 'Acl.Controller');
App::uses('CroogoTestCase', 'TestSuite');
App::uses('AclAppController', 'Acl.Controller');

/**
 * AcosController Test Case
 *
 */
class AcosControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
    public $fixtures = array(
        'plugin.acl.aco',
        'plugin.acl.aro',
        'plugin.acl.permission',
        'plugin.acl.user',
        'plugin.acl.role',
    );

    public function setUp() {
        parent::setUp();
        $this->Controller = new AcosController(new CakeRequest(), new CakeResponse());
        $this->Controller->constructClasses();
        $this->Controller->startupProcess();
            }

/**
 * testAdminIndex method
 *
 * @return void
 */
    public function testAdminIndex() {
        $result = $this->_testAction('admin/acl/acos/index');
        debug($result);
    }

/**
 * testAdminEmptyAcos method
 *
 * @return void
 */
    public function testAdminEmptyAcos() {
    }

/**
 * testAdminBuildAcl method
 *
 * @return void
 */
    public function testAdminBuildAcl() {
    }

/**
 * testAdminPruneAcos method
 *
 * @return void
 */
    public function testAdminPruneAcos() {
    }

/**
 * testAdminSynchronize method
 *
 * @return void
 */
    public function testAdminSynchronize() {
    }

}
