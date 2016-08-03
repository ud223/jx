<?php
namespace Home\Controller;
use Think\Controller;

class ApiController {
    public function getUser() {
        $user = D('user');
        $sceneModel = D('scene');

        $id = I('id');

        $d_user = $user->find($id);

        $scene = $sceneModel->where('user_id = '. $id . ' and is_use = 0')->find();

        if ($scene) {
            $d_user['scene_id'] = $scene['id'];
        }
        else {
            $d_user['scene_id'] = '';
        }

        echo fit_api(true, 200,'加载成功', $d_user);
    }

    public function updateUser() {
        $user = D('user');

        $user_id = I('id');
        $name = I('name');
        $tel = I('tel');
        $nickname = I('nickname');

        $tel_condition = array(
            'Tel' => $tel
        );

        $users = $user->where($tel_condition)->select();

        if (count($users) != 0) {
            if ($users[0]['id'] != $user_id) {
                echo fit_api(true, 0,'已有人设置了该号码, 请于管理员联系!', ''); exit;
            }
        }

        $condition = array('id' => $user_id);

        $data = array(
            'Name' => $name,
            'Tel' => $tel,
            'NickName' => $nickname
        );

        $user->where($condition)->save($data);

        echo fit_api(true, 200,'保存成功', '');
    }

    public function createOrder() {
        $lesson = D('lesson');
        $lesson_config = D('lesson_config');
        $order = D('order');

        $run_date = I('run_date');
        $user_id = I('user_id');
        $lesson_id = I('lesson_id');
        $type = I('type');
        $amount = I('amount');
        $pay_amount = I('pay_amount');

        if ($lesson_id != 0) {
            $order_condition = "state > 10 and user_id = ". $user_id . " and run_date = '". $run_date ."' and lesson_id = ". $lesson_id ;

            $orders = $order->where($order_condition)->select();

            if (count($orders) === 1) {
                echo fit_api(true, 0, '当天已预约相同课程!', '');
                exit;
            }
        }

        $confg_condition = array('id' => $lesson_id);

        $d_lesson_config = $lesson_config->where($confg_condition)->select();

        $start_time = $d_lesson_config[0]['start_time'];

        $order_id = date("YmdHis") . $this->get_millistime();

        $data = array(
            'order_id' => $order_id,
            'user_id' => $user_id,
            'lesson_id' => $lesson_id,
            'run_date' => $run_date,
            'start_time' => $start_time,
            'type' => $type,
            'amount' => $pay_amount,
            'vip_amount' => $amount,
            'state' => 10,
            'create_at' => date("Y-m-d H:i:s")
        );

//        echo fit_api(true, 200,'订单创建成功', $data);

        $order->add($data);

        echo fit_api(true, 200,'订单创建成功', $order_id);
    }

    public function createPtOrder() {
        $order = D('order');

        $run_date = I('run_date');
        $user_id = I('user_id');
        $coach_id = I('coach_id');
        $time = I('time');
        $lesson_id = I('lesson_id');
        $type = I('type');
        $amount = I('amount');
        $pay_amount = I('pay_amount');
        $start_time = I('start_time');

        if ($lesson_id != 0) {
            $order_condition = "state > 10 and user_id = ". $user_id . " and run_date = '". $run_date ."' and coach_id = ". $coach_id;

            $orders = $order->where($order_condition)->select();

            if (count($orders) === 1) {
                echo fit_api(true, 0, '当天已预约相同课程!', '');
                exit;
            }
        }

        $order_id = date("YmdHis") . $this->get_millistime();

        $data = array(
            'order_id' => $order_id,
            'user_id' => $user_id,
            'coach_id' => $coach_id,
            'lesson_id' => $lesson_id,
            'run_date' => $run_date,
            'start_time' => $start_time,
            'type' => $type,
            'amount' => $pay_amount,
            'pay_amount' => $pay_amount,
            'vip_amount' => $amount,
            'state' => 10,
            'time' => $time,
            'create_at' => date("Y-m-d H:i:s")
        );

//        echo fit_api(true, 200,'订单创建成功', $data); exit;

        $order->add($data);

        echo fit_api(true, 200,'订单创建成功', $order_id);
    }

    public function updatePayAmount() {
        $orderModel = D('order');

        $id = I('order_id');
        //实付金额总价
        $amount = I('amount');
        //vip支付金额
        $vip_amount = I('vip_amount');

        $order_condition = array(
          'order_id' => $id
        );

        $data = array(
            'amount' => $vip_amount,
            'pay_amount' => $amount
        );

   //     echo fit_api(true, 0,'订单价格确认失败', $data); exit;

        $result = $orderModel->where($order_condition)->save($data);

        if (false !== $result) {
            echo fit_api(true, 200,'订单价格确认成功', '');
        }
        else {
            echo fit_api(true, 0,'订单价格确认失败', '');
        }
    }

