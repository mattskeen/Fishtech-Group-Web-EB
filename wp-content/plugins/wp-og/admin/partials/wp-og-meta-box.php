<?php

/**
 * Provide a meta box view for the plugin
 *
 * @link       http://filipstefansson.github.io/wp-og
 * @since      0.1.0
 *
 * @package    WP_OG
 * @subpackage WP_OG/admin/partials
 */
?>

<?php
    // This is needed to find out what post type the post we're editing is
    global $current_screen;

    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'wp_og_inner_meta_box', 'wp_og_inner_meta_box_nonce' );

    // Use get_post_meta to retrieve an existing values from the database.
    $title = get_post_meta( $post->ID, '_og-title', true );
    $description = get_post_meta( $post->ID, '_og-description', true );
    $image = get_post_meta( $post->ID, '_og-image', true );
?>
    <p>
        <label for="og-title">Title:</label>
        <br>
        <input type="text" id="og-title" name="og-title" value="<?php echo esc_attr( $title ) ?>" class="large-text" placeholder="<?php the_title() ?>" />
        <br>
        <span class="description"><small><?php _e('A clear title without branding or mentioning the domain itself. Leave empty to use post title.', 'wp-og'); ?></small></span>
    </p>
    <p>
        <label for="og-description"><?php _e('Description', 'wp-og'); ?>:</label>
        <br>
        <textarea id="og-description" name="og-description" class="large-text" placeholder="<?php echo get_the_excerpt() ?>" ><?php echo esc_attr( $description ) ?></textarea>
        <br>
        <span class="description">
            <small>
                <?php _e('A clear description, at least two sentences long.', 'wp-og'); ?>
                <?php if( $current_screen->post_type == "post" ): ?> <?php _e('Leave empty to use excerpt.', 'wp-og'); ?> <?php endif; ?>
            </small>
        </span>
    </p>
    <p>
        <label for="og-image"><?php _e('Image:', 'wp-og'); ?></label>
        <br>
        <input type="text" id="og-image" name="og-image" value="<?php echo esc_attr( $image ) ?>" class="regular-text" placeholder="<?php echo get_option('og-image'); ?>"/>
        <button class="button" id="select_og_image"><?php _e('Select image', 'wp-og'); ?></button>
        <br>
        <span class="description"><small>
            <?php 
            $url = "options-general.php?page=wp-og";
            $link = sprintf( wp_kses( __( 'The minimum image size is 200 x 200 pixels. Leave empty to use the <a href="%s">default image</a>.', 'wp-og' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
            echo $link;
            ?>
        </small></span>
    </p>

    <p><?php _e('If you update these settings you might need to tell Facebook to scrape the new information.', 'wp-og'); ?> </p>
    <p>    
        <a href="https://developers.facebook.com/tools/debug/og/object?q=<?php the_permalink() ?>" class="button-secondary" target="_blank"><?php _e('Open Graph Debugger', 'wp-og'); ?></a>
    </p>
