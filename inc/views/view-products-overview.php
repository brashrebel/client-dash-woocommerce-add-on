<h3>Total Products</h3>
<p class="cd-wc-product-count">
	<a href="<?php admin_url(); ?>/edit.php?post_type=product">
		<?php echo $products['counts']['total']; ?>
	</a>
</p>

<h3>Categories</h3>
<?php if ( ! empty( $products['categories'] ) ) : ?>

	<ul id="cd-wc-categories" class="cd-wc-grid">
		<?php foreach ( $products['categories'] as $category_ID => $category ) : ?>
			<?php $cat_term = get_term( $category_ID, 'product_cat' ); ?>

			<li class="cd-wc-grid-item">
				<div class="cd-wc-grid-item-container<?php echo $this->admin ? ' cd-wc-hover' : ''; ?>">

					<?php
					if ( $this->admin ) {
						echo '<a href="' . get_admin_url() . "edit.php?s&post_type=product&product_cat=$cat_term->slug\">";
					}
					?>

					<h4 class="cd-wc-grid-item-title">
						<?php echo $cat_term->name; ?>

					</h4>

					<p class="cd-wc-grid-item-count">
						<?php echo count( $category ); ?>
					</p>

					<span>Products</span>

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

	<ul id="cd-wc-featured" class="cd-wc-grid">
		<?php foreach ( $products['featured'] as $featured ) : ?>

			<li class="cd-wc-grid-item">

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