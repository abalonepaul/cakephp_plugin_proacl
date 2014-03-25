<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('AclReflectorComponent', 'Acl.Controller/Component');

/**
 * AclReflectorComponent Test Case
 *
 */
class AclReflectorComponentTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$this->AclReflector = new AclReflectorComponent($Collection);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AclReflector);

		parent::tearDown();
	}

/**
 * testGetPluginName method
 *
 * @return void
 */
	public function testGetPluginName() {
	}

/**
 * testGetPluginControllerName method
 *
 * @return void
 */
	public function testGetPluginControllerName() {
	}

/**
 * testGetControllerClassName method
 *
 * @return void
 */
	public function testGetControllerClassName() {
	}

/**
 * testGetAllPluginPaths method
 *
 * @return void
 */
	public function testGetAllPluginPaths() {
	}

/**
 * testGetAllPluginNames method
 *
 * @return void
 */
	public function testGetAllPluginNames() {
	}

/**
 * testGetAllPluginControllers method
 *
 * @return void
 */
	public function testGetAllPluginControllers() {
	}

/**
 * testGetAllPluginControllersActions method
 *
 * @return void
 */
	public function testGetAllPluginControllersActions() {
	}

/**
 * testGetAllAppControllers method
 *
 * @return void
 */
	public function testGetAllAppControllers() {
	}

/**
 * testGetAllAppControllersActions method
 *
 * @return void
 */
	public function testGetAllAppControllersActions() {
	}

/**
 * testGetAllControllers method
 *
 * @return void
 */
	public function testGetAllControllers() {
	}

/**
 * testGetAllActions method
 *
 * @return void
 */
	public function testGetAllActions() {
	}

/**
 * testGetControllerActions method
 *
 * @return void
 */
	public function testGetControllerActions() {
	}

}
