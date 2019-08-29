<?php

class Woo_Etiqueta_Correios_i18n {

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-etiqueta-correios',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
