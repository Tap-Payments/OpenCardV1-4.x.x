<?php
namespace Opencart\Catalog\Model\Extension\Tap\Payment;
class Tap extends \Opencart\System\Engine\Model {
    public function getMethod(array $address): array {
        $this->load->language('extension/tap/payment/tap');

        if ($this->cart->hasSubscription()) {
            $status = false;
        } elseif (!$this->cart->hasShipping()) {
            $status = false;
        } elseif (!$this->config->get('payment_tap_standard_geo_zone_id')) {
            $status = true;
        } else {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_tap_standard_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

            if ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        }

        $method_data = [];

        if ($status) {
            $option_data['tap'] = [
                'code' => 'tap.tap',
                'name' => $this->config->get('payment_tap_title')
            ];

            $method_data = [
                'code'       => 'tap',
                'title'      => $this->config->get('payment_tap_title'),
                'option'     => $option_data,
                'sort_order' => $this->config->get('payment_cod_sort_order')
            ];
        }

        return $method_data;
    }
}
