<?php

/**
 * @group give_stats
 */
class Tests_Stats extends WP_UnitTestCase {

	protected $_post;
	protected $_user_id;
	protected $_stats;
	protected $_payment_stats;
	protected $payment_id;

	public function setUp() {
		parent::setUp();

		$this->_stats         = new Give_Stats();
		$this->_payment_stats = new Give_Payment_Stats;

		$post_id = $this->factory->post->create( array(
			'post_title'  => 'Test Donation',
			'post_type'   => 'give_forms',
			'post_status' => 'publish'
		) );

		$_multi_level_donations = array(
			array(
				'_give_id'     => array( 'level_id' => '1' ),
				'_give_amount' => '10.00',
				'_give_text'   => 'Basic Level'
			),
			array(
				'_give_id'     => array( 'level_id' => '2' ),
				'_give_amount' => '20.00',
				'_give_text'   => 'Intermediate Level'
			),
			array(
				'_give_id'     => array( 'level_id' => '3' ),
				'_give_amount' => '40.00',
				'_give_text'   => 'Advanced Level'
			),
		);

		$meta = array(
			'give_price'               => '0.00',
			'_give_price_option'       => 'multi',
			'_give_price_options_mode' => 'on',
			'_give_donation_levels'    => array_values( $_multi_level_donations ),
			'give_product_notes'       => 'Donation Notes',
			'_give_product_type'       => 'default'
		);

		foreach ( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		$this->_post = get_post( $post_id );

		/** Generate some donations */
		$this->_user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$user           = get_userdata( $this->_user_id );

		$user_info = array(
			'id'         => $user->ID,
			'email'      => 'testadmin@domain.com',
			'first_name' => $user->first_name,
			'last_name'  => $user->last_name
		);

		$donation_details = array(
			array(
				'id'      => $this->_post->ID,
				'options' => array(
					'price_id' => 1
				)
			)
		);

		$prices     = get_post_meta( $donation_details[0]['id'], '_give_donation_levels', true );

		$item_price = $prices[1]['_give_amount'];

		$purchase_data = array(
			'price'           => number_format( (float) $item_price, 2 ),
			'give_form_title' => get_the_title( $this->_post->ID ),
			'give_form_id'    => $this->_post->ID,
			'date'            => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
			'purchase_key'    => strtolower( md5( uniqid() ) ),
			'user_email'      => $user_info['email'],
			'user_info'       => $user_info,
			'currency'        => 'USD',
			'status'          => 'pending',
			'gateway'         => 'manual'
		);

		$_SERVER['REMOTE_ADDR'] = '10.0.0.0';
		$_SERVER['SERVER_NAME'] = 'give_virtual';

		$payment_id = give_insert_payment( $purchase_data );

		give_update_payment_status( $payment_id, 'complete' );

	}

	/*
	 *
	 * Give_Stats tests
	 *
	 */

	public function test_predefined_date_rages() {

		$out = $this->_stats->get_predefined_dates();

		$expected = array(
			'today'        => 'Today',
			'yesterday'    => 'Yesterday',
			'this_week'    => 'This Week',
			'last_week'    => 'Last Week',
			'this_month'   => 'This Month',
			'last_month'   => 'Last Month',
			'this_quarter' => 'This Quarter',
			'last_quarter' => 'Last Quarter',
			'this_year'    => 'This Year',
			'last_year'    => 'Last Year'
		);

		$this->assertEquals( $expected, $out );

	}

	public function test_setup_dates() {

		// Set start date only
		$this->_stats->setup_dates( 'yesterday' );
		$this->assertInternalType( 'numeric', $this->_stats->start_date );
		$this->assertEquals( $this->_stats->start_date, $this->_stats->end_date );

		// Set some valid predefined date ranges
		$this->_stats->setup_dates( 'yesterday', 'today' );
		$this->assertInternalType( 'numeric', $this->_stats->start_date );
		$this->assertInternalType( 'numeric', $this->_stats->end_date );
		$this->assertGreaterThan( $this->_stats->start_date, $this->_stats->end_date );

		// Set some valid dates
		$this->_stats->setup_dates( '2012-01-12', '2012-04-15' );
		$this->assertInternalType( 'numeric', $this->_stats->start_date );
		$this->assertInternalType( 'numeric', $this->_stats->end_date );
		$this->assertGreaterThan( $this->_stats->start_date, $this->_stats->end_date );

		// Set some valid date strings
		$this->_stats->setup_dates( 'January 15, 2013', 'February 24, 2013' );
		$this->assertInternalType( 'numeric', $this->_stats->start_date );
		$this->assertInternalType( 'numeric', $this->_stats->end_date );
		$this->assertGreaterThan( $this->_stats->start_date, $this->_stats->end_date );


		// Set some valid timestamps
		$this->_stats->setup_dates( '1379635200', '1379645200' );
		$this->assertInternalType( 'numeric', $this->_stats->start_date );
		$this->assertInternalType( 'numeric', $this->_stats->end_date );
		$this->assertGreaterThan( $this->_stats->start_date, $this->_stats->end_date );

		// Set some invalid dates
		$this->_stats->setup_dates( 'nonvaliddatestring', 'nonvaliddatestring' );
		$this->assertInstanceOf( 'WP_Error', $this->_stats->start_date );
		$this->assertInstanceOf( 'WP_Error', $this->_stats->end_date );

	}


	/*
	 *
	 * Give_Payment_Stats tests
	 *
	 */

	public function test_get_earnings_by_date() {

		$stats    = new Give_Payment_Stats;
		$earnings = $stats->get_earnings( 0, 'this_month' );
		$this->assertEquals( 20, $earnings );

	}

	public function test_get_sales_by_date() {

		$stats = new Give_Payment_Stats;
		$sales = $stats->get_sales( 0, 'this_month' );

		$this->assertEquals( 1, $sales );
	}

	public function test_get_earnings_by_date_of_donation() {

		$stats    = new Give_Payment_Stats;
		$earnings = $stats->get_earnings( $this->_post->ID, 'this_month' );
		$this->assertEquals( 20, $earnings );

	}

	public function test_get_sales_by_date_of_donation() {

		$stats = new Give_Payment_Stats;
		$sales = $stats->get_sales( $this->_post->ID, 'this_month' );
		$this->assertEquals( 1, $sales );
		
	}

}
