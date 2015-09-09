<?php
App::uses('RKImageProxyState', 'ApertureConnector.Model');

/**
 * RKImageProxyState Test Case
 *
 */
class RKImageProxyStateTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.aperture_connector.r_k_image_proxy_state'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->RKImageProxyState = ClassRegistry::init('ApertureConnector.RKImageProxyState');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->RKImageProxyState);

		parent::tearDown();
	}

}
