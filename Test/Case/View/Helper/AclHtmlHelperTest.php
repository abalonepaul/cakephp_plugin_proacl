<?php
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('AclHtmlHelper', 'Acl.View/Helper');
App::uses('AclAppController', 'Acl.Controller');
App::uses('AclRouter', 'Acl.Lib');
App::uses('AclManagerComponent', 'Acl.Controller/Component');

class AclHtmlHelperController extends AclAppController {
    public $components = array(
        'Auth',
        'Acl',
        'Session',
        'Acl.AclManager',
        'Acl.AclReflector',

    );
    public $helpers = array('Acl.AclHelper');
    }
/**
 * AclHtmlHelper Test Case
 *
 */
class AclHtmlHelperTest extends CakeTestCase {

    public $AclHtml = null;



/**
 * setUp method
 *
 * @return void
 */
    public function setUp() {
        parent::setUp();
        $this->Controller = new AclHtmlHelperController();
        $View = new View($this->Controller);
        $this->AclHtml = new AclHtmlHelper($View);
        $this->Controller->constructClasses();
        //$this->Controller->startupProcess();
        //$Controller->Auth = $this->getMock()
        /*$Controller->Auth
            ->staticExpects($this->any())
            ->method('user')
            ->will($this->returnValue( array(
            'id' => 1,
            'role_id' => 1,
            'email' => 'admin@test.loc',
            )));*/
        $this->Controller->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'email' => 'admin@test.loc',
            ));
    }

/**
 * tearDown method
 *
 * @return void
 */
    public function tearDown() {
        unset($this->AclHtml);

        parent::tearDown();
    }

    public function testLink() {
        $this->Controller->AclManager->initialize($this->Controller);
        $aroNodes = $this->Controller->AclManager->Acl->Aro->find('all', array('conditions' => array('foreign_key' => 1)));
        $acoPath = 'App';
        $this->assertTrue($this->Controller->AclManager->savePermissions($aroNodes,$acoPath,'grant'));
        $this->Controller->AclManager->setSessionPermissions();
        debug($this->AclHtml->link('TestLink','/users/login'));
    }

}
