<?php

/*
Plugin Name: Client Dash Woocommerce
Description: Seamlessly integrates WooCommerce with Client Dash.
Version: 0.1
Author: Joel Worsham
Author URI: http://joelworsham.com
License: GPL2
*/

/**
 * Our wrapper function to ensure the plugin only loads if Client Dash
 * is installed.
 *
 * @since Client Dash WooCommerce 0.1
 */
function clientdash_woocommerce() {

	// Need to include plugin file for use of is_plugin_active()
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if ( class_exists( 'ClientDash' ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

		/**
		 * Class ClientDash_WooCommerce
		 *
		 * The main plugin class. Does very awesome things.
		 *
		 * @package WordPress
		 * @subpackage Client Dash WooCommerce
		 *
		 * @since Client Dash WooCommerce 0.1
		 */
		class ClientDash_WooCommerce extends ClientDash {

			/**
			 * Client Dash WooCommerce version.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			public $version = 0.1;

			/**
			 * The path to our plugin.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			public $path;

			/**
			 * The url to our plugin directory.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			public $url;

			/**
			 * If the current user is admin or shop manager, this is true.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			public $admin = false;

			/**
			 * Option defaults for the plugin settings.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			public $option_defaults = array(
				// Role visibility for every content section. This must be typed out
				// EXACTLY accurately with ID's of pages, tabs, and content sections.
				// 1 is hidden from each specific role, 0 is not hidden
				'content_sections_roles' => array(
					'reports' => array(
						'woocommerce' => array(
							'products_overview' => array(
								'editor'       => 'visible',
								'author'       => 'visible',
								'contributor'  => 'visible',
								'subscriber'   => 'visible',
								'customer'     => 'visible',
								'shop_manager' => 'visible'
							),
							'orders_overview'   => array(
								'editor'       => 'visible',
								'author'       => 'hidden',
								'contributor'  => 'hidden',
								'subscriber'   => 'hidden',
								'customer'     => 'hidden',
								'shop_manager' => 'visible'
							)
						)
					)
				)
			);

			// Add our main content section
			function __construct() {

				global $ClientDash;

				// Merge our option defaults with Client Dash's
				$ClientDash->option_defaults = array_merge_recursive( $ClientDash->option_defaults, $this->option_defaults );

				// Establish the plugin root path
				$this->path = plugin_dir_path( __FILE__ );

				// Establish the plugin directory url
				$this->url = plugins_url( null, __FILE__ );

				// Whether shop manager or admin
				add_action( 'admin_init', array( $this, 'set_admin' ) );

				// Add our content sections
				$this->add_content_sections();

				// Register all files
				add_action( 'admin_init', array( $this, 'register_files' ) );

				// Include all files (conditionally)
				if ( isset( $_GET['page'] ) && $_GET['page'] == 'cd_reports'
				     && isset( $_GET['tab'] ) && $_GET['tab'] == 'woocommerce'
				) {
					add_action( 'admin_enqueue_scripts', array( $this, 'include_files' ) );
				}
			}

			/**
			 * Sets admin to true if shop manager or admin.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			public function set_admin() {

				if ( $this->get_user_role() == 'shop_manager' || current_user_can( 'manage_options' ) ) {
					$this->admin = true;
				}
			}

			/**
			 * Registers all plugin files.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			public function register_files() {

				// Main stylesheet
				wp_register_style(
					'clientdash-woocommerce',
					$this->url . '/assets/css/cd.woocommerce.css',
					null,
					$this->version
				);
			}

			/**
			 * Includes all plugin files.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			public function include_files() {

				// Main stylesheet
				wp_enqueue_style( 'clientdash-woocommerce' );
			}

			/**
			 * Adds all content sections for the plugin.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			private function add_content_sections() {

				// Add the products overview section
				$this->add_content_section( array(
					'name'     => 'Products Overview',
					'page'     => 'Reports',
					'tab'      => 'WooCommerce',
					'callback' => array( $this, 'products_overview' )
				) );

				// Add the orders overview section
				$this->add_content_section( array(
					'name'     => 'Orders Overview',
					'page'     => 'Reports',
					'tab'      => 'WooCommerce',
					'callback' => array( $this, 'orders_overview' )
				) );
			}

			/**
			 * The output for the products overview content section.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			public function products_overview() {

				// Get product total
				$all_products = get_posts( array(
					'post_type'   => 'product',
					'post_status' => 'publish',
					'numberposts' => - 1
				) );

				// Organize into our array
				$products = array(
					'counts'     => array(
						'total'      => 0,
						'categories' => 0,
						'featured'   => 0
					),
					'categories' => array(),
					'featured'   => array()
				);
				foreach ( $all_products as $product ) {

					// Update total
					$products['counts']['total'] ++;

					// If in categories, add it to them
					if ( has_term( '', 'product_cat', $product ) ) {
						foreach ( wp_get_post_terms( $product->ID, 'product_cat' ) as $category ) {
							if ( ! array_key_exists( $category->term_id, $products['categories'] ) ) {
								$products['categories'][ $category->term_id ] = array();
								$products['counts']['categories'] ++;
							}
							array_push( $products['categories'][ $category->term_id ], $product );
						}
					}

					// If featured, add it
					if ( get_post_meta( $product->ID, '_featured', true ) == 'yes' ) {
						array_push( $products['featured'], $product );
						$products['counts']['featured'] ++;
					}
				}
				?>
				<h3>Total Products</h3>
				<p class="cd-wc-product-count">
					<a href="<?php admin_url(); ?>/edit.php?post_type=product">
						<?php echo $products['counts']['total']; ?>
					</a>
				</p>

				<h3>Categories</h3>
				<?php if ( ! empty( $products['categories'] ) ) : ?>

					<ul class="cd-wc-categories">
						<?php foreach ( $products['categories'] as $category_ID => $category ) : ?>
							<?php $cat_term = get_term( $category_ID, 'product_cat' ); ?>

							<li class="cd-wc-category">
								<div class="cd-wc-category-container<?php echo $this->admin ? ' cd-wc-hover' : ''; ?>">

									<?php
									if ( $this->admin ) {
										echo '<a href="' . get_admin_url() . "edit.php?s&post_type=product&product_cat=$cat_term->slug\">";
									}
									?>

									<h4 class="cd-wc-category-title">
										<?php echo $cat_term->name; ?>

									</h4>

									<p class="cd-wc-category-count">
										<?php echo count( $category ); ?>
									</p>

									<span>products</span>

									<?php
									if ( $this->admin ) {
										echo '</a>';
									}
									?>
								</div>

							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					No product categories.
				<?php endif; ?>

				<h3>Featured</h3>
				<?php if ( ! empty( $products['featured'] ) ) : ?>

					<ul class="cd-wc-featured-list">
						<?php foreach ( $products['featured'] as $featured ) : ?>

							<li class="cd-wc-featured">

								<div class="cd-wc-featured-container<?php echo $this->admin ? ' cd-wc-hover' : ''; ?>">

									<?php
									if ( $this->admin ) {
										echo '<a href="' . get_edit_post_link( $featured->ID ) . '">';
									}
									?>

									<h4 class="cd-wc-featured-title">
										<?php echo $featured->post_title; ?>
									</h4>

									<?php if ( has_post_thumbnail( $featured->ID ) ) : ?>
										<p class="cd-wc-featured-thumb">
											<?php echo get_the_post_thumbnail( $featured->ID, 'medium' ); ?>
										</p>
									<?php endif; ?>

									<span class="dashicons dashicons-star-filled"></span>

									<?php
									if ( $this->admin ) {
										echo '</a>';
									}
									?>

								</div>

							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					No featured products.
				<?php endif; ?>
			<?php
			}

			/**
			 * The output for the orders overview content section.
			 *
			 * @since Client Dash WooCommerce 0.1
			 */
			public function orders_overview() {

				echo 'aoeifhae';
			}
		}

		// Instantiate the main plugin class
		new ClientDash_WooCommerce();
	} else {

		// Notify the user to activate Client Dash first
		add_action( 'admin_notices', 'clientdash_woocommerce_notice' );
	}
}

add_action( 'plugins_loaded', 'clientdash_woocommerce' );

/**
 * The admin notice warning to activate Client Dash.
 *
 * @since Client Dash WooCommerce 0.1
 */
function clientdash_woocommerce_notice() {

	if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		?>
		<div class="error">
			<p>
				WooCommerce must be activated in order to use the <strong>Client Dash WooCommerce</strong> extension.
			</p>
		</div>
	<?php
	}
	if ( ! class_exists( 'ClientDash' ) ) {
		?>
		<div class="error">
			<p>
				Client Dash must be activated in order to use the <strong>Client Dash WooCommerce</strong> extension.
			</p>
		</div>
	<?php
	}
}