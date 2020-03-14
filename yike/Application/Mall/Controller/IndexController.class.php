<?php
namespace Mall\Controller;
use Think\Controller;
use Think\Page;

class IndexController extends Controller {
    public function info() {
        $this->display();
    }
        public function index(){
        $db = M();

        $sql_text = 'select fit_source.*, fit_source_class.text as source_class_text, fit_photo.url, fit_photo.pic_class from fit_source left join fit_source_class on fit_source.source_class = fit_source_class.id left join fit_photo on fit_source.photo_id = fit_photo.id where fit_source.source_class = 7 order by fit_source.sort';

        $list_1 = $db->query($sql_text);

        $sql_text = 'select fit_source.*, fit_source_class.text as source_class_text, fit_photo.url, fit_photo.pic_class from fit_source left join fit_source_class on fit_source.source_class = fit_source_class.id left join fit_photo on fit_source.photo_id = fit_photo.id where fit_source.source_class = 10 order by fit_source.sort limit 3';

        $list_2 = $db->query($sql_text);

        $this->assign('data', array(
            'list_1' => $list_1,
            'list_2' => $list_2
        ));


//        $orderModel = M('order');
//        $orderDetailModel = M('order_detail');
//        $loginModel = M('login');
//        $addressModel = M('address');
//        $itemModel = M('item');
//        $photoModel = D('photo');
//
//        $order_condition = array(
//            'user_id' => 181
//        );
//
//        $orders = $orderModel->where($order_condition)->order('create_at desc')->select();
//
//        $list = array();
//
//        foreach ($orders as $o) {
//            $address_condition = array(
//                'id' => $o['address_id']
//            );
//
//            $address = $addressModel->where($address_condition)->find();
//
////            echo fit_api(true, 0, '获取成功!', $addressModel->getLastSql());
//
//            $o['address'] = $address;
//
//            $list[] = $o;
//        }
//
//        $list2 = array();
//
//        foreach ($list as $o) {
//            $detail_condition = array(
//                'order_id' => $o['id']
//            );
//
//            $details = $orderDetailModel->where($detail_condition)->select();
//
//            $details_list = array();
//
//            foreach ($details as $d) {
//                $product = $itemModel->find($d['item_id']);
//
//                $show_photo = $photoModel->where('id in ('. $product['show_photo'] . ')')->find();
//
//                $product['show_photo'] = $show_photo;
//
//                $d['product'] = $product;
//
//                $details_list[] = $d;
//            }
//
//            $o['details'] = $details_list;
//
//            $list2[] = $o;
//        }
//        print_r($list2); exit;

        $this->display();
    }

    public function mall() {
        $itemModel = D('item');
        $photoModel = D('photo');
        $db = M();

        $sql_text = 'select fit_source.*, fit_source_class.text as source_class_text, fit_photo.url, fit_photo.pic_class from fit_source left join fit_source_class on fit_source.source_class = fit_source_class.id left join fit_photo on fit_source.photo_id = fit_photo.id where fit_source.source_class = 12 order by fit_source.sort';

        $list_1 = $db->query($sql_text);

        $product = $itemModel->select();
        $list = array();

        foreach ($product as $itm) {
            $show_photo = $photoModel->where('id in ('. $itm['show_photo'] . ')')->select();
            $content_photo = $photoModel->where('id in ('. $itm['content_photo'] . ')')->select();

            $itm['show_photo'] = $show_photo;
            $itm['content_photo'] = $content_photo;

            $list[] = $itm;
        }

        $this->assign(data, array(
            'list' => $list,
            'list_1' => $list_1
        ));

        $this->display();
    }

    public function product() {
        $itemModel = D('item');
        $photoModel = D('photo');
        $colorModel = D('color');
        $db = M();

        $id = I('id');

        if (!$id) {
            Header("Location: /index/mall");
            exit;
        }

        $product = $itemModel->find($id);

        $color = $colorModel->where('id in ('. $product['color'] .')')->select();
        $show_photo = $photoModel->where('id in ('. $product['show_photo'] . ')')->select();
        $content_photo = $photoModel->where('id in ('. $product['content_photo'] . ')')->select();

        $product['color'] = $color;
        $product['show_photo'] = $show_photo;
        $product['content_photo'] = $content_photo;

        $is_sale = 1;

        if ((int)$id < 22) {
            $sql_text = 'select count(fit_order_detail.num) as num from fit_order_detail left join fit_order on fit_order_detail.order_id = fit_order.id where item_id = '. $id .' and (fit_order.state > 10 and fit_order.state < 80)';//

            $sales = $db->query($sql_text);

            $sale_count = $sales[0]['num'];

            if ((int)$id < 18) {
//                echo('小于18, id:'.$id.', 销售数量:'. $sale_count);
                if ((int)$sale_count > 100) {
                    $is_sale = 0;
                }
            }
            else {
//                echo('大于17, id:'.$id.', 销售数量:'. $sale_count);
                if ((int)$sale_count > 50) {
                    $is_sale = 0;
                }
            }
        }
//        print_r($sale_count[0]['num']); exit;

        $this->assign(data, array(
            'product' => $product,
            'is_sale' => $is_sale
        ));

        $this->display();
    }

