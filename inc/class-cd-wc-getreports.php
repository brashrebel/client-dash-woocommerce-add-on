<?php

/**
 * Class CD_WC_GetReports
 *
 * The object containing all WooCommerce order data.
 *
 * @package Client Dash
 * @package Client Dash WooCommerce
 *
 * @since Client Dash WooCommerce 0.1
 */
class CD_WC_GetReports extends CD_WC {

	/**
	 * This is the property that will inherit the WooCommerce Admin Reports object. This
	 * is required for any report retrieval and all will be done through this instance.
	 *
	 * @since Client Dash WooCommerce 0.1
	 */
	public $reports;

	/**
	 * The total number of orders on the site.
	 *
	 * @since Client Dash WooCommerce 0.1
	 */
	public $total_orders = 0;

	/**
	 * The total number of sales.
	 *
	 * @since Client Dash WooCommerce 0.1
	 */
	public $total_sales = 0;

	/**
	 * The total number of items purchased.
	 *
	 * @since Client Dash WooCommerce 0.1
	 */
	public $total_items = 0;

	/**
	 * The total number of ALL unique customers.
	 *
	 * @since Client Dash wooCommerce 0.1
	 */
	public $total_customers = 0;

	/**
	 * The total number of unique user customers.
	 *
	 * @since Client Dash wooCommerce 0.1
	 */
	public $total_usercustomers = 0;

	/**
	 * The total number of unique guest customers.
	 *
	 * @since Client Dash wooCommerce 0.1
	 */
	public $total_guestcustomers = 0;

	/**
	 * Construct the object.
	 *
	 * @since Client Dash WooCommerce 0.1
	 */
	function __construct() {

		global $CD_WC;

		// We need this WooCommerce file to do any report retrieval
		include_once( $CD_WC->woocommerce_path . 'includes/admin/reports/class-wc-admin-report.php' );

		// Instantiate the WooCommerce object for report retrieval
		$this->reports = new WC_Admin_Report();

		// Store the order information
		$this->get_all_order_info();
	}

	/**
	 * A wrapper function for a WooCommerce function.
	 *
	 * This is used to retrieve data from the database pertaining ONLY to WooCommerce
	 * specific data. The arguments are very complicated. I'll document SOME here, but
	 * the best place to reference this function in use is within the WooCommerce plugin
	 * itself inside of reports within /includes/admin/reports/class-wc-report...
	 *
	 * 'type' can accept a few things: meta|post_data|order_item_meta|order_item.
	 * 'name' allows you to give the output data a name within the output class.
	 * 'nocache' will disable transient caching.
	 * 'debug' will disable transient caching AND var_dump() the SQL query string.
	 *
	 * EG:
	 * 'data' => array(
	 *     '_order_total' => array(
	 *         'type'     => 'meta',
	 *         'function' => 'SUM',
	 *         'name'     => 'total_sales
	 *     ),
	 *     'nocache' => true,
	 *     'debug'   => false
	 * )
	 *
	 * @since Client Dash WooCommerce 0.1
	 *
	 * @param array $args The arguments to pass to the data retrieval.
	 *
	 * @return array|string The retrieved data.
	 */
	public function get_reports( $args ) {

		return $this->reports->get_order_report_data( $args );
	}

	/**
	 * Gets and stores the total site orders.
	 *
	 * @since Client Dash WooCommerce 0.1
	 */
	private function get_all_order_info() {

		// Gather some order totals information
		$order_totals = $this->get_reports( array(
			'data' => array(
				'_order_total' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_sales'
				),
				'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'total_orders'
				)
			),
			'nocache' => true
		) );

		// ...and assign them
		$this->total_sales = $order_totals->total_sales;
		$this->total_orders = absint( $order_totals->total_orders );

		// Gather the total items sold and assign it
		$this->total_items    = absint( $this->get_reports( array(
			'data' => array(
				'_qty' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM',
					'name'            => 'order_item_qty'
				)
			),
			'query_type' => 'get_var'
		) ) );

		// Gather total unique user customers
		$this->total_usercustomers = absint( $this->get_reports( array(
			'data' => array(
				'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'total_orders'
				)
			),
			'where_meta' => array(
				array(
					'meta_key'   => '_customer_user',
					'meta_value' => '0',
					'operator'   => '>'
				)
			)
		) ) );

		// Gather total unique guest customers
		$this->total_guestcustomers = $this->total_customers + absint( $this->get_reports( array(
			'data' => array(
				'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'total_orders'
				)
			),
			'where_meta' => array(
				array(
					'meta_key'   => '_customer_user',
					'meta_value' => '0',
					'operator'   => '='
				)
			)
		) ) );

		// Assign the customer data accordingly
		$this->total_customers = $this->total_usercustomers + $this->total_guestcustomers;
	}
}