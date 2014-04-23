<?php
App::uses('Controller', 'Controller');
App::uses('AclAppController', 'Acl.Controller');
App::uses('AclController', 'Acl.Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('AclManagerComponent', 'Acl.Controller/Component');


class AclManagerComponentController extends AclAppController {

    public $components = array(
        'Auth',
        'Acl',
        'Session',
        'Acl.AclManager',
        'Acl.AclReflector',

    );

    public function testMethod(){
        return true;
    }

    /**
     * Get the name of the Primary Key
     *
     * @return unknown string
     */
    public function getRolePrimaryKeyName() {

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


}

/**
 * AclManagerComponent Test Case
 *
 */
class AclManagerComponentTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
    public $fixtures = array(
        'plugin.acl.user',
        'plugin.acl.role',
        'plugin.acl.aco',
        'plugin.acl.aro',
        'plugin.acl.aros_acos',
        'app.patient',
        'app.consent_question',
        'app.private_message',
        'app.order',
        'app.message',
    );

/**
 * setUp method
 *
 * @return void
 */
    public function setUp() {
        parent::setUp();
        $Collection = new ComponentCollection();

        $this->AclManager = new AclManagerComponent($Collection);
        $this->Controller = new AclManagerComponentController(new CakeRequest(), new CakeResponse());
        $this->Controller->constructClasses();
        $this->Controller->startupProcess();
    }

/**
 * tearDown method
 *
 * @return void
 */
    public function tearDown() {
        unset($this->AclManager);

        parent::tearDown();
    }

/**
 * testCheckAclRequester method
 *
 * @return void
 */
    public function testCheckAclRequester() {
        Configure::write('acl.check_act_as_requester', true);
        $result = $this->AclManager->checkAclRequester('User');
        $this->assertEquals(true, $result);
    }

/**
 * testSetDisplayName method
 *
 * @return void
 */
    public function testSetDisplayName() {
        $this->assertEquals('last_name', $this->AclManager->setDisplayName('User', 'last_name'));
        $this->assertEquals('last_name', $this->AclManager->setDisplayName('User', 'User.last_name'));
    }
    /**
     * testSetDisplayName method
     *
     * @return void
     */
    public function testSetDisplayNameIncorrectField() {
        $this->assertEquals('proacl_display_name', $this->AclManager->setDisplayName('User', 'lst_name'));

    }

/**
 * testGetStoredControllerHash method
 *
 * @return void
 */
    public function testGetStoredControllerHash() {
        $hashFile = $this->Controller->AclManager->getStoredControllerHash();
        $this->assertArrayHasKey('App', $hashFile);
        $controllers = App::objects('Controller');
        foreach ($controllers as $key => $value) {
            $this->assertArrayHasKey(str_replace('Controller','',$value), $hashFile);
        }
    }

/**
 * testGetCurrentControllerHash method
 *
 * @return void
 */
    public function testGetCurrentControllerHash() {
            $hashFile = $this->Controller->AclManager->getCurrentControllerHash();
        $this->assertArrayHasKey('App', $hashFile);
        $controllers = App::objects('Controller');
        foreach ($controllers as $key => $value) {
            $this->assertArrayHasKey(str_replace('Controller','',$value), $hashFile);
        }
    }

/**
 * testGetMissingAcos method
 *
 * @return void
 */
    public function testGetMissingAcos() {
       $this->Controller->AclManager->Acl->Aco->deleteAll(array('id >=' => -1));
        $acos = $this->Controller->AclManager->getMissingAcos();
        //debug($acos);
        if ($this->AssertArrayHasKey(1,$acos)) {
            foreach($acos as $key => $aco) {
                $this->assertStringStartsWith('controllers', $aco);
            }
        }



    }

/**
 * testCreateAcos method
 *
 * @return void
 */
    public function testCreateAcos() {
       $this->Controller->AclManager->Acl->Aco->deleteAll(array('id >=' => -1));
       $log = $this->Controller->AclManager->createAcos();
       $this->assertContains('Created Aco node for', $log[0]);

    }

/**
 * testUpdateControllerHashFile method
 *
 * @return void
 */
    public function testUpdateControllerHashFile() {
        $this->assertEquals(true, $this->Controller->AclManager->updateControllerHashFile());
    }

/**
 * testIsControllerHashFileOutOfSync method
 *
 * @return void
 */
    public function testIsControllerHashFileOutOfSync() {
        //Files are in sync
        $this->assertFalse($this->Controller->AclManager->isControllerHashFileOutOfSync());

    }

/**
 * testGetAcosToPrune method
 *
 * @return void
 */
    public function testGetAcosToPrune() {
        $this->assertempty($this->Controller->AclManager->getAcosToPrune());
    }

/**
 * testPruneAcos method
 *
 * @return void
 */
    public function testPruneAcos() {
        $this->assertEmpty($this->Controller->AclManager->pruneAcos());
    }

/**
 * testSavePermissions method
 *
 * @return void
 */
    public function testSavePermissions() {
        $aroNodes = $this->Controller->AclManager->Acl->Aro->find('all', array('conditions' => array('foreign_key' => 1)));
        $acoPath = 'Users';
        $this->assertTrue($this->Controller->AclManager->savePermissions($aroNodes,$acoPath,'grant'));
    }

/**
 * testSetSessionPermissions method
 *
 * @return void
 */
    public function testSetSessionPermissions() {
        $this->Controller->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'email' => 'admin@test.loc',
            ));
        //debug($this->Controller->AclManager->Auth->user());
        $Role =& ClassRegistry::init(Configure::read('acl.aro.role.model'));
        $Role->id = 1;
        //debug($Role);
        $log = $this->Controller->AclManager->createAcos();
        $this->Controller->AclManager->Acl->allow($Role, 'controllers');

          $this->Controller->AclManager->setSessionPermissions();
          $session = $this->Controller->Session->read();
          $this->assertArrayHasKey('controllers/Acl/Acl/index', $session['ProAcl']['permissions']);
    }

}
