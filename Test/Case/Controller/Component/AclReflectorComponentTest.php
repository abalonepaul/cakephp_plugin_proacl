<?php
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('AclReflectorComponent', 'Acl.Controller/Component');

class AclReflectorTestController extends Controller {
    public $components = array('Acl.AclReflector','Acl.AclManager');
}
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
        $this->Controller = new AclReflectorTestController(new CakeRequest(), new CakeResponse());
        $this->Controller->constructClasses();
        $this->Controller->startupProcess();
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
        $this->assertFalse($this->Controller->AclReflector->getPluginName($this->Controller->name));
        $this->assertEquals('Acl', $this->Controller->AclReflector->getPluginName('Acl/acos'));
    }

/**
 * testGetPluginControllerName method
 *
 * @return void
 */
    public function testGetPluginControllerName() {
        $this->assertFalse($this->Controller->AclReflector->getPluginControllerName($this->Controller->name));
        $this->assertEquals('Acos', $this->Controller->AclReflector->getPluginControllerName('Acl/Acos'));
    }

/**
 * testGetControllerClassName method
 *
 * @return void
 */
    public function testGetControllerClassName() {
        $this->assertEquals('AclReflectorTestController',$this->Controller->AclReflector->getControllerClassName($this->Controller->name));
        $this->assertEquals('AcosController', $this->Controller->AclReflector->getControllerClassName('Acl/Acos'));
    }

/**
 * testGetAllPluginPaths method
 *
 * @return void
 */
    public function testGetAllPluginPaths() {
        $paths = $this->Controller->AclReflector->getAllPluginPaths();
        $plugins = App::objects('plugin');
        foreach ($plugins as $key => $value) {
            $this->assertContains(App::pluginPath($value),$paths);
        }

    }

/**
 * testGetAllPluginNames method
 *
 * @return void
 */
    public function testGetAllPluginNames() {
        $this->assertEquals(App::objects('plugin'), $this->Controller->AclReflector->getAllPluginNames());
    }

/**
 * testGetAllPluginControllers method
 *
 * @return void
 */
    public function testGetAllPluginControllers() {
        $controllers =  $this->Controller->AclReflector->getAllPluginControllers();
        $names = Set::classicExtract($controllers,'{n}.name');
        for ($i = 0; $i <= count($names) -1;$i++) {
            $this->assertEquals($names[$i], $controllers[$i]['name']);
        }

    }

/**
 * testGetAllPluginControllersActions method
 *
 * @return void
 */
    public function testGetAllPluginControllersActions() {
        $actions =  $this->Controller->AclReflector->getAllPluginControllersActions();
        $aclControllerActions = $this->Controller->AclReflector->getControllerActions('AcosController');
        foreach($aclControllerActions as $action) {
            $this->assertTrue(in_array('Acl/Acos/' . $action,$actions));
        }
    }

/**
 * testGetAllAppControllers method
 *
 * @return void
 */
    public function testGetAllAppControllers() {
        $controllers = Set::classicExtract($this->Controller->AclReflector->getAllAppControllers(), '{n}.name');

      $appControllers = App::objects('Controller');
      for ($i = 0; $i <= count($controllers) -1; $i++) {
          $this->assertContains($controllers[$i], $appControllers[$i]);
      }

    }

/**
 * testGetAllAppControllersActions method
 *
 * @return void
 */
    public function testGetAllAppControllersActions() {
        $actions =  $this->Controller->AclReflector->getAllAppControllersActions();
        $appControllers = App::objects('Controller');
        $className = str_replace('Controller', '', $this->Controller->AclReflector->getControllerClassName($appControllers[0]));
        $aclControllerActions = $this->Controller->AclReflector->getControllerActions($appControllers[0]);
            $this->assertTrue(in_array($className . '/' . $aclControllerActions[0],$actions));
    }

/**
 * testGetAllControllers method
 *
 * @return void
 */
    public function testGetAllControllers() {
        $controllers = $this->Controller->AclReflector->getAllControllers();
        $appControllers = $this->Controller->AclReflector->getAllAppControllers();
        $pluginControllers = $this->Controller->AclReflector->getAllPluginControllers();
        $this->assertEquals(am($appControllers,$pluginControllers), $controllers);
    }

/**
 * testGetAllActions method
 *
 * @return void
 */
    public function testGetAllActions() {
        $actions = $this->Controller->AclReflector->getAllActions();
        $appActions = $this->Controller->AclReflector->getAllAppControllersActions();
        $pluginActions = $this->Controller->AclReflector->getAllPluginControllersActions();
        $this->assertEquals(am($appActions,$pluginActions), $actions);

    }

/**
 * testGetControllerActions method
 *
 * @return void
 */
    public function testGetControllerActions() {
        $AcosController = new AcosController(new CakeRequest(),new CakeResponse());
        $methods = get_class_methods('AcosController');
        $acoActions = $this->Controller->AclReflector->getControllerActions('AcosController');
        $noFilter = $this->Controller->AclReflector->getControllerActions('AcosController',false);
        foreach($methods as $method) {
            $this->assertTrue(in_array($method,$noFilter));
        }
        $this->assertEquals(array(), $this->Controller->AclReflector->getControllerActions('Junk'));
        foreach ($acoActions as $action) {
            $this->assertTrue(in_array($action,$AcosController->methods));
        }

    }

}
