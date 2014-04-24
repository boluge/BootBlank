<?php
/*------------------------------------*\
    Sidebar Class Bootstrap
\*------------------------------------*/
function bootblank_main_class() {
    if ( is_active_sidebar('widget-area-1') && is_active_sidebar('widget-area-2') ) {
        echo 'col-sm-6';
    } else if ( is_active_sidebar('widget-area-1') || is_active_sidebar('widget-area-2') ) {
        echo 'col-sm-9';
    } else {
        // Classes on full width pages
        echo 'col-sm-12';
    }
}
function bootblank_sidebar_class() {
    echo 'col-sm-3';
}
/*------------------------------------*\
    Colonnnes personnalisées
\*------------------------------------*/
function custom_slider_columns_type($columns) {

    $columns['title'] = 'Titre du slider';
    $columns['date'] = 'Créé le';

    $columns = array_slice($columns, 0, 1) + array( 'thumbnail' =>'Miniature') + array_slice($columns, 1, count($columns)-1, true );

    return $columns;
}
add_filter('manage_edit-slider_columns', 'custom_slider_columns_type');

function custom_slider_columns_content($column) {
    global $post;

    switch ($column) {
        case 'thumbnail':
            the_post_thumbnail( 'vignette' );
            break;
        case 'publication_date':
            $args = array( 'post_type' => 'bootblank', 'numberposts' => -1, 'meta_query' => array( array( 'key' => '_book_info_author', 'value' => $post->ID ) ) );
            $books = get_posts($args);
            echo count($books);
            break;
    }
}
add_action('manage_slider_posts_custom_column', 'custom_slider_columns_content');

/*------------------------------------*\
    External Modules/Files
\*------------------------------------*/

// Login override CSS  --Front--
function bootblank_login_css()  {
    echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('template_directory') . '/css/login.css?v=1.0.0" />';
}
add_action('login_head', 'foundation_login_css'); // Add Override Login Css

function bootblank_dashboard_help() {
    echo '<p style="text-align:center;"><img src="'.get_template_directory_uri().'/img/logo.png"/></p>';
}
//Deletes empty classes and removes the sub menu class --front--
function change_submenu_class($menu) {
    $menu = preg_replace('/ class="sub-menu"/','/ class="dropdown" /',$menu);
    return $menu;
}
add_filter ('wp_nav_menu','change_submenu_class'); // Add class menu

// Préchargement des pages --front--
function bootblank_prefetch() {
    if ( is_single() ) {  ?>
        <!-- Préchargement de la page d\'accueil -->
        <link rel="prefetch" href="<?php echo home_url(); ?>" />
        <link rel="prerender" href="<?php echo home_url(); ?>" />

        <!-- Préchargement de l\'article suivant -->
        <link rel="prefetch" href="<?php echo get_permalink( get_adjacent_post(false,'',true) ); ?>" />
        <link rel="prerender" href="<?php echo get_permalink( get_adjacent_post(false,'',true) ); ?>" />
   <?php
   }
}
add_action('wp_head', 'bootblank_prefetch'); // Optimisation

/** Début Minification des fichiers HTML **/
function bootblank_html_minify_start() {
    ob_start( 'bootblank_html_minyfy_finish' );
}

function bootblank_html_minyfy_finish( $html ) {

    // Suppression des commentaires HTML, sauf les commentaires conditionnels pour IE
    $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);

    // Suppression des espaces vides
    $html = str_replace(array("\r\n", "\r", "\n", "\t"), '', $html);
    while ( stristr($html, '  '))
        $html = str_replace('  ', ' ', $html);

    return $html;
}
add_action('get_header', 'bootblank_html_minyfy_finish');
/** Fin Minification des fichiers HTML **/

// Encapsulage des videos
function standard_wrap_embeds( $html, $url, $args ) {
    if( 'video' == get_post_format( get_the_ID() ) ) {
        $html = '<div class="video-wrapper">' . $html . '</div>';
    } // end if
    return $html;
} // end standard_wrap_embebds
add_filter( 'embed_oembed_html', 'standard_wrap_embeds', 10, 3 ) ; // Video responsive

// Posts Formats
$post_formats = array( 'aside', 'chat', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio' );

function favicons() {
    $icosource = file_get_contents( get_template_directory_uri().'/assets/icons/icons.html' , "r");
    $head_ico = str_replace("%url%",  get_template_directory_uri(), $icosource);
    echo $head_ico;
}
add_action('wp_head', 'favicons');

// Remove jQuery lib
add_action( 'admin_enqueue_scripts', 'remove_jquery' );
function remove_jquery()
{
    wp_deregister_script('jquery');
}

// Obscure login screen error messages
function bootblank_login_obscure(){
    return '<strong>Sorry</strong>: Think you have gone wrong somwhere!';
}

// Posts Formats
$post_formats = array( 'aside', 'chat', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio' );

// Add Dashicons in the theme
function bootblank_dashicons() {
    wp_enqueue_style('dashicons');
}
// add_action('wp_enqueue_scripts', 'bootblank_dashicons'); // Utilisation de Dashicon WP 3.8

// Remove Actions
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'wp_generator'); // Remove Wordpress version

// Add Filter
add_filter( 'login_errors', 'bootblank_login_obscure' ); // Remove login error
// add_filter('jpeg_quality', function($arg){return 80;}); // Compression des images à 80% au lieu de 90%
add_filter( 'jpeg_quality', create_function( '', 'return 80;' ) ); // Idem php < 5.3

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

/*------------------------------------*\
    Add Color to editor Functions
\*------------------------------------*/
function my_mce4_options( $init ) {
$default_colours = '
    "000000", "Black",        "993300", "Burnt orange", "333300", "Dark olive",   "003300", "Dark green",   "003366", "Dark azure",   "000080", "Navy Blue",      "333399", "Indigo",       "333333", "Very dark gray",
    "800000", "Maroon",       "FF6600", "Orange",       "808000", "Olive",        "008000", "Green",        "008080", "Teal",         "0000FF", "Blue",           "666699", "Grayish blue", "808080", "Gray",
    "FF0000", "Red",          "FF9900", "Amber",        "99CC00", "Yellow green", "339966", "Sea green",    "33CCCC", "Turquoise",    "3366FF", "Royal blue",     "800080", "Purple",       "999999", "Medium gray",
    "FF00FF", "Magenta",      "FFCC00", "Gold",         "FFFF00", "Yellow",       "00FF00", "Lime",         "00FFFF", "Aqua",         "00CCFF", "Sky blue",       "993366", "Brown",        "C0C0C0", "Silver",
    "FF99CC", "Pink",         "FFCC99", "Peach",        "FFFF99", "Light yellow", "CCFFCC", "Pale green",   "CCFFFF", "Pale cyan",    "99CCFF", "Light sky blue", "CC99FF", "Plum",         "FFFFFF", "White"
';
$custom_colours = '
    "e14d43", "Color 1 Name", "d83131", "Color 2 Name", "ed1c24", "Color 3 Name", "f99b1c", "Color 4 Name", "50b848", "Color 5 Name", "00a859", "Color 6 Name",   "00aae7", "Color 7 Name", "282828", "Color 8 Name"
';
$init['textcolor_map'] = '['.$custom_colours.','.$default_colours.']';
return $init;
}
add_filter('tiny_mce_before_init', 'my_mce4_options');

/*------------------------------------*\
    External Modules/Files
\*------------------------------------*/

// Load any external files you have here
// require_once ('widget.php');
// require_once ('custompost.php');
// require_once ('woosupport.php');
?>