    public function vipPay() {
        $orderModel = D('order');
        $userModel = D('user');
        $logModel = D('log');
        $sceneModel = D('scene');

        $order_id = I('order_id');

        $order_condition = array(
            'order_id' => $order_id
        );

        $data = array(
            'state' => 20
        );

        $order = $orderModel->where($order_condition)->find();

        $user_condition = array(
            'id' => $order['user_id']
        );

        //普通约课订单
        $result = $orderModel->where($order_condition)->save($data);

        if (false !== $result) {
            $log_data = array(
                'msg' => 'order_bug_notify_5:update user vip amount',
                'type' => 'order',
                'create_date' => date("Y-m-d H:i:s")
            );

            $logModel->add($log_data);

//            $vip_amount = $order['vip_amount'];

            //订单状态更新成功
//            $result = $userModel->where($user_condition)->setDec('amount', $vip_amount);

            $data = array(
              'is_use' => 1
            );

            $result = $sceneModel->where('user_id = '. $order['user_id'])->save($data);

            if (false !== $result) {
                //用户vip金额抵扣成功
                $log_data = array(
                    'msg' => 'order_bug_notify_6: update user amount success',
                    'type' => 'order',
                    'other_id' => $order_id,
                    'create_date' => date("Y-m-d H:i:s")
                );

                $logModel->add($log_data);

                echo fit_api(true, 200, '预约成功!', '');
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

                echo fit_api(true, 0, 'vip金额抵扣失败 请马上与管理员联系!', '');
            }
        }
        else {
            //订单状态更新失败
            //用户vip金额抵扣成功
            $log_data = array(
                'msg' => 'order_bug_notify_8: update order state fail',
                'type' => 'order',
                'other_id' => $order_id,
                'create_date' => date("Y-m-d H:i:s")
            );

            $logModel->add($log_data);

            echo fit_api(true, 0, '订单状态更新失败 请马上与管理员联系!', '');
        }
    }

    public function getMyOrders() {
        $db = M();

        $user_id = I('user_id');

        $sql_text = "select fit_order.*, fit_lesson.name, fit_lesson.name_en, fit_lesson_config.end_time from fit_order left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.id left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id where fit_order.user_id = ".  $user_id ." and (`type` = 1  or `type` = 3) and fit_order.state > 10 order by fit_order.run_date desc, start_time desc";

        $d_orders = $db->query($sql_text);

        echo fit_api(true, 200, '获取成功', $d_orders);
    }

    public function getCocahOrders() {
        $db = M();

        $coach_id = I('coach_id');

        $sql_text = "select fit_order.*, fit_lesson.name, fit_user.name, fit_user.tel from fit_order left join fit_user on fit_order.user_id = fit_user.id left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.id left join fit_lesson on fit_lesson_config.lesson_id  = fit_lesson.id where fit_order.coach_id = ".  $coach_id ." and `type` in (1,3) and fit_order.state > 10 order by fit_order.run_date desc";

        $d_orders = $db->query($sql_text);

        echo fit_api(true, 200, '获取成功', $d_orders);
    }

    public function lesson_start() {
        $orderModel = D('order');

        $order_id = I('order_id');

        $order_condition = array(
          'order_id' => $order_id
        );

        $data = array(
          'state' => 30
        );

        $result = $orderModel->where($order_condition)->save($data);

        if (false !== $result) {
            echo fit_api(true, 200, '上课成功!', '');
        }
        else {
            echo fit_api(true, 0, '上课失败, 请与管理员联系!', '');
        }
    }

    public function lesson_end() {
        $orderModel = D('order');

        $order_id = I('order_id');

        $order_condition = array(
            'order_id' => $order_id
        );

        $data = array(
            'state' => 30
        );

        $result = $orderModel->where($order_condition)->save($data);

        if (false !== $result) {
            echo fit_api(true, 200, '下课成功!', '');
        }
        else {
            echo fit_api(true, 0, '下课失败, 请与管理员联系!', '');
        }
    }

    #region 教练时间api

