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
    }
}
?>