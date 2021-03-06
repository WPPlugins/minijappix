<?php
/*
Plugin Name: Mini Jappix
Plugin URI: http://www.apavel.me/wordpress-mini-jappix/
Description: This plugin add the javascript code for Jappix mini.
Version: 0.4.1
Author: Pavel Aurélien
Author URI: http://www.apavel.me
*/

/*  Copyright 2011  Pavel Aurélien  (email : le.manchot@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('wp_head', 'get_mini_jappix');
add_action('admin_menu', 'mini_jappix_menu');
add_action('admin_init', 'register_mysettings' );

add_action( 'init', 'my_plugin_init' );

function my_plugin_init() {
    $plugin_dir = basename(dirname(__FILE__));
    load_plugin_textdomain( 'minijappix', null, $plugin_dir );
}

function get_mini_jappix() {
    if(get_option('auto_login') == 1)
        $auto_login = "true";
    else
        $auto_login = "false";
    if(get_option('auto_show') == 1)
        $auto_show = "true";
    else
        $auto_show = "false";
    if(get_option('already_jquery') != 1)
        echo '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>';
    $groups = explode(',', get_option('join_groupchats'));
    $group = '';
    foreach ($groups as $value) {
        $group .= '"'.trim($value).'", ';
    }
    $group = substr ($group, 0, -2);
    $lng = get_option('language');
    echo '<script type="text/javascript">
$(document).ready( function(){
    jQuery.ajaxSetup({ cache: true });

    jQuery.getScript("https://static.jappix.com/server/get.php?l='.$lng.'&t=js&g=mini.xml", function() {
        JappixMini.launch({
            connection: {
                domain: "anonymous.jappix.com",
            },

            application: {
                network: {
                    autoconnect: '.$auto_login.',
                },

                interface: {
                    showpane: '.$auto_show.',
                    animate: true,
                },

                user: {
                    random_nickname: false,
                },

                groupchat: {
                    open: ['.$group.'],
                },
            },
        });
    });
});
</script>';

}

function mini_jappix_menu() {
    add_options_page('Mini Jappix Options', 'Mini Jappix', 'manage_options', 'minijappix', 'mini_jappix_options');
}

function register_mysettings() {
    //register our settings
    register_setting('mini_jappix', 'already_jquery');
    register_setting('mini_jappix', 'language');
    register_setting('mini_jappix', 'auto_login');
    register_setting('mini_jappix', 'auto_show');
    register_setting('mini_jappix', 'join_groupchats');
}

function mini_jappix_options() {
    if (!current_user_can('manage_options'))  {
        wp_die( __('You do not have sufficient permissions to access this page.', 'minijappix') );
    }
    ?>
    <div class="wrap">
        <h2>Mini Jappix</h2>

        <form method="post" action="options.php">
            <?php settings_fields( 'mini_jappix' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e("Auto login to the account", 'minijappix'); ?></th>
                    <td><input type="checkbox" name="auto_login" value="1" <?php checked('1', get_option('auto_login')); ?> /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e("Auto show the opened chat", 'minijappix'); ?></th>
                    <td><input type="checkbox" name="auto_show" value="1" <?php checked('1', get_option('auto_show')); ?> /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e("Chat rooms to join (if any)", 'minijappix'); ?></th>
                    <td><input type="text" name="join_groupchats" value="<?php echo get_option('join_groupchats'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e("jQuery is already included", 'minijappix'); ?></th>
                    <td><input type="checkbox" name="already_jquery" value="1" <?php checked('1', get_option('already_jquery')); ?> /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e("Mini Jappix language", 'minijappix'); ?></th>
                    <td>
                        <select id="language" name="language">
                            <option value="de" <?php selected('de', get_option('language')); ?>>Deutsch</option>
                            <option value="en" <?php selected('en', get_option('language')); ?>>English</option>
                            <option value="eo" <?php selected('eo', get_option('language')); ?>>Esperanto</option>
                            <option value="es" <?php selected('es', get_option('language')); ?>>Español</option>
                            <option value="fr" <?php selected('fr', get_option('language')); ?>>Français</option>
                            <option value="it" <?php selected('it', get_option('language')); ?>>Italiano</option>
                            <option value="ja" <?php selected('ja', get_option('language')); ?>>日本語</option>
                            <option value="nl" <?php selected('nl', get_option('language')); ?>>Nederlands</option>
                            <option value="pl" <?php selected('pl', get_option('language')); ?>>Polski</option>
                            <option value="ru" <?php selected('ru', get_option('language')); ?>>Русский</option>
                            <option value="sv" <?php selected('sv', get_option('language')); ?>>Svenska</option>
                        </select>
                    </td>
                </tr>

            </table>

            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'minijappix') ?>" />
            </p>

        </form>
    </div>
<?php }

function minijappix_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=minijappix">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'minijappix_settings_link' );
