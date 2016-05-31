<?php
/**
* Plugin Name: Caching Plugin
* Plugin URI: http://mypluginuri.com/
* Description: Reduces processing load of web pages.
* Version: 1.0 
* Author: eclarinalviel
* Author URI: Author's website
* License: A "Slug" license name e.g. GPL12
*/

add_action('admin_menu', 'plugin_setup_menu');

function plugin_setup_menu(){
    add_menu_page( 'Caching Plugin Page', 'Caching Settings', 'manage_options', 'test-plugin', 'test_init' );
}
 
function test_init(){
    echo "<h1>Caching Plugin</h1>";
    echo "<h3><b>Caching:</b></h3>";
?>
      <!-- Form to handle the upload - The enctype value here is very important -->
    <form  method="post" enctype="multipart/form-data">    
        <input type="radio" id='caching_on' name="cache_option" value="1" />
        <label for="caching_on">Caching On <i>(Recommended)</i></label>     
        <br/>
        <input type="radio" id="caching_off" name="cache_option" value="2" />
        <label for="caching_off">Caching Off</label>
        <input type="hidden" value="true" name="update_status" />
        <?php submit_button('Update Status') ?>

    </form>
<?php
    // if update button is clicked
    if (isset($_POST['update_status'])) {validate_caching_choice(); }
    echo "<h3><b>Delete Cached Pages</b></h3>";
    echo "<p>Cached pages are stored on your server as html and PHP files. If you need to delete them, use the button below.</p>";
    ?>
    <input type="hidden" value="true" name="delete" />
    <?php submit_button('Delete Cache');
    // if delete button is clicked
    if (isset($_POST['delete'])) {delete_cache(); }
}

function validate_caching_choice(){
    $choice = $_POST['cache_option'];  
    if(!empty($choice))
    {
        if( $choice == "1" ) 
        {
            showMessage("Yes to caching!");
            enable_caching();
        }
        else if( $choice== "2" ) 
        {
            showMessage("No to cache?");
        }else{
            showMessage("Something's ain't right.");
        }
   }else{
        showMessage("Empty Variable.");
    } 

    
}

function enable_caching(){
    // i'm using wordpress transient API because it lasts longer than WP object cache

    // CACHE POSTS
    
    if( ($posts = get_transient("posts")) === false) 
    {
        //select the data/posts you want from db
        $args = array(
            'post_type' => 'post',
              'orderby'   => 'title',
              'order'     => 'ASC',
              'post_status' => 'any'
        );

        $posts = new WP_Query($args);
        set_transient("posts", $posts, 0); //zero - no expiration for transients
    } 
    
    wp_die();
    if($posts->have_posts()){
        while($posts->have_posts()) : $posts->the_post(); 
        //display the post
        endwhile; 
    }else{ 
        //display no posts found message
    }  
    //restores the $post global to the current post in the main query.
    wp_reset_postdata(); 

}

function disable_caching(){

}

function delete_cache(){

}

function showMessage($message, $errormsg = false)
{
    if ($errormsg) {
        echo '<div id="message" class="error">';}
    else {
        echo '<div id="message" class="updated fade">';
    }
    echo "<p><strong>$message</strong></p></div>";
}    
 
?>