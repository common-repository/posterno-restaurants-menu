<?php
/**
 * The template for displaying the restaurant setup page in the dashboard.
 *
 * This template can be overridden by copying it to yourtheme/posterno/restaurant/restaurant-setup-page.php
 *
 * HOWEVER, on occasion PNO will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 * @package posterno-restaurants-menu
 */

use Posterno\Restaurants\Helper;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) {
	return;
}

$user_id    = get_current_user_id();
$listing_id = isset( $_GET['listing_id'] ) ? absint( $_GET['listing_id'] ) : false;

if ( ! Helper::can_user_setup_food_menu( $user_id, $listing_id ) ) {
	return;
}

$menu_data = Helper::get_menus_data_for_form( $listing_id );

?>

<h2><?php esc_html_e( 'Setup restaurant menu', 'posterno-restaurants-menu' ); ?></h2>
<p><?php echo sprintf( esc_html__( 'You are setting up the food menu for the "%s" listing.', 'posterno-restaurants-menu' ), get_the_title( $listing_id ) ); ?></p>

<?php if ( isset( $_GET['action'] ) && $_GET['action'] === 'saved' ) : ?>

	<?php

		$data = array(
			'message' => esc_html__( 'Menu successfully updated.', 'posterno-restaurants-menu' ),
			'type'    => 'success',
		);

		posterno()->templates
			->set_template_data( $data )
			->get_template_part( 'message' );

		?>

<?php endif; ?>

<form method="POST" action="<?php echo esc_url( pno_get_full_page_url() ); ?>" class="mb-5">
	<div class="card">
		<h5 class="card-header"><?php esc_html_e( 'Menus', 'posterno-restaurants-menu' ); ?></h5>
		<div class="card-body" x-data="<?php echo htmlspecialchars( wp_json_encode( $menu_data, ENT_QUOTES ) ); ?>">

			<p class="card-text"><?php esc_html_e( 'Create menus then add items.', 'posterno-restaurants-menu' ); ?></p>

			<div class="alert alert-primary" role="alert" x-show="items.length <= 0">
				<?php esc_html_e( 'Press the "Add menu" button to create your first menu.', 'posterno-restaurants-menu' ); ?>
			</div>

			<template x-for="item in Object.keys( items )" :key="item">

				<div class="form-group">
					<label class="font-weight-bold"><?php echo esc_html_e( 'Menu name', 'posterno-restaurants-menu' ); ?></label>
					<div class="input-group">
						<input type="text" class="form-control" x-model="items[item].group_name">
						<div class="input-group-append">
							<button class="btn btn-outline-secondary" type="button" @click="var newItems = items; delete newItems[item]; items = newItems.filter(function(e){return e});"><?php esc_html_e( 'Remove', 'posterno-restaurants-menu' ); ?></button>
						</div>
					</div>

					<small class="form-text text-muted">
						<?php echo esc_html_e( 'Example: lunch, dinner, etc.', 'posterno-restaurants-menu' ); ?>
					</small>
				</div>

			</template>

			<button type="button" class="btn btn-secondary btn-sm" x-on:click="items.push( { group_name: '' } )"><?php esc_html_e( 'Add menu', 'posterno-restaurants-menu' ); ?></button>

			<input type="hidden" name="restaurant_menus" x-bind:value="JSON.stringify(items,null,'\t')">

		</div>
		<div class="card-footer text-muted text-right">
			<input type="submit" class="btn btn-primary btn-sm text-decoration-none" value="<?php esc_html_e( 'Save menus', 'posterno-restaurants-menu' ); ?>">
		</div>
	</div>

	<?php wp_nonce_field( 'saving_restaurant_menus_list', 'save_restaurant_menus_nonce' ); ?>
</form>

