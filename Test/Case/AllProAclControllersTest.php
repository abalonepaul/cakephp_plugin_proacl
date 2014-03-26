<?php

class AllProAclControllersTest extends PHPUnit_Framework_TestSuite {

    public static function suite() {
        $suite = new CakeTestSuite('All ProAcl controller class tests');
        if (CakePlugin::loaded('Acl')) {
            $plugin = CakePlugin::path('Acl');
        } elseif (CakePlugin::loaded('ProAcl')) {
            $plugin = CakePlugin::path('ProAcl');
        }
        $path = $plugin . DS . 'Test' . DS . 'Case';
        $suite->addTestDirectory($path . DS . 'Controller');
        return $suite;
    }

}
