<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );
    wp_enqueue_style( 'main-css', get_stylesheet_directory_uri() . '/assets/css/main.css', false, '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );


/*
*User Register Form
*/
function recipe_user_registration_form($atts) {
    $atts = shortcode_atts( array(
        'role' => 'subscriber',         
    ), $atts, 'register' );
    
    $role_number = $atts["role"];

    if ($role_number == "shop_manager" ) { 
        $reg_form_role = (int) filter_var(AUTH_KEY, FILTER_SANITIZE_NUMBER_INT); 
    }  elseif ($role_number == "customer" ) { 
        $reg_form_role = (int) filter_var(SECURE_AUTH_KEY, FILTER_SANITIZE_NUMBER_INT); 
    } elseif ($role_number == "contributor" ) { 
        $reg_form_role = (int) filter_var(NONCE_KEY, FILTER_SANITIZE_NUMBER_INT); 
    } elseif ($role_number == "author" ) { 
        $reg_form_role = (int) filter_var(AUTH_SALT, FILTER_SANITIZE_NUMBER_INT); 
    } elseif ($role_number == "editor" ) { $
        $reg_form_role = (int) filter_var(SECURE_AUTH_SALT, FILTER_SANITIZE_NUMBER_INT); 
    } elseif ($role_number == "administrator" ) { 
        $reg_form_role = (int) filter_var(LOGGED_IN_SALT, FILTER_SANITIZE_NUMBER_INT); 
    } else { 
        $reg_form_role = 1001; 
    } 
    
    if(!is_user_logged_in()) { 
        $registration_enabled = get_option('users_can_register');
        if($registration_enabled) {
            $output = recipe_user_registration_fields($reg_form_role);
        } else {
            $output = __('<p>User registration is not enabled</p>');
        }
        return $output;
    }  
    $output = __('<p>You already have an account on this site, so there is no need to register again.</p>');
    return $output;
}
add_shortcode('user_register_form', 'recipe_user_registration_form');

function recipe_user_registration_fields($reg_form_role) {  ?> 
<?php
    ob_start();
    ?>  
    <form id="red_registration_form" class="red_form" action="" method="POST">
        <?php recipe_register_messages();   ?>
        <p>
            <label for="red_user_login"><?php _e('Username'); ?></label>
            <input name="red_user_login" id="red_user_login" class="red_input" placeholder="Username" type="text"/>
        </p>
        <p>
            <label for="red_user_email"><?php _e('Email'); ?></label>
            <input name="red_user_email" id="red_user_email" class="red_input" placeholder="Email" type="email"/>
        </p>
        <p>
            <label for="red_user_first"><?php _e('First Name'); ?></label>
            <input name="red_user_first" id="red_user_first" type="text" placeholder="First Name" class="red_input" />
        </p>
        <p>
            <label for="red_user_last"><?php _e('Last Name'); ?></label>
            <input name="red_user_last" id="red_user_last" type="text" placeholder="Last Name" class="red_input"/>
        </p>
        <p>
            <label for="password"><?php _e('Password'); ?></label>
            <input name="red_user_pass" id="password" class="red_input" placeholder="Password" type="password"/>
        </p>
        <p>
            <label for="password_again"><?php _e('Password'); ?></label>
            <input name="red_user_pass_confirm" id="password_again" placeholder="Password Again" class="red_input" type="password"/>
        </p>
        <p>
            <input type="hidden" name="red_csrf" value="<?php echo wp_create_nonce('red-csrf'); ?>"/>
            <input type="hidden" name="red_role" value="<?php echo $reg_form_role; ?>"/>
            <input type="submit" value="<?php _e('Register Now'); ?>"/>
        </p>
    </form>  
    <?php
    return ob_get_clean();
}

function red_add_new_user() {
    if (isset( $_POST["red_user_login"] ) && wp_verify_nonce($_POST['red_csrf'], 'red-csrf')) {
        $user_login       = sanitize_user($_POST["red_user_login"]);
        $user_email       = sanitize_email($_POST["red_user_email"]);
        $user_first       = sanitize_text_field( $_POST["red_user_first"] );
        $user_last        = sanitize_text_field( $_POST["red_user_last"] );
        $user_pass        = $_POST["red_user_pass"];
        $pass_confirm     = $_POST["red_user_pass_confirm"];
        $red_role         = sanitize_text_field( $_POST["red_role"] );    
      
        if ($red_role == (int) filter_var(AUTH_KEY, FILTER_SANITIZE_NUMBER_INT) ) { 
            $role = "shop_manager"; 
        } elseif ($red_role == (int) filter_var(SECURE_AUTH_KEY, FILTER_SANITIZE_NUMBER_INT) ) {
            $role = "customer"; 
        } elseif ($red_role == (int) filter_var(NONCE_KEY, FILTER_SANITIZE_NUMBER_INT) ) { 
            $role = "contributor"; 
        } elseif ($red_role == (int) filter_var(AUTH_SALT, FILTER_SANITIZE_NUMBER_INT)  ) { 
            $role = "author"; 
        } elseif ($red_role ==  (int) filter_var(SECURE_AUTH_SALT, FILTER_SANITIZE_NUMBER_INT) ) { $role = "editor"; 
        } elseif ($red_role == (int) filter_var(LOGGED_IN_SALT, FILTER_SANITIZE_NUMBER_INT) ) { $role = "administrator"; } else { $role = "subscriber"; 
        }
      
        if(username_exists($user_login)) {
            recipe_errors()->add('username_unavailable', __('Username already taken'));
        }
        if(!validate_username($user_login)) {
            recipe_errors()->add('username_invalid', __('Invalid username'));
        }
        if($user_login == '') {
            recipe_errors()->add('username_empty', __('Please enter a username'));
        }
        if(!is_email($user_email)) {
            recipe_errors()->add('email_invalid', __('Invalid email'));
        }
        if(email_exists($user_email)) {
            recipe_errors()->add('email_used', __('Email already registered'));
        }
        if($user_pass == '') {
            recipe_errors()->add('password_empty', __('Please enter a password'));
        }
        if($user_pass != $pass_confirm) {
            recipe_errors()->add('password_mismatch', __('Passwords do not match'));
        }    
        $errors = recipe_errors()->get_error_messages(); 

        if(empty($errors)) {         
            $new_user_id = wp_insert_user(array(
                  'user_login'      => $user_login,
                  'user_pass'           => $user_pass,
                  'user_email'      => $user_email,
                  'first_name'      => $user_first,
                  'last_name'           => $user_last,
                  'user_registered' => date('Y-m-d H:i:s'),
                  'role'                => $role
            )
        );

        if($new_user_id) {
            wp_new_user_notification($new_user_id);              
            wp_set_auth_cookie(get_user_by( 'email', $user_email )->ID, true);
            wp_set_current_user($new_user_id, $user_login);   
            do_action('wp_login', $user_login, wp_get_current_user());            
            wp_redirect(home_url()); exit;
        }         
      } 
    }
}
add_action('init', 'red_add_new_user');

function recipe_errors(){
    static $wp_error; 
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

function recipe_register_messages() {
    if($codes = recipe_errors()->get_error_codes()) {
        echo '<div class="recipe_errors">';
           foreach($codes as $code){
                $message = recipe_errors()->get_error_message($code);
                echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
            }
        echo '</div>';
    }   
}


/** 
* Login shortcode  
*/
add_shortcode( 'login-form', 'loginform_func' );

function loginform_func( $atts ) {
    $atts = shortcode_atts( array(
        'logo' => '1', 
    ), $atts, 'login-form' );
    
    if ( is_user_logged_in() ) {
        return "<p class='wpcookie-logged-user'>You are already logged in.</p>";
}       
        $content =  wp_login_form( 
                array( 
                    'echo' => false ,
                    'redirect'       => get_home_url() ,
                    'label_username' => __( 'Your Username ' ),
                    'label_password' => __( 'Your Password' ),
                    'label_remember' => __( 'Remember Me' )
                )
            )."<a href=".esc_url( wp_lostpassword_url() )." style='font-size: 1rem;width: 100%;margin-left: 25px;'>Lost your password?</a>";    

    $style1 = "<style>p.submit, p.forgetmenot {margin-top: 10px!important;}.login form{width: 303px;} div#login_error {width: 322px;}</style>"; 
    
    $error = "";
    
    if (isset($_GET['reason'])) {
        $er = "";
    
        if ($_GET['reason'] == 'invalid_username' || $_GET['reason'] == 'incorrect_password' || $_GET['reason'] == 'empty_username' || $_GET['reason'] == 'empty_password') {
            $er = "Invalid username or password, try again";
        }
        $error = '<p class="wpcookie_er">'.$er.'</p>';
    } 

    $style2 = '<style>
    .wpcookie-login-form label {
        display: block;
        font-size: 1rem;
        color: dimgrey;
    }
    .wpcookie-login-form input[type="text"], .wpcookie-login-form input[type="password"] {
        height: 2rem;
        width: 303px;
        border: 1px solid #e7e7e7;
        border-radius: 5px;
        background-color: #ffffff26;
    }
    .wpcookie-login-form p.wpcookie_er {
        font-size: 1rem;
        color: #b11414;
    }
    .wpcookie-login-form input[type="submit"] {
        color: white;
        background-color: #4e8ef5;
        outline: none;
        border: navajowhite;
        padding: 0.7rem;
        width: 303px;
        margin-top: 15px;
        border-radius: 5px;
        transition: all 0.5s ease;
        cursor: pointer;
    }
    .wpcookie-login-form input[type="submit"]:hover {
        background-color: #1d3eab;
    }
    .wpcookie-login-form {
        width: 350px;
        margin: auto;
        background-color: #ffffff;
        border-radius: 5px;
        padding: 20px;
        display: flex;
        justify-content: center;
        flex-direction: column;
        flex-wrap: nowrap;
        align-items: center;
    }
    p.wpcookie-logged-user {
        width: fit-content;
        font-weight: bold;
        border-radius: 5px;
        padding: 15px;
        background-color: #ffffffc2;
        color: black!important;
        margin: auto;
    }
    </style>
    '; 

$logo_id = get_theme_mod( 'custom_logo' );
$logo_url = '';

if ( $logo_id ) {
    $logo = wp_get_attachment_image_src( $logo_id, 'full' );
    if ( is_array( $logo ) ) {
        $logo_url = esc_url( $logo[0] );
    }
}

$logo ='<img src="'.$logo_url.'" alt="logo" style="padding: 15px; height: 106px;">';
    if ($atts["logo"] == 0 ) { $logo = ""; }    
    return "{$style2}{$style1}<div class='wpcookie-login-form'>{$logo}{$error}{$content}</div>";    
}
add_filter('login_redirect', 'my_login_redirect', 10, 3);

function my_login_redirect($redirect_to, $requested_redirect_to, $user) {
    if (is_wp_error($user)) {
        $error_types = array_keys($user->errors);
        $error_type = 'both_empty';
        if (is_array($error_types) && !empty($error_types)) {
            $error_type = $error_types[0];
        }
        if(isset($_SERVER['HTTP_REFERER']) ) {
                    $location =  strtok($_SERVER['HTTP_REFERER'], '?');
            if ( str_contains( $location, "wp-login.php") ) {return;}
        wp_redirect( $location . "?login=failed&reason=" . $error_type ); 
        //wp_redirect( home_url() );
        exit;
        } else return;

    } else {
        return home_url();
    }
}


/*
Create Recipe Custom Post Type
*/
function wpdocs_codex_recipe_init() {
    $labels = array(
        'name'                  => _x( 'Recipes' , 'recipe' ),
        'singular_name'         => _x( 'Recipe', 'recipe' ),
        'menu_name'             => _x( 'Recipes', 'recipe' ),
        'name_admin_bar'        => _x( 'Recipe', 'Add New on Toolbar', 'recipe' ),
        'add_new'               => __( 'Add New', 'recipe' ),
        'add_new_item'          => __( 'Add New recipe', 'recipe' ),
        'new_item'              => __( 'New recipe', 'recipe' ),
        'edit_item'             => __( 'Edit recipe', 'recipe' ),
        'view_item'             => __( 'View recipe', 'recipe' ),
        'all_items'             => __( 'All recipes', 'recipe' ),
        'search_items'          => __( 'Search recipes', 'recipe' ),
        'parent_item_colon'     => __( 'Parent recipes:', 'recipe' ),
        'not_found'             => __( 'No recipes found.', 'recipe' ),
        'not_found_in_trash'    => __( 'No recipes found in Trash.', 'recipe' ),
    );     
    $args = array(
        'labels'             => $labels,
        'description'        => 'Recipe custom post type.',
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'recipe' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail' ),
        //'show_in_rest'       => true
    );
     
    register_post_type( 'recipe', $args );
}
add_action( 'init', 'wpdocs_codex_recipe_init' );


function wpdocs_create_recipe_taxonomies() {
    $labels = array(
        'name'              => _x( 'Recipe Types',  'recipe' ),
        'singular_name'     => _x( 'Recipe Type', 'recipe' ),
        'search_items'      => __( 'Search Recipe Types', 'recipe' ),
        'all_items'         => __( 'All Recipe Types', 'recipe' ),
        'parent_item'       => __( 'Parent Recipe Type', 'recipe' ),
        'parent_item_colon' => __( 'Parent Recipe Type:', 'recipe' ),
        'edit_item'         => __( 'Edit Recipe Type', 'recipe' ),
        'update_item'       => __( 'Update Recipe Type', 'recipe' ),
        'add_new_item'      => __( 'Add New Recipe Type', 'recipe' ),
        'new_item_name'     => __( 'New Recipe Type Name', 'recipe' ),
        'menu_name'         => __( 'Recipe Type', 'recipe' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'recipe-type', 'with_front'=> false ),
    );

    register_taxonomy( 'recipe-type', array( 'recipe' ), $args );

}
add_action( 'init', 'wpdocs_create_recipe_taxonomies');


//add meta field
add_action("admin_menu", "admin_init");
function admin_init(){
    add_meta_box("recipe-meta-data", "Recipe Information", "recipe_metainfo", "recipe", "side", "low");
}

//call back function
function recipe_metainfo(){
    $recipe_ingredients = get_post_meta( get_the_ID(), 'recipe_ingredients', true );
    $recipe_instructions = get_post_meta( get_the_ID(), 'recipe_instructions', true );
    $gallery_data = get_post_meta( get_the_ID(), 'gallery_data', true );
    $video_data = get_post_meta(get_the_ID(), 'recipe_video_data', true);
    ?>
    <p><label for="recipe_ingredients">Ingredients:</label></p>
    <textarea name="recipe_ingredients" id="recipe_ingredients" rows="5" style="width: 100%;"><?php echo esc_textarea($recipe_ingredients); ?></textarea>
    
    <p><label>Instructions:</label></p>
    <textarea name="recipe_instructions" id="recipe_instructions" rows="5" style="width: 100%;"><?php echo esc_textarea($recipe_ingredients); ?></textarea>
    

    <p><label>Upload Images:</label></p>
    <div id="dynamic_form"> 
        <div id="add_field_row">     
            <input class="button upload_image_button" type="button" value="Upload Recipe Images" /> 
        </div> 
        <br/> 
        <div id="field_wrap"> 
            <?php      
                if ( isset( $gallery_data['image_url'] ) ) {         
                    for( $i = 0; $i < count( $gallery_data['image_url'] ); $i++ ) {         
            ?>             
            <div class="field_row">                 
                <div class="field_left">                     
                    <div class="form_field">                         
                        <label>Image URL</label>                         
                        <input type="text" class="meta_image_url" name="gallery[image_url][]" value="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>" />                     
                    </div>                 
                </div>
                <div class="field_right image_wrap">                     
                    <img src="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>" height="48" width="48" />                 
                </div>
                <div class="field_right">                     
                    <!-- <input class="button addimage" type="button" value="Choose File" /> -->                     
                    <input class="button" type="button" value="Remove" onclick="remove_field(this)" />                 
                </div>
                <div class="clear" /></div>              
            </div>         
            <?php } }  ?> 
        </div>
    </div>

    <style type="text/css">
      .field_left { float:left; }
      .field_right { float:left; margin-left:10px; }
      .clear { clear:both; }
      #dynamic_form { width:100%; }
      #dynamic_form input[type=text] { width:650px; }
      #dynamic_form .field_row { border:1px solid #999; margin-bottom:10px; padding:10px; }
      #dynamic_form label { padding:0 6px; }
    </style>
    
    <script type="text/javascript">
    jQuery(document).ready(function($){     
        jQuery( "#field_wrap" ).sortable();
        var custom_uploader;     
            jQuery('.upload_image_button').click(function(e) {         
            e.preventDefault();         //If the uploader object has already been created, reopen the dialog         
            if (custom_uploader) {             
                custom_uploader.open();             
                return;         
            }         //Extend the wp.media object         
            custom_uploader = wp.media.frames.file_frame = wp.media({             
                title: 'Choose Image',            
                button: {                 
                    text: 'Choose Image'             
                },             
                multiple: true         
            });         
            custom_uploader.on('select', function() {             
                var selection = custom_uploader.state().get('selection');             
                selection.map( function( attachment ) {                 
                    attachment = attachment.toJSON();                 
                    jQuery("#field_wrap").append('<div class="field_row"><div class="field_left"><div class="form_field"><label>Image URL</label><input type="text" class="meta_image_url" name="gallery[image_url][]" value="'+attachment.url+'" /></div></div><div class="field_right image_wrap"><img src="'+attachment.url+'" height="60" width="60" /></div><div class="field_right"><input class="button" type="button" value="Remove" onclick="remove_field(this)" /></div><div class="clear" /></div></div>');             
                });         
            });         
            custom_uploader.open();     
        }); 
    });

    function remove_field(obj) {     
        var parent=jQuery(obj).parent().parent();     
        parent.remove(); 
    }
    </script>

    <div id="recipe_videos_wrap">
        <div id="recipe_videos_fields">
            <?php
            if (!empty($video_data['video_url'])) {
                foreach ($video_data['video_url'] as $video_url) {
                    ?>
                    <div class="recipe_video_row">
                        <input type="text" name="recipe_videos[video_url][]" value="<?php echo esc_url($video_url); ?>" placeholder="Video URL" class="widefat recipe_video_url" />
                        <button type="button" class="button button-secondary recipe_upload_video">Upload Video</button>
                        <button type="button" class="button button-secondary recipe_remove_video">Remove</button>
                        <div class="recipe_video_preview">
                            <video width="150" height="100" controls>
                                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <button type="button" class="button button-primary" id="add_recipe_video_field">Add Video</button>
    </div>

    <!-- Hidden Template for Adding New Video Rows -->
    <template id="recipe_video_template">
        <div class="recipe_video_row">
            <input type="text" name="recipe_videos[video_url][]" placeholder="Video URL" class="widefat recipe_video_url" />
            <button type="button" class="button button-secondary recipe_upload_video">Upload Video</button>
            <button type="button" class="button button-secondary recipe_remove_video">Remove</button>
            <div class="recipe_video_preview" style="display: none;">
                <video width="150" height="100" controls>
                    <source src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </template>

    <script>
    jQuery(document).ready(function ($) {
        // Add New Video Row
        $('#add_recipe_video_field').click(function () {
            var templateHtml = $('#recipe_video_template').html();
            $('#recipe_videos_fields').append(templateHtml);
        });

        // Remove Video Row
        $(document).on('click', '.recipe_remove_video', function () {
            $(this).closest('.recipe_video_row').remove();
        });

        // Upload Video and Show Preview
        $(document).on('click', '.recipe_upload_video', function (e) {
            e.preventDefault();
            var button = $(this);
            var inputField = button.siblings('.recipe_video_url');
            var previewWrap = button.siblings('.recipe_video_preview');
            var videoElement = previewWrap.find('video source');

            var frame = wp.media({
                title: 'Upload Video',
                button: { text: 'Select Video' },
                multiple: false,
                library: { type: 'video' }
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                inputField.val(attachment.url);

                videoElement.attr('src', attachment.url);
                videoElement.parent()[0].load(); // Reload video element
                previewWrap.show();
            });

            frame.open();
        });

        // Show Preview when loading from database
        $(document).on('input', '.recipe_video_url', function () {
            var inputField = $(this);
            var previewWrap = inputField.siblings('.recipe_video_preview');
            var videoElement = previewWrap.find('video source');
            var videoUrl = inputField.val();

            if (videoUrl) {
                videoElement.attr('src', videoUrl);
                videoElement.parent()[0].load();
                previewWrap.show();
            } else {
                previewWrap.hide();
            }
        });

        // Trigger video previews on page load for existing data
        $('#recipe_videos_fields .recipe_video_url').trigger('input');
    });
    </script>

    <style>
    .recipe_video_row {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .recipe_video_row input {
        flex: 1;
    }

    .recipe_video_preview video {
        display: block;
    }
    </style>

<?php
}

function save_recipe_meta_details($post_id) {
    
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
    if ( wp_is_post_revision($post_id) ) return $post_id;

    $post = get_post($post_id);
    
    if ( !$post || !is_object($post) || !isset($post->ID) ) {
        return $post_id;
    }

    if ( isset($_POST['recipe_ingredients']) ) {
        update_post_meta($post->ID, "recipe_ingredients", $_POST["recipe_ingredients"]);
    }

    if ( isset($_POST['recipe_instructions']) ) {
        update_post_meta($post->ID, "recipe_instructions", $_POST["recipe_instructions"]);
    }

    if ( isset($_POST['gallery']) ) {
        $gallery_data = array();
        for ($i = 0; $i < count($_POST['gallery']['image_url']); $i++) {
            if ( '' != $_POST['gallery']['image_url'][$i] ) {
                $gallery_data['image_url'][] = $_POST['gallery']['image_url'][$i];
            }
        }
        if ( $gallery_data ) {
            update_post_meta($post->ID, 'gallery_data', $gallery_data);
        } else {
            delete_post_meta($post->ID, 'gallery_data');
        }
    }

    $videos = isset($_POST['recipe_videos']['video_url']) ? array_map('esc_url', $_POST['recipe_videos']['video_url']) : [];
    if (!empty($videos)) {
        update_post_meta($post->ID, 'recipe_video_data', ['video_url' => $videos]);
    } else {
        delete_post_meta($post->ID, 'recipe_video_data');
    }

    return $post_id;
}

add_action('save_post', 'save_recipe_meta_details');


add_action('wp_ajax_event_file_upload', 'event_file_upload');
add_action('wp_ajax_nopriv_event_file_upload', 'event_file_upload');

function event_file_upload(){
    $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg');
    $successCode = 0;
    $fileUrl = '';
    $filePath = '';
    $msg = '';
    if (in_array($_FILES['file']['type'], $arr_img_ext)) {
        if ( ! function_exists( 'wp_handle_upload' ) )  {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );  
        }
        if($_FILES['file']['name'] != '') {
            $uploadedfile = $_FILES['file'];
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
            if ( $movefile && ! isset( $movefile['error'] ) ) {
                $fileUrl = $movefile['url'];
                $filePath = $movefile['file'];
                $successCode = 1;
            } else {
                $msg = 'Somethings went wrong while file uploading.';
            }
        }
    }else{
        $msg = 'Please select valid files.';
    }
    $returnArray = array();
    $returnArray['successCode'] = $successCode;
    $returnArray['msg'] = $msg;
    $returnArray['fileUrl'] = $fileUrl;
    $returnArray['filePath'] = $filePath;
    echo json_encode($returnArray);
    exit();
}

add_action('wp_ajax_event_video_upload', 'event_video_upload');
add_action('wp_ajax_nopriv_event_video_upload', 'event_video_upload');

function event_video_upload(){
    $arr_video_ext = array('video/mp4', 'video/avi', 'video/mkv');
    $successCode = 0;
    $fileUrl = '';
    $filePath = '';
    $msg = '';
    if (in_array($_FILES['file']['type'], $arr_video_ext)) {
        if ( ! function_exists( 'wp_handle_upload' ) )  {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );  
        }
        if($_FILES['file']['name'] != '') {
            $uploadedfile = $_FILES['file'];
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
            if ( $movefile && ! isset( $movefile['error'] ) ) {
                $fileUrl = $movefile['url'];
                $filePath = $movefile['file'];
                $successCode = 1;
            } else {
                $msg = 'Somethings went wrong while file uploading.';
            }
        }
    } else {
        $msg = 'Please select valid video files (MP4, AVI, MKV).';
    }
    $returnArray = array();
    $returnArray['successCode'] = $successCode;
    $returnArray['msg'] = $msg;
    $returnArray['fileUrl'] = $fileUrl;
    $returnArray['filePath'] = $filePath;
    echo json_encode($returnArray);
    exit();
}

// Add the custom profile image field to user profile
function custom_user_profile_image_field($user) {
    $profile_image = get_user_meta($user->ID, 'profile_image', true);
    ?>
    <h3>Profile Image</h3>
    <table class="form-table">
        <tr>
            <th><label for="profile_image">Upload Profile Image</label></th>
            <td>
                <input type="hidden" name="profile_image" id="profile_image" value="<?php echo esc_attr($profile_image); ?>" />
                <img src="<?php echo esc_url($profile_image); ?>" style="max-width: 150px; display: block;" id="profile_image_preview" />
                <button class="button" type="button" id="upload_profile_image_button">Upload Image</button>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'custom_user_profile_image_field');
add_action('edit_user_profile', 'custom_user_profile_image_field');

// Save the profile image URL
function save_custom_user_profile_image($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'profile_image', esc_url_raw($_POST['profile_image']));
}
add_action('personal_options_update', 'save_custom_user_profile_image');
add_action('edit_user_profile_update', 'save_custom_user_profile_image');

// Enqueue media uploader script and styles
function enqueue_custom_profile_image_uploader_script($hook) {
    if ('profile.php' === $hook || 'user-edit.php' === $hook) {
        wp_enqueue_media();
        wp_enqueue_script('custom-profile-image-upload', get_stylesheet_directory_uri() . '/assets/js/custom-profile-image-upload.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_custom_profile_image_uploader_script');

/*
 * Set post views count using post meta//functions.php
 */
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}

// function to count views.
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