<?php if ( ! empty( $menu_data ) ) : ?>
<form action="#" method="POST">
	<h3><?php esc_html_e( 'Menu items setup', 'posterno-restaurants-menu' ); ?></h3>

	<div class="card">
		<div class="card-header">

			<ul class="nav nav-pills card-header-pills" role="tablist">

			<?php

			$counter = 0;
			foreach ( $menu_data['items'] as $menu_group ) :
				$counter++;
				?>

				<li class="nav-item">
					<a class="nav-link
					<?php
					if ( $counter === 1 ) :
						?>
						active<?php endif; ?> text-decoration-none" id="<?php echo sanitize_title_with_dashes( $menu_group['group_name'] ); ?>-tab" data-toggle="tab" href="#<?php echo sanitize_title_with_dashes( $menu_group['group_name'] ); ?>" role="tab" aria-controls="<?php echo sanitize_title_with_dashes( $menu_group['group_name'] ); ?>" aria-selected="true"><?php echo esc_html( $menu_group['group_name'] ); ?></a>
				</li>

			<?php endforeach; ?>

			</ul>

		</div>
		<div class="card-body">

			<div class="tab-content" id="restaurant-items-tabs-content">

				<?php

				$tab_counter = 0;

				foreach ( $menu_data['items'] as $menu_group_index => $menu_group ) :

					$menu_items_data = Helper::get_food_items_data_for_form( $listing_id, $menu_group_index );

					$tab_counter++;
					?>

					<div x-data="<?php echo htmlspecialchars( wp_json_encode( $menu_items_data, ENT_QUOTES ) ); ?>" class="tab-pane fade
					<?php
					if ( $tab_counter === 1 ) :
						?>
						show active<?php endif; ?>" id="<?php echo sanitize_title_with_dashes( $menu_group['group_name'] ); ?>" role="tabpanel" aria-labelledby="<?php echo sanitize_title_with_dashes( $menu_group['group_name'] ); ?>-tab">

						<div class="alert alert-primary" role="alert" x-show="fooditems.length <= 0">
							<?php esc_html_e( 'Press the "Add item" button to add items to your menus.', 'posterno-restaurants-menu' ); ?>
						</div>

						<template x-for="fooditem in Object.keys( fooditems )" :key="fooditem">

							<div class="form-items">
								<div class="form-row mb-3">
									<div class="col">
										<label x-bind:for="'dish-name-' + fooditem" class="font-weight-bold"><?php esc_html_e( 'Dish name', 'posterno-restaurants-menu' ); ?></label>
										<input require type="text" class="form-control" x-bind:id="'dish-name-' + fooditem" x-model="fooditems[fooditem].item_name">
										<a href="#" class="btn btn-sm btn-link pl-0 text-danger text-decoration-none" @click.prevent="var newFoodItems = fooditems; delete newFoodItems[fooditem]; fooditems = newFoodItems.filter(function(e){return e});"><?php esc_html_e( 'Delete', 'posterno-restaurants-menu' ); ?></a>
									</div>
									<div class="col">
										<label x-bind:for="'dish-price-' + fooditem" class="font-weight-bold"><?php esc_html_e( 'Price', 'posterno-restaurants-menu' ); ?></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text" id="basic-addon1"><?php echo esc_html( \PNO\CurrencyHelper::get_currency_symbol() ); ?></span>
											</div>
											<input require type="number" min="0" step=".01" class="form-control" x-bind:id="'dish-price-' + fooditem" x-model="fooditems[fooditem].item_price">
										</div>
									</div>
									<div class="col">
										<label x-bind:for="'dish-desc-' + fooditem" class="font-weight-bold"><?php esc_html_e( 'Description', 'posterno-restaurants-menu' ); ?></label>
										<input require type="text" class="form-control" x-bind:id="'dish-desc-' + fooditem" x-model="fooditems[fooditem].item_description">
									</div>
								</div>
							</div>

						</template>

						<button type="button" class="btn btn-secondary btn-sm" x-on:click="fooditems.push( { item_name: '', item_price: '', item_description: '' } )"><?php esc_html_e( 'Add item', 'posterno-restaurants-menu' ); ?></button>

						<input type="hidden" name="restaurant_items[][<?php echo esc_attr( $menu_group['group_name'] ); ?>]" x-bind:value="JSON.stringify(fooditems,null,'\t')">

					</div>

				<?php endforeach; ?>

			</div>

		</div>

		<div class="card-footer text-muted text-right">
			<input type="submit" class="btn btn-primary btn-sm text-decoration-none" value="<?php esc_html_e( 'Save items', 'posterno-restaurants-menu' ); ?>">
		</div>
	</div>

	<?php wp_nonce_field( 'submitting_food_items', 'food_items_submission_nonce' ); ?>
</form>
<?php endif; ?>