    public function ecoer() {
        $this->display();
    }

    public function scooter() {
        $bikeInfoModel = D('bike_info');
        $photoModel = D('photo');

        $info = $bikeInfoModel->find(1);

        $photo_ids = explode(',', $info['photo_ids']);

        $str_condition = "";

        foreach ($photo_ids as $p) {
            if ($str_condition) {
                $str_condition = $str_condition . ',';
            }

            $str_condition = $str_condition . "'" . $p . "'";
        }

        $photo_list = $photoModel->where("id in (". $str_condition .")")->select();

        $str_models = explode(',', $info['models']);

        for ($index = 0; $index < count($photo_list); $index++) {
            $photo_list[$index]['text'] = $str_models[$index];
        }

//        print_r($photo_list); exit;

        $this->assign('data', array(
            'list' => $photo_list
        ));

        $this->display();
    }

    public function cart() {
        $loginModel = D('login');

        $access = I('access');

        if (!$access) {
            header("Location: /index/login"); exit;
        }

//        $login_condition = array(
//            'key' => $access
//        );

        $str_condition = "`key` = '". $access ."' and expire_time > '". date('Y-m-d H:i:s', time()). "'";

        $login = $loginModel->where($str_condition)->select();

        if (!$login || count($login) < 1) {
            header("Location: /index/login"); exit;
        }

        $this->display();
    }

    public function checkout() {
        $orderModel = M('order');
        $orderDetailModel = M('order_detail');
        $addressModel = M('address');
        $itemModel = M('item');
        $photoModel = M('photo');
        $loginModel = M('login');
        $transport_priceModel = M('transport_price');

        $access = I('access');

        if (!$access) {
            header("Location: /index/login"); exit;
        }

        $str_condition = "`key` = '". $access ."' and expire_time > '". date('Y-m-d H:i:s', time()). "'";

//        exit($str_condition);

        $login = $loginModel->where($str_condition)->select();

        if (!$login || count($login) < 1) {
            header("Location: /index/login"); exit;
        }

        $order_id = I('id');

        $order_condition = array(
            'id' => $order_id
        );

        $order = $orderModel->where($order_condition)->find();

        $user_id = $order['user_id'];

        $login = $loginModel->where('user_id = '. $user_id)->order('login_time desc')->limit(1)->select();

        $address_condition = array(
            'user_id' => $user_id,
            'is_delete' => 0
        );

        $address = $addressModel->where($address_condition)->select();

        $freight = 0;

        foreach ($address as $addr) {
            if ($addr['is_default'] == '1') {

                $price = $transport_priceModel->where("type=1 and region like '%". $addr['province'] ."%'")->find();

                $freight = (int)$price['price'];

                break;
            }
        }

        $detail_condition = array(
          'order_id' => $order_id
        );

        $details = $orderDetailModel->where($detail_condition)->select();

        $count = 0;

        $list = array();

        foreach ($details as $d) {
            $item_condition = array(
              'id' => $d['item_id']
            );

            $product = $itemModel->find($d['item_id']);

            $show_photo = $photoModel->where('id in ('. $product['show_photo'] .')')->select();

            $product['show_photo'] = $show_photo;

            $d['product'] = $product;

            $list[] = $d;

            $count = $count + (Int)$d['num'];
        }

        $order['count'] = $count;

        $order['details'] = $list;

        $this->assign(data, array(
            'address' => $address,
            'order' => $order,
            'login_id' => $login[0]['key'],
            'freight' => $freight
        ));

//        print_r($freight); exit;

        $this->display();
    }

    public function success() {
        $this->display();
    }

    public function register() {
        $this->display();
    }

