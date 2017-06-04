<?php


/* ----------------------------------------------------------------------------------- */
/* jQuery Enqueue */
/* ----------------------------------------------------------------------------------- */

function canvas_enqueue_style() {
  //  wp_enqueue_style('canvas-bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', '', '', 'all');
  //  wp_enqueue_style('canvas-superfish', get_template_directory_uri() . '/css/superfish.css', '', '', 'all');
    wp_enqueue_style('canvas-fonts', get_template_directory_uri() . '/fonts/fonts.css', '', '', 'all');
    wp_enqueue_style('canvas-style', get_stylesheet_uri());
}

add_action('wp_enqueue_scripts', 'canvas_enqueue_style');

function canvas_enqueue_scripts() {
    if (!is_admin()) {
     //   wp_enqueue_script('canvas-superfish', get_template_directory_uri() . '/js/superfish.min.js', array('jquery'));
     //   wp_enqueue_script('canvas-modernizr', get_template_directory_uri() . '/js/modernizr.min.js', array('jquery'));
      //  wp_enqueue_script('canvas-custom', get_template_directory_uri() . 'assets/js/custom.js', array('jquery'));
      //  wp_enqueue_script('canvas-mobileslicknav', get_template_directory_uri() . '/js/jquery.slicknav.min.js', array('jquery'), '', true);
        if (is_singular() and get_site_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
}

add_action('wp_enqueue_scripts', 'canvas_enqueue_scripts');
//
/**
 * Load plugin notification file
 */
	//add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 825, 510, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu',      'twentyfifteen' ),
		'social'  => __( 'Social Links Menu', 'twentyfifteen' ),
	) );
