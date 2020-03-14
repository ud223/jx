<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/16
 * Time: 13:32
 */

namespace Mall\Controller;
use Think\Controller;

class MobileController extends Controller {
    private $app_id = 'wxf044a5c986c9aa5a';
    private $app_secret = 'ce9a1a362fd5a1faeb4c28d40becb675';
    private $access_token = '';

    public function index() {
        $db = M();

        $sql_text = 'select fit_source.*, fit_source_class.text as source_class_text, fit_photo.url, fit_photo.pic_class from fit_source left join fit_source_class on fit_source.source_class = fit_source_class.id left join fit_photo on fit_source.photo_id = fit_photo.id where fit_source.source_class = 13 order by fit_source.sort';

        $list_1 = $db->query($sql_text);

        $sql_text = 'select fit_source.*, fit_source_class.text as source_class_text, fit_photo.url, fit_photo.pic_class from fit_source left join fit_source_class on fit_source.source_class = fit_source_class.id left join fit_photo on fit_source.photo_id = fit_photo.id where fit_source.source_class = 14 order by fit_source.sort';

        $list_2 = $db->query($sql_text);

        $sql_text = 'select fit_source.*, fit_source_class.text as source_class_text, fit_photo.url, fit_photo.pic_class from fit_source left join fit_source_class on fit_source.source_class = fit_source_class.id left join fit_photo on fit_source.photo_id = fit_photo.id where fit_source.source_class = 15 order by fit_source.sort';

        $list_3 = $db->query($sql_text);

        $sql_text = 'select fit_source.*, fit_source_class.text as source_class_text, fit_photo.url, fit_photo.pic_class from fit_source left join fit_source_class on fit_source.source_class = fit_source_class.id left join fit_photo on fit_source.photo_id = fit_photo.id where fit_source.source_class = 16 order by fit_source.sort';

        $list_4 = $db->query($sql_text);

        $this->assign('data', array(
            'list_1' => $list_1,
            'list_2' => $list_2,
            'list_3' => $list_3,
            'list_4' => $list_4
        ));

//        print_r($list_1); exit;

        $this->display();
    }

    #region 微信用户方法

    public function reg() {
        $code = I('code');
//        $tmp_web_url = I('web_url');
//        $web_url = urldecode($tmp_web_url);

//        if ($web_url) {
//            if ($web_url == "http://mall.ecomoter.com/mobile/") {
//                $web_url = "/mobile/index";
//            }
//            else {
//                $web_url = str_replace("http://mall.ecomoter.com/", "/", $web_url);
//            }
//        }
//        else {
//            $web_url = "/mobile/index";
//        }
//        exit($code);
        $web_url = "/mobile/index";

        $open_id = $this->getOpenId($code);
        $userInfo = $this->getUserInfo($open_id);
//        print_r($userInfo); exit;
        $result = $this->addUser($userInfo);

//        if ($web_url == "/mobile/index") {
//            $web_url = $web_url;
//        }
//        else {
//            if (substr($web_url, strlen($web_url) - 1, 1) != "/") {
//                $web_url = $web_url;
//            }
//            else {
//                $web_url = $web_url;
//            }
//        }

        if ($result) {
            $return_data = array(
                'url' => $web_url,
                'user_id' => $result,
                'open_id' => $open_id
            );

//            $return_data = array(
//                'url' => '/show/home',
//                'user_id' => '3'
//            );

            $this->assign('data', $return_data);
        }
        else {
            exit("注册或登陆失败,请退出公众号重试!");
        }

        $this->display();
    }