    public function user_address() {
        $loginModel = D('login');

        $access = I('access');

        if (!$access) {
            header("Location: /index/login"); exit;
        }
//
//        $login_condition = array(
//            'key' => $access
//        );

        $str_condition = "`key` = '". $access ."' and expire_time > '". date('Y-m-d H:i:s', time()). "'";

        $login = $loginModel->where($str_condition)->select();

        if (!$login || count($login) < 1) {
            header("Location: /index/login"); exit;
        }

        $this->display();
    }

    public function user_order() {
        $loginModel = D('login');

        $access = I('access');

        if (!$access) {
            header("Location: /index/login"); exit;
        }

//        $login_condition = array(
//            'key' => $access
//        );

        $str_condition = "`key` = '". $access ."' and expire_time > '". date('Y-m-d H:i:s', time()). "'";

//        exit($str_condition);

        $login = $loginModel->where($str_condition)->select();

        if (!$login || count($login) < 1) {
            header("Location: /index/login"); exit;
        }

        $this->display();
    }

    public function user_order_detail() {
        $orderModel = M('order');
        $orderDetailModel = M('order_detail');
        $addressModel = M('address');
        $itemModel = M('item');
        $photoModel = D('photo');
        $loginModel = D('login');

        $id = I('id');
        $access = I('access');

        if (!$access) {
            header("Location: /index/login"); exit;
        }

//        $login_condition = array(
//            'key' => $access
//        );

        $str_condition = "`key` = '". $access ."' and expire_time > '". date('Y-m-d H:i:s', time()). "'";

        $login = $loginModel->where($str_condition)->select();

        if (!$login || count($login) < 1) {
            header("Location: /index/login"); exit;
        }

        $order = $orderModel->find($id);

        $address_condition = array(
            'id' => $order['address_id']
        );

        $address = $addressModel->where($address_condition)->select();

        $order['address'] = $address[0];

        $detail_condition = array(
            'order_id' => $order['id']
        );

        $details = $orderDetailModel->where($detail_condition)->select();

        $details_list = array();

        foreach ($details as $d) {
            $product = $itemModel->find($d['item_id']);

            $show_photo = $photoModel->where('id in ('. $product['show_photo'] . ')')->find();
//
            $product['show_photo'] = $show_photo;

            $d['product'] = $product;

            $details_list[] = $d;
        }

        $order['details'] = $details_list;

        $this->assign(data, array(
            'order' => $order
        ));

//        print_r($order); exit;

        $this->display();
    }

    public function user_pwd() {
        $loginModel = D('login');

        $access = I('access');

        if (!$access) {
            header("Location: /index/login"); exit;
        }

//        $login_condition = array(
//            'key' => $access
//        );

        $str_condition = "`key` = '". $access ."' and expire_time > '". date('Y-m-d H:i:s', time()). "'";

        $login = $loginModel->where($str_condition)->select();

        if (!$login || count($login) < 1) {
            header("Location: /index/login"); exit;
        }

        $this->display();
    }

    public function upgrade() {
        $this->display();
    }

    public function api_test() {
        $this->display();
    }

    public function order_pay() {
        $this->display();
    }

    public function notify_url() {
        import('Org.Fit.alipay.lib.alipay_notify');

        $alipay_config['partner']		= '2088521154312352';
        $alipay_config['seller_id']	= $alipay_config['partner'];
        $alipay_config['key']			= 'm6p3rv3bxc69fze0udbw5i5g2wd1s2aa';
        $alipay_config['notify_url'] = "http://mall.ecomoter.com/index/notify_url";
        $alipay_config['return_url'] = "http://mall.ecomoter.com/index/return_url";
        $alipay_config['sign_type']    = strtoupper('MD5');
        $alipay_config['input_charset']= strtolower('utf-8');
        $alipay_config['cacert']    = '/var/www/yike/Public/alipay/cacert.pem';
        $alipay_config['transport']    = 'http';
        $alipay_config['payment_type'] = "1";
        $alipay_config['service'] = "create_direct_pay_by_user";
        $alipay_config['anti_phishing_key'] = "";
        $alipay_config['exter_invoke_ip'] = "";

        //计算得出通知验证结果

        $alipayNotify = new \AlipayNotify($alipay_config);

        $verify_result = $alipayNotify->verifyNotify();

        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号
            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];

            $logModel = M('log');

            $log = array(
                id => date('YmdHis', time()),
                msg => '支付宝订单号:'. $trade_no .'; 系统订单号:'.$out_trade_no,
                type => 'order',
                create_date => date('Y-m-d H:i:s', time())
            );

