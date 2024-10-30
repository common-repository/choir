<?php

add_action('admin_init', 'choir_register_options'); 
add_action('admin_menu', 'choir_register_admin_menu');

function choir_register_options() {
  register_setting('choir-options', 'choir-apikey');
}

function choir_register_admin_menu() {
  add_submenu_page('plugins.php', "Choir", "Choir", 
    "manage_options", "choir", "choir_show_admin_settings_page");
}

function choir_show_admin_settings_page() {
?>
  <div class="wrap">
    <?php screen_icon(); ?>
    <h2>Choir settings</h2>

    <form method="post" action="options.php"> 
      <?php settings_fields('choir-options'); ?>
      <?php do_settings_sections('choir-options');  ?>
      
      <table class="form-table">
        <tr valign="top">
          <th scope="row">choir.io API key</th>
          <td>
            <input autofocus type="text" name="choir-apikey" value="<?php echo get_option('choir-apikey'); ?>" />
          </td>
        </tr>
      </table>
      
      <?php submit_button(); ?>
    </form>
  </div>
<?php
}
?>