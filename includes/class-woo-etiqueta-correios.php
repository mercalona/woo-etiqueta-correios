<?php

class Woo_Etiqueta_Correios
{

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct()
    {
        if (defined('WOO_ETIQUETA_CORREIOS_VERSION')) {
            $this->version = WOO_ETIQUETA_CORREIOS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'woo-etiqueta-correios';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
//        $this->define_public_hooks();
    }

    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woo-etiqueta-correios-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woo-etiqueta-correios-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-woo-etiqueta-correios-admin.php';
//        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-woo-etiqueta-correios-public.php';

        $this->loader = new Woo_Etiqueta_Correios_Loader();
    }

    private function set_locale()
    {
        $plugin_i18n = new Woo_Etiqueta_Correios_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks()
    {
        $plugin_admin = new Woo_Etiqueta_Correios_Admin($this->get_plugin_name(), $this->get_version());
//        var_dump(plugin_basename(__FILE__));
//        exit();
        $this->loader->add_filter('plugin_action_links_woo-etiqueta-correios/woo-etiqueta-correios.php', $plugin_admin, 'managePluginLinks');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('bulk_actions-edit-shop_order', $plugin_admin, 'addActionItemBulk');
        $this->loader->add_filter('handle_bulk_actions-edit-shop_order', $plugin_admin, 'handleActionsBulk', 10 , 3);
        $this->loader->add_action('admin_notices', $plugin_admin, 'renderAlert');
        $this->loader->add_action('admin_init', $plugin_admin, 'registerOptionFields');
        $this->loader->add_action('admin_menu', $plugin_admin, 'registerMenu');
//        $this->loader->add_action('current_screen', $plugin_admin, 'general');
    }

    private function define_public_hooks()
    {
//        $plugin_public = new Woo_Etiqueta_Correios_Public($this->get_plugin_name(), $this->get_version());
//        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
//        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    public function run()
    {
        $this->loader->run();
    }

    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    public function get_loader()
    {
        return $this->loader;
    }

    public function get_version()
    {
        return $this->version;
    }

}
