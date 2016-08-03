<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/5/17
 * Time: 下午11:04
 */
namespace Home\Controller;
use Think\Controller;

class ShowController extends Controller {
    private $app_id = 'wx8f573f8116d3d740';
    private $app_secret = 'f6c37757e5f11a12b4cdd80c2cda8b7e';
    private $access_token = '';

    public function index(){
        $this->display();
    }

    public function home() {
//        $db = M();
//
//        $sql_text = 'select fit_lesson_config.id as lesson_config_id, fit_lesson_config.lesson_id, fit_lesson.name, fit_lesson.name_en, fit_lesson.show_pic, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.start_time, fit_lesson_config.end_time from fit_lesson_config left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id order by fit_lesson_config.start_time';
//
//        $this->assign('data', array(
//            'lesson' => $db->query($sql_text)
//        ));

        $this->display();
    }

    public function gt_aboutus() {
        $this->display();
    }

    public function gt_course() {
        $db = M();

        $id = I('id');

        $sql_text = 'select fit_lesson_config.id as lesson_config_id, fit_lesson_config.lesson_id, fit_lesson_config.run_date, fit_lesson.name, fit_lesson.name_en, fit_lesson.local, fit_lesson.show_pic, fit_lesson.local, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.start_time, fit_lesson_config.end_time, fit_lesson.remark, fit_lesson.lat, fit_lesson.lng from fit_lesson_config left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id where fit_lesson_config.id = '. $id;

        $data = $db->query($sql_text);

//        print_r($data); exit;

        $this->assign('data', array(
            'lesson' => $data
        ));

        $this->display();
    }

    public function gt_orderdetail() {
        $db = M();

        $id = I('id');

        $sql_text = "select fit_order.order_id, fit_order.lesson_id, fit_order.state, fit_order.coach_id, fit_order.type, fit_order.pay_amount, fit_lesson.name, fit_lesson.name_en, fit_lesson_config.run_date, fit_order.start_time, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.end_time, fit_lesson.local,  fit_lesson.lat, fit_lesson.lng from fit_order left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.id left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id where order_id = '". $id ."'";

        $order = $db->query($sql_text);

//        print_r($order); exit;

        if ($order[0]['state'] != 10) {
            if ($order[0]['type'] == '1') {
                header('location:/show/me_order?id='. $id . '&back=1');
            }
            else {
                header('location:/show/gt_congratulation?id='. $id . '&back=1');
            }

            exit;
        }

        $coach_sql = 'select * from fit_user where id = '. $order[0]['coach_id'];

        $coach = $db->query($coach_sql);

//        print_r($coach_sql); exit;

        if ($order[0]['type'] == 3) {
            $order[0]['name'] = '私人教练';
            $order[0]['name_en'] = $coach[0]['Name'];
            $order[0]['local'] = '深圳市南山区 科发路202号嵘兴通讯大厦101房';
            $order[0]['price'] = $order[0]['pay_amount'];
            $order[0]['vip_price_1'] = $order[0]['pay_amount'];
        }

        $this->assign('data', array(
           'order' =>  $order
        ));

        $this->display();
    }

    public function gt_pt() {
        $db = M();

        $sql_text = "select fit_user.id, fit_user.Name, fit_coach_config.price, fit_coach_config.title, fit_coach_config.label, fit_coach_config.show_pic, fit_coach_config.show_pic_big from fit_user left join fit_coach_config on fit_user.id = fit_coach_config.user_id where fit_user.is_coach = 1";

        $coach_list = $db->query($sql_text);

        $this->assign('data', $coach_list);

        $this->display();
    }

    public function gt_ptdetail() {
        $db = M();

        $id = I('id');

        $sql_text = "select fit_user.id, fit_user.Name, FLOOR(fit_coach_config.price) as price, fit_coach_config.title, fit_coach_config.label, fit_coach_config.show_pic, fit_coach_config.show_pic_big from fit_user left join fit_coach_config on fit_user.id = fit_coach_config.user_id where fit_user.is_coach = 1 and fit_user.id = ". $id;

        $coach_list = $db->query($sql_text);

        $this->assign('data', $coach_list);

        $this->display();
    }

