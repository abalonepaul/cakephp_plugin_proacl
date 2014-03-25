<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('AclHtmlHelper', 'Acl.View/Helper');

/**
 * AclHtmlHelper Test Case
 *
 */
class AclHtmlHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->AclHtml = new AclHtmlHelper($View);
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

}
