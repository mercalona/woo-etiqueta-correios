<?php

class Woo_Etiqueta_Correios_Admin
{

    private $plugin_name;
    private $version;
    private $api_url;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->api_url = 'https://api.mercalona.com/';

    }

    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-etiqueta-correios-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-etiqueta-correios-admin.js', array('jquery'), $this->version, false);
    }

    public function general()
    {
        echo "<pre>";
        var_dump(get_current_screen());
        echo "</pre>";
        exit();
    }

    public function addActionItemBulk($bulk_actions)
    {
        $bulk_actions['woo_etiqueta_correios'] = __('Gerar etiquetas correios', 'woo_etiqueta_correios');
        return $bulk_actions;
    }

    public function handleActionsBulk($redirect_to, $action_name, $post_ids)
    {
        if ('woo_etiqueta_correios' === $action_name) {
            $orderAddresses = $this->getOrdersData($post_ids);
            $response = $this->sendOrdersToMercalona($orderAddresses);
            if ($response->success) {
                $link_id = $response->public_id;
                $message = $response->message;
                $redirect_to = add_query_arg('mercalona_status', 'success', $redirect_to);
                $redirect_to = add_query_arg('mercalona_label_id', $link_id, $redirect_to);
                $redirect_to = add_query_arg('mercalona_message', $message, $redirect_to);
            } else {
                $message = $response->message;
                $redirect_to = add_query_arg('mercalona_status', 'error', $redirect_to);
                $redirect_to = add_query_arg('mercalona_message', $message, $redirect_to);
            }
        }

        return $redirect_to;
    }

    private function sendOrdersToMercalona($addresses)
    {
        $auth = base64_encode(get_option('mercalona_id') . ':' . get_option('mercalona_token'));
        $url = $this->api_url . 'api/order/bulk-create';
        $args = [
            'method' => 'POST',
            'headers' => [
                'Authorization' => "Bearer $auth"
            ],
            'body' => ['orders' => $addresses],
        ];
        $response = wp_remote_post($url, $args);
        $response_body = wp_remote_retrieve_body($response);
        $response_decoded = json_decode($response_body);
        return $response_decoded;
    }

    private function getOrdersData($post_ids)
    {
        $addresses = [];
        foreach ($post_ids as $post_id) {
            $addresses[] = $this->getOrderData($post_id);
        }
        return $addresses;
    }

    private function getOrderData($order_id)
    {


        $order_data = get_post_meta($order_id);
        return [
            'public_id' => $order_id,
            'name' => @$order_data['_shipping_first_name'][0] . ' ' . @$order_data['_shipping_last_name'][0],
            'email' => @$order_data['_billing_email'][0],
            'phone' => @$order_data['_billing_phone'][0],
            'cellphone' => @$order_data['_billing_cellphone'][0],
            'cpf' => @$order_data['_billing_cpf'][0],
            'cnpj' => @$order_data['_billing_cnpj'][0],
            'street' => @$order_data['_shipping_address_1'][0] . ', ' . @$order_data['_shipping_address_2'][0],
            'neighborhood' => @$order_data['_billing_neighborhood'][0],
            'number' => @$order_data['_billing_number'][0],
            'city' => @$order_data['_shipping_city'][0],
            'state' => @$order_data['_shipping_state'][0],
            'postcode' => @$order_data['_shipping_postcode'][0],
            'country' => @$order_data['_shipping_country'][0],
        ];
    }

    public function vDump($thing, $stop = false)
    {
        echo "<pre>";
        var_dump($thing);
        echo "</pre>";
        if ($stop) exit();
    }

    public function renderAlert()
    {
        if (!empty($_REQUEST['mercalona_status'])) {
            $message = __($_REQUEST['mercalona_message']);
            if ($_REQUEST['mercalona_status'] == 'success') {
                $link = $this->api_url . 'api/order-list/label-download/' . strval($_REQUEST['mercalona_label_id']);
                $class = 'notice notice-success';
                printf('<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s">%3$s</a></p></div>', esc_attr($class), esc_html($message), esc_url($link));
            } else {
                $class = 'notice notice-error';
                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
            }
        }
    }

    public function registerMenu()
    {
        add_submenu_page('woocommerce', 'Etiqueta Correios', 'Etiqueta Correios', 'manage_options', 'woo-etiqueta-correios', [$this, 'renderEtiquetaCorreiosConfigPage']);
    }

    public function renderEtiquetaCorreiosConfigPage()
    {
        ?>
        <div>
            <h1>Configurações | Etiqueta Correios | Mercalona</h1>
            <form method="post" action="options.php">
                <?php settings_fields('woo-etiqueta-correios'); ?>
                <hr>
                <p><h4>Configuração de API</h4>Preencha os campos a baixo com os dados de sua conta. <a target="_blank"
                                                                                                        href="https://app.mercalona.com/system/settings">Clique
                    aqui para pegar sua credencial</a></p>
                <hr>
                <table>
                    <tr valign="top">
                        <th class="text-right" scope="row"><label for="mercalona_id">Mercalona ID</label></th>
                        <td>
                            <input class="input-style" type="text" id="mercalona_id" name="mercalona_id"
                                   value="<?php echo esc_attr(get_option('mercalona_id')); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th class="text-right" scope="row"><label for="mercalona_token">Mercalona Token</label></th>
                        <td>
                            <input class="input-style" type="text" id="mercalona_token" name="mercalona_token"
                                   value="<?php echo esc_attr(get_option('mercalona_token')); ?>"/>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <style>
            .text-right {
                text-align: right;
            }

            .input-style {
                width: 250px;
            }
        </style>
        <?php
    }

    public function registerOptionFields()
    {
        register_setting('woo-etiqueta-correios', 'mercalona_id');
        register_setting('woo-etiqueta-correios', 'mercalona_token');
    }

    public function managePluginLinks($links)
    {
        $merca_links = ['<a href="' . get_admin_url() . 'admin.php?page=woo-etiqueta-correios">Configurações</a>'];
        return array_merge($merca_links, $links);
    }


}