    public function saveCoachTemplate() {
        $templateTimeModel = D('coach_template_time');

        $coach_id = I('coach_id');
        $week_num = I('week_num');
        $time = I('time');
        $is_work = I('is_work');

        $template = $templateTimeModel->where('coach_id = '. $coach_id ." and week_num = ". $week_num)->select();

        if (!$template || count($template) == 0) {
            $data = array(
                'coach_id' => $coach_id,
                'week_num' => $week_num,
                'time' => $time,
                'is_work' => $is_work
            );

            $result = $templateTimeModel->add($data);

            if ($result) {
                echo fit_api(true, 200, '保存成功!', $result);
                exit;
            }
            else {
                echo fit_api(true, 200, '保存失败!', $result);
                exit;
            }
        }
        else {
            $data = array(
                'time' => $time,
                'is_work' => $is_work
            );

            $result = $templateTimeModel->where('coach_id = '. $coach_id ." and week_num = ". $week_num)->save($data);


            if ($result == false) {
                echo fit_api(true, 200, '保存失败!', $result);
                exit;
            }
            else {
                if ($result == 0) {
                    echo fit_api(true, 200, '保存成功!', $result);
                    exit;
                }
                else {
                    echo fit_api(true, 200, '保存失败!', $result);
                    exit;
                }
            }
        }
    }

    public function saveCoachTime() {
        $coachTimeModel = D('coach_config_time');

        $coach_id = I('coach_id');
        $run_date = I('run_date');
        $time = I('time');
        $is_work = I('is_work');

        $temple = $coachTimeModel->where('coach_id = '. $coach_id ." and run_date = '". $run_date . "'")->select();

//        echo fit_api(true, 200, '保存成功!', $temple);
//        exit;

        if (!$temple || count($temple) == 0) {
            $data = array(
                'coach_id' => $coach_id,
                'run_date' => $run_date,
                'time' => $time,
                'is_work' => $is_work
            );

            $result = $coachTimeModel->add($data);

            if ($result) {
                echo fit_api(true, 200, '保存成功!', $result);
                exit;
            }
            else {
                echo fit_api(true, 200, '保存失败!', $result);
                exit;
            }
        }
        else {
            $data = array(
                'time' => $time,
                'is_work' => $is_work
            );

            $result = $coachTimeModel->where('coach_id = '. $coach_id ." and run_date = '". $run_date . "'")->save($data);

            if ($result == false) {
                echo fit_api(true, 200, '保存失败!', $result);
                exit;
            }
            else {
                if ($result >= 0) {
                    echo fit_api(true, 200, '保存成功!', $result);
                    exit;
                }
                else {
                    echo fit_api(true, 200, '保存失败!', $result);
                    exit;
                }
            }
        }
    }

    public function getTempletConfig() {
        $templetTimeModel = D('coach_template_time');

        $coach_id = I('coach_id');
        $week_num = I('week_num');

        $temple = $templetTimeModel->where('coach_id = '. $coach_id ." and week_num = ". $week_num)->select();

        echo fit_api(true, 200, '加载成功!', $temple);
    }

    public function getCoachTime() {
        $coachTimeModel = D('coach_config_time');

        $coach_id = I('coach_id');
        $run_date = I('run_date');

        $time = $coachTimeModel->where('coach_id = '. $coach_id ." and run_date = '". $run_date . "'")->select();

        echo fit_api(true, 200, '加载成功!', $time);
    }

    public function getCoachTimeConfig() {
        $coach_template_Model = M('coach_template_time');
        $coach_time_configModel = M('coach_config_time');
        $orderModel = M('order');

        $coach_id = I('coach_id');
        $date = I('date');
        $week_num = I('week_num');

//        echo fit_api(true, 200, '获取成功!', 'date:'.$date.'; week:'.$week_num); exit;

        $config = $coach_time_configModel->where("coach_id = ". $coach_id ." and run_date = '". $date ."'")->select();

        if (!$config || count($config)) {
            $config = $coach_template_Model->where('coach_id='. $coach_id .' and week_num='. $week_num)->select();
        }
//        echo fit_api(true, 200, '获取成功!', $config); exit;

        $order = $orderModel->where("type = 3 and coach_id = ". $coach_id ." and run_date = '". $date ."'")->select();

        $order_time = '';

        foreach ($order as $o) {
            $str_times = $o['start_time'];

            $times = explode(',', $str_times);

            foreach ($times as $time) {
                if ($order_time != '') {
                    $order_time = $order_time . ',';
                }

                $tmp_time = explode(':', $time)[0];

                $order_time = $order_time . $tmp_time;
//                        echo fit_api(true, 200, '获取成功!', $config); exit;
            }
        }

        $config["order_time"] = $order_time;

        echo fit_api(true, 200, '获取成功!', $config);
    }

    #endregion

    #region 课程api

