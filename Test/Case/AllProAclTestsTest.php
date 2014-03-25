<?php

class AllProAclTestsTest extends PHPUnit_Framework_TestSuite {

    public static function suite() {
        $suite = new CakeTestSuite('All Acl plugin tests');
        if (CakePlugin::loaded('Acl')) {
            $plugin = CakePlugin::path('Acl');
        } elseif (CakePlugin::loaded('ProAcl')) {
            $plugin = CakePlugin::path('ProAcl');
        }
        $path = $plugin . DS . 'Test' . DS . 'Case' . DS;
        //$path = CakePlugin::path('Acl') . DS . 'Test' . DS . 'Case' . DS;
        $suite->addTestFile($path . 'AllProAclControllersTest.php');
        $suite->addTestFile($path . 'AllProAclComponentsTest.php');
        return $suite;
    }

}
