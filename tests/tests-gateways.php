<?php


/**
 * @group give_gateways
 */
class Test_Gateways extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		$this->_post = Give_Helper_Form::create_multilevel_form();

	}

	public function test_payment_gateways() {
		$out = give_get_payment_gateways();
		$this->assertArrayHasKey( 'paypal', $out );
		$this->assertArrayHasKey( 'manual', $out );

		$this->assertEquals( 'PayPal Standard', $out['paypal']['admin_label'] );
		$this->assertEquals( 'PayPal', $out['paypal']['checkout_label'] );

		$this->assertEquals( 'Test Payment', $out['manual']['admin_label'] );
		$this->assertEquals( 'Test Payment', $out['manual']['checkout_label'] );
	}

	public function test_enabled_gateways() {

		global $give_options;


		$enabled_gateway_list = give_get_enabled_payment_gateways();

		$first_gateway_id     = current( array_keys( $enabled_gateway_list ) );
		$this->assertEquals( 'manual', give_get_default_gateway(null) );

		// Test when default is set to paypal
		$give_options['default_gateway'] = 'paypal';
		$this->assertEquals( 'paypal', give_get_default_gateway(null) );

		// Test default is set to manual and we ask for it sorted
		$give_options['default_gateway'] = 'manual';
		$this->assertEquals( 'manual', give_get_default_gateway(null) );

		// Reset these so the rest of the tests don't fail
		unset( $give_options['default_gateway'], $give_options['gateways']['paypal'], $give_options['gateways']['manual'] );

	}

	public function test_is_gateway_active() {
		$this->assertFalse( give_is_gateway_active( 'paypal' ) );
	}

	public function test_default_gateway() {

		global $give_options;

		$give_options['gateways']           = array();
		$give_options['gateways']['paypal'] = '1';
		$give_options['gateways']['manual'] = '1';

		$this->assertEquals( 'paypal', give_get_default_gateway(null) );

		$give_options['default_gateway']    = 'manual';
		$give_options['gateways']           = array();
		$give_options['gateways']['manual'] = '1';
		$give_options['gateways']['stripe'] = '1';

		$this->assertEquals( 'manual', give_get_default_gateway(null) );
	}

	public function test_get_gateway_admin_label() {
		global $give_options;

		$give_options['gateways']           = array();
		$give_options['gateways']['paypal'] = '1';
		$give_options['gateways']['manual'] = '1';

		$this->assertEquals( 'PayPal Standard', give_get_gateway_admin_label( 'paypal' ) );
		$this->assertEquals( 'Test Payment', give_get_gateway_admin_label( 'manual' ) );
	}

	public function test_get_gateway_checkout_label() {
		global $give_options;

		$give_options['gateways']           = array();
		$give_options['gateways']['paypal'] = '1';
		$give_options['gateways']['manual'] = '1';

		$this->assertEquals( 'PayPal', give_get_gateway_checkout_label( 'paypal' ) );
		$this->assertEquals( 'Test Donation', give_get_gateway_checkout_label( 'manual' ) );
	}

	public function test_chosen_gateway() {
		$this->assertEquals( 'paypal', give_get_chosen_gateway($this->_post->ID) );
	}

	public function test_no_gateway_error() {

		global $give_options;

		$give_options['gateways'] = array();

		give_no_gateway_error();

		$errors = give_get_errors();

		$this->assertArrayHasKey( 'no_gateways', $errors );
		$this->assertEquals( 'You must enable a payment gateway to use Give', $errors['no_gateways'] );
	}
}