    public function getLessonByWeekNum() {
        $db = M();

        $run_date = I('run_date');

        $sql_text = "select fit_lesson_config.id as lesson_config_id, fit_lesson_config.run_date, fit_lesson_config.lesson_id, fit_lesson.name, fit_lesson.name_en, fit_lesson.show_pic, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.start_time, fit_lesson_config.end_time from fit_lesson_config left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id where (run_date < '".$run_date."' and end_date > '". $run_date ."') and is_show = 1  order by run_date";

        $lesson_config = $db->query($sql_text);

        echo fit_api(true, 200, '获取成功!', $lesson_config);
    }

    #endregion

    #region 工具方法

    public function get_millistime() {
        list($usec, $sec) = explode(' ', microtime());

        $usec2msec = $usec * 1000;  //计算微秒部分的毫秒数(微秒部分并不是微秒,这部分的单位是秒)
        $usec2msec2int = intval($usec2msec);
        $sec2msec = $sec * 1000;    //计算秒部分的毫秒数
        $sec2msec2int = intval($sec2msec);

        $msec = $sec2msec2int + $usec2msec2int; //加起来就对了

        return $msec;
    }

    public function random($length = 6 , $numeric = 0) {
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        if($numeric) {
            $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
            $max = strlen($chars) - 1;
            for($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }

    public function Post($curlPost,$url) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);

        return $return_str;
    }

    public function sms_valid() {
        $encode='UTF-8';  //页面编码和短信内容编码为GBK。重要说明：如提交短信后收到乱码，请将GBK改为UTF-8测试。如本程序页面为编码格式为：ASCII/GB2312/GBK则该处为GBK。如本页面编码为UTF-8或需要支持繁体，阿拉伯文等Unicode，请将此处写为：UTF-8
        $username='ddwlkj';  //用户名
        $password_md5='6A0F1AB9C95A9FF8D8A389397CAC3ECD';  //32位MD5密码加密，不区分大小写
        $apikey='c14903f4cd6c0805969192b092d6de57';  //apikey秘钥（请登录 http://m.5c.com.cn 短信平台-->账号管理-->我的信息 中复制apikey）

        $url = "http://m.5c.com.cn/api/send/index.php?";

        $mobile = I('phone');

        if (!$mobile) {
            echo fit_api(true, 0, '没有获取到手机号码!', ''); exit;
        }

        $mobile_code = $this->random(4,1);

        $content='您好，您的验证码是：'.$mobile_code.'【高手高尔夫】';

        $contentUrlEncode = urlencode($content);

        $data=array (
            'username'=>$username,
            'password_md5'=>$password_md5,
            'apikey'=>$apikey,
            'mobile'=>$mobile,
            'content'=>$contentUrlEncode,
            'encode'=>$encode,
        );
        $result = $this->curlSMS($url,$data);
        //print_r($data); //测试
//        return $result;

        echo fit_api(true, 200, '验证短信已发送!', $mobile_code);
    }

    function curlSMS($url,$post_fields=array()) {
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);//用PHP取回的URL地址（值将被作为字符串）
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//使用curl_setopt获取页面内容或提交数据，有时候希望返回的内容作为变量存储，而不是直接输出，这时候希望返回的内容作为变量
        curl_setopt($ch,CURLOPT_TIMEOUT,30);//30秒超时限制
        curl_setopt($ch,CURLOPT_HEADER,1);//将文件头输出直接可见。
        curl_setopt($ch,CURLOPT_POST,1);//设置这个选项为一个零非值，这个post是普通的application/x-www-from-urlencoded类型，多数被HTTP表调用。
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);//post操作的所有数据的字符串。
        $data = curl_exec($ch);//抓取URL并把他传递给浏览器
        curl_close($ch);//释放资源
        $res = explode("\r\n\r\n",$data);//explode把他打散成为数组
        return $res[2]; //然后在这里返回数组。
    }

//    public function sms_valid() {
//        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
//
//        $phone = I('phone');
//
//        if (!$phone) {
//            echo fit_api(true, 0, '没有获取到手机号码!', ''); exit;
//        }
//
//        $mobile_code = $this->random(4,1);
//
//        $post_data = "account=cf_tianshi&password=fit@angelhere&mobile=". $phone ."&content=".rawurlencode("您的验证码是：".$mobile_code);
//        header("Content-type:text/html; charset=UTF-8");
//        //密码可以使用明文密码或使用32位MD5加密
//        $gets =  $this->Post($post_data, $target);
//
//        echo fit_api(true, 200, '验证短信已发送!', $mobile_code);
//    }

    #endregion


} 