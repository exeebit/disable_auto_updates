<?php

defined('ABSPATH') or die('Unauthorized Access');

$upload_dir = wp_upload_dir();
global $dau_upload;
global $dau_dir;

$dau_upload = $upload_dir['basedir'];
 
if(!empty($dau_upload)) {
    $dau_dir = $dau_upload . '/disable-auto-updates';
    if(!file_exists($dau_dir)) {
        wp_mkdir_p($dau_dir);
    }
    if(file_exists($dau_dir) && !file_exists("$dau_dir/log.txt")) {
        $log_file = fopen("$dau_dir/log.txt", "wb");
        fwrite($log_file,'');
        fclose($log_file);
    }
}

global $current_user;
wp_get_current_user();
$user = $current_user -> user_login;

global $dau_services;
$dau_services = ['disable-all', 'disable-plugin', 'disable-theme', 'disable-core', 'disable-admin-notice', 'hide-notification'];
$submitted = [];

function dau_check_files($file_name) {
    if(in_array($file_name, $GLOBALS['dau_services']) && file_exists($GLOBALS['dau_dir'] . "/$file_name.php")) {
        echo 'checked';
    }
}

if(isset($_POST['submit'])) {

    foreach ($_POST as $key => $value) {
        if(isset($key) && $key == true && $key != 'submit') {
            $submitted[] = esc_html(filter_var($key, FILTER_SANITIZE_STRING));
        }
    }

    foreach($dau_services as $service) {

        if(in_array($service, $submitted)) {

            $content = '';
            $log_content = '';

            if($service === "disable-all") {
                $content = '<?php

defined("ABSPATH") or die("Unauthorized Access");

define( "WP_AUTO_UPDATE_CORE", false );
add_filter("auto_update_plugin", "__return_false");
add_filter("auto_update_theme", "__return_false");';
                if(!file_exists("$dau_dir/$service.php")) {
	                $log_content = "All auto updates have been disabled - " . current_time('mysql') . " by $user<br />";
                }
            } elseif($service === "disable-plugin") {
	            $content = '<?php
                
defined("ABSPATH") or die("Unauthorized Access");

add_filter("auto_update_plugin", "__return_false");';
	            if(!file_exists("$dau_dir/$service.php")) {
		            $log_content = "Plugin auto update has been disabled - " . current_time('mysql') . " by $user<br />";
	            }
            } elseif($service === "disable-theme") {
	            $content = '<?php
                                
defined("ABSPATH") or die("Unauthorized Access");

add_filter("auto_update_theme", "__return_false");';
	            if(!file_exists("$dau_dir/$service.php")) {
		            $log_content = "Theme auto update has been disabled - " . current_time('mysql') . " by $user<br />";
	            }
            } elseif($service === "disable-core") {
	            $content = '<?php
                                
defined("ABSPATH") or die("Unauthorized Access");

define( "WP_AUTO_UPDATE_CORE", false );';
	            if(!file_exists("$dau_dir/$service.php")) {
		            $log_content = "Core auto update has been disabled - " . current_time('mysql') . " by $user<br />";
	            }
            } elseif($service === 'disable-admin-notice') {
                $content = '<?php
                                
defined("ABSPATH") or die("Unauthorized Access");
                
add_action("admin_enqueue_scripts", "hide_notices");
add_action("login_enqueue_scripts", "hide_notices");
    
function hide_notices() {
    if (current_user_can( "manage_options" )) {
        echo "<style>.update-nag, .updated, .error, .is-dismissible, .notice { display: none; }</style>";
    }
}';

                if(!file_exists("$dau_dir/$service.php")) {
                    $log_content = "Admin notice has been disabled - " . current_time('mysql') . " by $user<br />";
                }


            } else {
	            $content = '<?php
                                
defined("ABSPATH") or die("Unauthorized Access");

function dau_remove_notifications() {
    global $wp_version;
    return(object) array("last_checked"=> time(),"version_checked"=> $wp_version,);
}
    
add_filter("pre_site_transient_update_core","dau_remove_notifications");
add_filter("pre_site_transient_update_plugins","dau_remove_notifications");
add_filter("pre_site_transient_update_themes","dau_remove_notifications");';
	            if(!file_exists("$dau_dir/$service.php")) {
		            $log_content = "Update notifications have been disabled along with core, theme and plugin updates - " . current_time('mysql') . " by $user<br />";
	            }
            }

	        $file = fopen("$dau_dir/$service.php", "wb");
	        $log_file = fopen("$dau_dir/log.txt", "a");
            fwrite($file, $content);
            fwrite($log_file, $log_content);
            fclose($file);
	        fclose($log_file);

        } else {
            if(file_exists("$dau_dir/$service.php")) {
                unlink("$dau_dir/$service.php");
	            if($service === "disable-all") {
		            $log_content_delete = "All auto updates have been enabled - " . current_time('mysql') . " by $user<br />";
	            } elseif($service === "disable-plugin") {
		            $log_content_delete = "Plugin auto update has been enabled - " . current_time('mysql') . " by $user<br />";
	            } elseif($service === "disable-theme") {
		            $log_content_delete = "Theme auto update has been enabled - " . current_time('mysql') . " by $user<br />";
	            } elseif($service === "disable-core") {
		            $log_content_delete = "Core auto update has been enabled - " . current_time('mysql') . " by $user<br />";
	            } elseif($service === "disable-admin-notice") {
		            $log_content_delete = "Admin notice has been enabled - " . current_time('mysql') . " by $user<br />";
	            } elseif($service === "hide-notification") {
		            $log_content_delete = "Update notifications have been enabled along with core, theme and plugin updates - " . current_time('mysql') . " by $user<br />";
	            }

	            $log_file_delete = fopen("$dau_dir/log.txt", "a");
	            fwrite($log_file_delete, $log_content_delete);
	            fclose($log_file_delete);

            }
        }

    }

}
?>

<div id="dau">
    <h1 id="dau-title">Disable Auto Updates <sub style="font-size: 12px">V 1.4</sub></h1>
    <hr align="left" width="600">

    <form action="" method="POST" id="dau-form">
        <label for="dau-disable">
            Disable all updates (Core, Plugins and Theme)
            <input type="checkbox" name="disable-all" class="dau-form-tick" <?php dau_check_files('disable-all'); ?>><br>
        </label><br>
        <label for="dau-disable-plugin">
            Disable plugin updates
            <input type="checkbox" name="disable-plugin" class="dau-form-tick" <?php dau_check_files('disable-plugin'); ?>><br>
        </label><br>
        <label for="dau-disable-theme">
            Disable Theme updates
            <input type="checkbox" name="disable-theme" class="dau-form-tick" <?php dau_check_files('disable-theme'); ?>><br>
        </label><br>
        <label for="dau-disable-core">
            Disable core updates
            <input type="checkbox" name="disable-core" class="dau-form-tick" <?php dau_check_files('disable-core'); ?>><br>
        </label><br>
        <label for="dau-hide-notification">
            Hide update notification
            <input type="checkbox" name="hide-notification" class="dau-form-tick" <?php dau_check_files('hide-notification'); ?>><br>
        </label><br />
        <label for="dau-disable-admin-notice">
            Disable Admin Notice
            <input type="checkbox" name="disable-admin-notice" class="dau-form-tick" <?php dau_check_files('disable-admin-notice'); ?>><br>
        </label>
        <button name="submit" id="dau-button">Save</button>
    </form>
    <div id="dau-console">
        <?php echo file_get_contents("$dau_dir/log.txt"); ?>
    </div>
</div>

<?php

$this->thankyou();
