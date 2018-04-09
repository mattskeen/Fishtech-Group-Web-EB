<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product, $dfd_native;
$unique_id = uniqid('woo-single-image-');

$woo_image_section_class = '';

$woo_image_section_class .= (isset($dfd_native['woo_single_thumb_position']) && !empty($dfd_native['woo_single_thumb_position'])) ? ' '.$dfd_native['woo_single_thumb_position'] : '';
?>

<div class="images <?php echo esc_attr($woo_image_section_class) ?>">
	<div class="single-product-image woocommerce-product-gallery__image">
		<?php
		if ( has_post_thumbnail() ) {
			$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
			$thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
			if(function_exists('wc_get_image_size')) {
				$image_size = wc_get_image_size('shop_single');
				$img_url = dfd_aq_resize($image_link, $image_size['width'], $image_size['height'], $image_size['crop'], true, true);
				if(!$img_url) {
					$img_url = $image_link;
				}
				$image = '<img src="'.esc_url($img_url).'" class="dfd-woo-main-image wp-post-image" alt="'.esc_attr($image_title).'" />';
			} else {
				$image = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			}

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div itemprop="image"  id="'. esc_attr($unique_id) .'" class="woocommerce-main-image %s"><a href="'.esc_url($image_link).'" class="prettyPhoto" data-thumb="'.esc_url($thumb_url[0]).'" data-rel="prettyPhoto[woo_single_gal]">%s</a></div>', $image_title, $image, $image_link ), $post->ID );
		} else {
			if(function_exists('wc_placeholder_img_src')) {
				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', wc_placeholder_img_src() ), $post->ID );
			} else {
				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', woocommerce_placeholder_img_src() ), $post->ID );
			}
		}
		?>
	</div>
	
	<div class="single-product-thumbnails">
		<?php do_action( 'woocommerce_product_thumbnails' ); ?>
	</div>
</div>