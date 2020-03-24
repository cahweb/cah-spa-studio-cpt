<?php
/**
 * Helper class to queue up and load the CPT template automatically.
 * 
 * @author Mike W. Leavitt
 * @since 0.1.0
 */

if( !class_exists( 'CAH_SPAStudioCPTTemplater' ) ) {
    class CAH_SPAStudioCPTTemplater
    {
        // Prevents instantiation
        private function __construct() {}

        /**
         * Sets up the template filter, so the post type loads the
         * correct template.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @return void
         */
        public static function set() {
            add_filter( 'template_include', [ __CLASS__, 'add' ] );
            add_action( 'wp_enqueue_scripts', [ __CLASS__, 'load_style' ], 15, 0 );
        }

        /**
         * Intercept the post template, and replace instances of our
         * CPT with our custom template.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @param string $template  The template that WP is planning to use for the CPT
         * @return void
         */
        public static function add( $template ) {

            if( is_singular( 'studio' ) ) {
                $template = CAH_SPA_STUDIO__PLUGIN_DIR . 'templates/single-spa_studio.php';
            }

            return $template;
        }


        /**
         * When using the Responsive Accordion plugin, it forces the content to use
         * Open Sans as a typeface, and flags it as !important, so it's difficult to
         * corret. Since this stylesheet is loaded afterward, the typeface should
         * be overridden.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         * 
         * @return void
         */
        public static function load_style() {
            global $post;

            if( 'studio' == $post->post_type ) {
                wp_enqueue_style( 'cah-spa-studio-accordion-style', CAH_SPA_STUDIO__PLUGIN_DIR_URL . 'dist/css/accordion-style.css', [], CAH_SPA_STUDIO__VERSION, 'all' );
            }
        }
    }
}
?>