    public function me_order() {
        $db = M();

        $id = I('id');

        $sql_text = "select fit_order.order_id, fit_order.lesson_id, fit_order.coach_id, fit_order.user_id, fit_order.pay_amount, fit_lesson.name, fit_lesson.name_en, fit_lesson.local, fit_lesson.phone, fit_order.run_date, fit_order.start_time, fit_order.type, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.end_time, fit_lesson.local, fit_lesson.lat, fit_lesson.lng from fit_order left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.id left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id where order_id = '". $id ."'";

        $order = $db->query($sql_text);

        $coach_sql = 'select * from fit_user where id = '. $order[0]['coach_id'];

        $coach = $db->query($coach_sql);

        if ($order[0]['type'] == 3) {
            $order[0]['name'] = '私人教练';
            $order[0]['name_en'] = $coach[0]['Name'];
            $order[0]['local'] = $order[0]['local'];
            $order[0]['price'] = $order[0]['pay_amount'];
            $order[0]['vip_price_1'] = $order[0]['pay_amount'];
        }
//        print_r($order); exit;

        $this->assign('data', array(
           'order' => $order
        ));

        $this->display();
    }

    public function gt_congratulation() {
        $db = M();

        $id = I('id');

        $sql_text = "select fit_order.order_id, fit_order.lesson_id, fit_lesson.name, fit_lesson.name_en, fit_order.run_date, fit_order.start_time, fit_order.amount, fit_order.vip_amount, fit_lesson_config.end_time, fit_lesson.local from fit_order left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.id left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id where order_id = '". $id ."'";

        $this->assign('data', array(
            'order' => $db->query($sql_text)
        ));

        $this->display();
    }

    #region 教练页面

    public function coach_home() {
        $this->display();
    }

    public function coach_start() {
        $db = M();

        $id = I('id');

        $sql_text = "select fit_order.order_id, fit_order.lesson_id, fit_lesson.name, fit_lesson.name_en, fit_order.run_date, fit_order.type, fit_order.start_time, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.end_time, fit_lesson.local, fit_user.name as user_name from fit_order left join fit_user on fit_order.user_id = fit_user.id left join fit_lesson on fit_order.lesson_id = fit_lesson.id left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.lesson_id where order_id = '". $id ."'";

        $this->assign('data', array(
            'order' => $db->query($sql_text)
        ));

        $this->display();
    }

    public function coach_end() {
        $db = M();

        $id = I('id');

        $sql_text = "select fit_order.order_id, fit_order.lesson_id, fit_lesson.name, fit_lesson.name_en, fit_order.run_date, fit_order.type, fit_order.start_time, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.end_time, fit_lesson.local, fit_user.name as user_name from fit_order left join fit_user on fit_order.user_id = fit_user.id left join fit_lesson on fit_order.lesson_id = fit_lesson.id left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.lesson_id where order_id = '". $id ."'";

        $this->assign('data', array(
            'order' => $db->query($sql_text)
        ));

        $this->display();
    }

    #endregion

    #region 购买vip
    public function gt_vipcard() {
        $this->display();
    }

    public function gt_charge() {
        $this->display();
    }

    #endregion;

    #region 我的个人中心

    public function me_profile() {
        $this->display();
    }

    public function me_edit() {
        $this->display();
    }

    public function gt_register() {
        $order_id = I('order_id');

        $this->assign('order_id', $order_id);

        $this->display();
    }

    public function gt_registersuccess() {
        $db = M();

        $order_id = I('order_id');

        $sql_text = "select fit_order.order_id, fit_order.lesson_id, fit_order.state, fit_order.type, fit_lesson.name, fit_lesson.name_en, fit_order.run_date, fit_order.start_time, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.end_time, fit_lesson.local from fit_order left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.id left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id where order_id = '". $order_id ."'";

        $order = $db->query($sql_text);

//        print_r($order); exit;

        $this->assign('data', array(
            'order' =>  $order
        ));

        $this->display();
    }

    public function me_finance() {
        $this->display();
    }

    public function me_orders() {
        $this->display();
    }

    #endregion;

    #region 微信功能代码

