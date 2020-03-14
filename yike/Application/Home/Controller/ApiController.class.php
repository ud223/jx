<?php
namespace Home\Controller;
use Think\Controller;

class ApiController {
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
        $smsModel = M('sms');

        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";

        $phone = I('phone');

        if (!$phone) {
            echo fit_api(true, 0, '没有获取到手机号码!', ''); exit;
        }

        $mobile_code = $this->random(4,1);

        $post_data = "account=cf_ecomoter&password=echo5838&mobile=". $phone ."&content=".rawurlencode("您的校验码是：".$mobile_code);
        header("Content-type:text/html; charset=UTF-8");
        //密码可以使用明文密码或使用32位MD5加密
        $gets =  $this->Post($post_data, $target);

        $smsModel->id = uniqid().time().rand(10000000,99999999);
        $smsModel->phone_number = $phone;
        $smsModel->text = "您的校验码是：".$mobile_code;
        $smsModel->code = $mobile_code;
        $smsModel->msg_type = 0;
        $smsModel->create_at = date('Y-m-d H:i:s', time());

        $smsModel->add();

        echo fit_api(true, 200, '验证短信已发送!', $mobile_code);
    }

    #endregion


} 