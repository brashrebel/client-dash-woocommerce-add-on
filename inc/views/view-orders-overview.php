<h3>Total Sales</h3>
<p class="cd-wc-product-count">
	<a href="" onclick="return false">
		$<?php echo ! empty( $reports->total_sales ) ? $reports->total_sales : 0 ; ?>
	</a>
</p>

<h3>Orders</h3>
<ul class="cd-wc-grid">
	<li class="cd-wc-grid-item">
		<div class="cd-wc-grid-item-container">

			<h4 class="cd-wc-grid-item-title">
				Orders Placed
			</h4>

			<p class="cd-wc-grid-item-count">
				<?php echo $reports->total_orders; ?>
			</p>

			<span>Total</span>

		</div>
	</li>

	<li class="cd-wc-grid-item">
		<div class="cd-wc-grid-item-container">

			<h4 class="cd-wc-grid-item-title">
				Products
			</h4>

			<p class="cd-wc-grid-item-count">
				<?php echo $reports->total_items; ?>
			</p>

			<span>Total</span>

		</div>
	</li>
</ul>

<h3>Unique Customers</h3>
<ul class="cd-wc-grid">
	<li class="cd-wc-grid-item">
		<div class="cd-wc-grid-item-container">

			<h4 class="cd-wc-grid-item-title">
				All
			</h4>

			<p class="cd-wc-grid-item-count">
				<?php echo $reports->total_customers; ?>
			</p>

			<span>Total</span>

		</div>
	</li>

	<li class="cd-wc-grid-item">
		<div class="cd-wc-grid-item-container">

			<h4 class="cd-wc-grid-item-title">
				Registered
			</h4>

			<p class="cd-wc-grid-item-count">
				<?php echo $reports->total_usercustomers; ?>
			</p>

			<span>Total</span>

		</div>
	</li>

	<li class="cd-wc-grid-item">
		<div class="cd-wc-grid-item-container">

			<h4 class="cd-wc-grid-item-title">
				Guests
			</h4>

			<p class="cd-wc-grid-item-count">
				<?php echo $reports->total_guestcustomers; ?>
			</p>

			<span>Total</span>

		</div>
	</li>
</ul>