<?php
App::uses('AclAppController', 'Acl.Controller');

/**
 * AclAppController Test Case
 *
 */
class AclAppControllerTest extends ControllerTestCase {

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
        'plugin.acl.permission',
    );

    public function setUp() {
        $this->controller = new AclAppController(new CakeRequest(), new CakeResponse());

    }

/**
 * testGetRolePrimaryKeyName method
 *
 * @return void
 */
    public function testGetRolePrimaryKeyName() {
        //debug($this->controller);
        $this->assertEquals('id', $this->controller->getRolePrimaryKeyName());
    }

}
