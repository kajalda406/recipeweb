<?php
get_header();
?>

<?php 
setPostViews(get_the_ID()); 
?>

<div class="recipe-data">
    <h1><?php echo get_the_title(); ?></h1>
    <br/>

    <p><strong>Display Views:</strong>
    <?php 
    echo getPostViews(get_the_ID());
    ?>
    </p>
    <?php if( has_post_thumbnail() ){ ?>
    <div class="recipe-img">
        <img src="<?php echo wp_get_attachment_url(get_post_thumbnail_id()); ?>" alt="<?php echo get_the_title(); ?>" width="800">
    </div>
    <br/>
    <?php } ?>

    <?php
    global $post;
    $author_id = $post->post_author;
    $profile_image = get_user_meta($author_id, 'profile_image', true);
    $author_name = get_the_author_meta('display_name', $author_id);
    $author_bio = get_the_author_meta('description', $author_id);
    $recipe_ingredients = get_post_meta( get_the_ID(), 'recipe_ingredients', true );
    $recipe_instructions = get_post_meta( get_the_ID(), 'recipe_instructions', true );
    $gallery_data = get_post_meta(get_the_ID(), 'gallery_data', true);
    $video_data = get_post_meta(get_the_ID(), 'recipe_video_data', true);    
  
    if ( ! empty( $recipe_ingredients ) ) {
        echo "<div>";
        echo "<h3>Ingredients</h3>";
        echo $recipe_ingredients;
        echo "</div>";
    }
    echo "<br/>";

    if ( ! empty( $recipe_instructions ) ) {
        echo "<div>";
        echo "<h3>Instructions</h3>";
        echo $recipe_instructions;
        echo "</div>";
    }
    ?>

    <div class="crecipe-cat">
        <p><strong>Category:</strong> <?php echo get_the_term_list( $post->ID, 'recipe-type' ); ?></p>
    </div>

    <?php the_content(); ?>


    <?php
    

    if (!empty($gallery_data['image_url'])) {
        echo "<h3>Gallery</h3>";
        echo '<div class="custom-gallery d-flex">';
        foreach ($gallery_data['image_url'] as $image_url) {
            echo '<div class="gallery-item">';
            echo '<img src="' . esc_url($image_url) . '" alt="Gallery Image" style="width:300px; height:auto;">';
            echo '</div>';
        }
        echo '</div>';
    }
    
    if (!empty($video_data['video_url'])) {
        echo "<h3>Videos</h3>";
        echo '<div class="recipe-videos-gallery">';
        foreach ($video_data['video_url'] as $video_url) {
            echo '<div class="recipe-video-item">';
            echo '<video width="320" height="240" controls>';
            echo '<source src="' . esc_url($video_url) . '" type="video/mp4">';
            echo 'Your browser does not support the video tag.';
            echo '</video>';
            echo '</div>';
        }
        echo '</div>';
    }

?>

<div class="recipe-author">
    <h3>About the Author</h3>
    <div class="author-info">
        <?php if ($profile_image) : ?>
            <img src="<?php echo esc_url($profile_image); ?>" alt="<?php echo esc_attr($author_name); ?>" class="author-profile-img" style="width:100px; height:100px; border-radius:50%;">
        <?php else : ?>
            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/default-profile.jpg'); ?>" alt="Default Profile Image" class="author-profile-img" style="width:100px; height:100px; border-radius:50%;">
        <?php endif; ?>
        
        <h4><?php echo esc_html($author_name); ?></h4>
        <p><?php echo esc_html($author_bio); ?></p>
    </div>
</div>


</div>


<?php
get_footer();
?>