    public function order_pay() {
//        \Think\Log::write('微信pay调试，步骤_1','WARN');

        $orderModel = M('');

        $id = I('id');

        if ($id) {
            $sql_text = "select fit_order.order_id, fit_lesson.name, fit_order.amount, fit_user.openid, fit_order.state, fit_order.type from fit_order left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.id left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id left join fit_user on fit_order.user_id = fit_user.id where fit_order.order_id = '". $id ."'";

            $order = $orderModel->query($sql_text);

//            print_r($order); exit;

            if ($order[0]['state'] != 10) {
                if ($order[0]['type'] == '1') {
                    header('location:/show/me_order?id='. $id . '&back=1');
                }
                else {
                    header('location:/show/gt_congratulation?id='. $id . '&back=1');
                }

                exit;
            }

            $clsPayment = new \Org\Fit\WeChat\Payment();
            $JsParam = $clsPayment->WeChatShopPayParam($order[0], $order[0]["openid"]);

            $this->assign('JsParam',$JsParam);
            $this->assign('OrderSn', $order[0]["order_id"]);

//            $this->display();
        }

//        print_r($order); exit;
        $this->display();
    }


    public function pay_notify() {
        import('Org.Fit.WeChat.JsApiPay');
        import('Org.Fit.WeChat.WeChatPayReturn');

        $logModel = D('log');

        $log_data = array(
            'msg' => 'order_bug_notify_1',
            'type' => 'order',
            'create_date' => date("Y-m-d H:i:s")
        );

        $logModel->add($log_data);

        $clsPayment = new \Org\Fit\WeChat\Payment();

        $PayResult = $clsPayment->ReceiveWeChatShopNotify();

        $order_id = $PayResult["out_trade_no"];

        $log_data = array(
            'msg' => 'order_bug_notify_2:get order_id'. $order_id,
            'type' => 'order',
            'create_date' => date("Y-m-d H:i:s")
        );

        $logModel->add($log_data);

        $orderModel = D('order');
        $userModel = D('user');

        $order_condition = array(
            'order_id' => $order_id
        );

        $data = array(
            'state' => 20
        );

        $order = $orderModel->where($order_condition)->select();

        $user_id = $order[0]['user_id'];
        $amount = $order[0]['amount'];
        $vip_amount = $order[0]['vip_amount'];

        $log_data = array(
            'msg' => 'order_bug_notify_3:get order_data'. $vip_amount,
            'type' => 'order',
            'create_date' => date("Y-m-d H:i:s")
        );

        $logModel->add($log_data);

        $user_condition = array(
            'id' => $user_id
        );

        if ($order[0]['type'] == '1') {
            $log_data = array(
                'msg' => 'order_bug_notify_4:update order data',
                'type' => 'order',
                'create_date' => date("Y-m-d H:i:s")
            );

            $logModel->add($log_data);

            //普通约课订单
            $result = $orderModel->where($order_condition)->save($data);

            if (false !== $result) {
                $log_data = array(
                    'msg' => 'order_bug_notify_5:update user vip amount',
                    'type' => 'order',
                    'create_date' => date("Y-m-d H:i:s")
                );

                $logModel->add($log_data);
                //订单状态更新成功
                $result = $userModel->where($user_condition)->setDec('amount', $vip_amount);

                if (false !== $result) {
                    //用户vip金额抵扣成功
                    $log_data = array(
                        'msg' => 'order_bug_notify_6: update user amount success',
                        'type' => 'order',
                        'other_id' => $order_id,
                        'create_date' => date("Y-m-d H:i:s")
                    );

                    $logModel->add($log_data);
                }
                else {
                    //用户vip金额抵扣失败
                    $log_data = array(
                        'msg' => 'order_bug_notify_7: update user amount fail',
                        'type' => 'order',
                        'other_id' => $order_id,
                        'create_date' => date("Y-m-d H:i:s")
                    );

                    $logModel->add($log_data);
                }
            }
            else {
                //订单状态更新失败
                $log_data = array(
                    'msg' => 'order_bug_notify_8: update order state fail',
                    'type' => 'order',
                    'other_id' => $order_id,
                    'create_date' => date("Y-m-d H:i:s")
                );

                $logModel->add($log_data);
            }
        }
        else {
            //vip充值订单
            $result = $userModel->where($user_condition)->setInc('amount', $vip_amount);

            if (false !== $result) {
                //vip充值数据库更新成功
                //订单状态更新失败
                $log_data = array(
                    'msg' => 'order_bug_notify_21: update user vip_amount fail',
                    'type' => 'order',
                    'other_id' => $order_id,
                    'create_date' => date("Y-m-d H:i:s")
                );

                $logModel->add($log_data);
            }
            else {
                //vip充值数据库更新失败
                //订单状态更新失败
                $log_data = array(
                    'msg' => 'order_bug_notify_22: update user vip_amount fail',
                    'type' => 'order',
                    'other_id' => $order_id,
                    'create_date' => date("Y-m-d H:i:s")
                );

                $logModel->add($log_data);
            }
        }

        return $PayResult;
    }

