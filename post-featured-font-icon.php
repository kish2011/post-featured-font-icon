<?php
/*
Plugin Name: Post Featured Font Icon
Plugin URI: http://wordpress.org/extend/plugins/post-featured-font-icon/
Version: 1.0
Description: Set Post Featured Icon for featured image, add font icon to post title
Author: Kishore
Author URI: http://blog.kishorechandra.co.in/
*/


/**
 * Added a new meta box
 * @param <string>
 * @param <string>
 * @return <string>
 */

function set_post_featured_icon_meta_boxes( $post_type, $post ) {
    $types = apply_filters( 'icon_post_type', array('post', 'page') );

    if(in_array($post_type, $types)){
        add_meta_box(
            'post-featured-icon',
            __( 'Set Featured Icon', '_tk' ),
            'post_icon_sidebar',
            $post_type,
            'side',
            'low'
        );
    }
}

add_action( 'add_meta_boxes', 'set_post_featured_icon_meta_boxes', 10, 2 );

/**
 * Shows no thumbnail for pages
 * @param <null>
 * @return <string>
 */

function post_icon_sidebar(){
    global $post;

    $custom = get_post_custom($post->ID);

    if (!empty($custom["set-post-featured-icon"][0])) {
        $set_post_featured_icon = $custom["set-post-featured-icon"][0];
    } else {
        $set_post_featured_icon = null;
    }

    if (!empty($custom["set-post-featured-icon-to-title"][0])) {
        $set_post_featured_icon_to_title = $custom["set-post-featured-icon-to-title"][0];
    } else {
        $set_post_featured_icon_to_title = null;
    }

    if (!empty($custom["post-featured-icon"][0])) {
        $post_featured_icon = $custom["post-featured-icon"][0];
    } else {
        $post_featured_icon = null;
    }
?>
    <input type="checkbox" name="set-post-featured-icon"
    <?php if( !empty( $set_post_featured_icon ) ) {
        ?>checked="checked"<?php
    } ?> />
<?php
    _e( 'Check this to replace featured image with font icon.');

?>
    <br />
    <input type="checkbox" name="set-post-featured-icon-to-title"
    <?php if( !empty( $set_post_featured_icon_to_title ) ) {
        ?>checked="checked"<?php
    } ?> />
<?php
    _e( 'Check this to add font icon to post title.');

?>
    <div class="wrap">
        <h4><?php _e('Icon Picker'); ?></h4>
        <input class="regular-text" type="hidden" id="icon_picker_example_icon1" name="post-featured-icon" value="<?php if( $post_featured_icon == true ) { echo esc_attr( $post_featured_icon  ); } ?>"/>
        <div id="preview_icon_picker_example_icon1" data-target="#icon_picker_example_icon1" class="button icon-picker <?php if( !empty( $post_featured_icon ) ) { $v=explode('|',$post_featured_icon); echo $v[0].' '.$v[1]; } ?>"></div>
    </div>
    <?php
}


/**
 * Saves the detalis like meta date for posts
 * @param <int>
 * @return <int>
 */

function save_details($post_ID = 0) {
    $post_ID = (int) $post_ID;
    $post_type = get_post_type( $post_ID );
    $post_status = get_post_status( $post_ID );

    if ($post_type) {
        if (isset($_POST["set-post-featured-icon"])) {
            update_post_meta($post_ID, "set-post-featured-icon", $_POST["set-post-featured-icon"]);
        } else {
            delete_post_meta( $post_ID, 'set-post-featured-icon');
        }

        if (isset($_POST["set-post-featured-icon-to-title"])) {
            update_post_meta($post_ID, "set-post-featured-icon-to-title", $_POST["set-post-featured-icon-to-title"]);
        } else {
            delete_post_meta( $post_ID, 'set-post-featured-icon-to-title');
        }

        if (isset($_POST["post-featured-icon"])) {
            update_post_meta($post_ID, "post-featured-icon", $_POST["post-featured-icon"]);
        } else {
            delete_post_meta( $post_ID, 'post-featured-icon');
        }
    }
    return $post_ID;
}

add_action('save_post', 'save_details');


/**
 * Show icon for post image
 * @param <string>
 * @param <int>
 * @param <int>
 * @return <string>
 */

