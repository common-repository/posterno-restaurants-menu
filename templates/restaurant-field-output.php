<?php
/**
 * The template for displaying the output of the restaurant field on listings pages.
 *
 * This template can be overridden by copying it to yourtheme/posterno/restaurant/restaurant-field-output.php
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

$value = Helper::get_formatted_menu_values( $data->value );

if ( empty( $value ) ) {
	return;
}

foreach ( $value as $menu_group ) : ?>

	<?php if ( isset( $menu_group['group_title'] ) ) : ?>
		<h4 class="mt-5 mb-3"><?php echo esc_html( $menu_group['group_title'] ); ?></h4>

		<hr>
	<?php endif; ?>

	<?php foreach ( $menu_group['menu_items'] as $menu_item ) : ?>

		<div class="row mb-3">
			<div class="col-sm-8">
				<h6 class="menu-title mb-2 font-weight-bold"><?php echo esc_html( $menu_item['item_name'] ); ?></h6>

				<?php if ( ! empty( $menu_item['item_description'] ) ) : ?>
					<p class="menu-detail m-0 text-black-50"><?php echo esc_html( $menu_item['item_description'] ); ?></p>
				<?php endif; ?>
			</div>
			<div class="col-sm-4 menu-price-detail text-right">
				<?php if ( ! empty( $menu_item['item_price'] ) ) : ?>
					<h5 class="menu-price m-0 font-weight-bold"><?php echo \PNO\CurrencyHelper::price( $menu_item['item_price'] ); //phpcs:ignore ?></h5>
				<?php endif; ?>
			</div>
		</div>

		<?php endforeach; ?>

<?php endforeach; ?>
