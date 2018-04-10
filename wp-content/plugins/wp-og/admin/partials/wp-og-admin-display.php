<?php

/**
 * Provide a admin area view for the plugin
 *
 * @link       http://filipstefansson.github.io/wp-og
 * @since      0.1.0
 *
 * @package    WP_OG
 * @subpackage WP_OG/admin/partials
 */
?>

<?php
    /* Load all the post types and the post types the user has selected */
    $args = array(
        'public'   => true
    );
    $post_types = get_post_types($args);
    $selected_post_types = get_option('og-post_types');
?>

<div class="wrap">
    <h2><?php _e( 'Open Graph Tags', 'wp-og' ); ?></h2>
    <p>
    <?php _e( 'Take control of what the Facebook crawler picks up from each page by using Open Graph meta tags. These tags provide structured info about the page such as the title, description, preview image, and more.', 'wp-og' ); ?></p>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <form method="post" action="options.php"> 
                    <h2><?php _e('Edit default settings', 'wp-og'); ?></h2>
                    <p><?php _e('The settings below will be used as defaults and on the frontpage.', 'wp-og'); ?> </p>
                    <?php settings_fields( 'wp-og' ); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                <label for="og-site_name"><?php _e('Site name', 'wp-og'); ?> – <small>og:site_name</small></label>
                            </th>
                            <td>
                                <input type="text" name="og-site_name" id="og-site_name" value="<?php echo esc_attr( get_option('og-site_name') ); ?>" class="regular-text"/>
                                <p class="description">
                                    <?php _e('Provide a site name, e.g. "My Favorite News"', 'wp-og'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="og-title"><?php _e('Title', 'wp-og'); ?> – <small>og:title</small></label>
                            </th>
                            <td>
                                <input type="text" name="og-title" id="og-title" value="<?php echo esc_attr( get_option('og-title') ); ?>" class="regular-text"/>
                                <p class="description">
                                    <?php _e('A clear title without branding or mentioning the domain itself.', 'wp-og'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="og-description"><?php _e('Description', 'wp-og'); ?> – <small>og:description</small></label>
                            </th>
                            <td>
                                <textarea name="og-description" id="og-description" class="large-text"><?php echo esc_attr( get_option('og-description') ); ?></textarea>
                                <p class="description">
                                    <?php _e('A clear description, at least two sentences long.', 'wp-og'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="og-app_id"><?php _e('App ID', 'wp-og'); ?> – <small>og:app_id</small></label>
                            </th>
                            <td>
                                <input type="text" name="og-app_id" id="og-app_id" value="<?php echo esc_attr( get_option('og-app_id') ); ?>" class="regular-text"/>
                                <p class="description">
                                    <?php _e('A Facebook App ID that identifies your website to Facebook.', 'wp-og'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="og-image"><?php _e('Image', 'wp-og'); ?> – <small>og:image</small></label>
                            </th>
                            <td>
                                <input type="text" name="og-image" id="og-image" value="<?php echo esc_attr( get_option('og-image') ); ?>" class="regular-text"/>
                                <button id="select_og_image" class="button"><?php _e('Select image', 'wp-og'); ?></button>
                                <p class="description">
                                    <?php _e('The default image to show on Facebook.', 'wp-og'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php _e('Activate for post types', 'wp-og'); ?></label>
                            </th>
                            <td>
                                <?php  foreach ( $post_types as $post_type ): ?>
                                    <?php $selected = in_array($post_type, $selected_post_types) ? "checked" : ""; ?>
                                    <p>
                                        <label for="og-post_types_<?php echo $post_type ?>">
                                            <input name="og-post_types[]" id="og-post_types_<?php echo $post_type ?>" type="checkbox" value="<?php echo $post_type ?>" <?php echo $selected ?>/>
                                            <span><?php echo $post_type ?></span>
                                        </label>
                                    </p>
                                <?php endforeach; ?> 
                                <p class="description">
                                    <?php _e('Select where you want the OG tags to appear.', 'wp-og'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
       
            <div id="postbox-container-1" class="postbox-container">
                <div class="postbox">
                    <h3 class="hndle"><?php _e('Open Graph Debugger', 'wp-og'); ?></h3>
                    <div class="inside">
                        <p><?php _e('If you update these settings you might need to tell Facebook to scrape the new information.', 'wp-og'); ?> </p>
                        <p>    
                            <a href="https://developers.facebook.com/tools/debug/og/object?q=<?php echo home_url() ?>" class="button" target="_blank"><?php _e('Open Graph Debugger', 'wp-og'); ?></a>
                        </p>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle"><?php _e('Open Graph Best Practices', 'wp-og'); ?></h3>
                    <div class="inside">
                        <p><?php _e('Learn more about how to use the OG tags in the best possible way. ', 'wp-og'); ?></p>
                        <p>    
                            <a href="https://developers.facebook.com/docs/sharing/best-practices#tags" class="button" target="_blank"><?php _e('Learn More', 'wp-og'); ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