            $logModel->add($log);

            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                $orderModel = D('order');

                $data = array(
                    'state' => 20
                );

                $orderModel->where("id='". $out_trade_no ."'")->save($data);

                //调试用，写文本函数记录程序运行情况是否正常
//                logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $orderModel = D('order');

                $data = array(
                    'state' => 20
                );

                $orderModel->where("id='". $out_trade_no ."'")->save($data);

                //调试用，写文本函数记录程序运行情况是否正常
//                logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";		//请不要修改或删除
        }
        else {
            echo "fail";
        }


        $this->display();
    }

    public function return_url() {
//        import('Org.Fit.alipay.config.php');
        import('Org.Fit.alipay.lib.alipay_notify');

        $alipay_config['partner']		= '2088521154312352';
        $alipay_config['seller_id']	= $alipay_config['partner'];
        $alipay_config['key']			= 'm6p3rv3bxc69fze0udbw5i5g2wd1s2aa';
        $alipay_config['notify_url'] = "http://mall.ecomoter.com/index/notify_url";
        $alipay_config['return_url'] = "http://mall.ecomoter.com/index/return_url";
        $alipay_config['sign_type']    = strtoupper('MD5');
        $alipay_config['input_charset']= strtolower('utf-8');
        $alipay_config['cacert']    = '/var/www/yike/Public/alipay/cacert.pem';
        $alipay_config['transport']    = 'http';
        $alipay_config['payment_type'] = "1";
        $alipay_config['service'] = "create_direct_pay_by_user";
        $alipay_config['anti_phishing_key'] = "";
        $alipay_config['exter_invoke_ip'] = "";


        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);

        $verify_result = $alipayNotify->verifyReturn();

        //验证成功
        if($verify_result) {
            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];

            //支付宝交易号
            $trade_no = $_GET['trade_no'];

            //交易状态
            $trade_status = $_GET['trade_status'];

            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
//                $orderModel = D('order');
//
//                $data = array(
//                    'state' => 20
//                );
//
//                $orderModel->where("id='". $out_trade_no ."'")->save($data);
            }
            else {

            }

            echo "支付成功, 请关闭当前页面!";
        }
        else {
            echo "验证失败";
        }


        $this->display();
    }

    public function apply() {
        $contentModel = D('content');

        $item = $contentModel->find(4);

        $this->assign('data', array(
            'item' => $item
        ));

        $this->display();
    }

    public function apply_table() {
        $this->display();
    }

    public function authorized() {
        $storeModel = M();

        $str_sql = "select * from fit_point where is_use=1 and join_type like '%经销商%'";

        $stores = $storeModel->query($str_sql);

//        print_r($stores); exit;

        $this->assign('data', array(
            'list' => $stores
        ));

        $this->display();
    }

    public function serverman() {
        $storeModel = M();

        $str_sql = "select * from fit_point where is_use=1 and join_type like '%服务商%'";

        $stores = $storeModel->query($str_sql);

//        print_r($stores); exit;

        $this->assign('data', array(
            'list' => $stores
        ));

        $this->display();
    }

    public function server() {
        $contentModel = D('content');

        $content = $contentModel->find(2);

        $this->assign('data', array(
            'content' => $content
        ));

        $this->display();
    }

    public function buy_question() {
        $contentModel = D('content');

        $content = $contentModel->find(6);

        $this->assign('data', array(
            'content' => $content
        ));

        $this->display();
    }

    public function test_self() {
        $contentModel = D('content');

        $content = $contentModel->find(3);

        $this->assign('data', array(
            'content' => $content
        ));

        $this->display();
    }

    public function repair_log() {
        $id = I('id');

        $this->assign('data', array(
            'car_id' => $id
        ));

        $this->display();
    }

    public function lose_log() {
        $id = I('id');

        $this->assign('data', array(
            'car_id' => $id
        ));

        $this->display();
    }

    public function user_info(){
        $loginModel = D('login');

        $access = I('access');

        if (!$access) {
            header("Location: /index/login"); exit;
        }

        $str_condition = "`key` = '". $access ."' and expire_time > '". date('Y-m-d H:i:s', time()). "'";

        $login = $loginModel->where($str_condition)->select();

        if (!$login || count($login) < 1) {
            header("Location: /index/login"); exit;
        }

        $this->display();
    }

    /**
     *
     * 验证码生成
     */
    public function verify(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 18;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 130;
        $Verify->imageH = 50;
        //$Verify->expire = 600;
        $Verify->entry();
    }
}