    public function addUser($user_info) {
        $userModel = D('user');

        $openid = $user_info['openid'];
        $nickname = $user_info['nickname'];
        $sex = $user_info['sex'];
//        $language = $data['language'];
        $city = $user_info['city'];
        $province = $user_info['province'];
        $country = $user_info['country'];
        $headimgurl = $user_info['headimgurl'];



        $user_condition = array('open_id' => $openid);

        $result = $userModel->where($user_condition)->select();

        $data = array(
            'open_id' => $openid,
            'nick_name' => $nickname,
            'name' => $nickname,
            'sex' => $sex,
            'head_pic' => $headimgurl
        );
//        print_r($data); exit;
        //如果该openid用户已经添加
        if ($result && count($result) === 1) {
            $r = $userModel->where($user_condition)->save($data);

            if (false !== $r || 0 !== $r) {
                return $result[0]['id'];
            }
//
//            return $r;
//            return $result[0]['id'];
        }

        $r = $userModel->add($data);

        return $r;
    }

    public function login() {
        $this->display();
    }

    public function mall() {
        $itemModel = D('item');
        $photoModel = D('photo');
        $db = M();

        $sql_text = 'select fit_source.*, fit_source_class.text as source_class_text, fit_photo.url, fit_photo.pic_class from fit_source left join fit_source_class on fit_source.source_class = fit_source_class.id left join fit_photo on fit_source.photo_id = fit_photo.id where fit_source.source_class = 9 order by fit_source.sort';

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

        $sql_text = 'select fit_source.*, fit_source_class.text as source_class_text, fit_photo.url, fit_photo.pic_class from fit_source left join fit_source_class on fit_source.source_class = fit_source_class.id left join fit_photo on fit_source.photo_id = fit_photo.id where fit_source.source_class = 14 order by fit_source.sort';

        $list_2 = $db->query($sql_text);

        $this->assign(data, array(
            'list' => $list,
            'list_1' => $list_1,
            'list_2' => $list_2
        ));

        $this->display();
    }

    public function product() {
        $itemModel = D('item');
        $photoModel = D('photo');
        $colorModel = D('color');

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

        $this->assign(data, array(
            'product' => $product
        ));

        $this->display();
    }

    public function cart() {
        $loginModel = D('login');

        $access = I('access');

        $access = I('access');

        if (!$this->validLogin($access)) {
            header("Location: /mobile/login"); exit;
        }

        $this->assign('data', array(
            'access' => $access
        ));

        $this->display();
    }

    public function checkout() {
        $orderModel = M('order');
        $orderDetailModel = M('order_detail');
        $addressModel = M('address');
        $itemModel = M('item');
        $photoModel = M('photo');
        $loginModel = M('login');

        $access = I('access');

        if (!$this->validLogin($access)) {
            header("Location: /mobile/login"); exit;
        }

        $order_id = I('id');

        $order_condition = array(
            'id' => $order_id
        );

        $order = $orderModel->where($order_condition)->find();

        $user_id = $order['user_id'];

        $login = $loginModel->where('user_id = '. $user_id)->order('login_time desc')->limit(1)->select();

        $address_condition = array(
            'user_id' => $user_id
        );

        $address = $addressModel->where($address_condition)->select();

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
            'access' => $access,
            'address' => $address,
            'order' => $order,
            'login_id' => $login[0]['key']
        ));

        $this->display();
    }

    public function me() {
        $access = I('access');

        if (!$this->validLogin($access)) {
            header("Location: /mobile/login"); exit;
        }

        $this->assign('data', array(
            'access' => $access
        ));

        $this->display();
    }

    public function me_addr() {
        $access = I('access');

        if (!$this->validLogin($access)) {
            header("Location: /mobile/login"); exit;
        }

        $this->assign('data', array(
            'access' => $access
        ));

        $this->display();
    }

    public function me_editaddr() {
        $addressModel = M('address');

        $access = I('access');

        if (!$this->validLogin($access)) {
            header("Location: /mobile/login"); exit;
        }

        $id = I('id');

        if ($id) {
            $addr = $addressModel->find($id);

            $this->assign('data', array(
                'access' => $access,
                'title' => '编辑地址',
                'item' => $addr
            ));
        }
        else {
            $this->assign('data', array(
                'access' => $access,
                'title' => '新增地址'
            ));
        }

        $this->display();
    }

    public function me_order() {
        $access = I('access');

        if (!$this->validLogin($access)) {
            header("Location: /mobile/login"); exit;
        }

        $this->assign('data', array(
            'access' => $access
        ));

        $this->display();
    }

    public function me_order_detail() {
        $orderModel = M('order');
        $orderDetailModel = M('order_detail');
        $addressModel = M('address');
        $itemModel = M('item');
        $photoModel = D('photo');
        $loginModel = D('login');

        $id = I('id');
        $access = I('access');

//        if (!$this->validLogin($access)) {
//            header("Location: /mobile/login"); exit;
//        }

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
            'access' => $access,
            'order' => $order
        ));

