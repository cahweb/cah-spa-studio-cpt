<?php
/**
 * Registers and manages the SPA Studio CPT.
 * 
 * @author Mike W. Leavitt
 * @since 0.1.0
 */

if( !class_exists( 'CAH_SPAStudioCPTRegistrar' ) ) {
    class CAH_SPAStudioCPTRegistrar
    {
        // Prevents instantiation
        private function __construct() {}

        private static $_text_domain;

        // Public Methods

        /**
         * Registers the Studio CPT and sets related editor actions.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @return void
         */
        public static function register() {
            // CPT labels
            $labels = apply_filters( 'spa_studio_cpt_labels', array(
                'singular'      => 'Studio',
                'plural'        => 'Studios',
                'text_domain'   => 'spa_studio_cpt',
            ));

            // Registering the post type with WP
            register_post_type( 'studio', self::_args( $labels ) );

            // Add our new metabox to the editor
            //add_action( 'add_meta_boxes', [ __CLASS__, 'register_metabox' ], 10, 0 );

            // Point WP to our custom save function, so we can
            // store the new post metadata.
            //add_action( 'save_post_studio', [ __CLASS__, 'save' ] );
        }


        /**
         * Registers the extra metabox we'll need.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @return void
         */
        public static function register_metabox() {
            // The arguments here are:
            //      - the name of the metabox
            //      - the box's title in the editor
            //      - function to call for HTML markup
            //      - the post type to add the box for
            //      - situations to show the box in
            //      - priority for box display
            add_meta_box(
                'studio_sidebar',
                'Right Sidebar',
                [ __CLASS__, 'build' ],
                'studio',
                'normal',
                'low'
            );

            add_meta_box(
                'studio_accordions',
                'Disciplines',
                [ 'CAH_SPAStudioCPTEditor', 'build_accordion' ],
                'studio',
                'normal',
                'low'
            );
        }


        /**
         * Builds the HTML markup for the new metabox.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @return void
         */
        public static function build() {
            global $post;

            $content = get_post_meta( $post->ID, 'spa-studio-sidebar-content', true );

            wp_editor( isset( $content ) ? $content : '', 'sidebar-content', [ 'textarea_rows' => 6] );
        }


        /**
         * Saves our new metadata whenever save_post runs for this 
         * post type.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         * 
         * @return void
         */
        public static function save() {
            global $post;

            if( !is_object( $post ) ) return;

            if( isset( $_POST['sidebar-content'] ) ) {
                update_post_meta( $post->ID, 'spa-studio-sidebar-content', $_POST['sidebar-content'] );
            }
        }


        // Private Methods

        /**
         * Creates, filters, and returns the array of arguments to be 
         * passed to register_post_type() in 
         * CAH_SPAStudioCPTRegistrar::register(), above.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @param array $labels  An array of labels, defined in register(), which contain the singular label, plural label, and text domain for the CPT.
         * 
         * @return array
         */
        private static function _args( array $labels ) : array {
            $singular = $labels['singular'];
            $plural = $labels['plural'];
            $text_domain = $labels['text_domain'];

            return apply_filters( 'spa_studio_cpt_args', array(
                'label' => __( 'Studio', $text_domain ),
                'description' => __( 'Studios', $text_domain ),
                'labels' => self::_labels( $singular, $plural, $text_domain ),
                'supports' => array( 'thumbnail', 'title', 'editor', 'custom-fields', 'page-attributes', 'post-formats' ),
                'taxonomies' => self::_taxonomies(),
                'hierarchical' => false,
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 5,
                'menu_icon' => 'dashicons_tickets_alt',
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => true,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'capability_type' => 'post',
            ));
        }


        /**
         * Creates the full array of labels for our CPT, which is passed as part
         * of the $args array to register_post_type().
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @param string $singular      The singular label for the CPT.
         * @param string $plural        The plural label for the CPT.
         * @param string $text_domain   The text domain for the CPT.
         * 
         * @return array
         */
        private static function _labels( string $singular, string $plural, string $text_domain ) : array {
            
            self::$_text_domain = $text_domain;

            return array(
                'name'                  => self::_wpstr( $plural, 'Post Type General Name' ),
                'singular_name'         => self::_wpstr( $singular, 'Post Type Singular Name'),
                'menu_name'             => self::_wpstr( $plural ),
                'name_admin_bar'        => self::_wpstr( $singular ),
                'archives'              => self::_wpstr( "$plural Archives" ),
                'parent_item_colon'     => self::_wpstr( "Parent $singular:" ),
                'all_items'             => self::_wpstr( "All $plural" ),
                'add_new_item'          => self::_wpstr( "Add New $singular" ),
                'add_new'               => self::_wpstr( "Add New" ),
                'new_item'              => self::_wpstr( "New $singular" ),
                'edit_item'             => self::_wpstr( "Edit $singular" ),
                'update_item'           => self::_wpstr( "Update $singular" ),
                'view_item'             => self::_wpstr( "View $singular" ),
                'delete_item'           => self::_wpstr( "Delete $singular" ),
                'search_items'          => self::_wpstr( "Search $plural" ),
                'not_found'             => self::_wpstr( "$singular Not Found" ),
                'not_found_in_trash'    => self::_wpstr( "$singular Not Found in Trash" ),
                'featured_image'        => self::_wpstr( "$singular Banner" ),
                'set_featured_image'    => self::_wpstr( "Set $singular Banner" ),
                'remove_featured_image' => self::_wpstr( "Remove $singular Banner" ),
                'use_featured_image'    => self::_wpstr( "Use as $singular Banner" ),
                'insert_into_item'      => self::_wpstr( "Insert into $singular" ),
                'uploaded_to_this_item' => self::_wpstr( "Uploaded to this $singular" ),
                'items_list'            => self::_wpstr( "$plural List" ),
                'items_list_navigation' => self::_wpstr( "$plural List Navigation" ),
                'filter_items_list'     => self::_wpstr( "Filter $plural List" ),
            );
        }


        /**
         * Filters the taxonomies, to be passed to _args(), above.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @return array
         */
        private static function _taxonomies() : array {
            $tax = array();
            $tax = apply_filters( 'spa_studio_cpt_taxonomies', $tax );

            foreach( $tax as $t ) {
                if( !taxonomy_exists( $t ) ) {
                    unset( $tax[$t] );
                }
            }

            return $tax;
        }

        
        /**
         * A little helper function to generate a WP localized string. 
         * This seemed cleaner than typing "$text_domain" over and 
         * over again.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @param string $label  The label we're trying to localize.
         * @param string $context  The context, in case we're calling the _x() function.
         * 
         * @return string
         */
        private static function _wpstr( string $label, string $context = null ) : string {

            if( $context ) {
                return _x( $label, $context, self::$_text_domain );
            }
            return __( $label, self::$_text_domain );
        }
    }
}
?>