<?php
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
/**
 * ACL Reflector Component
 * @author Paul
 *
 * @property AclReflectorComponent $AclReflector
 *
 */
class AclReflectorComponent extends Component {

    private $Controller = null;

    /**
     * Initialize the ACL Reflector Component
     * @param Controller $controller
     */
    public function initialize(Controller $controller) {

        $this->Controller = $controller;
    }

    /**
     * get the Plugin Name from a Controller
     * @param string $controllerName
     * @return unknown|boolean
     */
    public function getPluginName($controllerName = null) {

        $arr = String::tokenize($controllerName, '/');
        if (count($arr) == 2) {
            return $arr[0];
        } else {
            return false;
        }
    }

    /**
     * Get the Controller Name from a Plugin Controller
     * @param string $controllerName
     * @return unknown|boolean
     */
    public function getPluginControllerName($controllerName = null) {

        $arr = String::tokenize($controllerName, '/');
        if (count($arr) == 2) {
            return $arr[1];
        } else {
            return false;
        }
    }

    /**
     * Get the Controller Class Name
     * @param unknown $controllerName
     * @return string|unknown
     */
    public function getControllerClassName($controllerName) {

        if (strrpos($controllerName, 'Controller') !== strlen($controllerName) - strlen(
            'Controller')) {
            /* If $controller does not already end with 'Controller' */

            if (stripos($controllerName, '/') === false) {
                $className = $controllerName . 'Controller';
            } else {
                /* Case of plugin controller */
                $className = substr($controllerName,
                    strripos($controllerName, '/') + 1) . 'Controller';
            }

            return $className;
        } else {
            return $controllerName;
        }
    }

    /**
     * Get all Plugin Paths
     * @return multitype:NULL
     */
    public function getAllPluginPaths() {

        $pluginNames = CakePlugin::loaded();

        $pluginPaths = array();
        foreach ($pluginNames as $pluginName) {
            $pluginPaths[] = CakePlugin::path($pluginName);
        }

        return $pluginPaths;
    }

    /**
     * Get all Plugin Names
     * @return multitype:Ambigous <>
     */
    public function getAllPluginNames() {

        $pluginNames = array();

        $pluginPaths = $this->getAllPluginPaths();
        foreach ($pluginPaths as $pluginPath) {
            $pathParts = explode('/', $pluginPath);
            for($i = count($pathParts) - 1; $i >= 0; $i --) {
                if (! empty($pathParts[$i])) {
                    $pluginNames[] = $pathParts[$i];
                    break;
                }
            }
        }

        return $pluginNames;
    }

    /**
     * Get all of the controllers for a Plugin
     * @param string $filterDefaultController
     * @return multitype:multitype:string unknown
     */
    public function getAllPluginControllers($filterDefaultController = false) {

        $pluginPaths = $this->getAllPluginPaths();

        $pluginsControllers = array();
        $Folder = new Folder();

        // Loop through the plugins
        foreach ($pluginPaths as $pluginPath) {
            $didCD = $Folder->cd($pluginPath . DS . 'Controller');

            if (! empty($didCD)) {
                $files = $Folder->findRecursive('.*Controller\.php');

                if (strrpos($pluginPath, DS) == strlen($pluginPath) - 1) {
                    $pluginPath = substr($pluginPath, 0,
                        strlen($pluginPath) - 1);
                }

                $pluginName = substr($pluginPath,
                    strrpos($pluginPath, DS) + 1);

                foreach ($files as $fileName) {
                    $file = basename($fileName);

                    // Get the controller name
                    $className = Inflector::camelize(
                        substr($file, 0, strlen($file) - strlen('.php')));

                    if (! $filterDefaultController || Inflector::camelize(
                        $pluginName) . 'Controller' != $className) {
                        App::uses($className,
                            $pluginName . '.Controller');

                        if (! preg_match(
                            '/^' . Inflector::camelize($pluginName) . 'App/',
                            $className)) {
                            $pluginsControllers[] = array(
                                'file' => $fileName,
                                'name' => Inflector::camelize($pluginName) . "/" . substr(
                                    $className, 0,
                                    strlen($className) - strlen(
                                        'Controller'))
                            );
                        }
                    }
                }
            }
        }

        sort($pluginsControllers);

        return $pluginsControllers;
    }

