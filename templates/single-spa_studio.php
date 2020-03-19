<?php
/**
 * Template Name: SPA Studio CPT Single Page Template
 * Template Post Type: studio
 */

get_header();

the_post();

?>

<div class="container-fluid px-0 m-0">
    <div class="header-media header-media-content-block header-media-default media-background-container mb-0 d-flex flex-column">
        <img src="<?= get_the_post_thumbnail_url() ?>" class="media-background object-fit-cover">
        <div class="header-content">
            <div class="container d-flex align-items-center align-items-sm-end">
                <div class="row no-gutters w-100">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="header-title-wrapper">
                            <h1 class="header-title"><?= get_the_title() ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5 mb-4">
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