//        print_r($order); exit;

        $this->display();
    }

    public function success() {
        $this->display();
    }

    public function me_ordercomment() {
        $order_id = I('id');
        $access = I('access');

        if (!$this->validLogin($access)) {
            header("Location: /mobile/login"); exit;
        }

        $this->assign('data', array(
            'access' => $access,
           'order_id' => $order_id
        ));

        $this->display();
    }

    public function me_resetpwd() {
        $access = I('access');

        if (!$this->validLogin($access)) {
            header("Location: /mobile/login"); exit;
        }

        $this->assign('data', array(
            'access' => $access
        ));

        $this->display();
    }

    function validLogin($access) {
        $loginModel = D('login');

        if (!$access) {
            return false;
        }

        $str_condition = "`key` = '". $access ."' and expire_time > '". date('Y-m-d H:i:s', time()). "'";

        $login = $loginModel->where($str_condition)->select();

        if (!$login || count($login) < 1) {
            return false;
        }

        return true;
    }

    public function scooter() {
        $bikeInfoModel = D('bike_info');
        $photoModel = D('photo');

        $info = $bikeInfoModel->find(2);

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

    public function about() {
        $contentModel = D('content');

        $about = $contentModel->find(1);

        $this->assign('data', array(
            'content' => $about
        ));

        $this->display();
    }

    public function question() {
        $contentModel = D('content');

        $question = $contentModel->find(2);

        $this->assign('data', array(
            'content' => $question
        ));

        $this->display();
    }

    public function test_self() {
        $contentModel = D('content');

        $test_self = $contentModel->find(3);

        $this->assign('data', array(
            'content' => $test_self
        ));

        $this->display();
    }


    public function user_notes() {
        $contentModel = D('content');

        $test_self = $contentModel->find(5);

        $this->assign('data', array(
            'content' => $test_self
        ));

        $this->display();
    }

    public function getOpenId($code) {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=". $this->app_id ."&secret=". $this->app_secret ."&code=". $code ."&grant_type=authorization_code";

        $weixin = file_get_contents($url);
        $jsondecode = json_decode($weixin);
        $array = get_object_vars($jsondecode);
//        print_r($array); exit;
        $open_id = $array['openid'];
        $this->access_token = $array['access_token'];

        return $open_id;
    }

    public function getUserInfo($open_id) {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=". $this->access_token ."&openid=". $open_id ."&lang=zh_CN";

        $weixin = file_get_contents($url);

        $jsondecode = json_decode($weixin);
        $array = get_object_vars($jsondecode);

        return $array;
    }

    public function clear() {
        $this->display();
    }

    public function usermsg_edit() {
        $msg = D('user_msg');

        $id = I('id');

        if (empty($id) || $id == 0) {
            echo fit_api(true, 1, '参数错误!', '');
        }
        else {
            $db = M();
            $item = $db->query("select a.*,b.user_name from fit_user_msg a left join fit_user b on a.user_id = b.id where a.Id=" . $id);

            //$item = $msg->find($id);

            if ($item && count($item) > 0) {
                if ($item[0]['read'] == 1) {
                    $item[0]['readtext'] = '已读';
                } else {
                    $item[0]['readtext'] = '未读';
                }
            }

            $this->assign('data', array(
                'item' => $item
            ));
        }

        $this->display();
    }
}