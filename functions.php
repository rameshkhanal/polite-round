<?php
/**
 * Child theme version, used for asset cache busting.
 */
if ( ! defined( 'POLITE_ROUND_VERSION' ) ) {
	$polite_round_theme = wp_get_theme( get_stylesheet() );
	define( 'POLITE_ROUND_VERSION', $polite_round_theme->get( 'Version' ) );
}

/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */
/**
 * Loads the child theme textdomain.
 */
function polite_round_load_language() {
    load_child_theme_textdomain( 'polite-round', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'polite_round_load_language' );

/**
 * Child theme setup.
 */
function polite_round_setup() {
	add_theme_support(
		'custom-background',
		apply_filters(
			'polite_round_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);
}
add_action( 'after_setup_theme', 'polite_round_setup' );

/**
 * Enqueue Style for child theme.
 */
add_action( 'wp_enqueue_scripts', 'polite_round_enqueue_scripts');
function polite_round_enqueue_scripts() {

	$polite_round_parent_version = wp_get_theme( get_template() )->get( 'Version' );

	// Locally hosted webfonts.
	wp_enqueue_style(
		'polite-round-fonts',
		get_stylesheet_directory_uri() . '/assets/fonts/fonts.css',
		array(),
		POLITE_ROUND_VERSION
	);

	// Parent stylesheet, registered with a version.
	wp_register_style(
		'polite-style',
		get_template_directory_uri() . '/style.css',
		array(),
		$polite_round_parent_version
	);

	wp_enqueue_style(
		'polite-round-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'polite-style' ),
		POLITE_ROUND_VERSION
	);
}

/**
 * Polite Theme Customizer and overide from child theme
 *
 * @package Polite
 */

if ( !function_exists('polite_default_theme_options_values') ) :

    function polite_default_theme_options_values() {

        $default_theme_options = array(

            /*Logo Options*/
            'polite_logo_width_option' => '300',

            /*Top Header*/
            'polite_enable_top_header'=> 0, 
            'polite_enable_top_header_social'=> 0,
            'polite_enable_top_header_menu'=> 0,

           /*Header Options*/
            'polite_enable_offcanvas'  => 0,
            'polite_enable_search'  => 0,

            /*Menu Options*/
            'polite_mobile_menu_text'  => esc_html__('Menu','polite-round'),
            'polite_mobile_menu_option'=> 'menu-text',

            /*Header Image*/
            'polite_enable_header_image_overlay'=> 0,
            'polite_slider_overlay_color'=> '#000000',
            'polite_slider_overlay_transparent'=> '0.1',
            'polite_header_image_height'=> '100',

            /*Colors Options*/
            'polite_primary_color' => '#d42929',

            /*Slider Options*/
            'polite_enable_slider'      => 0,
            'polite-select-category'    => 0,
    
            /*Boxes Section */
            'polite_enable_promo'       => 1,
            'polite-promo-select-category'=> 0,
            
            /*Blog Page*/
            'polite-sidebar-blog-page' => 'right-sidebar',
            'polite-column-blog-page'  => 'one-column',
            'polite-blog-image-layout' => 'left-image',
            'polite-content-show-from' => 'excerpt',
            'polite-excerpt-length'    => 25,
            'polite-pagination-options'=> 'numeric',
            'polite-read-more-text'    => '',
            'polite-show-hide-share'   => 1,
            'polite-show-hide-category'=> 1,
            'polite-show-hide-date'=> 1,
            'polite-show-hide-author'=> 1,
            'polite-show-hide-read-time'=>1,
            'polite-font-family-url'=>'Muli',
            'polite-blog-exclude-category'=> '',
            'polite-blog-image-round'=>'round',

            /*Single Page */
            'polite-single-page-featured-image' => 1,
            'polite-single-page-related-posts'  => 0,
            'polite-single-page-related-posts-title' => esc_html__('Related Posts','polite-round'),
            'polite-sidebar-single-page'=> 'single-right-sidebar',
            'polite-single-social-share' => 1,


            /*Sticky Sidebar*/
            'polite-enable-sticky-sidebar' => 1,

            /*Footer Section*/
            'polite-footer-copyright'  => esc_html__('Copyright All Rights Reserved 2022','polite-round'),

            /*Breadcrumb Options*/
            'polite-extra-breadcrumb' => 1,
            'polite-breadcrumb-selection-option'=> 'theme',

        );
return apply_filters( 'polite_default_theme_options_values', $default_theme_options );
}
endif;

function polite_round_sanitize_select( $input, $setting ) {
    $choices = $setting->manager->get_control( $setting->id )->choices;
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function polite_round_customize_register( $wp_customize ) {

   $default = polite_default_theme_options_values();

       /*Blog Page Show content from*/
    $wp_customize->add_setting('polite_options[polite-content-show-from]', array(
        'capability' => 'edit_theme_options',
        'transport' => 'refresh',
        'default' => $default['polite-content-show-from'],
        'sanitize_callback' => 'polite_sanitize_select'
    ));

    $wp_customize->add_control('polite_options[polite-content-show-from]', array(
        'choices' => array(
            'excerpt' => __('Show from Excerpt', 'polite-round'),
            'content' => __('Show from Content', 'polite-round'),
            'hide'    => __('Hide Content', 'polite-round'),
        ),
        'label' => __('Select Content Display From', 'polite-round'),
        'description' => __('You can enable excerpt from Screen Options inside post section of dashboard', 'polite-round'),
        'section' => 'polite_blog_page_section',
        'settings' => 'polite_options[polite-content-show-from]',
        'type' => 'select',
        'priority' => 12,
    ));

       /*Make Image round in blog page*/
       $wp_customize->add_setting('polite_options[polite-blog-image-round]', array(
        'capability' => 'edit_theme_options',
        'transport' => 'refresh',
        'default' => $default['polite-blog-image-round'],
        'sanitize_callback' => 'polite_sanitize_select'
    ));

    $wp_customize->add_control('polite_options[polite-blog-image-round]', array(
        'choices' => array(
            'default' => __('Default Image Design', 'polite-round'),
            'round' => __('Round Image Design', 'polite-round'),
        ),
        'label' => __('Make image round', 'polite-round'),
        'description' => __('You can make the design of the image round', 'polite-round'),
        'section' => 'polite_blog_page_section',
        'settings' => 'polite_options[polite-blog-image-round]',
        'type' => 'select',
        'priority' => 12,
    ));


    /*Read time Show hide*/
    $wp_customize->add_setting('polite_options[polite-show-hide-read-time]', array(
        'capability' => 'edit_theme_options',
        'transport' => 'refresh',
        'default' => $default['polite-show-hide-read-time'],
        'sanitize_callback' => 'polite_sanitize_checkbox'
    ));

    $wp_customize->add_control('polite_options[polite-show-hide-read-time]', array(
        'label' => __('Show Read Time', 'polite-round'),
        'description' => __('Option to hide the read time on the blog page.', 'polite-round'),
        'section' => 'polite_blog_page_section',
        'settings' => 'polite_options[polite-show-hide-read-time]',
        'type' => 'checkbox',
        'priority' => 15,
    ));

    /*Font Family URL*/
        $wp_customize->add_setting( 'polite_options[polite-font-family-url]', array(
            'capability'        => 'edit_theme_options',
            'transport' => 'refresh',
            'default'           => $default['polite-font-family-url'],
            'sanitize_callback' => 'polite_round_sanitize_select'
        ) );
        $choices = polite_round_google_fonts();
        $wp_customize->add_control( 'polite_options[polite-font-family-url]', array(
            'label'     => __( 'URL of Font Family', 'polite-round' ),
            'description' => sprintf('%1$s <a href="%2$s" target="_blank">%3$s</a> %4$s',
                        __( 'Select the font here. More options are available', 'polite-round' ),
                        esc_url('https://www.templatesell.com/item/polite-plus-masonry-wordpress-theme/'),
                        __('in the premium version Polite Plus' , 'polite-round'),
                        __('check now.' ,'polite-round')
            ),
            'choices'   => $choices,
            'section'   => 'polite_blog_page_section',
            'settings'  => 'polite_options[polite-font-family-url]',
            'type'      => 'select',
            'priority'  => 15,
        ) );


}
add_action( 'customize_register', 'polite_round_customize_register', 999 );


/* Word read count Pagination */
if (!function_exists('polite_round_read_time')) :
    /**
     * @param $content
     *
     * @return string
     */
    function polite_round_read_time()
    {
        $read_words = 200;
        $content    = strip_shortcodes( get_post_field( 'post_content' ) );
        $content    = wp_strip_all_tags( $content, true );
        $strip_tags = html_entity_decode( $content, ENT_QUOTES, get_bloginfo( 'charset' ) );
        $count      = str_word_count( $strip_tags );
        $word_per_min = (absint($count) / $read_words);
        $word_per_min = ceil($word_per_min);

        if (absint($word_per_min) > 0) {
            /* translators: %s: number of minutes a post takes to read. */
            $word_count_strings = sprintf( _n( '%s Min Reading', '%s Min Reading', $word_per_min, 'polite-round' ), number_format_i18n( $word_per_min ) );
            if ('post' == get_post_type()):
                echo '<span class="min-read">';
                echo esc_html($word_count_strings);
                echo '</span>';
            endif;

        }
    }
endif;



/**
 * Google Fonts
 *
 * @param null
 * @return array
 *
 * @since Prefer 1.0.0
 *
 */
if (!function_exists('polite_round_google_fonts')) :
    function polite_round_google_fonts()
    {
        $polite_round_google_fonts = array(
            'Muli' => 'Muli',
            'Lato' => 'Lato',
            'Open+Sans' => 'Open Sans',
            'Montserrat' => 'Montserrat',
            'Alegreya:400,400italic,700,900' => 'Alegreya',
            'Alex+Brush' => 'Alex Brush'
        );
        return apply_filters('polite_round_google_fonts', $polite_round_google_fonts);
    }
endif;


/**
 * Enqueue the list of fonts.
 */
function polite_round_customizer_fonts()
{
    wp_enqueue_style(
		'polite_round_customizer_fonts',
		get_stylesheet_directory_uri() . '/assets/fonts/fonts.css',
		array(),
		POLITE_ROUND_VERSION
	);
}

add_action('customize_controls_print_styles', 'polite_round_customizer_fonts');
add_action('customize_preview_init', 'polite_round_customizer_fonts');

add_action(
    'customize_controls_print_styles',
    function (){
        ?>
        <style>
            <?php
            foreach ( array_keys( polite_round_google_fonts() ) as $polite_round_font ) {
                $polite_round_family = str_replace( '+', ' ', strtok( $polite_round_font, ':' ) );
                printf(
                    '.customize-control select option[value*="%1$s"] { font-family: "%2$s"; font-size: 22px; }',
                    esc_attr( $polite_round_font ),
                    esc_attr( $polite_round_family )
                );
            }
            ?>
        </style>
        <?php
    }
);



if (!function_exists('polite_round_dynamic_css')) :

    function polite_round_dynamic_css()
    {
        global $polite_theme_options;
        $polite_round_google_fonts = polite_round_google_fonts(); 
        $polite_font_family = isset( $polite_theme_options['polite-font-family-url'] )
            ? $polite_theme_options['polite-font-family-url']
            : '';

        /* Paragraph Font Options */
        if ( ! isset( $polite_round_google_fonts[ $polite_font_family ] ) ) {
            $polite_round_defaults = polite_default_theme_options_values();
            $polite_font_family = $polite_round_defaults['polite-font-family-url'];
        }
        $polite_font_body_family = isset( $polite_round_google_fonts[ $polite_font_family ] )
            ? $polite_round_google_fonts[ $polite_font_family ]
            : '';

        $custom_css = '';
        //Paragraph Font Options 
        if (!empty($polite_font_body_family)) {
            $custom_css .= sprintf(
                'body, .entry-content p { font-family: "%s"; }',
                $polite_font_body_family
            );
        }
        wp_add_inline_style('polite-style', $custom_css);
    }
endif;
add_action('wp_enqueue_scripts', 'polite_round_dynamic_css', 99);