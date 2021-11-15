/* To disable the Gutenberg’s CSS loading on the front-end  */
function tw_unload_files() {
 
        wp_dequeue_style ( 'wp-block-library' );
        wp_dequeue_style ( 'wp-block-library-theme' );
 
 }
 add_action( 'wp_enqueue_scripts', 'tw_unload_files', 100 );

/* To disable Elementor’s Google font  */
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

/*To disable Elementor’s Font Awesome */
add_action('elementor/frontend/after_register_styles',function() { 
 
        foreach( [ 'solid', 'regular', 'brands' ] as $style ) {
                wp_deregister_style( 'elementor-icons-fa-' . $style );
        }
 
}, 20 );

/*To disable Elementor’s icons (eicons) */
function ti_unload_files() {
 
        if ( is_admin() || current_user_can( 'manage_options' ) ) {
                return;
        }
 
        wp_deregister_style( 'elementor-icons' ); 
 
 }
 add_action( 'wp_enqueue_scripts', 'ti_unload_files', 100 );

/*To fix “Image elements do not have explicit width and height” warning from CWV / PSI */

add_filter( 'the_content', 'add_image_dimensions' );

function add_image_dimensions( $content ) {

    preg_match_all( '/<img[^>]+>/i', $content, $images);

    if (count($images) < 1)
        return $content;

    foreach ($images[0] as $image) {
        preg_match_all( '/(alt|title|src|width|class|id|height)=("[^"]*")/i', $image, $img );

        if ( !in_array( 'src', $img[1] ) )
            continue;

        if ( !in_array( 'width', $img[1] ) || !in_array( 'height', $img[1] ) ) {
            $src = $img[2][ array_search('src', $img[1]) ];
            $alt = in_array( 'alt', $img[1] ) ? ' alt=' . $img[2][ array_search('alt', $img[1]) ] : '';
            $title = in_array( 'title', $img[1] ) ? ' title=' . $img[2][ array_search('title', $img[1]) ] : '';
            $class = in_array( 'class', $img[1] ) ? ' class=' . $img[2][ array_search('class', $img[1]) ] : '';
            $id = in_array( 'id', $img[1] ) ? ' id=' . $img[2][ array_search('id', $img[1]) ] : '';
            list( $width, $height, $type, $attr ) = getimagesize( str_replace( "\"", "" , $src ) );

            $image_tag = sprintf( '<img src=%s%s%s%s%s width="%d" height="%d" />', $src, $alt, $title, $class, $id, $width, $height );
            $content = str_replace($image, $image_tag, $content);
        }
    }

    return $content;
}

/**
 * Disable jQuery migrate
 */

function dequeue_jquery_migrate( $scripts ) {
    if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            [ 'jquery-migrate' ]
        );
    }
}
add_action( 'wp_default_scripts', 'dequeue_jquery_migrate' );

/**
 * Disable the emoji's
 */
function disable_emojis() {
 remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
 remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
 remove_action( 'wp_print_styles', 'print_emoji_styles' );
 remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
 remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
 remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
 remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
 add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
 add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 * 
 * @param array $plugins 
 * @return array Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
 if ( is_array( $plugins ) ) {
 return array_diff( $plugins, array( 'wpemoji' ) );
 } else {
 return array();
 }
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
 if ( 'dns-prefetch' == $relation_type ) {
 /** This filter is documented in wp-includes/formatting.php */
 $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

$urls = array_diff( $urls, array( $emoji_svg_url ) );
 }

return $urls;
}

/**
 * Disable XML-RPC
 */

add_filter( 'xmlrpc_enabled', '__return_false' );