function icon_post_image_html( $html, $post_id, $post_image_id ) {

    global $post;

    $custom = get_post_custom( $post_id );

    if (!empty($custom["set-post-featured-icon"][0])) {
        $set_post_featured_icon = $custom["set-post-featured-icon"][0];
    } else {
        $set_post_featured_icon = null;
    }

    if (!empty($custom["post-featured-icon"][0])) {
        $post_featured_icon = $custom["post-featured-icon"][0];
    } else {
        $post_featured_icon = null;
    }

    if( !empty( $post_featured_icon ) && !empty( $set_post_featured_icon )  ) {
        $v=explode('|',$post_featured_icon);
        $html = '<i class="'. $v[0].' '.$v[1].'"></i>';
    }

    return $html;

}

add_filter( 'post_thumbnail_html', 'icon_post_image_html', 10, 3 );


/**
 * Add Icon to post title
 * @param <string>
 * @param <int>
 * @return <string>
 */

function icon_post_title_html($title, $id) {
    global $post;

    $custom = get_post_custom($id);

    if (!empty($custom["set-post-featured-icon-to-title"][0])) {
        $set_post_featured_icon_to_title = $custom["set-post-featured-icon-to-title"][0];
    } else {
        $set_post_featured_icon_to_title = null;
    }

    if (!empty($custom["post-featured-icon"][0])) {
        $post_featured_icon = $custom["post-featured-icon"][0];
    } else {
        $post_featured_icon = null;
    }


    if( !empty( $post_featured_icon ) && !empty( $set_post_featured_icon_to_title ) ) {
        $v=explode('|',$post_featured_icon);
        $html = '<i class="'. $v[0].' '.$v[1].'"></i>';

        $title = $html. $title;
    }
    return $title;
}

/**
 * Add Icon to post title
 * @param <array>
 * @return null
 */

function condition_filter_title($array){
    global $wp_query;

    if( $array === $wp_query ) {
        add_filter('the_title', 'icon_post_title_html', 10, 2);
    } else {
        remove_filter('the_title', 'icon_post_title_html', 10, 2);
    }
}

add_action('loop_start','condition_filter_title');

function the_post_font_icon( $id = null ) {
    global $post;

    $id = ( null === $id ) ? $post->ID : $post_id;

    $custom = get_post_custom($id);

    if (!empty($custom["post-featured-icon"][0])) {
        $post_featured_icon = $custom["post-featured-icon"][0];
    } else {
        $post_featured_icon = null;
    }

    if( !empty( $post_featured_icon ) ) {
        $v=explode('|',$post_featured_icon);
        $the_post_font_icon = '<i class="'. $v[0].' '.$v[1].'"></i>';
    } else {
        $the_post_font_icon = null;
    }

    echo $the_post_font_icon;
}

/**
 * Load Icon Picker Font in Admin Area
 * @param <array>
 * @return null
 */

function icon_picker_scripts() {
    $css = plugin_dir_url( __FILE__ ) . 'css/icon-picker.css';
    wp_enqueue_style( 'dashicons-picker', $css, array( 'dashicons' ), '1.0' );

    $font1 = plugin_dir_url( __FILE__ ) . 'fonts/genericons/genericons.css';
    wp_enqueue_style( 'genericons', $font1, '', '');

    $font2 = plugin_dir_url( __FILE__ ) . 'fonts/font-awesome/css/font-awesome.css';
    wp_enqueue_style( 'font-awesome', $font2,'','');

    $js = plugin_dir_url( __FILE__ ) . '/js/icon-picker.js';
    wp_enqueue_script( 'dashicons-picker', $js, array( 'jquery' ), '1.0' );
}
// Make sure we only enqueue on our admin page //
add_action( 'admin_enqueue_scripts', 'icon_picker_scripts' );

/**
 * Load Icon Font CSS in Front Area
 * @param <array>
 * @return null
 */

function load_icon_style_scripts() {
    $font2 = plugin_dir_url( __FILE__ ) . 'fonts/font-awesome/css/font-awesome.css';
    if (!is_admin()) {
        wp_enqueue_style( 'font-awesome', $font2,'','');
    }
}
add_action( 'wp_enqueue_scripts', 'icon_picker_scripts' );
