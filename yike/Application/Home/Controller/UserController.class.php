<?php
namespace Home\Controller;

use Think\Controller;


class UserController extends Controller
{
    private $website = "http://yike.ecomoter.com";
    private $mallwebsite = "http://mall.ecomoter.com";

    public function testapi()
    {

    }

    //发送短信方法
    public function sms_valid($phone, $content)
    {
        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";

        if (!$phone) {
            echo fit_api(true, 0, '没有获取到手机号码!', '');
            exit;
        }

        $post_data = "account=cf_ecomoter&password=echo5838&mobile=" . $phone . "&content=" . $content;
        header("Content-type:text/html; charset=UTF-8");
        //密码可以使用明文密码或使用32位MD5加密
        $gets = $this->Post($post_data, $target);

        return $gets;
    }

    public function Post($curlPost, $url)
    {
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

    public function getRegSmsCode()
    {
        $smsModel = M('sms');
        $phone = I('phone_number');
        $msg_type = I('msg_type');
        $mobile_code = rand(1000, 9999);

        $content = "您的校验码是：【" . $mobile_code . "】。请不要把校验码泄露给其他人。";

        $contentUrlEncode = urlencode($content);

        $data = array(
            'id' => md5(uniqid() . time() . rand(10000000, 99999999)),
            'phone_number' => $phone,
            'text' => $content,
            'msg_type' => $msg_type,
            'code' => $mobile_code,
            'create_at' => time()
        );

        $result = $this->sms_valid($phone, $contentUrlEncode);

        $result = $smsModel->add($data);

        if (!$result) {
            echo fit_api(true, 1, '验证短信发送失败!', '');
            exit;
        }

        echo fit_api(true, 0, '验证短信已发送!', '');
    }

    //注册用户及机车sn
    public function Register()
    {
        $phone_number = I('phone_number');
        $password = I('password');
        $car_sn = I('car_sn');
        $system = I('system');
        $system_type = I('system_type');
        $phone_type = I('phone_type');
        $udid = I('udid');
        $checkcode = I('checkcode');

        if ($phone_number && $checkcode && $password) {
            //检查机车sn是否注册
            $car = M('Car');
            if ($car_sn) {
                $condition = array(
                    "car_sn" => $car_sn
                );
                $car_data = $car->where($condition)->select();

                if (!$car_data) {
                    echo fit_api(true, 6, 'car_sn不存在!', '');
                    exit;
                }

                $num = $car->query("select * from fit_user_car a left join fit_car b on a.car_id=b.id where car_sn='$car_sn'");//生成id
                if ($num) {
                    echo fit_api(true, 6, 'car_sn已被绑定!', '');
                    exit;
                }
            } else {
                $car_sn = '';
            }
            //检查手机号是否注册
            $user = M('User');

            $condition = array(
                "phone_number" => $phone_number
//            "user_name" => $user_name,
//            "_logic" => "or"
            );

            $num = $user->where($condition)->select();

            if ($num) {
                echo fit_api(true, 5, '该手机已绑定!', '');
                exit;
            }

            //检查验证码
            if (CheckCode($phone_number, $checkcode, 0) == 1) {
                //注册
                //$num = $car->query("select count(id) as num from fit_car");//生成id
                //$num = $num[0]["num"] + 1;
                //while (strlen($num) < 6) {
                //    $num = '0' . $num;
                //}

                //$id = uniqid() . time() . rand(10000000, 99999999) . $num;
                //$car->id = $id;
                //$car->car_sn = $car_sn;

                //$result = $car->add();


                $user->car_id = $car_data[0]["id"];
                $user->user_name = $phone_number;
                $user->password = md5($password);
                $user->phone_number = $phone_number;
                //$user->push_alias = $car_sn;

                $result = $user->add();
                $user_id = $user->getLastInsID();

                $user_car = M('user_car');
                $user_car->user_id = $user_id;
                $user_car->car_id = $car_data[0]["id"];
                $user_car->add();

                if (false == $result) {
                    echo fit_api(true, 3, '注册失败!', '');
                } else {
                    $key = md5(uniqid() . time() . rand(10000000, 99999999));
                    $this->insertLoginLog($key, $user_id, $system, $system_type, $phone_type, $udid);

                    $cl = array();
                    $cl[] = array(
                        "car_id" => $car_data[0]["id"],
                        "car_sn" => $car_sn,
                        "carSimpleName" => "",
                        "carName" => "",
                        "carImgUrl" => "",
                        "isDefault" => 1,
                        "bluetoochMac" => '',
                        "bluetoochName" => '',
                        "bluetoochCommandPwd" => ''
                    );
                    $data = array(
                        "access_token" => $key,
                        "userId" => $user_id,
                        "pushAlias" => $user_id,
                        "carList" => $cl
                    );
                    echo fit_api(true, 0, '注册成功!', $data);
                }
            } else {
                echo fit_api(true, 4, '验证码错误!', '');
            }
        } else {
            echo fit_api(true, 1, '参数为空!', '');
        }
    }

    //更新昵称
    public function UpdateNick()
    {
        $access_token = I('access_token');
        $nick_name = I('nick_name');

        if ($access_token && $nick_name) {
            $login = M('Login');
            $condition['key'] = $access_token;
            $user_data = $login->where($condition)->select();

            if ($user_data) {
                $userid = $user_data[0]["user_id"];
                $push_alias = $user_data[0]["push_alias"];

                $user = M('User');

                $user->nick_name = $nick_name;

                $condition = array(
                    'id' => $userid
                );
                $result = $user->where($condition)->save();

                if ($result != false) {
                    $carList = array();

                    //查询用户关联机车
                    $user_car = M('user_car');

                    $conditon = array(
                        "user_id" => $userid
                    );

                    $user_car_data = $user_car->where($conditon)->select();

                    //这里判断默认car和关联car
                    if ($user_car_data) {
                        for ($i = 0; $i < count($user_car_data); $i++) {
                            $car = M('Car');
                            $condition = array(
                                "id" => $user_car_data[$i]["car_id"]
                            );

                            $car_data = $car->where($condition)->select();

                            if ($car_data) {
                                $default = 1;
                                if ($user_data[0]["car_id"] != $car_data[0]["id"]) {
                                    $default = 2;
                                }

                                $carList[] = array(
                                    "car_id" => $car_data[0]["id"],
                                    "car_sn" => $car_data[0]["car_sn"],
                                    "carImgUrl" => $car_data[0]["car_imgurl"] . '?id=' . time(),
                                    "carSimpleName" => $car_data[0]["car_simple_name"],
                                    "carName" => $car_data[0]["car_name"],
                                    "isDefault" => $default,
                                    "bluetoochMac" => $car_data[0]["bluetoochMac"],
                                    "bluetoochName" => $car_data[0]["bluetoochName"],
                                    "bluetoochCommandPwd" => $car_data[0]["bluetoochCommandPwd"]
                                );
                            }
                        }
                    }

                    $result = array(
                        "access_token" => $access_token,
                        "userId" => $userid,
                        "nickName" => $user_data[0]["nick_name"],
                        "userPhone" => $user_data[0]["phone_number"],
                        "userHeadImgUrl" => $user_data[0]["head_pic"],
                        "pushAlias" => $push_alias,
                        "carList" => $carList
                    );

                    echo fit_api(true, 0, '更新成功!', $result);
                } else {
                    echo fit_api(true, 3, '更新失败!', '');
                }
            } else {
                echo fit_api(true, 99, 'access_token不正确!', '');
            }
        } else {
            echo fit_api(true, 1, '参数为空!', '');
        }
    }

    //更新个人信息
    public function UpdateUserInfo()
    {
        $access_token = I('access_token');
        $headpicFile = I('headpicFile');
        $nickname = I('nickname');
        $userPhone = I('userPhone');
        $nickRealName = I('nickRealName');

        if (!$access_token || !$headpicFile || !$nickname || !$userPhone || !$nickRealName) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            //查询用户关联机车
            $user = M('user');

            $condition = array(
                "id" => $userid
            );

            $path = dirname(dirname(__FILE__));
            $path = substr($path, 0, strrpos($path, '/', 0));
            $path = substr($path, 0, strrpos($path, '/', 0)) . '/Public/uploads/';

            $file = $this->save_img($headpicFile);
            //$filename = md5(time() . mt_rand(10, 99)) . ".png"; //图片名称
            //file_put_contents($path . $filename, $headpicFile);              //保存文件,内容为二进制流

            if ($file) {
                $user->nick_name = $nickname;
                $user->head_pic = $this->website . '/Public/uploads/' . $file;
                $user->phone_number = $userPhone;
                $user->name = $nickRealName;

                $result = $user->where($condition)->save();

                if ($result != false) {
                    echo fit_api(true, 0, '更新成功!', array("userHeadImgUrl" => $this->website . '/Public/uploads/' . $file));
                } else {
                    echo fit_api(true, 4, '更新失败!', '');
                }
            } else {
                echo fit_api(true, 4, '文件上传失败!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    public function save_img($image)
    {
        //如果上传的文件没有图片base64加密前缀 就是原始图片路径 直接返回
        if (strpos($image, "data:image/jpeg;") < 0) {
            return $image;
        }

        $s = base64_decode(str_replace('data:image/jpeg;base64,', '', $image));

        $pic_name = date('YmdHis') . rand(1000000, 9999999) . '.jpg';

        $full_pic_name = SITE_DIR . UPLOAD_DIR . $pic_name;
//        echo fit_api(true, 0, '保存服务网点失败!'.$full_pic_name, '');exit;
        $file_count = file_put_contents($full_pic_name, $s);

        if (!$file_count) {
            return false;
        } else {
            return $pic_name;
        }
    }

    //app登录
    public function Login()
    {
        $user_name = I('user_name');
        $password = I('password');
        $system = I('system');
        $system_type = I('system_type');
        $phone_type = I('phone_type');
        $udid = I('udid');

        if (!$user_name || !$password) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        //获取user_id及car_id
        $user = M('User');
        $condition["user_name"] = $user_name;
        $condition["password"] = md5($password);
        $user_data = $user->where($condition)->select();

        if ($user_data) {
            //插入login表
            $key = md5(uniqid() . time() . rand(10000000, 99999999));
            $num = $this->insertLoginLog($key, $user_data[0]['id'], $system, $system_type, $phone_type, $udid);

            if ($num > 0) {
                $user_id = $user_data[0]["id"];
                $push_alias = $user_data[0]["push_alias"];

                $carList = array();

                //查询用户关联机车
                $user_car = M('user_car');

                $conditon = array(
                    "user_id" => $user_id
                );

                $user_car_data = $user_car->where($conditon)->select();

                //这里判断默认car和关联car
                if ($user_car_data) {
                    for ($i = 0; $i < count($user_car_data); $i++) {
                        $car = M('Car');
                        $condition = array(
                            "id" => $user_car_data[$i]["car_id"]
                        );

                        $car_data = $car->where($condition)->select();

                        if ($car_data) {
                            $default = 1;
                            if ($user_data[0]["car_id"] != $car_data[0]["id"]) {
                                $default = 2;
                            }

                            $carList[] = array(
                                "car_id" => $car_data[0]["id"],
                                "car_sn" => $car_data[0]["car_sn"],
                                "carImgUrl" => $car_data[0]["car_imgurl"] . '?id=' . time(),
                                "carSimpleName" => $car_data[0]["car_simple_name"],
                                "carName" => $car_data[0]["car_name"],
                                "isDefault" => $default,
                                "bluetoochMac" => $car_data[0]["bluetoochMac"],
                                "bluetoochName" => $car_data[0]["bluetoochName"],
                                "bluetoochCommandPwd" => $car_data[0]["bluetoochCommandPwd"]
                            );
                        }
                    }
                }

                $result = array(
                    "access_token" => $key,
                    "userId" => (int)$user_id,
                    "nickName" => $user_data[0]["nick_name"],
                    "userPhone" => $user_data[0]["phone_number"],
                    "userHeadImgUrl" => $user_data[0]["head_pic"],
                    "pushAlias" => $user_id,
                    "carList" => $carList
                );

                echo fit_api(true, 0, '登录成功!', $result);
            } else {
                echo fit_api(true, 3, 'login插入失败!', "");
            }
        } else {
            echo fit_api(true, 2, '用户名或密码错误!', '');
        }
    }

    //注销
    public function Logout()
    {
        $access_token = I('access_token');

        if (!$access_token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $login->is_expire = 1;

        $condition = array(
            'key' => $access_token
        );

        $result = $login->where($condition)->save();

        if (false !== $result) {
            echo fit_api(true, 0, '成功!', '');
        } else {
            echo fit_api(true, 2, '失败!', '');
        }
    }

    //增加登录记录
    public function insertLoginLog($key, $uid, $system, $system_type, $phone_type, $udid)
    {
//        header( 'Access-Control-Allow-Origin:*' );

        $login = M('Login');

        $login->key = $key;
        $login->user_id = $uid;
        $login->login_time = date('Y-m-d H:i:s', time());
        $login->expire_time = date("Y-m-d H:i:s", strtotime("8 hour"));
        $login->is_expire = 0;
        $login->system = $system;
        $login->system_type = $system_type;
        $login->phone_type = $phone_type;
        $login->udid = $udid;

        return $login->add();
    }

    //忘记密码验证接口
    public function SetNewPwdStep1()
    {
        $phone_number = I('phone_number');
        //$access_token = I('access_token');
        $checkcode = I('checkcode');

        if ($phone_number && $checkcode) {
            if (CheckCode($phone_number, $checkcode, 5) == 1) {
                echo fit_api(true, 0, '成功!', '');
            } else {
                echo fit_api(true, 2, '验证码不正确!', '');
            }
        } else {
            echo fit_api(true, 1, '参数为空!', '');
        }
    }

    //设置新密码
    public function SetNewPwdStep2()
    {
        $phone_number = I('phone_number');
        //$access_token = I('access_token');
        $newpassword = I('newpassword');

        if ($phone_number && $newpassword) {

            $user = M('User');
            $user->password = md5($newpassword);

            //查找用户帐号字段，更新密码
            $condition = array(
                "user_name" => $phone_number
            );

            $result = $user->where($condition)->save();


            echo fit_api(true, 0, '更新成功!', '');

        } else {
            echo fit_api(true, 1, '参数为空!', '');
        }
    }

    //重新绑定手机
    public function ReBindPhone()
    {
        $new_user_name = I('new_user_name');
        $new_phone = I('new_phone');
        $new_checkcode = I('new_checkcode');
        $old_phone = I('old_phone');
        $old_checkcode = I('old_checkcode');

        if (!$old_checkcode) {
            echo fit_api(true, 2, '旧手机号验证码错误!', '');
            exit;
        }
        if (!$new_checkcode) {
            echo fit_api(true, 4, '新手机号验证码错误!', '');
            exit;
        }

        //检查新手机号是否存在
        $db = M('User');

        $condition["phone_number"] = $new_phone;

        $num = $db->where($condition)->select();

        if ($num) {
            echo fit_api(true, 5, '新手机号已存在!', '');
            exit;
        }

        $sms = M('Sms');

        $condition = array(
            "code" => $old_checkcode,
            "phone_number" => $old_phone
        );

        $num = $sms->where($condition)->select();

        if ($num) {
            //旧手机验证码正确，继续验证新手机号验证码
            $condition = array(
                "code" => $new_checkcode,
                "phone_number" => $new_phone
            );
            $num = $sms->where($condition)->select();

            if ($num) {
                //新手机验证码正确
                $user = M('User');

                $condition = array(
                    "phone_number" => $old_phone
                );

                $user->phone_number = $new_phone;
                if ($new_user_name)
                    $user->user_name = $new_user_name;
                $num = $user->where($condition)->save();

                if ($num != false) {
                    echo fit_api(true, 0, '解绑成功!', '');
                } else {
                    echo fit_api(true, 6, '其它错误!', '');
                }
            } else {
                echo fit_api(true, 4, '新手机号验证码错误!', '');
            }
        } else {
            echo fit_api(true, 2, '旧手机号验证码错误!', '');
        }

    }

    //解绑用户的机车
    public function UnbundlingCar()
    {
        $access_token = I('access_token');
        $car_id = I('car_id');
        $phone_number = I('phone_number');
        $checkcode = I('checkcode');

        if ($access_token && $car_id && $phone_number && $checkcode) {
            $login = M('Login');
            $condition['key'] = $access_token;
            $data = $login->where($condition)->select();

            if ($data) {
                $userid = $data[0]["user_id"];

                //测试用户，且验证码为000000，直接解绑
                if ((substr($phone_number, 0, 2) == '88' && $checkcode == '000000') || CheckCode($phone_number, $checkcode, 4) == 1) {
                    $user = M('User');
                    $condition = array(
                        "id" => $userid
                    );

                    $user_data = $user->where($condition)->select();

                    if ($user_data[0]["car_id"] == $car_id) {
                        $user->car_id = "";
                        $user->where($condition)->save();
                    }

                    $user_car = M('user_car');

                    $condition = array(
                        "user_id" => $userid,
                        "car_id" => $car_id
                    );

                    $result = $user_car->where($condition)->delete();

                    if (false !== $result) {
                        //更新机车状态为1
                        $carmodel = M('Car');
                        $condition = array(
                            "id" => $car_id
                        );
                        $carmodel->state = 1;
                        $result = $carmodel->where($condition)->save();

                        if (substr($phone_number, 0, 2) == '88') {
                            $gps = M('gps');
                            $condition = array(
                                "car_id" => $car_id
                            );
                            $gps->where($condition)->delete();

                            $history = M('run_history');
                            $condition = array(
                                "car_id" => $car_id
                            );
                            $history->where($condition)->delete();
                        }

                        echo fit_api(true, 0, '解绑成功!', '');
                    } else {
                        echo fit_api(true, 3, '绑定失败!', '');
                    }
                } else {
                    echo fit_api(true, 4, '验证码错误!', '');
                }
            } else {
                echo fit_api(true, 99, 'access_token不正确!', '');
            }
        } else {
            echo fit_api(true, 1, '参数为空!', '');
        }
    }

    //绑定机车到用户
    public function BindCar()
    {
        $access_token = I('access_token');
        $car_sn = I('car_sn');

        $success = '';
        if ($access_token && $car_sn) {
            $login = M('Login');
            $condition['key'] = $access_token;
            $data = $login->where($condition)->select();

            if ($data) {
                $userid = $data[0]["user_id"];
                $phone_number = $data[0]["phone_number"];

                $car = M('Car');

                $condition = array(
                    "car_sn" => $car_sn
                );
                $data = $car->where($condition)->select();

                if ($data) {
                    $car_id = $data[0]["id"];
                    $iccid = $data[0]["iccid"];
                    $user_car_M = M('user_car');

                    $conditon = array(
                        "car_id" => $car_id
                    );
                    $user_car_data = $user_car_M->where($conditon)->select();

                    if ($user_car_data) {
                        echo fit_api(true, 5, '机车序列号已被绑定!', '');
                        exit;
                    }

                    if (substr($phone_number, 0, 2) != '88') { //正式用户，激活机车
                        $recharge = M('recharge');
                        $recharge_log = M('recharge_log');

                        $condition = array(
                            "iccid" => $iccid
                        );
                        $recharge_data = $recharge->where($condition)->select();

                        if (count($recharge_data) == 0) {
                            $html = Recharge($iccid);

                            $json_string = json_decode($html);

                            $recharge_log->iccid = $iccid;
                            $recharge_log->time = date('Y-m-d H:i:s', time());
                            $recharge_log->html = $json_string;
                            $recharge_log->add();

                            if ($json_string->code != '0') {
                                $success = '充值失败';
                            } else {
                                //$json_string->orderNumber
                                $recharge->order_num = $json_string->orderNumber;
                                $recharge->iccid = $iccid;
                                $recharge->time = date('Y-m-d H:i:s', time());
                                $recharge->add();
                                $success = '充值成功';
                            }
                        }
                    }

                    $user_car = M('user_car');
                    $user_car->user_id = $userid;
                    $user_car->car_id = $car_id;
                    $result = $user_car->add();

                    $user = M('user');
                    $condition = array(
                        "id" => $userid
                    );
                    $user->car_id = $car_id;
                    $result = $user->where($condition)->save();

                    $car_info = array(
                        "car_id" => $car_id,
                        "car_sn" => $car_sn,
                        "carSimpleName" => $data[0]['car_simple_name'],
                        "carName" => $data[0]['car_name'],
                        "carImgUrl" => $data[0]['car_imgurl'] . '?id=' . time(),
                        "isDefault" => 2,
                        "bluetoochMac" => $data[0]['bluetoochMac'],
                        "bluetoochName" => $data[0]['bluetoochName'],
                        "bluetoochCommandPwd" => $data[0]['bluetoochCommandPwd']
                    );

                    if (false !== $result) {
                        //更新机车状态为2
                        $carmodel = M('Car');
                        $condition = array(
                            "id" => $car_id
                        );
                        $carmodel->state = 2;
                        $result = $carmodel->where($condition)->save();

                        //根据iccid发送短信激活
                        //$car_iccid = $data[0]["iccid"];

                        //if($car_iccid) {
                        //    IccidSendSmsActity($car_iccid);
                        // }

                        echo fit_api(true, 0, '绑定成功!' . $success, $car_info);
                    } else {
                        echo fit_api(true, 3, '绑定失败!' . $success, '');
                    }
                } else {
                    echo fit_api(true, 4, '机车序列号不存在!', '');
                }
            } else {
                echo fit_api(true, 99, 'access_token不正确!', '');
            }
        } else {
            echo fit_api(true, 1, '参数为空!', '');
        }
    }

    //获取用户消息列表
    public function GetMsgList()
    {
        $access_token = I('access_token');
        $currentPage = I('currentPage');
        $pageSize = I('pageSize', 20);

        if (!$access_token || !$currentPage) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            $msg = M('user_msg');

            $condition = array(
                "user_id" => $userid
            );

            $msg_data = $msg->where($condition)->order('Id desc')->select();

            //分页
            $result = array("msgList" => array());

            if ($currentPage < 1) {
                $currentPage = 1;
            }

            for ($i = ($currentPage - 1) * $pageSize; $i < $currentPage * $pageSize; $i++) {
                if ($i >= count($msg_data)) {
                    break;
                } else {
                    $result["msgList"][] = array(
                        "msgId" => $msg_data[$i]["Id"],
                        "timeInfo" => $msg_data[$i]["time"],
                        "isReaded" => $msg_data[$i]["read"],
                        "msgTitle" => $msg_data[$i]["title"],
                        "msgAbstract" => $msg_data[$i]["abstract"]
                    );
                }
            }

            echo fit_api(true, 0, '成功!', $result);
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    //获取我的消息详情
    public function GetUserMsgInfo()
    {
        $access_token = I('access_token');
        $msgId = I('msgId');

        if (!$access_token || !$msgId) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $msg = M('user_msg');

            $condition = array(
                "Id" => $msgId
            );

            $msg_data = $msg->where($condition)->select();

            $result = array("msgList" => array());

            if ($msg_data) {
                $result = array(
                    "msgId" => $msgId,
                    "timeInfo" => $msg_data[0]["time"],
                    "isReaded" => $msg_data[0]["read"],
                    "msgTitle" => $msg_data[0]["title"],
                    "msgAbstract" => $msg_data[0]["abstract"],
                    "msgInfo" => $msg_data[0]["info"],
                    "msgInfoUrl" => $this->mallwebsite . '/mobile/usermsg_edit?id=' . $msgId
                );

                $msg->read = 1;
                $msg->where($condition)->save();
            }
            echo fit_api(true, 0, '成功!', $result);
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    //删除我的消息
    public function DeleteMsg()
    {
        $access_token = I('access_token');
        $msgId = I('msgId');

        if (!$access_token || !$msgId) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $msg = M('user_msg');

            $condition = array(
                "Id" => $msgId
            );

            $result = $msg->where($condition)->delete();

            if ($result != false) {
                echo fit_api(true, 0, '删除成功!', '');
            } else {
                echo fit_api(true, 3, '失败!', '');
            }

        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    //删除服务器全部我的消息
    public function DeleteUserMsg()
    {
        $access_token = I('access_token');

        if (!$access_token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];
            $msg = M('user_msg');

            $condition = array(
                "user_id" => $userid
            );

            $result = $msg->where($condition)->delete();

            if ($result != false) {
                echo fit_api(true, 0, '删除成功!', '');
            } else {
                echo fit_api(true, 3, '失败!', '');
            }

        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    //标记已读（全部服务器我的消息）
    public function UpdateUserMsg()
    {
        $access_token = I('access_token');

        if (!$access_token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];
            $msg = M('user_msg');

            $condition = array(
                "user_id" => $userid
            );
            $msg->read = 1;
            $result = $msg->where($condition)->save();
            echo fit_api(true, 0, '更新成功!', '');
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    //常见问题
    public function Question()
    {
        echo $this->mallwebsite . '/mobile/question';
    }

    //关于
    public function About()
    {
        echo $this->mallwebsite . '/mobile/about';
    }

    //版本检测升级
    public function GetVersion()
    {
        $versionCode = I('versionCode');
        $system_type = I('system_type');

        if (!$versionCode || !$system_type) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $version = M('version');

        $condition['id'] = $system_type;

        $version_data = $version->where($condition)->select();

        if ($version_data) {
            $code = $version_data[0]["version_code"];

            $result = array();

            $tmp1 = (int)$versionCode;
            $tmp2 = (int)$code;

            if ($tmp1 < $tmp2) {
                $result = array(
                    "hasNewVerison" => 1,
                    "newVersionUrl" => $this->website . $version_data[0]["new_version_url"],
                    "isMustUpdate" => $version_data[0]["is_must_update"],
                    "newVersionName" => $version_data[0]["newVersionName"],
                    "newVersionSize" => $version_data[0]["version_size"]
                );
            } else {
                $result = array(
                    "hasNewVerison" => 2,
                    "newVersionUrl" => $this->website . $version_data[0]["new_version_url"],
                    "isMustUpdate" => $version_data[0]["is_must_update"],
                    "newVersionName" => $version_data[0]["newVersionName"],
                    "newVersionSize" => $version_data[0]["version_size"]
                );
            }
            echo fit_api(true, 0, 'success!', $result);

        } else {
            echo fit_api(true, 2, 'no version!', '');
        }
    }

    //推送开关设置
    public function setMsgPushInfo()
    {
        $access_token = I('access_token');
        $pushTypeId = I('pushTypeId');
        $isReceive = I('isReceive');

        if (!$access_token || !$pushTypeId || !$isReceive) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];
            $user_pushsetModel = M('user_pushset');

            $condition = array(
                "user_id" => $userid,
                "pushtype_id" => $pushTypeId
            );

            $user_pushset_data = $user_pushsetModel->where($condition)->select();

            if ($user_pushset_data) {
                $user_pushsetModel->isReceive = $isReceive;
                $user_pushsetModel->time = date('Y-m-d H:i:s', time());
                $user_pushsetModel->where($condition)->save();
            } else {
                $user_pushsetModel->user_id = $userid;
                $user_pushsetModel->pushtype_id = $pushTypeId;
                $user_pushsetModel->isReceive = $isReceive;
                $user_pushsetModel->time = date('Y-m-d H:i:s', time());
                $user_pushsetModel->add();
            }

            echo fit_api(true, 0, '成功!', '');
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    //获取推送设置列表
    public function GetMsgPushSettingList()
    {
        $access_token = I('access_token');

        if (!$access_token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            $user_pushsetModel = M('user_pushset');

            $user_pushset_data = $user_pushsetModel->query("select a.*,b.isReceive from fit_pushtype a left join (select * from fit_user_pushset where user_id=$userid) b on a.id = b.pushtype_id");

            if ($user_pushset_data) {
                for ($i = 0; $i < count($user_pushset_data); $i++) {

                    if ($user_pushset_data[$i]["isReceive"] == '') {
                        $result["msgPushSettingList"][] = array(
                            "pushTypeId" => intval($user_pushset_data[$i]["id"]),
                            "pushTypeName" => $user_pushset_data[$i]["pushtype_name"],
                            "isReceive" => 1
                        );
                    } else {
                        $result["msgPushSettingList"][] = array(
                            "pushTypeId" => intval($user_pushset_data[$i]["id"]),
                            "pushTypeName" => $user_pushset_data[$i]["pushtype_name"],
                            "isReceive" => intval($user_pushset_data[$i]["isReceive"])
                        );
                    }
                }
            } else {
                $result["msgPushSettingList"] = array();
            }
            echo fit_api(true, 0, '成功!', $result);
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }
}