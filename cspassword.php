<?php
/*
Plugin Name: Password Content ShortCode 
Plugin URI: http://www.zetrider.ru/password-content-shortcode.html
Description: Password for the content of records WordPress
Version: 2.2
Author: ZetRider
Author URI: http://www.zetrider.ru
Author Email: ZetRider@bk.ru
*/
/*  Copyright 2013  zetrider  (email: zetrider@bk.ru)

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

define('CSP_PLUGIN_NAME', dirname(plugin_basename(__FILE__)) );

load_plugin_textdomain('cs-password', PLUGINDIR . '/' . CSP_PLUGIN_NAME . '/lang/');

function csp_add_button() {
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
   if ( get_user_option('rich_editing') == 'true') {
     add_filter('mce_external_plugins', 'csp_tinymce_plugin');
     add_filter('mce_buttons', 'csp_register_button');
   }
}
add_action('init', 'csp_add_button');

function csp_register_button($buttons) {
   array_push($buttons, "|", "cspassword");
   return $buttons;
}

function csp_tinymce_plugin($plugin_array) {
   $plugin_array['cspassword'] = WP_PLUGIN_URL . '/' . CSP_PLUGIN_NAME .'/js/csp_mce.js';
   return $plugin_array;
}

function cs_password_menu(){
	add_options_page('CS Password', 'CS Password', 8, 'setting_cspassword', 'setting_cspassword');
}
add_action('admin_menu', 'cs_password_menu');

function setting_cspassword() {
?>
<div class="wrap">

	<h2><?php _e("Password Content Shortcode", "clone-spc"); ?></h2>
	<br>
	<b>Shortcode:</b> [cspasswordcode password=''][/cspasswordcode]<br>
	<b>CSS Class:</b> .csp_form{}, .csp_input{}, .csp_submit{}
	<hr>
	
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<strong><?php _e("Message when an error entering the password:","cs-password"); ?></strong> <small>(<?php _e("Default:", "cs-password"); ?> <?php _e("Access Denied", "cs-password"); ?>)</small><br>
		<input type="text" name="cspassword_error" size="60" value="<?php echo get_option('cspassword_error') ?>" /><br><br>
		<strong><?php _e("The text before the input field:","cs-password"); ?></strong> <small>(<?php _e("Default:", "cs-password"); ?> <?php _e("Content with a password", "cs-password"); ?>)</small><br>
		<input type="text" name="cspassword_text" size="60" value="<?php echo get_option('cspassword_text') ?>" /><br><br>
		<strong><?php _e("The name of the input buttons:","cs-password"); ?></strong> <small>(<?php _e("Default:", "cs-password"); ?> <?php _e("Access", "cs-password"); ?>)</small><br>
		<input type="text" name="cspassword_submit" size="60" value="<?php echo get_option('cspassword_submit') ?>" /><br><br>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="cspassword_error, cspassword_submit, cspassword_text" />
		<input type="submit" name="update" value="<?php _e("Save","cs-password"); ?>" class="button-primary">
	</form>
</div>    
<?php 
}

function cspassword_shortcode($atts, $content = null) {
	extract(shortcode_atts(array(
		'password' => ''
	), $atts));
	
	$text 	= get_option('cspassword_text');
	$submit = get_option('cspassword_submit');
	$error 	= get_option('cspassword_error');
	
	$csp_text 	= (($text == '')?__("Content with a password", "cs-password"):$text);
	$csp_submit = (($submit == '')?__("Access", "cs-password"):$submit);
	$csp_error 	= (($error == '')?__("Access Denied", "cs-password"):$error);
	
	$form = '
	<form action="#csp_form" method="post" class="csp_form" id="csp_form">
		'.$csp_text.' 
		<input type="text" size="20" name="csp_input">
		<input type="submit" name="csp_submit" value="'.$csp_submit.'">
	</form>
	';
	
	if (isset($_POST['csp_submit'])) {
		if ($_POST['csp_input'] == $password AND $password != '') {
			return $content;
		}
		else
		{
			return '
			'.$form.'
			<strong>
			'.$csp_error.'
			</strong>
			';
		}
	}
	else
	{
		return $form;
	}
}
add_shortcode('cspasswordcode', 'cspassword_shortcode');