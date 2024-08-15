<?php
namespace Opencart\Admin\Controller\Extension\Tap\Payment;

class Tap extends \Opencart\System\Engine\Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/tap/payment/tap');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_tap', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $data['success'] = $this->session->data['success'];
        }

        if (isset($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
        } else {
                $data['error_warning'] = '';
        }

        if (isset($this->error['testpublicapi_key'])) {
                $data['error_testpublicapi_key'] = $this->error['testpublicapi_key'];
        } else {
                $data['error_testpublicapi_key'] = '';
        }

        if (isset($this->error['testsecretapi_key'])) {
                $data['error_testsecretapi_key'] = $this->error['testsecretapi_key'];
        } else {
                $data['error_testsecretapi_key'] = '';
        }

         if (isset($this->error['livepublicapi_key'])) {
                $data['error_livepublicapi_key'] = $this->error['livepublicapi_key'];
        } else {
                $data['error_livepublicapi_key'] = '';
        }

         if (isset($this->error['livesecretapi_key'])) {
                $data['error_livesecretapi_key'] = $this->error['livesecretapi_key'];
        } else {
                $data['error_livesecretapi_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/tap/payment/tap', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/tap/payment/tap', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
        
        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->getNewOrderStatuses($this->model_localisation_order_status->getOrderStatuses());

       if (isset($this->request->post['payment_tap_testpublicapi_key'])) {
                $data['payment_tap_testpublicapi_key'] = $this->request->post['payment_tap_testpublicapi_key'];
        } else {
                $data['payment_tap_testpublicapi_key'] = $this->config->get('payment_tap_testpublicapi_key');
        }

        if (isset($this->request->post['payment_tap_testsecretapi_key'])) {
                $data['payment_tap_testsecretapi_key'] = $this->request->post['payment_tap_testsecretapi_key'];
        } else {
                $data['payment_tap_testsecretapi_key'] = $this->config->get('payment_tap_testsecretapi_key');
        }

         if (isset($this->request->post['payment_tap_livepublicapi_key'])) {
                $data['payment_tap_livepublicapi_key'] = $this->request->post['payment_tap_livepublicapi_key'];
        } else {
                $data['payment_tap_livepublicapi_key'] = $this->config->get('payment_tap_livepublicapi_key');
        }

         if (isset($this->request->post['payment_tap_livesecretapi_key'])) {
                $data['payment_tap_livesecretapi_key'] = $this->request->post['payment_tap_livesecretapi_key'];
        } else {
                $data['payment_tap_livesecretapi_key'] = $this->config->get('payment_tap_livesecretapi_key');
        }

        if (isset($this->request->post['payment_tap_prefix'])) {
                $data['payment_tap_prefix'] = $this->request->post['payment_tap_prefix'];
        } else {
                $data['payment_tap_prefix'] = $this->config->get('payment_tap_prefix');
        }

        if (isset($this->request->post['payment_tap_standard_geo_zone_id'])) {
                $data['payment_tap_standard_geo_zone_id'] = $this->request->post['payment_tap_standard_geo_zone_id'];
        } else {
                $data['payment_tap_standard_geo_zone_id'] = $this->config->get('payment_tap_standard_geo_zone_id');
        }

        if (isset($this->request->post['payment_tap_order_status_id'])) {
                $data['payment_tap_order_status_id'] = $this->request->post['payment_tap_order_status_id'];
        } else {
                $data['payment_tap_order_status_id'] = $this->config->get('payment_tap_order_status_id');
                if (!$data['payment_tap_order_status_id']) {
                    $data['payment_tap_order_status_id'] = 2;
                }
        }

        if (isset($this->request->post['payment_tap_status'])) {
                $data['payment_tap_status'] = $this->request->post['payment_tap_status'];
        } else {
                $data['payment_tap_status'] = $this->config->get('payment_tap_status');
        }
        
        if (isset($this->request->post['payment_tap_post_url'])) {
                $data['payment_tap_post_url'] = $this->request->post['payment_tap_post_url'];
        } else {
                $data['payment_tap_post_url'] = $this->config->get('payment_tap_post_url');
                }
        
        if (isset($this->request->post['payment_tap_charge_mode'])) {
                $data['payment_tap_charge_mode'] = $this->request->post['payment_tap_charge_mode'];
        } else {
                $data['payment_tap_charge_mode'] = $this->config->get('payment_tap_charge_mode');
                }

        if (isset($this->request->post['payment_tap_ui_mode'])) {
                $data['payment_tap_ui_mode'] = $this->request->post['payment_tap_ui_mode'];
        } else {
                $data['payment_tap_ui_mode'] = $this->config->get('payment_tap_ui_mode');
                }

        if (isset($this->request->post['payment_tap_title'])) {
                $data['payment_tap_title'] = $this->request->post['payment_tap_title'];
        } else {
                $data['payment_tap_title'] = $this->config->get('payment_tap_title');
        }


        if (isset($this->request->post['payment_tap_desc'])) {
                $data['payment_tap_desc'] = $this->request->post['payment_tap_desc'];
        } else {
                $data['payment_tap_desc'] = $this->config->get('payment_tap_desc');
        }

        if (isset($this->request->post['payment_tap_debug'])) {
                $data['payment_tap_debug'] = $this->request->post['payment_tap_debug'];
        } else {
                $data['payment_tap_debug'] = $this->config->get('payment_tap_debug');
        }

        //Defaults
        if (empty($data['payment_tap_title'])) {
            $data['payment_tap_title'] = 'Tap Payments';
        }
        if (empty($data['payment_tap_desc'])) {
            $data['payment_tap_desc'] = 'Pay Via Tap Payments.';
        }
        if (empty($data['payment_tap_prefix'])) {
            $data['payment_tap_prefix'] = 'Order #';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view('extension/tap/payment/tap', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/tap/payment/tap')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        if (isset($this->request->post['payment_tap_testpublicapi_key']) && !$this->request->post['payment_tap_testpublicapi_key']) {
                $this->error['testpublicapi_key'] = $this->language->get('error_testpublicapi_key');
        }

        if (isset($this->request->post['payment_tap_testsecretapi_key']) && !$this->request->post['payment_tap_testsecretapi_key']) {
                $this->error['testsecretapi_key'] = $this->language->get('error_testsecretapi_key');
        }

        if (isset($this->request->post['payment_tap_livepublicapi_key']) && !$this->request->post['payment_tap_livepublicapi_key']) {
                $this->error['livepublicapi_key'] = $this->language->get('error_livepublicapi_key');
        }

        if (isset($this->request->post['payment_tap_livesecretapi_key']) && !$this->request->post['payment_tap_livesecretapi_key']) {
                $this->error['livesecretapi_key'] = $this->language->get('error_livesecretapi_key');
        }
        return !$this->error;
    }
    
    public function getNewOrderStatuses($statuses) {
        $result = array();
        $skipStatuses = array(
            'Canceled',
            'Canceled Reversal',
            'Chargeback',
            'Denied',
            'Expired',
            'Failed',
            'Refunded',
            'Reversed',
            'Voided'
        );
        foreach ($statuses as $key => $status) {
            if (!in_array($status['name'], $skipStatuses)) {
                $result[] = $status;
            }
        }
        return $result;
    }
    
    public function install() {
        $this->load->model('extension/tap/payment/tap');
        $this->model_extension_tap_payment_tap->install();
    }

    public function uninstall() {
            $this->load->model('extension/tap/payment/tap');
            $this->model_extension_tap_payment_tap->uninstall();
    }
}