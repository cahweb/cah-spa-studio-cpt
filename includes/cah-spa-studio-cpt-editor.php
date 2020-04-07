<?php
/**
 * A static helper class for building and managing the editor for the CPT.
 */

if( !class_exists( 'CAH_SPAStudioCPTEditor' ) ) {
    class CAH_SPAStudioCPTEditor
    {
        private function __construct() {}

        /**
         * Sets up our initial actions. Called from the main plugin file.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         * 
         * @return void
         */
        public static function setup() {
            add_action( 'add_meta_boxes', [ __CLASS__, 'register_metaboxes' ], 10, 0 );

            add_action( 'save_post_studio', [ __CLASS__, 'save' ], 10, 0 );

            // Scripts are empty at the moment, so we don't technically need this yet.
            add_action( 'admin_enqueue_scripts', [ __CLASS__, 'maybe_load_scripts' ] );
        }


        /**
         * Register any custom metaboxes.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         */
        public static function register_metaboxes() {
            // The arguments here are:
            //      - the name of the metabox
            //      - the box's title in the editor
            //      - function to call for HTML markup
            //      - the post type to add the box for
            //      - situations to show the box in
            //      - priority for box display
            add_meta_box(
                'right_menu_links',
                'Sidebar Menu Links',
                [ __CLASS__, 'menu_links' ],
                'studio',
                'normal',
                'low'
            );
            
            add_meta_box(
                'studio_sidebar',
                'Right Sidebar',
                [ __CLASS__, 'sidebar_box' ],
                'studio',
                'normal',
                'low'
            );
        }


        /**
         * Save the extra metadata for our studio post.
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

            $sections = [];

            if( isset( $_POST['section_names'] ) && !empty( $_POST['section_names'] ) ) {
                foreach( $_POST['section_names'] as $i => $section ) {
                    $new_section = [ 'name' => $section, 'links' => [], ];

                    if( isset( $_POST["section_${i}_link_hrefs"] ) ) {
                        foreach( $_POST["section_${i}_link_hrefs"] as $j => $link ) {
                            $new_section['links'][] = [
                                'name' => isset( $_POST["section_${i}_link_names"][$j] ) ? $_POST["section_${i}_link_names"][$j] : '',
                                'href' => $link,
                            ];
                        }
                    }

                    $sections[] = $new_section;
                }
            }

            update_post_meta( $post->ID, 'spa-studio-link-sections', $sections );
        }


        /**
         * Create the metabox for our sidebar content.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         * 
         * @return void
         */
        public static function sidebar_box() {
            global $post;

            $content = get_post_meta( $post->ID, 'spa-studio-sidebar-content', true );

            wp_editor( isset( $content ) ? $content : '', 'sidebar-content', [ 'textarea_rows' => 6] );
        }


        public static function menu_links() {

            global $post;

            $sections = maybe_unserialize( get_post_meta( $post->ID, 'spa-studio-link-sections', true ) );

            if( !is_array( $sections ) || empty( $sections ) ) {
                $sections = [
                    [
                        'name' => '',
                        'links' => [
                            [
                                'name' => '',
                                'href' => '',
                            ],
                        ],
                    ],
                ];
            }

            ?>

            <div class="inner-meta" id="sidebar-link-box">
            <?php foreach( $sections as $id => $section ) : ?>
                <table class="link-section" id="section-<?= $id ?>">
                    <tr>
                        <td><label>Section Name: </label></td>
                        <td colspan="3"><input type="text" id="name-section-<?= $id ?>" name="section_names[]" size="50" value="<?= isset( $section['name'] ) ? $section['name'] : '' ?>"></td>
                        <td></td>
                        <td>
                            <button type="button" id="delete-section-<?= $id ?>" class="button button-delete button-delete-section" aria-label="Delete Section">
                                <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                            </button>
                        </td>
                    </tr>
                <?php if( isset( $section['links'] ) && !empty( $section['links'] ) ) : 
                        foreach( $section['links'] as $i => $link ) :
                ?>
                    <tr id="section-<?= $id ?>-link-<?= $i ?>" class="link-entry">
                        <td><label>Link Text:</label></td>
                        <td><input type="text" id="label-section-<?= $id ?>-link-<?= $i ?>" name="section_<?= $id ?>_link_names[]" value="<?= isset( $link['name'] ) ? $link['name'] : '' ?>"></td>
                        <td><label>Link Address:</label></td>
                        <td colspan="2"><input type="text" id="href-section-<?= $id ?>-link-<?= $i ?>" name="section_<?= $id ?>_link_hrefs[]" value="<?= isset( $link['href'] ) ? $link['href'] : '' ?>" size="100"></td>
                        <td>
                            <button type="button" id="delete-section-<?= $id ?>-link-<?= $i ?>" class="button button-delete button-delete-link" aria-label="Delete Link">
                                <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                            </button>
                        </td>

                    </tr>
                <?php   endforeach;
                    endif;
                ?>
                    <tr>
                        <td>
                            <button type="button" class="button button-primary button-add-link" id="button-add-link-section-<?= $id ?>">
                                <span class="dashicons dashicons-plus"></span>
                            </button>
                        </td>
                    </tr>
                </table>
            <?php endforeach; ?>
            <button type="button" class="button button-primary" id="button-add-section">
                <span class="dashicons dashicons-plus"></span>
            </button>
            </div>

            <?php
        }


        /**
         * Load our admin scripts and styles if we're creating a new studio post or
         * editing an existing one.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         * 
         * @return void
         */
        public static function maybe_load_scripts() {
            global $pagenow, $post;
            if( ( 'post-new.php' == $pagenow && isset( $_GET['post_type'] ) && 'studio' == $_GET['post_type'] )
                || ( 'post.php' == $pagenow && 'studio' == $post->post_type && isset( $_GET['action'] ) && 'edit' == $_GET['action'] ) 
            ) {
                wp_enqueue_script( 
                    'cah-spa-studio-admin', 
                    CAH_SPA_STUDIO__PLUGIN_DIR_URL . 'src/js/admin.js', 
                    [ 'jquery' ], 
                    CAH_SPA_STUDIO__VERSION, 
                    true
                );

                wp_enqueue_style( 
                    'cah-spa-studio-admin-style', 
                    CAH_SPA_STUDIO__PLUGIN_DIR_URL . 'dist/css/admin-style.css', 
                    [], CAH_SPA_STUDIO__VERSION, 
                    'all' 
                );

            }
        }
    }
}
?>