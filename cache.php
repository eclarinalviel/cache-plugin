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
    add_menu_page( 'Caching Plugin Page', 'Caching Settings', 'manage_options', 'cache-plugin', 'test_init' );
}
 
function test_init(){
    echo "<h1>Caching Plugin</h1>";
    echo "<h3><b>Caching:</b></h3>";
    echo "<p>Turning the Caching On will cache your posts and pages to reduce processing load.</p>";
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
    if (isset($_POST['delete'])) {delete_post_cache(); }
}

function validate_caching_choice(){
    $choice = $_POST['cache_option'];  
    if(!empty($choice))
    {
        if( $choice == "1" ) 
        {
            showMessage("Cache successfully turned on..");
            post_caching();
        }
        else if( $choice== "2" ) 
        {
            showMessage("Cache successfully turned off..");
            
        }else{
            showMessage("Something's ain't right.");
        }
   }else{
        showMessage("Empty Variable.");
    }     
}

function post_caching(){
    // CACHE POSTS
    if( ($posts = get_transient("posts")) === false) // if there's no transient yet called posts
    {
        $args = array(
            'post_type' => 'post',
              'orderby'   => 'datee',
              'order'     => 'DESC',
              'post_status' => 'publish'
        );
        $posts = new WP_Query($args);
        set_transient("posts", $posts, 28800); //zero - no expiration for transients
        return $posts;
    } 
    //restores the $post global to the current post in the main query.
   wp_reset_postdata(); 

}

add_filter('the_content', 'post_filter');
function post_filter($posts){
    //if there's a post in transient, get all posts then save to array to be use in add_filter
    if(!empty($posts))
    {
        $query = get_posts($posts);
        // $filtered_post = array();
        foreach($query as $post) {
            //Replace current posts with data from transient/cache
            $filtered_post = get_the_ID($post);
            $new_post = get_post($filtered_post);
            //print_r($new_post->post_content);          
        }  

       return $new_post->post_content;
    }else{showMessage("Theres no post.");}
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
