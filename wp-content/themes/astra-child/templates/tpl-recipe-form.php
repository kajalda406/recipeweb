<?php
/*
Template Name: Recipe Submit Form
*/
get_header();

?>
<section class="recipe-form-section">
	<div class="container">

		<?php 
		$recipeNameErr=$recipeDescErr=$recipeCatErr=$recipeIngredErr=$recipeInstruErr=$RecipephotoErr=$recipeVideoErr="";

		if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST)) { 
			$successMessage = "";

		    $recipe_title=$recipe_description=$recipe_type=$recipe_ingredients=$recipe_instructions=$recipePhoto=$recipeVideo="";
		    

		    if( !wp_verify_nonce($_POST['_wpnonce'], 'wp-recipe-nonce') ) {
		        $providenonceErr = 'Sorry, saving was not possible because your form appears to be invalid.';
		    }

		    if(empty($_POST['recipe_title'])){
		        $recipeNameErr = 'Recipe Name is required.';
		    }else if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST['recipe_title'] )) {
		        $recipeNameErr = 'Only letters and spaces allowed.';
		    }

		    if(empty($_POST['recipe_description'])){
		        $recipeDescErr = 'Description is required.';
		    }

		    if( empty($_POST['recipe-type']) ){
		    	$recipeCatErr = 'Category is required.';
		    }

		    if( empty($_POST['recipe_ingredients']) ){
		    	$recipeIngredErr = 'Ingredients is required.';
		    }
  
		    $allowedImageTypes = array('image/jpeg', 'image/png');


		    if(empty($_FILES["file-upload-foto"]["name"])) {
		        $RecipephotoErr = 'Please upload photos.';
		    }elseif ($_FILES["file-upload-foto"]["size"] > 5 * 1024 * 1024) {
		      	$RecipephotoErr = 'The uploaded file is too large.';
		    }elseif( !in_array($_FILES["file-upload-foto"]["type"], $allowedImageTypes)){
		        $RecipephotoErr = 'Only JPG and PNG files are allowed.';
		    }

		    if(empty($recipeNameErr) && empty($recipeDescErr) && empty($recipeCatErr) ) {
		        $userID ="";
		        $recipe_title =  $_POST['recipe_title'];
		        $recipe_description = $_POST['recipe_description'];
                $recipe_type = intval($_POST['recipe-type']);
		        $recipe_ingredients = $_POST['recipe_ingredients'];
		        $recipe_instructions = $_POST['recipe_instructions'];


		        $post = array(
		            'post_author'   => $userID,
		            'post_title'    => $recipe_title,
		            'post_content'  => $recipe_description,
		            'post_status'   => 'draft',  
		            'post_type'     => 'recipe', 
		        );

		        
		        $recipeID = wp_insert_post($post);

		        if(!is_wp_error($recipeID)){

                    if ($recipe_type) {
                        wp_set_object_terms($recipeID, $recipe_type, 'recipe-type'); 
                    }

		        	update_post_meta($recipeID, 'recipe_ingredients', $recipe_ingredients);
		        	update_post_meta($recipeID, 'recipe_instructions', $recipe_instructions);
		        }

				if (isset($_POST['uploadedFiles'])) {
				    $gallery_data = array(); 
				    $i = 0;
				    foreach ($_POST['uploadedFiles'] as $uploadFileItem) {
				        
				        $gallery_data['image_url'][] = $uploadFileItem; 

				        if ($i == 0) {
				            $filename = basename($uploadFileItem);
				            $wp_filetype = wp_check_filetype($filename, null);

				            $attachment = array(
				                'post_mime_type' => $wp_filetype['type'],
				                'post_title' => sanitize_file_name($filename),
				                'post_content' => '',
				                'post_status' => 'inherit'
				            );

				            $attach_img_id = wp_insert_attachment($attachment, $uploadFileItem, $recipeID);

				            require_once(ABSPATH . 'wp-admin/includes/image.php');

				            $attach_data = wp_generate_attachment_metadata($attach_img_id, $uploadFileItem);

				            wp_update_attachment_metadata($attach_img_id, $attach_data);

				            set_post_thumbnail($recipeID, $attach_img_id);
				        }

				        $i++;
				    }

				    if ($gallery_data) {
				        update_post_meta($recipeID, 'gallery_data', $gallery_data);
				    }
				}

                
                if (isset($_POST['uploadedVideos']) && !empty($_POST['uploadedVideos'])) {
                    $video_data = array_map('esc_url_raw', $_POST['uploadedVideos']);
                    update_post_meta($recipeID, 'recipe_video_data', ['video_url' => $video_data]);
                }


		        set_post_thumbnail($recipeID, $attach_img_id );
		        $successMessage =  'Your recipe has been successfully saved!';
		        $_POST =[];
		    }

		    if(!empty($successMessage)) {
		            echo '<div class="alert alert-success" role="alert" style="color: green;">'. $successMessage.'</div>';
		    }  
		    
		}
		?>

		<div class="inner search-form">
			<form id="recipe_submit_form" name="recipe_submit_form" method="post" action="<?php the_permalink(); ?>" enctype="multipart/form-data">
				<div class="control-group">
					<div class="heading"><h2>Submit Recipe</h2></div>
					<div class="floating-label-group">
						<label for="recipe_title">Recipe Name</label>
						<input type="text" name="recipe_title" class="input-field" value="<?php echo !empty($_POST['recipe_title'])?$_POST['recipe_title']:''; ?>">
						
						<span class="form-error" style="color: red;"><?php echo $recipeNameErr; ?></span>
					</div>
					<div class="floating-label-group">
						<label class="floating-label">Recipe Description</label>
						<textarea name="recipe_description" rows="8"><?php echo !empty($_POST['recipe_description']) ? esc_textarea($_POST['recipe_description']) : ''; ?></textarea>
						<span class="form-error" style="color: red;"><?php echo $recipeDescErr; ?></span>
					</div>

					<div class="floating-label-group">
						<label class="floating-label">Recipe Ingredients</label>
						<textarea name="recipe_ingredients" rows="8"><?php echo !empty($_POST['recipe_ingredients']) ? esc_textarea($_POST['recipe_ingredients']) : ''; ?></textarea>
						<span class="form-error" style="color: red;"><?php echo $recipeDescErr; ?></span>
					</div>

					<div class="floating-label-group">
						<label class="floating-label">Recipe Instructions</label>
						<textarea name="recipe_instructions" rows="8"><?php echo !empty($_POST['recipe_instructions']) ? esc_textarea($_POST['recipe_instructions']) : ''; ?></textarea>
					</div>
				</div>

				<div class="control-group">
					<label for="recipe-type">Choose Category</label>
				    <select id="recipe-type" name="recipe-type">
                    <?php 
                    $recipetype = get_terms(array('taxonomy' => 'recipe-type', 'hide_empty' => false));
                    foreach ($recipetype as $recipe) {
                        echo '<option value="'.$recipe->term_id.'">'.$recipe->name.'</option>';
                    }
                    ?>
                </select>
				</div>

				<div class="floating-label-group file-uload">
					<label class="heading" style="margin-bottom: 10px; margin-top:35px;">Upload Photos </label>

					<div class="loader-image" style="display: none;">Processing</div>
					<div class="uploaded-images">
						<?php if(isset($_POST['uploadedFiles'])){ ?>
							<?php foreach($_POST['uploadedFiles'] as $upldimg) { ?>
								<div class="img">
									<img src="<?php echo $upldimg; ?>" style="width:130px; height:130px;">
									<input type="hidden" name="uploadedFiles[]" value="<?php echo $upldimg; ?>">
									<span class="close">close</span>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
					<div class="file-upload">
						<input type="file" name="file-upload-foto" class="eventImages wpcf7-file" accept="image/png, image/jpeg">
						<div class="add-files"></div>
					</div>
					<span class="dec">File Formats: <b>PNG, JPG,</b> File Size: <b>max. 5 MB</b></span>
					<span class="form-error" style="color: red;"><?php echo $RecipephotoErr; ?></span>
				</div>

                <div class="floating-label-group file-uload">
                    <label class="heading" style="margin-bottom: 10px; margin-top:35px;">Videos</label>

                    <div class="loader-image" style="display: none;">Processing</div>
                    <div class="uploaded-videos">
                        <?php if(isset($_POST['uploadedVideos'])){ ?>
                            <?php foreach($_POST['uploadedVideos'] as $uploadVideo) { ?>
                                <div class="video">
                                    <video width="130" height="130" controls>
                                        <source src="<?php echo $uploadVideo; ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <input type="hidden" name="uploadedVideos[]" value="<?php echo $uploadVideo; ?>">
                                    <span class="close">close</span>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="file-upload">
                        <input type="file" name="file-upload-video[]" class="eventVideos wpcf7-file" accept="video/mp4, video/avi, video/mkv" multiple>
                        <div class="add-files"></div>
                    </div>
                    
                </div>


				<?php wp_nonce_field( 'wp-recipe-nonce' ); ?>

				<div class="form-action">
					<input class="wpcf7-submit" type="submit" value="Submit Recipe">
				</div>
			</form>
		</div>
	</div>
</section>

<script type="text/javascript"> 
	jQuery(document).ready(function(){
		jQuery('body').on('change', '.eventImages', function() {
			$this = jQuery(this);
			file_data = jQuery(this).prop('files')[0];
			form_data = new FormData();
			form_data.append('file', file_data);
			form_data.append('action', 'event_file_upload');

			jQuery('.loader-image').show();

			jQuery.ajax({
				url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
				type: 'POST',
				contentType: false,
				processData: false,
				dataType: "JSON",
				data: form_data,
				success: function (response) {
					$this.val('');
					if(response.successCode == 1){
						var uploadImageString = '<div class="img"><img src="'+response.fileUrl+'" style="width:130px; height:130px;"><input type="hidden" name="uploadedFiles[]" value="'+response.fileUrl+'"><span class="close">close</span></div>';
						jQuery('.uploaded-images').append(uploadImageString);
					}else{
						alert(response.msg);
					}

					jQuery('.loader-image').hide();
				}
			});
		});

        jQuery(document).on('click', '.close', function(){
            jQuery(this).parents('.img').remove();
        });

        jQuery('body').on('change', '.eventVideos', function() {
        $this = jQuery(this);
        file_data = jQuery(this).prop('files')[0];
        form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('action', 'event_video_upload');

        jQuery('.loader-image').show();

        jQuery.ajax({
            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
            type: 'POST',
            contentType: false,
            processData: false,
            dataType: "JSON",
            data: form_data,
            success: function (response) {
                $this.val(''); // Clear the input field
                if(response.successCode == 1){
                    var uploadVideoString = '<div class="video"><video width="130" height="130" controls><source src="'+response.fileUrl+'" type="video/mp4">Your browser does not support the video tag.</video><input type="hidden" name="uploadedVideos[]" value="'+response.fileUrl+'"><span class="close">close</span></div>';
                    jQuery('.uploaded-videos').append(uploadVideoString);
                } else {
                    alert(response.msg);
                }

                jQuery('.loader-image').hide();
            }
        });
        });

        jQuery(document).on('click', '.close', function () {
            jQuery(this).closest('.video').remove();
        });
		
	});
</script>

<?php

get_footer();