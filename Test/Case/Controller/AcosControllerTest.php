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
        $this->AclAcos = $this->generate('Acl.Acos', array(
            'methods' => array(
                'redirect',
            ),
            'components' => array(
                'Auth' => array('user'),
                'Session',

            ),
        ));
        $this->AclAcos->Auth
            ->staticExpects($this->any())
            ->method('user')
            ->will($this->returnValue( array(
            'id' => 1,
            'role_id' => 1,
            'email' => 'admin@test.loc',
            )));
        $this->Controller->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'email' => 'admin@test.loc',
            ));
    }

/**
 * testAdminIndex method
 *
 * @return void
 */
    public function testAdminIndex() {
        $view = $this->_testAction('/admin/acl/acos/index',array('return' => 'view'));
        $this->assertNoErrors();
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
    }

/**
 * testAdminEmptyAcos method
 *
 * @return void
 */
    public function testAdminEmptyAcos() {
        $view = $this->_testAction('/admin/acl/acos/synchronize/run',array('return' => 'view'));
        //$this->assertTextContains('The following actions ACOs have been created', $view);
        $this->assertNoErrors();
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);

        $this->Controller->AclManager->Acl->allow(array('Role' => array('id' => 1)),'controllers');
        $view = $this->_testAction('/admin/acl/acos/empty_acos/',array('return' => 'view'));
        $this->assertNoErrors();
        $this->assertContains('This page allows you to clear all actions ACOs.', $view);
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);

        $view = $this->_testAction('/admin/acl/acos/empty_acos/run',array('return' => 'view'));
        $this->assertNoErrors();
        $this->assertTextContains('ACL: The actions in the ACO table have been deleted', $view);
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
        $view = $this->_testAction('/admin/acl/acos/synchronize/run',array('return' => 'view'));

    }

/**
 * testAdminBuildAcl method
 *
 * @return void
 */
    public function testAdminBuildAcl() {
        //Test the response when there are no ACOs to add.
        $view = $this->_testAction('/admin/acl/acos/build_acl',array('return' => 'view'));
        $this->assertNoErrors();
        $this->assertTextContains('There is no ACO node to create', $view);
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
        $view = $this->_testAction('/admin/acl/acos/build_acl/run',array('return' => 'view'));
        $this->assertNoErrors();
        $this->assertTextContains('There was no new actions ACOs to create', $view);
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
        //Empty the ACOs
        $view = $this->_testAction('/admin/acl/acos/empty_acos/run',array('return' => 'view'));

        $view = $this->_testAction('/admin/acl/acos/build_acl/run',array('return' => 'view'));
        $this->assertNoErrors();
        $this->assertTextContains('The following actions ACOs have been created', $view);
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);

    }

/**
 * testAdminPruneAcos method
 *
 * @return void
 */
    public function testAdminPruneAcos() {

        $view = $this->_testAction('/admin/acl/acos/prune_acos',array('return' => 'view'));
        $this->assertTextContains('There is no ACO node to delete', $view);
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
        $view = $this->_testAction('/admin/acl/acos/prune_acos',array('data' => array('run' => true),'return' => 'view'));
        //$this->assertTextContains('The following actions ACOs have been created', $view);
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);
    }

/**
 * testAdminSynchronize method
 *
 * @return void
 */
    public function testAdminSynchronize() {
        $view = $this->_testAction('/admin/acl/acos/synchronize',array('return' => 'view'));
        //$this->assertTextContains('The following actions ACOs have been created', $view);
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);

        $view = $this->_testAction('/admin/acl/acos/synchronize/run',array('return' => 'view'));
        $this->assertTextNotContains('xdebug-error', $view);
        $this->assertTextNotContains('cake-error', $view);

    }

}