    /**
     * Get all Controller Actions for a Plugin
     *
     * @param string $filterDefaultController
     * @return multitype:string
     */
    public function getAllPluginControllersActions($filterDefaultController = false) {

        $pluginControllers = $this->getAllPluginControllers();

        $pluginControllersActions = array();

        foreach ($pluginControllers as $pluginController) {
            $pluginName = $this->getPluginName($pluginController['name']);
            $controllerName = $this->getPluginControllerName(
                $pluginController['name']);

            if (! $filterDefaultController || $pluginName != $controllerName) {
                $controllerClassName = $controllerName . 'Controller';

                $controllerCleanedMethods = $this->getControllerActions(
                    $controllerClassName);

                foreach ($controllerCleanedMethods as $action) {
                    $pluginControllersActions[] = $pluginName . '/' . $controllerName . '/' . $action;
                }
            }
        }

        sort($pluginControllersActions);

        return $pluginControllersActions;
    }

    /**
     * Get all Application Controllers
     * @return multitype:multitype:string unknown
     */
    public function getAllAppControllers() {

        $controllers = array();

        App::uses('Folder', 'Utility');
        $Folder = new Folder();

        $didCD = $Folder->cd(APP . 'Controller');
        if (! empty($didCD)) {
            $files = $Folder->findRecursive('.*Controller\.php');

            foreach ($files as $fileName) {
                $file = basename($fileName);

                // Get the controller name
                $controllerClassName = Inflector::camelize(
                    substr($file, 0, strlen($file) - strlen('.php')));
                App::uses($controllerClassName, 'Controller');

                $controllers[] = array(
                    'file' => $fileName,
                    'name' => substr($controllerClassName, 0,
                        strlen($controllerClassName) - strlen('Controller'))
                );
            }
        }

        sort($controllers);

        return $controllers;
    }

    /**
     * get all of the actions for Application Controllers
     * @return multitype:string
     */
    public function getAllAppControllersActions() {

        $controllers = $this->getAllAppControllers();

        $controllersActions = array();

        foreach ($controllers as $controller) {
            $controllerClassName = $controller['name'];

            $controllerCleanedMethods = $this->getControllerActions(
                $controllerClassName);

            foreach ($controllerCleanedMethods as $action) {
                $controllersActions[] = $controller['name'] . '/' . $action;
            }
        }

        sort($controllersActions);

        return $controllersActions;
    }

    /**
     * Gets all Controllers
     * @return multitype:
     */
    public function getAllControllers() {

        $appControllers = $this->getAllAppControllers();
        $pluginControllers = $this->getAllPluginControllers();

        return array_merge($appControllers, $pluginControllers);
    }

    /**
     * Get all Actions
     * @return multitype:
     */
    public function getAllActions() {

        $appControllersActions = $this->getAllAppControllersActions();
        $pluginControllersActions = $this->getAllPluginControllersActions();

        return array_merge($appControllersActions,
            $pluginControllersActions);
    }

    /**
     * Return the methods of a given class name. Depending on the
     * $filterBaseMethods parameter, it can return the parent methods.
     *
     * @param string $className (eg: 'AcosController')
     * @param boolean $filterBaseMethods
     */
    public function getControllerActions($className, $filterBaseMethods = true) {

        $className = $this->getControllerClassName(
            $className);

        $methods = get_class_methods($className);

        if (isset($methods) && ! empty($methods)) {
            if ($filterBaseMethods) {
                $baseMethods = get_class_methods('Controller');

                $controllerCleanedMethods = array();
                foreach ($methods as $method) {
                    if (! in_array($method, $baseMethods) && strpos($method,
                        '_') !== 0) {
                        $controllerCleanedMethods[] = $method;
                    }
                }

                return $controllerCleanedMethods;
            } else {
                return $methods;
            }
        } else {
            return array();
        }
    }
}