    public function pay_success() {
        $orderModel = D('order');

        $order_id = I('id');

        $order_condition = array(
          'order_id' => $order_id
        );

        $order = $orderModel->where($order_condition)->select();

        $type = $order[0]['type'];

//        $this->assign('order_id', $order_id);

        $web_url = '/show/me_order?id='. $order_id . '&back=1';

        if ($type == 2) {
           header('location:/show/gt_congratulation?id='. $order_id . '&back=1'); exit;
        }

        $this->assign('web_url', $web_url);

        $this->display();
    }

    public function pay_fail() {
        $order_id = I('id');

        $this->assign('order_id', $order_id);

        $this->display();
    }

    public function vip_detail() {
        $orderModel = D('order');

        $order_id = I('id');

        $order_condition = array(
          'order_id' => $order_id
        );

        $order = $orderModel->where($order_condition)->select();

        $this->assign('order', $order[0]);

        $this->display();
    }

    #region 微信用户方法

    public function reg() {
        $code = $_GET['code'];
        $id = I('id');

//        $tmp_web_url = $this->getParam('web_url');
//        $web_url = urldecode($tmp_web_url);
//        exit($web_url);
//        if ($web_url) {
//            if ($web_url == "http://fit.webetter100.com/") {
//                $web_url = "/show/index";
//            }
//            else {
//                $web_url = str_replace("http://fit.webetter100.com/", "/", $web_url);
//            }
//        }
//        else {
//            $web_url = "/show/index";
//        }

        $web_url = "/show/index";

        $open_id = $this->getOpenId($code);
        $userInfo = $this->getUserInfo($open_id);

        $result = $this->addUser($userInfo);

        if ($web_url == "/") {
            $web_url = $web_url;
        }
        else {
            if (substr($web_url, strlen($web_url) - 1, 1) != "/") {
                $web_url = $web_url;
            }
            else {
                $web_url = $web_url;
            }
        }

        if ($result) {
            $return_data = array(
                'url' => $web_url,
                'user_id' => $result
            );

//            $return_data = array(
//                'url' => '/show/home',
//                'user_id' => '3'
//            );

            $this->assign('data', $return_data);
        }
        else {
            exit("reg fail, please try again!". $userInfo['openid']);
        }

        $this->display();
    }

    public function addUser($user_info) {
        $user = D('user');

        $openid = $user_info['openid'];
        $nickname = $user_info['nickname'];
        $sex = $user_info['sex'];
//        $language = $data['language'];
        $city = $user_info['city'];
        $province = $user_info['province'];
        $country = $user_info['country'];
        $headimgurl = $user_info['headimgurl'];

//        print_r($user_info); exit;

        $user_condition = array('openid' => $openid);

        $result = $user->where($user_condition)->find();

        $data = array(
            'openid' => $openid,
            'NickName' => $nickname,
            'Name' => $nickname,
            'Sex' => $sex,
            'HeadPic' => $headimgurl
        );
//        print_r($data); exit;
        //如果该openid用户已经添加
        if ($result) {
            $r = $user->where($user_condition)->save($data);

            if (false !== $r || 0 !== $r) {
                return $result['id'];
            }

            return false;
        }

        $r = $user->add($data);

        return $r;
    }

    #endregionfang方法方法 fang

    #region 微信工具方法

