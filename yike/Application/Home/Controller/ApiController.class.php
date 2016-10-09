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

//    public function sms_valid() {
//        $encode='UTF-8';  //页面编码和短信内容编码为GBK。重要说明：如提交短信后收到乱码，请将GBK改为UTF-8测试。如本程序页面为编码格式为：ASCII/GB2312/GBK则该处为GBK。如本页面编码为UTF-8或需要支持繁体，阿拉伯文等Unicode，请将此处写为：UTF-8
//        $username='ddwlkj';  //用户名
//        $password_md5='6A0F1AB9C95A9FF8D8A389397CAC3ECD';  //32位MD5密码加密，不区分大小写
//        $apikey='c14903f4cd6c0805969192b092d6de57';  //apikey秘钥（请登录 http://m.5c.com.cn 短信平台-->账号管理-->我的信息 中复制apikey）
//
//        $url = "http://m.5c.com.cn/api/send/index.php?";
//
//        $mobile = I('phone');
//
//        if (!$mobile) {
//            echo fit_api(true, 0, '没有获取到手机号码!', ''); exit;
//        }
//
//        $mobile_code = $this->random(4,1);
//
//        $content='您好，您的验证码是：'.$mobile_code.'【高手高尔夫】';
//
//        $contentUrlEncode = urlencode($content);
//
//        $data=array (
//            'username'=>$username,
//            'password_md5'=>$password_md5,
//            'apikey'=>$apikey,
//            'mobile'=>$mobile,
//            'content'=>$contentUrlEncode,
//            'encode'=>$encode,
//        );
//        $result = $this->curlSMS($url,$data);
//        //print_r($data); //测试
////        return $result;
//
//        echo fit_api(true, 200, '验证短信已发送!', $mobile_code);
//    }

//    function curlSMS($url,$post_fields=array()) {
//        $ch=curl_init();
//        curl_setopt($ch,CURLOPT_URL,$url);//用PHP取回的URL地址（值将被作为字符串）
//        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//使用curl_setopt获取页面内容或提交数据，有时候希望返回的内容作为变量存储，而不是直接输出，这时候希望返回的内容作为变量
//        curl_setopt($ch,CURLOPT_TIMEOUT,30);//30秒超时限制
//        curl_setopt($ch,CURLOPT_HEADER,1);//将文件头输出直接可见。
//        curl_setopt($ch,CURLOPT_POST,1);//设置这个选项为一个零非值，这个post是普通的application/x-www-from-urlencoded类型，多数被HTTP表调用。
//        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);//post操作的所有数据的字符串。
//        $data = curl_exec($ch);//抓取URL并把他传递给浏览器
//        curl_close($ch);//释放资源
//        $res = explode("\r\n\r\n",$data);//explode把他打散成为数组
//        return $res[2]; //然后在这里返回数组。
//    }

    public function sms_valid() {
        $smsModel = M('sms');

        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";

        $phone = I('phone');

        if (!$phone) {
            echo fit_api(true, 0, '没有获取到手机号码!', ''); exit;
        }

        $mobile_code = $this->random(4,1);

        $post_data = "account=cf_ecomoter&password=echo5838&mobile=". $phone ."&content=".rawurlencode("您的验证码是：".$mobile_code);
        header("Content-type:text/html; charset=UTF-8");
        //密码可以使用明文密码或使用32位MD5加密
        $gets =  $this->Post($post_data, $target);

        $smsModel->id = uniqid().time().rand(10000000,99999999);
        $smsModel->phone_number = $phone;
        $smsModel->text = "您的验证码是：".$mobile_code;
        $smsModel->code = $mobile_code;
        $smsModel->msg_type = 0;
        $smsModel->create_at = date('Y-m-d H:i:s', time());

        $smsModel->add();

        echo fit_api(true, 200, '验证短信已发送!', $mobile_code);
    }

    #endregion


} 