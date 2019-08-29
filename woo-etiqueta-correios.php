<?php

/**
 *
 * @link              http://mercalona.com
 * @since             1.0.0
 * @package           Woo_Etiqueta_Correios
 *
 * @wordpress-plugin
 * Plugin Name:       Mercalona | Woo Etiqueta Correios
 * Plugin URI:        https://gitlab.com/mercalona-wordpress/woo-etiqueta-correios
 * Description:       Plugin gerador de etiquetas para encomendas do Correios.
 * Version:           1.0.0
 * Author:            Mercalona
 * Author URI:        http://mercalona.com/
 * Text Domain:       woo-etiqueta-correios
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'WOO_ETIQUETA_CORREIOS_VERSION', '1.0.0' );

function activate_woo_etiqueta_correios() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-etiqueta-correios-activator.php';
    Woo_Etiqueta_Correios_Activator::activate();
}

function deactivate_woo_etiqueta_correios() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-etiqueta-correios-deactivator.php';
    Woo_Etiqueta_Correios_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_etiqueta_correios' );
register_deactivation_hook( __FILE__, 'deactivate_woo_etiqueta_correios' );

require plugin_dir_path( __FILE__ ) . 'includes/class-woo-etiqueta-correios.php';
function run_woo_etiqueta_correios() {
	$plugin = new Woo_Etiqueta_Correios();
	$plugin->run();
}
run_woo_etiqueta_correios();
