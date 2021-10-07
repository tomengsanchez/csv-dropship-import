<?php
//use Group_Care as GlobalGroup_Care;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/tomengsanchez
 * @since             1.0.0
 * @package           Drophip Connect
 *
 * @wordpress-plugin
 * Plugin Name:       Dropship Imports
 * Plugin URI:        https://github.com/tomengsanchez
 * Description:       Imports Dropship Using CSV
 * Version:           1.0.0
 * Author:            Tomeng
 * Author URI:        https://github.com/tomengsanchez
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       csv-dropship-import
 * Domain Path:       /languages
 */

class Dropship_Import{
	public function __construct()
	{
		
		include_once __DIR__ . "/incl/dsi_loader.php";
		$this->load();
	}
	public function load(){
		$loader = new DSI_Loader();
	}
}

$ds = new Dropship_Import();
$ds;