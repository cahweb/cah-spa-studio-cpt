<?php
/**
 * Template Name: SPA Studio CPT Single Page Template
 * Template Post Type: studio
 */

get_header();

the_post();

?>

<div class="container mt-5 mb-4">
    <div class="row">
        <div class="col-12 mx-auto">
        <?php if( has_post_thumbnail( $post->ID ) ) {
            the_post_thumbnail( 'full' );
        } ?>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12 col-md-7">
            <div class="row">
                <div class="col-12 mr-1">
                <?php the_content(); ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-5" style="border-left: 1px solid #555">
            <div class="row">
                <div class="col-12 ml-2">
                <?php
                    $sidebar = get_post_meta( $post->ID, 'spa-studio-sidebar-content', true );

                    $sidebar = apply_filters( 'the_content', $sidebar );

                    echo $sidebar;
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

get_footer();
?>