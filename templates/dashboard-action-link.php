<?php
/**
 * The template for displaying action link within the manage listings table.
 *
 * This template can be overridden by copying it to yourtheme/posterno/restaurant/dashboard-action-link.php
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

?>

<a class="dropdown-item" href="<?php echo esc_url( Helper::get_menu_setup_link( $data->listing_id ) ); ?>">
	<i class="fas fa-utensils mr-2"></i>
	<?php esc_html_e( 'Restaurant menu', 'posterno-restaurants-menu' ); ?>
</a>
