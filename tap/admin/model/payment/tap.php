<?php
namespace Opencart\Admin\Model\Extension\Tap\Payment;

class Tap extends \Opencart\System\Engine\Model {
    public function install() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tap_order` (
                `id` int(11) AUTO_INCREMENT NOT NULL,
                `order_id` int(11) NOT NULL,
                transaction_id varchar(255),
                `response` TEXT,
                PRIMARY KEY(id)
            ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "tap_order`;");
    }

    public function getOrder($order_id) {
        $qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tap_order` WHERE `order_id` = '" . (int)$order_id . "' LIMIT 1");

        if ($qry->num_rows) {
                $order = $qry->row;
                return $order;
        } else {
                return false;
        }
    }
}