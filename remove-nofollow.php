<?php
/*
Plugin Name: Remove Nofollow
Plugin URI: http://www.richardsramblings.com/plugins/wp-remove-nofollow/
Description: Removes the "nofollow" attribute added by WordPress to links in comments and author URLs.
Version: 1.00
License: GPL
Author: Richard D. LeCour
Author URI: http://www.richardsramblings.com/
*/

add_option("remove_nofollow_comment", "checkbox");
add_option("remove_nofollow_author", "");

// DON'T REMOVE ATTRIBUTES WHEN IN ADMIN PAGES
if (strpos($_SERVER['REQUEST_URI'], 'wp-admin') === false) {
   if (get_option('remove_nofollow_comment')) {
      add_filter('comment_text', 'remove_nofollow');
   }
   if (get_option('remove_nofollow_author')) {
      add_filter('get_comment_author_link', 'remove_nofollow');
   }
}

function remove_nofollow($text) {
   $text = preg_replace("/(<a[^>]*[^\s])(\s*nofollow\s*)/i", "$1", $text);
   $text = preg_replace("/(<a[^>]*[^\s])(\s*rel=[\"\']\s*[\"\'])/i", "$1", $text);
   return $text;
}


add_action('admin_menu', 'add_remove_nofollow_admin');

function add_remove_nofollow_admin() {
   if (function_exists('add_options_page')) {
      add_options_page('Nofollow Options', 'Remove Nofollow', 8, basename(__FILE__), 'remove_nofollow_admin');
   }
}

function remove_nofollow_admin() {
   ?>
   <div class="wrap">
      <h2>Nofollow Options</h2>
      <form action="options.php" method="post">
      <?php wp_nonce_field('update-options') ?>

      <table class="optiontable editform"><tbody>
         <tr>
            <th scope="row">Nofollow Attributes:</th>
            <td>
               <label for="remove_nofollow_comment">
                  <input id="remove_nofollow_comment" type="checkbox"
                        name="remove_nofollow_comment" value="checkbox"
                        <?php if (get_option('remove_nofollow_comment')) echo "checked='checked'"; ?> />
                  Remove "nofollow" attributes from comments.
               </label>
            </td>
         </tr>
         <tr>
            <td></td>
            <td>
               <label for="remove_nofollow_author">
                  <input id="remove_nofollow_author" type="checkbox"
                        name="remove_nofollow_author" value="checkbox"
                        <?php if (get_option('remove_nofollow_author')) echo "checked='checked'"; ?> />
                  Remove "nofollow" attributes from comment author's links.
               </label>
            </td>
         </tr>
      </tbody></table>

      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="page_options" value="remove_nofollow_author,remove_nofollow_comment" />
      <p class="submit">
         <input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
      </p>
      </form>
   </div>
   <?php
}
?>