    public function getOpenId($code) {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=". $this->app_id ."&secret=". $this->app_secret ."&code=". $code ."&grant_type=authorization_code";

        $weixin = file_get_contents($url);
        $jsondecode = json_decode($weixin);
        $array = get_object_vars($jsondecode);

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

    public function getCurl($url) {//get https的内容
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不输出内容
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result =  curl_exec($ch);
        curl_close ($ch);
        return $result;
    }

    public function dataPost($post_string, $url) {//POST方式提交数据
        $context = array('http' => array('method' => "POST", 'header' => "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) \r\n Accept: */*", 'content' => $post_string));
        $stream_context = stream_context_create($context);
        $data = file_get_contents($url, FALSE, $stream_context);
        return $data;
    }

    public function getAccessToken() {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->app_id."&secret=".$this->app_secret;
        $data = $this->getCurl($url);//通过自定义函数getCurl得到https的内容

        $resultArr = json_decode($data, true);//转为数组
        return $resultArr["access_token"];//获取access_token
    }

    #endregion

    #region 微信菜单方法

    public function menuCreate() {
        $this->createMenu();
    }

    public function createMenu() {
        $accessToken = $this->getAccessToken();//获取access_token
        $menuPostString = '{ "button":[{ "type":"view", "name":"约练课程",  "url":"http://www.gaoshougolf.com/show/index" }] }';

        $menuPostUrl = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accessToken;//POST的url
        $menu = $this->dataPost($menuPostString, $menuPostUrl);//将菜单结构体POST给微信服务器
        var_dump($menu);
    }

    public function wx_map() {
        $this->display();
    }

    #endregion

    #endregion

    public function clear() {
        $this->display();
    }

    public function info() {
        $this->display();
    }

    public function valid() {
        $echoStr = $_GET["echostr"];

        //valid signature , option
//        if($this->checkSignature()) {
//            echo $echoStr;
//            exit;
//        }

        $this->responseMsg();

    }

    private function checkSignature() {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()  {
        //get post data, May be due to the different environments
        //$postStr = //$GLOBALS["HTTP_RAW_POST_DATA"];
        $postStr = file_get_contents('php://input');

        //extract post data
        if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $ev = $postObj->Event;
            $time = time();
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if(!empty( $keyword )) {
                $msgType = "text";
                $contentStr = "欢迎来到高手高尔夫!";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            } else{
                if ($ev == "subscribe"){
                    $msgType = "text";

                    $contentStr = $this->getContent($postObj);//"Hi，终于等到你，欢迎来到高手高尔夫！这里能为你提供专业的高尔夫指导, 让你球技日渐精进!" . $param;
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                    echo $resultStr;
                }

                //echo "Input something...";
            }

        } else {
            echo "test";
            exit;
        }
    }

    public function getContent($postObj) {
        $db = D('');
        $sceneModel = M('scene');
        $userModel = M('user');

        $param = str_replace('qrscene_', '', $postObj->EventKey);
        $openid = (string)$postObj->FromUserName;

//        $condition_find_scene_by_id = array('id' => $param);

        $scene = $sceneModel->find($param);

//        return $param;

        if ($scene) {
            if ($scene['user_id'] != '') {
                return '该二维码已经被扫过了!';
            }

            if ($scene['is_close'] == '1') {
                return '该二维码优惠卷已经被使用!';
            }

            $condition_find_user_by_openid = array('openid' => $openid);

            $user = $userModel->where($condition_find_user_by_openid)->find();

            $user_id = false;

            if ($user) {
                $user_id = $user['id'];
            }
            else {

                $data = array();

                $data['openid'] = $openid;

                $user_id = $userModel->add($data);
            }

            $sql_text = 'select fit_scene.id, fit_channel.name as channel_name, fit_setmeal.name as setmeal_name, is_use, fit_scene.create_date, fit_scene.ticket from fit_scene left join fit_channel on fit_scene.channel_id = fit_channel.id left join fit_setmeal on fit_scene.setmeal_id = fit_setmeal.id where fit_scene.user_id = '.$user_id;

            $tmp_scene = $db->query($sql_text);

            if ($tmp_scene && count($tmp_scene) > 0) {
                return '欢迎'. $tmp_scene[0]['channel_name'] .'用户, 您已经获得过一次初次免费体验机会, 无法再次获得!';
            }

            $data = array();

            $data['user_id'] = $user_id;

            $condition_scene = array('id' => $param);

            $result = $sceneModel->where($condition_scene)->save($data);

            if ($result) {
                $sql_text = 'select fit_scene.id, fit_channel.name as channel_name, fit_setmeal.name as setmeal_name, is_use, fit_scene.create_date, fit_scene.ticket from fit_scene left join fit_channel on fit_scene.channel_id = fit_channel.id left join fit_setmeal on fit_scene.setmeal_id = fit_setmeal.id where fit_scene.id = '.$param;

                $scenes = $db->query($sql_text);

                return '欢迎'. $scenes[0]['channel_name'] .'用户, 您获得了初次免费体验约课的机会一次!';
            }
        }

        return '欢迎来到高手高尔夫!';
    }
}