register_nav_menus( array(
        'secondary' => __( 'Primary Menu',      'twentyfifteen' ),
        'social'  => __( 'Social Links Menu', 'twentyfifteen' ),
    ) );
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );

	/*
	 * Enable support for custom logo.
	 *
	 * @since Twenty Fifteen 1.5
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 248,
		'width'       => 248,
		'flex-height' => true,
	) );




function woodpecker_get_option($name) {
    $options = get_option('woodpecker_options');
    if (isset($options[$name]))
        return $options[$name];
}




function woodpecker_import_file($file, $post_id = 0, $import_date = 'file') {
    set_time_limit(120);
    // Initially, Base it on the -current- time.
    $time = current_time('mysql', 1);
//     Next, If it's post to base the upload off:
    $time = gmdate('Y-m-d H:i:s', @filemtime($file));
//     A writable uploads dir will pass this test. Again, there's no point overriding this one.
    if (!( ( $uploads = wp_upload_dir($time) ) && false === $uploads['error'] )) {
        return new WP_Error('upload_error', $uploads['error']);
    }
    $wp_filetype = wp_check_filetype($file, null);
    extract($wp_filetype);
    if ((!$type || !$ext ) && !current_user_can('unfiltered_upload')) {
        return new WP_Error('wrong_file_type', __('Sorry, this file type is not permitted for security reasons.', 'canvas')); //A WP-core string..
    }
    $file_name = str_replace('\\', '/', $file);
    if (preg_match('|^' . preg_quote(str_replace('\\', '/', $uploads['basedir'])) . '(.*)$|i', $file_name, $mat)) {
        $filename = basename($file);
        $new_file = $file;
        $url = $uploads['baseurl'] . $mat[1];
        $attachment = get_posts(array('post_type' => 'attachment', 'meta_key' => '_wp_attached_file', 'meta_value' => ltrim($mat[1], '/')));
        if (!empty($attachment)) {
            return new WP_Error('file_exists', __('Sorry, That file already exists in the WordPress media library.', 'canvas'));
        }
        //Ok, Its in the uploads folder, But NOT in WordPress's media library.
        if ('file' == $import_date) {
            $time = @filemtime($file);
            if (preg_match("|(\d+)/(\d+)|", $mat[1], $datemat)) { //So lets set the date of the import to the date folder its in, IF its in a date folder.
                $hour = $min = $sec = 0;
                $day = 1;
                $year = $datemat[1];
                $month = $datemat[2];
                // If the files datetime is set, and it's in the same region of upload directory, set the minute details to that too, else, override it.
                if ($time && date('Y-m', $time) == "$year-$month") {
                    list($hour, $min, $sec, $day) = explode(';', date('H;i;s;j', $time));
                }
                $time = mktime($hour, $min, $sec, $month, $day, $year);
            }
            $time = gmdate('Y-m-d H:i:s', $time);
            // A new time has been found! Get the new uploads folder:
            // A writable uploads dir will pass this test. Again, there's no point overriding this one.
            if (!( ( $uploads = wp_upload_dir($time) ) && false === $uploads['error'] ))
                return new WP_Error('upload_error', $uploads['error']);
            $url = $uploads['baseurl'] . $mat[1];
        }
    } else {
        $filename = wp_unique_filename($uploads['path'], basename($file));
        // copy the file to the uploads dir
        $new_file = $uploads['path'] . '/' . $filename;
        if (false === @copy($file, $new_file))
            return new WP_Error('upload_error', sprintf(__('The selected file could not be copied to %s.', 'canvas'), $uploads['path']));
        // Set correct file permissions
        $stat = stat(dirname($new_file));
        $perms = $stat['mode'] & 0000666;
        @ chmod($new_file, $perms);
        // Compute the URL
        $url = $uploads['url'] . '/' . $filename;
        if ('file' == $import_date)
            $time = gmdate('Y-m-d H:i:s', @filemtime($file));
    }
    //Apply upload filters
    $return = apply_filters('wp_handle_upload', array('file' => $new_file, 'url' => $url, 'type' => $type));
    $new_file = $return['file'];
    $url = $return['url'];
    $type = $return['type'];
    $title = preg_replace('!\.[^.]+$!', '', basename($file));
    $content = '';

    if ($time) {
        $post_date_gmt = $time;
        $post_date = $time;
    } else {
        $post_date = current_time('mysql');
        $post_date_gmt = current_time('mysql', 1);
    }

    // Construct the attachment array
    $attachment = array(
        'post_mime_type' => $type,
        'guid' => $url,
        'post_parent' => $post_id,
        'post_title' => $title,
        'post_name' => $title,
        'post_content' => $content,
        'post_date' => $post_date,
        'post_date_gmt' => $post_date_gmt
    );
    $attachment = apply_filters('afs-import_details', $attachment, $file, $post_id, $import_date);
    //Win32 fix:
    $new_file = str_replace(strtolower(str_replace('\\', '/', $uploads['basedir'])), $uploads['basedir'], $new_file);
    // Save the data
    $id = wp_insert_attachment($attachment, $new_file, $post_id);
    if (!is_wp_error($id)) {
        $data = wp_generate_attachment_metadata($id, $new_file);
        wp_update_attachment_metadata($id, $data);
    }
    //update_post_meta( $id, '_wp_attached_file', $uploads['subdir'] . '/' . $filename );

    return $id;
}
add_action( 'init', 'codex_project_init' );
/**
 * Register a team post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function codex_project_init() {
	$labels = array(
		'name'               => _x( 'Teams', 'post type general name', 'canvas' ),
		'singular_name'      => _x( 'Team', 'post type singular name', 'canvas' ),
		'menu_name'          => _x( 'Teams', 'admin menu', 'canvas' ),
		'name_admin_bar'     => _x( 'Team', 'add new on admin bar', 'canvas' ),
		'add_new'            => _x( 'Add New', 'team', 'canvas' ),
		'add_new_item'       => __( 'Add New Team', 'canvas' ),
		'new_item'           => __( 'New Team', 'canvas' ),
		'edit_item'          => __( 'Edit Team', 'canvas' ),
		'view_item'          => __( 'View Team', 'canvas' ),
		'all_items'          => __( 'All Teams', 'canvas' ),
		'search_items'       => __( 'Search Teams', 'canvas' ),
		'parent_item_colon'  => __( 'Parent Teams:', 'canvas' ),
		'not_found'          => __( 'No teams found.', 'canvas' ),
		'not_found_in_trash' => __( 'No teams found in Trash.', 'canvas' )
	);

	$args = array(
		'labels'             => $labels,
                'description'        => __( 'Description.', 'canvas' ),
		    
            'public' => true, 
            'query_var'=> true, 
            'publicly_queryable'=> true,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'menu_position' => 5,
        
            'has_archive' => true,
            'supports' => array(
                    'title',
                    'author',
                    'thumbnail',
                    'revisions',
                    'comments',
                    'editor',
                    'excerpt')
         
	);

	register_post_type( 'team', $args );
	$labels = array(
		'name'               => _x( 'News', 'post type general name', 'canvas' ),
		'singular_name'      => _x( 'New', 'post type singular name', 'canvas' ),
		'menu_name'          => _x( 'News', 'admin menu', 'canvas' ),
		'name_admin_bar'     => _x( 'New', 'add new on admin bar', 'canvas' ),
		'add_new'            => _x( 'Add New', 'new', 'canvas' ),
		'add_new_item'       => __( 'Add New New', 'canvas' ),
		'new_item'           => __( 'New New', 'canvas' ),
		'edit_item'          => __( 'Edit New', 'canvas' ),
		'view_item'          => __( 'View New', 'canvas' ),
		'all_items'          => __( 'All News', 'canvas' ),
		'search_items'       => __( 'Search News', 'canvas' ),
		'parent_item_colon'  => __( 'Parent News:', 'canvas' ),
		'not_found'          => __( 'No news found.', 'canvas' ),
		'not_found_in_trash' => __( 'No news found in Trash.', 'canvas' )
	);

	$args = array(
		'labels'             => $labels,
                'description'        => __( 'Description.', 'canvas' ),
		    
            'public' => true, 
            'query_var'=> true, 
            'publicly_queryable'=> true,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'menu_position' => 5,
        
            'has_archive' => true,
            'supports' => array(
                    'title',
                    'author',
                    'thumbnail',
                    'revisions',
                    'comments',
                    'editor',
                    'excerpt')
         
	);

	register_post_type( 'new', $args );
	$labels = array(
		'name'               => _x( 'Partners', 'post type general name', 'canvas' ),
		'singular_name'      => _x( 'Partner', 'post type singular name', 'canvas' ),
		'menu_name'          => _x( 'Partners', 'admin menu', 'canvas' ),
		'name_admin_bar'     => _x( 'Partner', 'add partner on admin bar', 'canvas' ),
		'add_new'            => _x( 'Add Partner', 'partner', 'canvas' ),
		'add_new_item'       => __( 'Add Partner Partner', 'canvas' ),
		'new_item'           => __( 'Partner Partner', 'canvas' ),
		'edit_item'          => __( 'Edit Partner', 'canvas' ),
		'view_item'          => __( 'View Partner', 'canvas' ),
		'all_items'          => __( 'All Partners', 'canvas' ),
		'search_items'       => __( 'Search Partners', 'canvas' ),
		'parent_item_colon'  => __( 'Parent Partners:', 'canvas' ),
		'not_found'          => __( 'No partners found.', 'canvas' ),
		'not_found_in_trash' => __( 'No partners found in Trash.', 'canvas' )
	);

	$args = array(
		'labels'             => $labels,
                'description'        => __( 'Description.', 'canvas' ),
		    
            'public' => true, 
            'query_var'=> true, 
            'publicly_queryable'=> true,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'menu_position' => 5,
        
            'has_archive' => true,
            'supports' => array(
                    'title',
                    'author',
                    'thumbnail',
                    'revisions',
                    'comments',
                    'editor',
                    'excerpt')
         
	);

	register_post_type( 'partner', $args );
	$labels = array(
		'name'               => _x( 'Events', 'post type general name', 'canvas' ),
		'singular_name'      => _x( 'Event', 'post type singular name', 'canvas' ),
		'menu_name'          => _x( 'Events', 'admin menu', 'canvas' ),
		'name_admin_bar'     => _x( 'Event', 'add event on admin bar', 'canvas' ),
		'add_new'            => _x( 'Add Event', 'event', 'canvas' ),
		'add_new_item'       => __( 'Add Event Event', 'canvas' ),
		'new_item'           => __( 'Event Event', 'canvas' ),
		'edit_item'          => __( 'Edit Event', 'canvas' ),
		'view_item'          => __( 'View Event', 'canvas' ),
		'all_items'          => __( 'All Events', 'canvas' ),
		'search_items'       => __( 'Search Events', 'canvas' ),
		'parent_item_colon'  => __( 'Parent Events:', 'canvas' ),
		'not_found'          => __( 'No events found.', 'canvas' ),
		'not_found_in_trash' => __( 'No events found in Trash.', 'canvas' )
	);

	$args = array(
		'labels'             => $labels,
                'description'        => __( 'Description.', 'canvas' ),
		    
            'public' => true, 
            'query_var'=> true, 
            'publicly_queryable'=> true,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'menu_position' => 5,
        
            'has_archive' => true,
            'supports' => array(
                    'title',
                    'author',
                    'thumbnail',
                    'revisions',
                    'comments',
                    'editor',
                    'excerpt')
         
	);

	register_post_type( 'event', $args );
            $labels = array(
        'name'               => _x( 'Sliders', 'post type general name', 'canvas' ),
        'singular_name'      => _x( 'Slide', 'post type singular name', 'canvas' ),
        'menu_name'          => _x( 'Sliders', 'admin menu', 'canvas' ),
        'name_admin_bar'     => _x( 'Slide', 'add slide on admin bar', 'canvas' ),
        'add_new'            => _x( 'Add Slide', 'slide', 'canvas' ),
        'add_new_item'       => __( 'Add Slide Slide', 'canvas' ),
        'new_item'           => __( 'Slide Slide', 'canvas' ),
        'edit_item'          => __( 'Edit Slide', 'canvas' ),
        'view_item'          => __( 'View Slide', 'canvas' ),
        'all_items'          => __( 'All Sliders', 'canvas' ),
        'search_items'       => __( 'Search Sliders', 'canvas' ),
        'parent_item_colon'  => __( 'Parent Sliders:', 'canvas' ),
        'not_found'          => __( 'No sliders found.', 'canvas' ),
        'not_found_in_trash' => __( 'No sliders found in Trash.', 'canvas' )
    );

    $args = array(
        'labels'             => $labels,
                'description'        => __( 'Description.', 'canvas' ),
            
            'public' => true, 
            'query_var'=> true, 
            'publicly_queryable'=> true,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'menu_position' => 5,
        
            'has_archive' => true,
            'supports' => array(
                    'title',
                    'author',
                    'thumbnail',
                    'revisions',
                    'comments',
                    'editor',
                    'excerpt')
         
    );

    register_post_type( 'slide', $args );
  }


class themeslug_walker_nav_menu extends Walker_Nav_Menu {
  
// add classes to ul sub-menus
function start_lvl( &$output, $depth ) {
    // depth dependent classes
    $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
    $display_depth = ( $depth + 1); // because it counts the first submenu as 0
    $classes = array(
        'sub-menu',
        ( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
        ( $display_depth >=2 ? 'sub-sub-menu' : '' ),
        'menu-depth-' . $display_depth
        );
    $class_names = implode( ' ', $classes );
  
    // build html
    $output .= "\n" . $indent . '<ul class="dropdown-menu">' . "\n";
}
  
// add main/sub classes to li's and links
 function start_el( &$output, $item, $depth, $args ) {
    global $wp_query;
    $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
  
    // depth dependent classes
    $depth_classes = array(
        ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
        ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
        ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
        'menu-item-depth-' . $depth
    );
    $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );
  
    // passed classes
    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );
  
    // build html
    $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="dropdown">';
    if(($item->url=='#'))
    $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="dropdown">';
  
    // link attributes
    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
    $attributes .= ''.($item->url=='#') ? "data-toggle='dropdown'" : 'class="main-menu-link"';
  
    $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
        $args->before,
        $attributes,
        $args->link_before,
        apply_filters( 'the_title', $item->title, $item->ID ),
        $args->link_after,
        $args->after
    );
  
    // build html
    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
}
}
function canvas_widgets_init() {

	register_sidebar( array(
		'name' => __( 'Footer', 'canvas' ),
		'id' => 'sidebar_1',
		'description' => __( 'The main sidebar appears on the right on each page except the front page template', 'wpb' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

        register_sidebar( array(
        'name' => __( 'social links', 'canvas' ),
        'id' => 'sidebar_2',
        'description' => __( 'The main sidebar appears on the right on each page except the front page template', 'wpb' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );  

	}

add_action( 'widgets_init', 'canvas_widgets_init' );

