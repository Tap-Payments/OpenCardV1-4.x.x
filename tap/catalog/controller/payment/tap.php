<?php
namespace Opencart\Catalog\Controller\Extension\tap\Payment;

class tap extends \Opencart\System\Engine\Controller {
	public function index() {
            $this->load->language('extension/tap/payment/tap');
            $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        //$data['amount'] = $order_info['total'];
        $data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $data['products'] = $this->cart->getProducts();
        $data['order_id'] = $this->session->data['order_id'];
        $data['test_public_key'] = $this->config->get('payment_tap_testpublicapi_key');
        $data['entry_post_url'] = $this->url->link('extension/tap/payment/tap|webhook', 'order_id='.$data['order_id'], true);

        $data['entry_ui_mode'] = $this->config->get('payment_tap_ui_mode');
            //var_dump($this->config->get('payment_tap_test'));exit;    
        if ($this->config->get('payment_tap_debug'))
            {
                $active_sk = $this ->config->get('payment_tap_testsecretapi_key');
                $active_pk = $this ->config->get('payment_tap_testpublicapi_key');    
            }
            else{
                $active_sk = $this ->config->get('payment_tap_livesecretapi_key');
                $active_pk = $this ->config->get('payment_tap_livepublicapi_key');
            }
            $data ['active_sk'] = $active_sk;
            $data ['active_pk'] = $active_pk;
        $data['itemprice1'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $data['itemname1'] ='Order ID - '.$order_info['order_id'];
        $data['currencycode'] = $order_info['currency_code'];
        $data['ordid'] = $order_info['order_id'];
        $data['entey_charge_mode'] = $this->config->get('payment_tap_charge_mode');
        $data['cstemail'] = $order_info['email'];
        $data['cstname'] = html_entity_decode($order_info['firstname'], ENT_QUOTES, 'UTF-8');
        $data['cstlastname'] = html_entity_decode($order_info['lastname'], ENT_QUOTES, 'UTF-8');
         if (empty($data['cstmobile'])) {
            $data['cstmobile'] = '00000000';
         }
         else{
            $data['cstmobile'] = $order_info['telephone'];
         }
        $data['cstmobile'] = $order_info['telephone'];
        $data['cntry'] = $order_info['shipping_iso_code_2'];
                $counteries = [
                [
                    "country" => "Egypt", 
                    "code" => "20", 
                    "iso" => "EG" 
                ],
                [
                   "country" => "Kuwait", 
                   "code" => "965", 
                   "iso" => "KW" 
                ],
                [
                   "country" => "Saudi Arabia", 
                   "code" => "966", 
                   "iso" => "SA" 
                ],
                [
                    "country" => "United Arab Emirates", 
                    "code" => "971", 
                    "iso" => "AE" 
                ],
                [
                   "country" => "Bahrain", 
                   "code" => "973", 
                   "iso" => "BH" 
                ],
                [
                   "country" => "Oman", 
                   "code" => "968", 
                   "iso" => "OM" 
                ],
                [
                   "country" => "Qatar", 
                   "code" => "974", 
                   "iso" => "QA" 
                ],
                [
                   "country" => "Jordan", 
                   "code" => "962", 
                   "iso" => "JD" 
                ],
                [
                   "country" => "Lebnon", 
                   "code" => "961", 
                   "iso" => "LB" 
                ]


        ];
        $country_code = '';
        foreach($counteries as $country) {
            if($country['iso'] == $data['cntry']) {
                $country_code = $country['code'];
            }
        }
        $ref = '';
        $data['cntry'] = $country_code;
        
        if($data['currencycode']=="KWD"){
            $Total_price = number_format((float)$data['amount'], 3, '.', '');
        }
        else{
            $Total_price = number_format((float)$data['amount'], 2, '.', '');
        }

        $amount = number_format((float)$data['amount'], 2, '.', '');
        $Hash = 'x_publickey'.$active_pk.'x_amount'.$Total_price.'x_currency'.$data['currencycode'].'x_transaction'.$ref.'x_post'.$data['entry_post_url'];
        $data['hashstring'] = hash_hmac('sha256', $Hash, $active_sk);
        $data['returnurl'] = $this->url->link('extension/tap/payment/tap|callback');

         //echo '<pre>'; var_dump($data);exit;
            return $this->load->view('extension/tap/payment/tap', $data);
	}

    public function callback() {
        $this->load->model('extension/tap/payment/tap');
        $this->load->language('extension/tap/payment/tap');
        if (isset($this->request->get['tap_id'])) {
            $tap_id = $this->request->get['tap_id'];
            // $order_info = $this->model_checkout_order->getOrder(17);
            //$order_id = $this->session->data['order_id'];
          
        } 
        else {
            $order_id = 0;
            $this->response->redirect($this->url->link('checkout/cart'));
        }
        if ($this->config->get('payment_tap_debug'))
            {
                $active_sk = $this ->config->get('payment_tap_testsecretapi_key');
            }
        else{
                $active_sk = $this ->config->get('payment_tap_livesecretapi_key');
            }
        $charge_mode = $this->config->get('payment_tap_charge_mode');
        if ($charge_mode == 'charge') {
            $transaction_url = 'https://api.tap.company/v2/charges/';
        }
        else {
            $transaction_url = 'https://api.tap.company/v2/authorize/';
        }

                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $transaction_url.$tap_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "{}",
                CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer ".$active_sk
                ),
            )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response);
        }
        $order_id = $response->reference->order;
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $order_total = $amount;
        $order_currency = $order_info['currency_code'];
        $response_currency = $response->currency;
        if ($order_currency == "KWD" || $order_currency == "BHD" || $order_currency=="OMR" || $order_currency == 'JOD'){
            $order_total = number_format((float)$order_total, 3, '.', '');
        }
        else {
            $order_total = number_format((float)$order_total, 2, '.', '');
        }
        $charge_mode = $this->config->get('payment_tap_charge_mode');

        if (($order_total == $response->amount) && ($order_currency == $response_currency)) { 
            if ($order_info && ($response->status == 'CAPTURED' || $response->status == 'AUTHORIZED')) {
                $error = '';
                $comment = 'Tap payment successful'.("<br>").('ID').(':'). ($_GET['tap_id'].("<br>").('Payment Type :') . ($response->source->payment_method).("<br>").('Payment Ref:'). ($response->reference->payment));
                $this->model_checkout_order->addHistory($order_id, $this->config->get('payment_tap_order_status_id'), $comment);

                $this->response->redirect($this->url->link('checkout/success'));
            } 
            else {
                $error = $this->language->get('text_unable');
                $this->model_checkout_order->addHistory($order_id, 10,'Transaction Failed');
            }
        }
        else {
            if ($charge_mode == 'charge' && $response->status == 'CAPTURED') {
                $refund_url = "https://api.tap.company/v2/refunds/";
                $refund_object["charge_id"]                 = $tap_id;
                $refund_object["amount"]   = $response->amount;
                $refund_object["currency"]               = $response_currency;
                $refund_object["reason"]           = "Order currency and response currency mismatch(fraudulent)";
                $refund_object["post_url"] = ""; 
                
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $refund_url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => json_encode($refund_object),
                        CURLOPT_HTTPHEADER => array(
                                "authorization: Bearer ".$active_sk,
                                "content-type: application/json"
                        ),
                    )
                );

                $response = curl_exec($curl);
                $response = json_decode($response);
                $err = curl_error($curl);
                curl_close($curl);
                $this->model_checkout_order->addHistory($order_id, 8, $refund_object["reason"].'---Refunded---'.$response->id);
                $error = $this->language->get('text_unable');
            }
            if ($charge_mode == 'Authorize' && $response->status == 'AUTHORIZED') {
                $void_url = 'https://api.tap.company/v2/authorize/'.$tap_id.'/void';
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $void_url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{}",
                        CURLOPT_HTTPHEADER => array(
                                "authorization: Bearer ".$active_sk,
                                "content-type: application/json"
                        ),
                    )
                );

                $response = curl_exec($curl);
                $response = json_decode($response);
                $err = curl_error($curl);
                curl_close($curl);
                $this->model_checkout_order->addHistory($order_id, 8,'---Void---'.$response->id);
                $error = $this->language->get('text_unable');
            }
            
        }
        
    



        // $Comment = 'Tap payment successful'.("<br>").('ID').(':'). ($_GET['tap_id'].("<br>").('Payment Type :') . ($response->source->payment_method).("<br>").('Payment Ref:'). ($response->reference->payment));
        // if ($order_info && $response->status == 'CAPTURED') {
        //     $this->load->model('checkout/order');
        //     $error = '';
        //     //$this->model_checkout_order->addOrderHistory($order_id, 0);
        //      $this->model_checkout_order->addHistory($order_id, $this->config->get('payment_tap_order_status_id'), $Comment);
        //     $this->response->redirect($this->url->link('checkout/success'));
        // } 
        // else {
        //     $error = $this->language->get('text_unable');
        // }
        if ($error) {
            $data['breadcrumbs'] = array();
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_basket'),
                'href' => $this->url->link('checkout/cart')
            );
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_checkout'),
                'href' => $this->url->link('checkout/checkout', '', 'SSL')
            );
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_failed'),
                'href' => $this->url->link('checkout/success')
            );
            $data['heading_title'] = $this->language->get('text_failed');
            $data['text_message'] = sprintf($this->language->get('text_failed_message'), $error, $this->url->link('information/contact'));
            $data['button_continue'] = $this->language->get('button_continue');
            $data['continue'] = $this->url->link('common/home');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');
            if (isset($this->request->post['payment_tap_charge_mode'])) {
            $data['payment_tap_charge_mode'] = $this->request->post['payment_tap_charge_mode'];
            } else {
            $data['payment_tap_charge_mode'] = $this->config->get('payment_tap_charge_mode');
            }
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success')) {
                echo 'in dir template';exit;
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/success', $data));
            } else {
                $this->response->setOutput($this->load->view('common/success', $data));
            }
        } 
    }

    public function webhook() {
         $response = json_decode(file_get_contents('php://input'));
         $headers = apache_request_headers();
            $orderid = $response->reference->order;
            $status = $response->status;
            $charge_id = $response->id;
            $Comment = 'Tap payment successful'.("<br>").('ID').(':'). ($charge_id.("<br>").('Payment Type :') . ($response->source->payment_method).("<br>").('Payment Ref:'). ($response->reference->payment));
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($orderid);
            if ($order_info && $status == 'CAPTURED') {
                $error = '';
                $order_status_id = $this->config->get('payment_tap_order_status_id');
                $this->model_checkout_order->addOrderHistory($orderid, $order_status_id, $Comment);
            }
            if($order_info && $status == 'DECLINED' || $response->status == 'CANCELLED' || $response->status == 'FAILED' || $response->status == 'PENDING' || $response->status == 'ERROR' ){
                $error = '';
                $Comment = 'Tap payment Failed'.("<br>").('ID').(':'). ($charge_id.("<br>").('Payment Type :') . ($response->source->payment_method).("<br>").('Payment Ref:'). ($response->reference->payment));
                $order_status_id = "1";
                $this->model_checkout_order->addOrderHistory($orderid, $order_status_id, $Comment);
                
        }

        }
	
}