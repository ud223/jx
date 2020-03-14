<?php
namespace Home\Controller;

use Think\Controller;

class CarController extends Controller
{
    private $wsdl = "http://apicenter.huidesichuang.com/Service.asmx?wsdl";
    //高德经纬度转地址key
    private $key = "7d284748b987ff435d222e6e5092d2e0";

    private $website = "http://yike.ecomoter.com";
    private $mallwebsite = "http://mall.ecomoter.com";

    public function testapi()
    {
        //print_r($arr);
        //echo $pic_file;exit;

        //echo strpos($latitude,".");
        //PushMessage('100c1c0000101013b','测试一下','标题');
        //echo phpinfo();exit;
        //echo date('Y-m-d H:i:s', time());exit;
        //echo date("Y-m-d H:i:s", strtotime("8 hour"));
        //echo $run_history->getlastsql();exit;

        //$oldtime = '2010-11-10 22:19:21';
        //$catime = strtotime($oldtime);
        //echo $catime;
//print_r(Recharge('89860616090023587544'));
        // echo date('Y-m-d 00:00:00', strtotime('this monday'));exit;
        //echo date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));
        for ($i = 0; $i < 7; $i++) {

            echo date('D', time() - ((date('w') == 0 ? 7 : date('w')) - $i - 1) * 24 * 3600);
        }
    }

//gps经纬度转换高德经纬度
    public
    function GetAmapGps($lng, $lat)
    {
        $url = "http://restapi.amap.com/v3/assistant/coordinate/convert?locations=$lng,$lat&coordsys=gps&output=json&key=$this->key";

        $result = file_get_contents($url);

        $json_string = json_decode($result);

        if ($json_string->status == '1') {
            return $json_string->locations;
        }
        return $lng . ',' . $lat;
    }

//经纬度查询地址
    public
    function GetAddress($lng, $lat)
    {
        $result = $this->GetAmapGps($lng, $lat);

        $lng = substr($result, 0, strpos($result, ','));
        $lat = substr($result, strpos($result, ',') + 1);

        $url = "http://restapi.amap.com/v3/geocode/regeo?key=" . $this->key . "&location=" . $lng . "," . $lat;

        $result = file_get_contents($url);

        $json_string = json_decode($result);

        if ($json_string->regeocode->formatted_address) {
            return $json_string->regeocode->formatted_address;
        }
        return '';
    }

//增加登录记录
    public
    function insertLoginLog($key, $uid)
    {
//        header( 'Access-Control-Allow-Origin:*' );

        $login = M('Login');

        $login->key = $key;
        $login->user_id = $uid;
        $login->login_time = date('Y-m-d H:i:s', time());
        $login->expire_time = date("Y-m-d H:i:s", strtotime("8 hour"));
        $login->is_expire = 2;
        return $login->add();
    }

    public
    function GetSimList($Iccid, $userName, $passWord)
    {
        $client = new \SoapClient($this->wsdl);
        $param = array('iccid' => $Iccid, 'userName' => $userName, 'passWord' => $passWord);
        $ret = $client->GetSimList($param);
        $array = object_array($ret);

        $arr = json_decode($array['GetSimListResult'], true);
        return $arr['ds'][0]['Sim'];
    }

//上传机车信息
    public
    function uploadCar()
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            echo fit_api(true, 0, 'no auth', '');
            exit;
        }

        $post_data = $GLOBALS['HTTP_RAW_POST_DATA'];
        //将JSON格式数据进行解码，解码后不是JSON数据格式，不可用echo直接输出
        $arr = json_decode($post_data, true);

        $tmp_auth = $_SERVER['HTTP_AUTHORIZATION'];

        $tmp_auth = str_replace('Basic', '', $tmp_auth);

        $auth = base64_decode($tmp_auth);

        $arr = $_POST;

        $phone_number = $arr['phone_number'];
        $battery_number = $arr['battery_number'];
        $arm_ver = $arr['arm_ver'];
        $muc_ver = $arr['muc_ver'];
        $ctl_ver = $arr['ctl_ver'];
        $bms_ver = $arr['bms_ver'];
        $car_sn = $arr['car_sn'];
        $blue_mac = $arr['blue_mac'];
        $blue_name = $arr['blue_name'];
        $blue_password = $arr['blue_password'];
        $iccid = $arr['iccid'];
        $b_charge_num = $arr['B_charge_num'];
        $b_discharge_protection_num = $arr['B_discharge_protection_num'];
        $b_short_circuit_num = $arr['B_short-circuit_num'];

//        $phone_number = I('phone_number');
//        $battery_number = I('battery_number');
//        $arm_ver = I('arm_ver');
//        $muc_ver = I('muc_ver');
//        $ctl_ver = I('ctl_ver');
//        $bms_ver = I('bms_ver');
//        $car_sn = I('car_sn');

        //参数必填判断
        if (!$car_sn) {
            echo fit_api(true, 0, '$car_sn parameters is imperfect!', '');
        }
        if (!$phone_number && $phone_number != 0) {
            echo fit_api(true, 0, '$phone_number parameters is imperfect!', '');
        }

        if (!$battery_number && $battery_number != 0) {
            echo fit_api(true, 0, '$battery_number parameters is imperfect!', '');
        }

        if (!$arm_ver && $arm_ver != 0) {
            echo fit_api(true, 0, '$arm_ver parameters is imperfect!', '');
        }

        if (!$muc_ver && $muc_ver != 0) {
            echo fit_api(true, 0, '$muc_ver parameters is imperfect!', '');
        }

        if (!$ctl_ver && $ctl_ver != 0) {
            echo fit_api(true, 0, '$ctl_ver parameters is imperfect!', '');
        }

        if (!$bms_ver && $bms_ver != 0) {
            echo fit_api(true, 0, '$bms_ver parameters is imperfect!', '');
        }

        if (!$iccid && $iccid != 0) {
            echo fit_api(true, 0, '$iccid parameters is imperfect!', '');
        }

        if (!$b_charge_num && $b_charge_num != 0) {
            echo fit_api(true, 0, '$b_charge_num parameters is imperfect!', '');
        }

        if (!$b_discharge_protection_num && $b_discharge_protection_num != 0) {
            echo fit_api(true, 0, '$b_discharge_protection_num parameters is imperfect!', '');
        }

        if (!$b_short_circuit_num && $b_short_circuit_num != 0) {
            echo fit_api(true, 0, '$b_short_circuit_num parameters is imperfect!', '');
        }

        if ($blue_password == "") {
            echo fit_api(true, 0, '$blue_password parameters is imperfect!', '');
        }
        if ($car_sn != $auth) {
            echo fit_api(true, 0, 'auth fail', '');
            exit;
        }

//        if (!preg_match("/^[1][3-8]\\d{9}$/",$phone_number)){
//            echo fit_api(true, 0, 'phone format is err!', '');
//            exit;
//        }

        $car = M('Car');

        $car->phone_number = $phone_number;
        $car->battery_number = $battery_number;
        $car->arm_ver = $arm_ver;
        $car->muc_ver = $muc_ver;
        $car->ctl_ver = $ctl_ver;
        $car->bms_ver = $bms_ver;
        $car->bluetoochMac = $blue_mac;
        $car->bluetoochName = $blue_name;
        $car->iccid = $iccid;
        $car->b_charge_num = $b_charge_num;
        $car->b_discharge_protection_num = $b_discharge_protection_num;
        $car->b_short_circuit_num = $b_short_circuit_num;

        $sim = $this->GetSimList($iccid, 'yxs', 'yxs123456');

        if ($sim)
            $car->phone_number = $sim;

        $car->bluetoochCommandPwd = $blue_password;

        $condition = array(
            "car_sn" => $car_sn
        );

        $run_history_car_id = '';
        $car_data = $car->where($condition)->select();

        $lock = 0;    //default 0
        if (count($car_data) > 0) {
            $run_history_car_id = $car_data[0]["id"];
            $lock = $car_data[0]["locked"];
            $result = $car->where($condition)->save();
        } else {
            //判断是否增加car_sn
            if (strlen($car_sn) != 17) {
                echo fit_api(true, 0, 'save fail,car_sn error!', '');
                exit;
            }

            $year = hexdec(substr($car_sn, 0, 2));
            $month = hexdec(substr($car_sn, 2, 2));
            $day = hexdec(substr($car_sn, 4, 2));
            $sn = hexdec(substr($car_sn, 6, 5));
            $cartype = hexdec(substr($car_sn, 11, 2));
            $sd = hexdec(substr($car_sn, 13, 2));
            $key = hexdec(substr($car_sn, 15, 2));

            $truekey = ($year + $month + $day + $cartype + $sd + (($sn & 0xf0000) >> 16) + (($sn & 0x0ff00) >> 8) + ($sn & 0x000ff)) % 0xff;

            if ($key != $truekey) {
                echo fit_api(true, 0, 'save fail,car_sn error!', '');
                exit;
            }

            $num = $car->query("select count(id) as num from fit_car");//生成id
            $num = $num[0]["num"] + 1;
            while (strlen($num) < 6) {
                $num = '0' . $num;
            }

            $id = uniqid() . time() . rand(10000000, 99999999) . $num;
            $run_history_car_id = $id;
            $car->id = $id;
            $car->car_sn = $car_sn;

            $result = $car->add();
        }

        if (false !== $result) {
            $tempver = substr($arm_ver, 1);
            $flaotver = floatval($tempver);

            if ($flaotver < 4.0) {
                //增加机车历史时间段，仅更新开始时间。结束时间在gps数据上传时更新
                $run_history = M('run_history');
                $run_history->car_id = $run_history_car_id;
                $run_history->start_date = date('Y-m-d H:i:s', time());
                $run_history->add();
            }


            $json = array(
                'result' => true,
                'code' => 1,
                'msg' => 'save success!',
                'data' => '',
                'lock' => $lock,
                'date' => date('Y-m-d', time()),
                'birthday' => '2017-01-01'
            );

            $str = json_encode($json);

            echo $str;
        } else {
            echo fit_api(true, 0, 'save fail!', '');
        }
    }

//检查机车状态，参数为8位16进制字符串，左高右低
    private
    function CheckCarState($state)
    {
        $state4 = decbin(hexdec(substr($state, 0, 2)));

        while (strlen($state4) < 8) {
            $state4 = "0" . $state4;
        }

        $state3 = decbin(hexdec(substr($state, 2, 2)));

        while (strlen($state3) < 8) {
            $state3 = "0" . $state3;
        }

        $state2 = decbin(hexdec(substr($state, 4, 2)));

        while (strlen($state2) < 8) {
            $state2 = "0" . $state2;
        }

        $state1 = decbin(hexdec(substr($state, 6, 2)));

        while (strlen($state1) < 8) {
            $state1 = "0" . $state1;
        }
        return $state4 . $state3 . $state2 . $state1;
    }

//上传机车状态信息(消息类型:1 异常提醒......代扩展)
    public
    function uploadCarState()
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            echo fit_api(true, 0, 'head no auth', '');
            exit;
        }

        $post_data = $GLOBALS['HTTP_RAW_POST_DATA'];
        //将JSON格式数据进行解码，解码后不是JSON数据格式，不可用echo直接输出
        $arr = json_decode($post_data, true);

        $tmp_auth = $_SERVER['HTTP_AUTHORIZATION'];

        $tmp_auth = str_replace('Basic', '', $tmp_auth);

        $auth = base64_decode($tmp_auth);

        $arr = $_POST;

        $car_state = $arr['car_state'];
        $powr_state = $arr['power_state'];
        $car_sn = $arr['car_sn'];

//        $car_state = I('car_state');
//        $powr_state = I('powr_state');
//        $car_sn = I('car_sn');

        if ($car_sn == '') {
            echo fit_api(true, 0, '$car_sn parameters is imperfect!' . $car_sn, '');
            exit;
        }

        if ($car_state == '') {
            echo fit_api(true, 0, '$car_state parameters is imperfect!' . $car_state, '');
            exit;
        }

        if ($powr_state == '') {
            echo fit_api(true, 0, '$powr_state parameters is imperfect!' . $powr_state, '');
            exit;
        }

        if ($car_sn != $auth) {
            //echo fit_api(true, 0, 'auth fail', '');
            echo fit_api(true, 0, 'auth fail', 'car_sn:' . $car_sn . '; auth_id:' . $auth);
            exit;
        }

        $db = M();
        $num = $db->query("select id  from fit_car where car_sn='" . $car_sn . "'");

        if ($num) {
            $car_id = $num[0]["id"];

            $user_car = M('user_car');

            $condition = array(
                "car_id" => $car_id
            );

            $user_car_data = $user_car->where($condition)->select();

            $user_id = $user_car_data[0]["user_id"];

            $state = M('Car_state');

            $state->car_state = $car_state;
            $state->powr_state = $powr_state;
            $state->creat_at = time();

            $condition = array(
                "car_id" => $car_id
            );

            $car_state_data = $state->where($condition)->select();

            if (count($car_state_data) > 0) {
                //检查状态码，如果与上次不一致且含1则推送上。
                $old_car_state = $car_state_data[0]["car_state"];

                $state_log = M('car_state_log');
                $state_log->car_state = $car_state;
                $state_log->old_car_state = $old_car_state;
                $state_log->car_id = $car_id;
                $state_log->car_sn = $car_sn;
                $state_log->creat_at = date('Y-m-d H:i:s', time());


                $old_car_state = $this->CheckCarState($old_car_state);
                $temp = $this->CheckCarState($car_state);

                //如果最后两位为1，则重置前面所有异常码为0
                if (substr($temp, 30, 1) == '1' || substr($temp, 31, 1) == '1') {
                    $temp = '000000000000000000000000000000' . substr($temp, 30, 1) . substr($temp, 31, 1);
                    $car_state = '000000' . substr($car_state, 6, 2);
                    $state->car_state = '000000' . substr($car_state, 6, 2);
                }

                //判断是否进行推送
                $user_pushsetModel = M('user_pushset');
                $user_pushset_data = $user_pushsetModel->query("select * from fit_user_pushset where pushtype_id = 1 and user_id = $user_id");
                $isReceive = 2;
                if (count($user_pushset_data) == 0 || $user_pushset_data[0]["isReceive"] == 1) {
                    $isReceive = 1;
                }

                if ((strpos($temp, '1', 0) && $temp != $old_car_state) || (strpos($temp, '1', 0) && time() - $car_state_data[0]["creat_at"] > 1800)) {

                    $state_log->memo = '$temp:' . $temp . '$old_car_state:' . $old_car_state . "time:" . time() . "at:" . $car_state_data[0]["creat_at"];
                    $state_log->add();

                    if (substr($temp, 31, 1) == '1') {
                        $msg = M('user_msg');
                        $msg->user_id = $user_id;
                        $msg->title = '车辆异常';
                        $msg->abstract = '车辆非法位移，异常代码为：' . $car_state;


                        if ($isReceive == 1) {
                            $msg->info = '您的爱车于' . date('Y-m-d H:i:s', time()) . '已发生非法位移，请您确认机车安全状态，如有必要，请进行机车远程锁死操作,机车sn:' . $car_sn . '。';
                            $msg->time = date('Y-m-d H:i:s', time());
                            $msg->read = 2;
                            $msg->err_code_msg .= '车辆异常移动';
                            $msg->add();

                            $msgid = $msg->getLastInsID();
                            PushMessage($user_id, '您的爱车于' . date('Y-m-d H:i:s', time()) . '已发生非法位移，请您确认机车安全状态，如有必要，请进行机车远程锁死操作,机车sn:' . $car_sn . '。', 'E客智慧', fit_api(true, 0, '', array(
                                "msgId" => $msgid,
                                "type" => 1,
                                "typeName" => "异常提醒",
                                "car_id" => $car_id,
                                "msgAbstract" => '您的爱车于' . date('Y-m-d H:i:s', time()) . '已发生非法位移，请您确认机车安全状态，如有必要，请进行机车远程锁死操作,机车sn:' . $car_sn . '。',
                                "userId" => $user_id,
                                "msgTitle" => '车辆非法位移'
                            )));
                        }

                        $result = $state->where($condition)->save();
                    }

                    if (substr($temp, 30, 1) == '1') {
                        if ($isReceive == 1) {
                            $msg = M('user_msg');
                            $msg->user_id = $user_id;
                            $msg->title = '车辆异常';
                            $msg->abstract = '车辆发生侧翻，异常代码为：' . $car_state;
                            $msg->info = '您的爱车于' . date('Y-m-d H:i:s', time()) . '已发生侧翻，请您确认机车安全状态，如有必要，请进行机车远程锁死操作,机车sn:' . $car_sn . '。';
                            $msg->time = date('Y-m-d H:i:s', time());
                            $msg->read = 2;
                            $msg->err_code_msg .= '车辆侧翻';
                            $msg->add();

                            $msgid = $msg->getLastInsID();
                            PushMessage($user_id, '您的爱车于' . date('Y-m-d H:i:s', time()) . '已发生侧翻，请您确认机车安全状态，如有必要，请进行机车远程锁死操作,机车sn:' . $car_sn . '。', 'E客智慧', fit_api(true, 0, '', array(
                                "msgId" => $msgid,
                                "type" => 1,
                                "typeName" => "异常提醒",
                                "car_id" => $car_id,
                                "msgAbstract" => '您的爱车于' . date('Y-m-d H:i:s', time()) . '已发生侧翻，请您确认机车安全状态，如有必要，请进行机车远程锁死操作,机车sn:' . $car_sn . '。',
                                "userId" => $user_id,
                                "msgTitle" => '车辆发生侧翻'
                            )));
                        }
                        $result = $state->where($condition)->save();
                    }

                    if (strpos(substr($temp, 0, 24), '1', 0)) {
                        $msg = M('user_msg');
                        $msg->user_id = $user_id;
                        $msg->title = '车辆异常';
                        $msg->abstract = '车辆检测到异常，异常代码为：' . $car_state;
                        $msg->info = '您的爱车于' . date('Y-m-d H:i:s', time()) . '检测到异常，异常代码为：' . $car_state . '，为了您的骑行安全，请前往售后点进行检修,机车sn:' . $car_sn . '。';
                        $msg->time = date('Y-m-d H:i:s', time());
                        $msg->read = 2;

                        for ($i = 0; $i < 24; $i++) {
                            if (substr($temp, $i, 1) == '1') {
                                if ($i == 23)
                                    $msg->err_code_msg .= '车辆下管异常<br>';
                                else if ($i == 22)
                                    $msg->err_code_msg .= '车辆过压异常<br>';
                                else if ($i == 21)
                                    $msg->err_code_msg .= '车辆欠压异常<br>';
                                else if ($i == 20)
                                    $msg->err_code_msg .= '车辆堵转异常<br>';
                                else if ($i == 19)
                                    $msg->err_code_msg .= '车辆刹车异常<br>';
                                else if ($i == 18)
                                    $msg->err_code_msg .= '车辆调速把异常<br>';
                                else if ($i == 17)
                                    $msg->err_code_msg .= '车辆过流异常<br>';
                                else if ($i == 16)
                                    $msg->err_code_msg .= '保留<br>';
                                else if ($i == 15)
                                    $msg->err_code_msg .= '车辆3g异常<br>';
                                else if ($i == 14)
                                    $msg->err_code_msg .= '车辆GPS异常<br>';
                                else if ($i == 13)
                                    $msg->err_code_msg .= '车辆蓝牙异常<br>';
                                else if ($i == 12)
                                    $msg->err_code_msg .= '车辆音频异常<br>';
                                else if ($i == 11)
                                    $msg->err_code_msg .= '车辆陀螺仪异常<br>';
                                else if ($i == 10)
                                    $msg->err_code_msg .= '车辆光感异常<br>';
                                else if ($i == 9)
                                    $msg->err_code_msg .= '车辆显示屏异常<br>';
                                else if ($i == 8)
                                    $msg->err_code_msg .= '保留<br>';
                                else if ($i == 7)
                                    $msg->err_code_msg .= '车辆电池通信异常<br>';
                                else if ($i == 6)
                                    $msg->err_code_msg .= '车辆控制器通信异常<br>';
                                else if ($i == 5)
                                    $msg->err_code_msg .= '车辆灯控通信异常<br>';
                                else if ($i == 4)
                                    $msg->err_code_msg .= '车辆MCU通信异常<br>';
                                else if ($i == 3)
                                    $msg->err_code_msg .= '车辆电池高温异常<br>';
                                else if ($i == 2)
                                    $msg->err_code_msg .= '机车锁死<br>';
                                else if ($i == 1)
                                    $msg->err_code_msg .= '保留<br>';
                                else if ($i == 0)
                                    $msg->err_code_msg .= '保留<br>';
                            }
                        }

                        if (substr($old_car_state, 0, 24) != substr($temp, 0, 24) || time() - $car_state_data[0]["creat_at"] > 1800) {
                            if ($isReceive == 1) {
                                $msg->add();
                                $msgid = $msg->getLastInsID();
                                PushMessage($user_id, '您的爱车于' . date('Y-m-d H:i:s', time()) . '检测到异常，异常代码为：' . $car_state . '，为了您的骑行安全，请前往售后点进行检修,机车sn:' . $car_sn . '。', 'E客智慧', fit_api(true, 0, '', array(
                                    "msgId" => $msgid,
                                    "type" => 1,
                                    "typeName" => "异常提醒",
                                    "car_id" => $car_id,
                                    "msgAbstract" => '您的爱车于' . date('Y-m-d H:i:s', time()) . '检测到异常，异常代码为：' . $car_state . '，为了您的骑行安全，请前往售后点进行检修,机车sn:' . $car_sn . '。',
                                    "userId" => $user_id,
                                    "msgTitle" => '车辆检测到异常'
                                )));
                            }
                            $result = $state->where($condition)->save();
                        }
                    }
                }
            } else {
                $state->id = $car_id;
                $state->car_id = $car_id;
                $state->creat_at = time();
                $result = $state->add();
            }

            if (false !== $result) {
                echo fit_api(true, 1, 'save success!', date('Y-m-d H:i:s', time()));
            } else {
                echo fit_api(true, 0, 'save fail!', '');
            }
        } else {
            echo fit_api(true, 0, 'car_sn non-existent!', '');
        }
    }

//上传机车电池状态信息
    public
    function uploadBatteryState()
    {
        $post_data = $GLOBALS['HTTP_RAW_POST_DATA'];
        //将JSON格式数据进行解码，解码后不是JSON数据格式，不可用echo直接输出
        $arr = json_decode($post_data, true);

        $tmp_auth = $_SERVER['HTTP_AUTHORIZATION'];

        $tmp_auth = str_replace('Basic', '', $tmp_auth);

        $auth = base64_decode($tmp_auth);

//        $discharge_currnet = I('discharge_currnet');
//        $charge_currnet = I('charge_currnet');
//        $filling_time = I('filling_time');
//        $temp = I('temp');
//        $soc_rt = I('soc_rt');
//        $car_sn = I('car_sn');

        $arr = $_POST;

        $discharge_currnet = $arr['discharge_currnet'];
        $charge_currnet = $arr['charge_currnet'];
        $filling_time = $arr['filling_time'];
        $temp = $arr['temp'];
        $soc_rt = $arr['soc_rt'];
        $car_sn = $arr['car_sn'];

        if ($car_sn == '') {
            echo fit_api(true, 0, '$car_sn parameters is imperfect!', '');
            exit;
        }
        if ($discharge_currnet == '') {

            echo fit_api(true, 0, '$discharge_currnet parameters is imperfect!' . $discharge_currnet, '');
            exit;
        }

        if ($charge_currnet == '') {
            echo fit_api(true, 0, '$charge_currnet parameters is imperfect!' . $charge_currnet, '');
            exit;
        }

        if ($filling_time == '') {
            echo fit_api(true, 0, '$filling_time parameters is imperfect!' . $filling_time, '');
            exit;
        }

        if ($temp == '') {
            echo fit_api(true, 0, '$temp parameters is imperfect!', '');
            exit;
        }

        if ($soc_rt == '') {
            echo fit_api(true, 0, '$soc_rt parameters is imperfect!', '');
            exit;
        }

        if ($car_sn != $auth) {
            echo fit_api(true, 0, 'auth fail', '');
            exit;
        }

        if (!preg_match("/^\\d+$/", $discharge_currnet)) {
            echo fit_api(true, 0, 'discharge_currnet format is err!', '');
            exit;
        }

        if (!preg_match("/^\\d+$/", $charge_currnet)) {
            echo fit_api(true, 0, 'charge_currnet format is err!', '');
            exit;
        }

        $db = M();
        $b_num = $db->query("select * from fit_car where car_sn='" . $car_sn . "'");

        if ($b_num) {
            $car_id = $b_num[0]["id"];

            $battery = M('Battery');

            $battery->discharge_currnet = $discharge_currnet;
            $battery->charge_currnet = $charge_currnet;
            $battery->filling_time = $filling_time;
            $battery->temp = $temp;
            $battery->soc_rt = $soc_rt;
            $battery->time = time();

            $condition = array(
                "battery_sn" => $car_id
            );

            $battery_data = $battery->where($condition)->select();

            if (count($battery_data) > 0) {
                $result = $battery->where($condition)->save();
            } else {
                $battery->id = $car_id;
                $battery->battery_sn = $car_id;
                $result = $battery->add();
            }

            if (false !== $result) {
                echo fit_api(true, 1, 'save success!', '');
            } else {
                echo fit_api(true, 0, 'save fail!', '');
            }
        } else {
            echo fit_api(true, 0, 'car_sn non-existent!', '');
        }
    }

//上传机车gps信息
    public
    function uploadGps()
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            echo fit_api(true, 0, 'no auth', '');
            exit;
        }

        $tmp_auth = $_SERVER['HTTP_AUTHORIZATION'];

        $post_data = $GLOBALS['HTTP_RAW_POST_DATA'];
        //将JSON格式数据进行解码，解码后不是JSON数据格式，不可用echo直接输出
        $arr = explode('&', $post_data);

        $tmp_auth = str_replace('Basic', '', $tmp_auth);

        $auth = base64_decode($tmp_auth);

        $arr = $_POST;

//        foreach ($arr as $a) {
//            $tmp = explode('=', $a);
//
//            $data[$tmp[0]] = $tmp[1];
//        }

        $o_direction = $arr['o_direction'];
        $a_direction = $arr['a_direction'];
        $longitude = $arr['longitude'];
        $latitude = $arr['latitude'];
        $time = $arr['time'];
        $mileage = $arr['mileage'];
        $endurance = $arr['endurance'];
        $speed = $arr['speed'];
        $car_sn = $arr['car_sn'];
        $gps_state = $arr['gps_state'];
        $gsm_state = $arr['gsm_state'];

        $discharge_currnet = $arr['discharge_currnet'];
        $charge_currnet = $arr['charge_currnet'];
        $filling_time = $arr['filling_time'];
        $temp = $arr['temp'];
        $soc_rt = $arr['soc_rt'];
        $batteryStatus = $arr['battery_state'];
        $period = $arr['period'];
//        $o_direction = I('o_direction');
//        $a_direction = I('a_direction');
//        $longitude = I('longitude');
//        $latitude = I('latitude');
//        $time = I('time');
//        $mileage = I('mileage');
//        $speed = I('speed');
//        $car_sn = I('car_sn');

//        echo fit_api(true, 0, implode(' ', $arr), ''); exit;

        if ($car_sn == '') {
            echo fit_api(true, 0, 'car_sn parameters is imperfect!', $arr);
            exit;
        }

        if ($o_direction == '') {
            echo fit_api(true, 0, '$o_direction parameters is imperfect!', $arr);
            exit;
        }

        if ($a_direction == '') {
            echo fit_api(true, 0, '$a_direction parameters is imperfect!', $arr);
            exit;
        }

        if ($longitude == '') {
            echo fit_api(true, 0, '$longitude parameters is imperfect!', $arr);
            exit;
        }

        if ($latitude == '') {
            echo fit_api(true, 0, '$latitude parameters is imperfect!', $arr);
            exit;
        }

        if ($time == '') {
            echo fit_api(true, 0, '$time parameters is imperfect!', $arr);
            exit;
        }

        if ($mileage == '') {
            echo fit_api(true, 0, '$mileage parameters is imperfect!', $arr);
            exit;
        }

        if ($endurance == '') {
            echo fit_api(true, 0, '$endurance parameters is imperfect!', $arr);
            exit;
        }

        if ($speed == '') {
            echo fit_api(true, 0, '$speed parameters is imperfect!', $arr);
            exit;
        }

        if ($car_sn != $auth) {
            echo fit_api(true, 0, 'auth fail! car_sn:' . $car_sn . '; auth:' . $auth, '');
            exit;
        }

        if ($gps_state == '') {
            echo fit_api(true, 0, '$gps_state parameters is imperfect!', $arr);
            exit;
        }

        if ($gsm_state == '') {
            echo fit_api(true, 0, '$gsm_state parameters is imperfect!', $arr);
            exit;
        }

        if ($discharge_currnet == '') {
            echo fit_api(true, 0, '$discharge_currnet parameters is imperfect!', $arr);
            exit;
        }

        if ($charge_currnet == '') {
            echo fit_api(true, 0, '$charge_currnet parameters is imperfect!', $arr);
            exit;
        }

        if ($filling_time == '') {
            echo fit_api(true, 0, '$filling_time parameters is imperfect!', $arr);
            exit;
        }

        if ($temp == '') {
            echo fit_api(true, 0, '$temp parameters is imperfect!', $arr);
            exit;
        }

        if ($soc_rt == '') {
            echo fit_api(true, 0, '$soc_rt parameters is imperfect!', $arr);
            exit;
        }

        if (!preg_match("/^\\d+(\.\\d+)?$/", $mileage)) {
            echo fit_api(true, 0, 'mileage format is err!', '');
            exit;
        }

        if (!preg_match("/^\\d+(\.\\d+)?$/", $endurance)) {
            echo fit_api(true, 0, 'endurance format is err!', '');
            exit;
        }

        if (!preg_match("/^\\d+(\.\\d+)?$/", $latitude)) {
            echo fit_api(true, 0, 'latitude format is err!', '');
            exit;
        }

        $db = M();
        $num = $db->query("select id  from fit_car where car_sn='" . $car_sn . "'");

        if ($num) {
            $car_id = $num[0]["id"];

            $num = $db->query("select count(id) as num from fit_gps");//生成id
            $num = $num[0]["num"] + 1;
            while (strlen($num) < 6) {
                $num = '0' . $num;
            }

            $gps = M('Gps');

            $gps->id = uniqid() . time() . rand(10000000, 99999999) . $num;
            $gps->car_id = $car_id;
            $gps->o_direction = $o_direction;
            $gps->a_direction = $a_direction;

            $tempLat = doubleval($latitude);
            $tempLong = doubleval($longitude);

            $gps->longitude = $longitude;
            $gps->latitude = $latitude;


            $gps->time = $time;
            $gps->mileage = $mileage;
            $gps->endurance = $endurance;
            $gps->speed = $speed;
            $gps->gps_state = $gps_state;
            $gps->gsm_state = $gsm_state;

            $gps->discharge_currnet = $discharge_currnet;
            $gps->charge_currnet = $charge_currnet;
            $gps->filling_time = $filling_time;
            $gps->temp = $temp;
            $gps->soc_rt = $soc_rt;
            $gps->batteryStatus = $batteryStatus;


            $result = $gps->add();

            if (false !== $result) {

                if ($speed != "0") {

                    //更新机车历史时间段，仅更新结束时间。开始时间在uploadcar上传时更新
                    $run_history = M('run_history');

                    $condition = array(
                        'car_id' => $car_id
                    );

                    $history_data = $run_history->where($condition)->order('start_date desc')->limit('1')->select();

                    if ($history_data) {
                        if ($period) {
                            $period_arr = explode('/', $period);

                            //检查日期，如果小于90秒内的，则更新，没有则新增一条历史行车记录
                            $period_run_history = M('run_history');
                            $period_history_data = $period_run_history->query("select * from fit_run_history where car_id='" . $car_id . "' and  start_date >= '" . $period_arr[0] . "'-interval 90 second and start_date <= '" . $period_arr[0] . "' order by start_date desc limit 1");

                            if ($period_history_data) {
                                $period_condition = array("id" => $period_history_data[0]["id"]);
                                $period_run_history->start_date = $period_arr[0];
                                $period_run_history->end_date = $period_arr[1];
                                $period_run_history->where($period_condition)->save();
                            } else {
                                $period_run_history->car_id = $car_id;
                                $period_run_history->start_date = $period_arr[0];
                                $period_run_history->end_date = $period_arr[1];
                                $period_run_history->add();
                            }
                        } else {
                            $history_data_id = $history_data[0]["id"];
                            $run_history->end_date = date('Y-m-d H:i:s', time());
                            $run_history->where(" id=" . $history_data_id)->save();
                        }
                    } else {
                        if ($period) {
                            //检查日期，如果小于90秒内的，则更新，没有则新增一条历史行车记录
                            $period_arr = explode('/', $period);

                            $run_history->car_id = $car_id;
                            $run_history->start_date = $period_arr[0];
                            $run_history->end_date = $period_arr[1];
                            $run_history->add();
                        }
                    }
                }

                //更新fit_car的gps,gsm refresh time
                $car = M('Car');

                $condition = array(
                    "id" => $car_id
                );

                //只记录经纬度正确的时间
                if ($tempLat >= 0 && $tempLat <= 90 && $tempLong >= 0 && $tempLong <= 180) {
                    $car->gps_refreshtime = date('Y-m-d H:i:s', time());
                }

                $car->gsm_refreshtime = date('Y-m-d H:i:s', time());
                $car->where($condition)->save();

                echo fit_api(true, 1, 'save success!', '');
            } else {
                echo fit_api(true, 0, 'save fail!', '');
            }
        } else {
            echo fit_api(true, 0, 'car_sn non-existent!', '');
        }
    }

//获取当前机车状态
    public function GetCarStatus()
    {
        $access_token = I('access_token');
        $car_id = I('car_id');

        if (!$access_token || !$car_id) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];
            $phone_number = $data[0]["phone_number"];

            //查询用户关联机车
            $user_car = M('user_car');

            $conditon = array(
                "user_id" => $userid,
                "car_id" => $car_id
            );
            $user_car_data = $user_car->where($conditon)->select();

            //这里判断默认car和关联car
            if ($user_car_data) {
                $car = M('Car');

                $condition = array(
                    "id" => $car_id
                );
                $data = $car->where($condition)->select();

                //根据iccid发送短信
                $car_iccid = $data[0]["iccid"];
                $gps_refreshtime = $data[0]["gps_refreshtime"];
                $car_last_sendmsg_time = $data[0]["last_sendmsg_time"];

                if ($car_iccid) {
                    if ((time() - $car_last_sendmsg_time) > 600 && (!$gps_refreshtime || (time() - strtotime($gps_refreshtime) > 600))) {
                        IccidSendSms($car_iccid, 'CAR_OPEN');

                        $car->last_sendmsg_time = time();
                        $car->where($condition)->save();
                    }
                }

                //临时放在这里

                if (substr($phone_number, 0, 2) != '88') { //正式用户，激活机车

                    $db = M();

                    $recharge = M('rechargebak');


                    $condition = array(
                        "iccid" => $car_iccid
                    );
                    $recharge_data = $recharge->where($condition)->select();

                    if (count($recharge_data) == 0) {
                        //$html = Recharge($car_iccid);

                        // $json_string = json_decode($html);

                        // if ($json_string->code != '0') {
                        //     $success = '充值失败';
                        //} else {
                        //$json_string->orderNumber
                        // $recharge->order_num = $json_string->orderNumber;
                        $recharge->order_num = '';
                        $recharge->iccid = $car_iccid;
                        $recharge->time = date('Y-m-d H:i:s', time());
                        $recharge->add();
                        $success = '充值成功';
                        // }


                    }

                }

                $gps = M('Gps');
                $conditon = array(
                    "car_id" => $car_id
                );

                $gps_data = $gps->where($conditon)->order('time desc')->limit('1')->select();

                //查找最后一条有效经纬度数据
                $conditon = array(
                    "longitude" => array('LT', 180),
                    "latitude" => array('LT', 90),
                    "car_id" => $car_id
                );
                $gps_validate_data = $gps->where($conditon)->order('time desc')->limit('1')->select();

                $battery = M('Battery');
                $conditon = array(
                    "battery_sn" => $car_id
                );
                $battery_data = $battery->where($conditon)->select();

                $batt = '';
                if ($battery_data) {
                    $batt = $battery_data[0]['soc_rt'];
                }

                $result = array(
                    "id" => $data[0]["id"],
                    "isLocked" => count($data) == 0 ? '' : $data[0]["locked"],
                    "carImgUrl" => count($data) == 0 ? '' : $data[0]["car_imgurl"] . '?id=' . time(),
                    "gpsRefreshTime" => count($data) == 0 ? '' : $data[0]["gps_refreshtime"],
                    "gsmRefreshTime" => count($data) == 0 ? '' : $data[0]["gsm_refreshtime"],
                    "remainderEnergy" => count($gps_data) == 0 ? '' : $gps_data[0]["soc_rt"],
                    "batteryStatus" => count($gps_data) == 0 ? '' : $gps_data[0]["batteryStatus"],

                    "longitude" => count($gps_validate_data) == 0 ? '0' : $gps_validate_data[0]["longitude"] == '' ? '0' : $gps_validate_data[0]["longitude"],
                    "latitude" => count($gps_validate_data) == 0 ? '0' : $gps_validate_data[0]["latitude"] == '' ? '0' : $gps_validate_data[0]["latitude"],
                    "refreshTime" => count($gps_validate_data) == 0 ? '' : $gps_validate_data[0]["time"],
                    "address" => count($gps_data) == 0 ? '' : $gps_data[0]["address"],
                    "remainderRange" => count($gps_data) == 0 ? '' : $gps_data[0]["endurance"],
                    "gpsState" => count($gps_data) == 0 ? '' : $gps_data[0]["gps_state"],
                    "gsmState" => count($gps_data) == 0 ? '' : $gps_data[0]["gsm_state"],

                    "gpsRefreshTime" => count($data) == 0 ? '' : $data[0]["gps_refreshtime"],
                    "gsmRefreshTime" => count($data) == 0 ? '' : $data[0]["gsm_refreshtime"],
                    "advertiseImgUrl" => "",
                    "advertiseHtmlUrl " => ""
                );

                echo fit_api(true, 0, '成功!', $result);
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    private
    function GetWeek($n)
    {
        if ($n == '1')
            return '一';
        else if ($n == '2')
            return '二';
        else if ($n == '3')
            return '三';
        else if ($n == '4')
            return '四';
        else if ($n == '5')
            return '五';
        else if ($n == '6')
            return '六';
        else
            return '天';
    }

//获取车辆行驶历史记录
    public
    function GetCarHistoryGpsList()
    {
        $access_token = I('access_token');
        $car_id = I('car_id');
        $currentPage = I("currentPage");
        $pageSize = I("pageSize", 20);

        if (!$access_token || !$car_id || !$currentPage) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            //查询用户关联机车
            $user_car = M('user_car');

            $conditon = array(
                "user_id" => $userid,
                "car_id" => $car_id
            );
            $user_car_data = $user_car->where($conditon)->select();

            //这里判断默认car和关联car
            if ($user_car_data) {
                $run_history = M('run_history');

                $condition = array(
                    'car_id' => $car_id,
                    'start_date' => array('exp', 'is not null'),
                    'end_date' => array('exp', 'is not null')
                );

                if ($currentPage < 1) {
                    $currentPage = 1;
                }

                //$history_data = $run_history->where($condition)->order('id desc')->limit(($currentPage - 1) * $pageSize . ',' . $pageSize)->select();
                $history_data = $run_history->query("select * from fit_run_history a where car_id='" . $car_id . "' and start_date is not null AND end_date is not null  order by start_date desc limit " . ($currentPage - 1) * $pageSize . ',' . $pageSize);//生成id
                //$history_data = $run_history->query("select * from fit_run_history a where car_id='".$car_id."' and start_date is not null AND end_date is not null and (select count(*) from fit_gps where car_id='".$car_id."' and cast(longitude AS decimal)>0 and cast(longitude AS decimal) <180 and  cast(latitude as decimal)>0 and cast(latitude as decimal)<90 and time >=a.start_date and time <=a.end_date ) > 1  order by id desc limit ".($currentPage - 1) * $pageSize . ',' . $pageSize);//生成id
                //$history_data = $run_history->query("select * from fit_run_history a where car_id='".$car_id."' and (select count(*) from fit_gps where time >=a.start_date and time <=a.end_date ) > 0  order by id desc");//生成id
                $result = array('runHistroyList' => array());
                for ($i = 0; $i < count($history_data); $i++) {
                    //查询时间段内的gps详细信息
                    $gps = M('Gps');

                    $condition = array(
                        "time" => array(array('EGT', $history_data[$i]['start_date']), array('ELT', $history_data[$i]['end_date']), 'AND'),
                        "car_id" => $car_id
                    );
                    $gps_data = $gps->where($condition)->order('time asc')->select();

                    if (count($gps_data) == 1) {
                        //计算距离
                        $distance = 0;

                        //计算速度
                        $speed = 0;

                        //计算时间
                        $second = strtotime($history_data[$i]['end_date']) - strtotime($history_data[$i]['start_date']);
                        $total = (int)$second;
                        $hour = 0;
                        $minute = 0;
                        $sec = 0;
                        while ($total >= 3600) {
                            $total = $total - 3600;
                            $hour++;
                        }
                        while ($total >= 60) {
                            $total = $total - 60;
                            $minute++;
                        }
                        $second = $total;
                        //echo $hour.$minute.$second;

                        $result['runHistroyList'][] = array(
                            'runHistroyId' => $history_data[$i]['id'],
                            'runimeInfo' => date("Y-m-d", strtotime($history_data[$i]['start_date'])) . ' 星期' . $this->GetWeek(date('N', strtotime($history_data[$i]['start_date']))),
                            'startTime' => date('H:i', strtotime($history_data[$i]['start_date'])),
                            'endTime' => date('H:i', strtotime($history_data[$i]['end_date'])),
                            'usedTime' => $hour . ':' . $minute . ':' . $second,
                            'distance' => $distance,
                            'averageSpeed' => $gps_data[0]['speed'],
                            'startLongitude' => $gps_data[0]['longitude'],
                            'startLatitude' => $gps_data[0]['latitude'],
                            'startAddress' => $gps_data[0]['address'],
                            'endLongitude' => $gps_data[0]['longitude'],
                            'endLatitude' => $gps_data[0]['latitude'],
                            'endAddress' => $gps_data[0]['address']
                        );
                    } else if (count($gps_data) > 1) {
                        //计算距离
                        //$distance = GetDistance($gps_data[0]['latitude'], $gps_data[0]['longitude'], $gps_data[count($gps_data) - 1]['latitude'], $gps_data[count($gps_data) - 1]['longitude']);
                        $distance = $gps_data[count($gps_data) - 1]['mileage'] - $gps_data[0]['mileage'];

                        //计算时间
                        $second = strtotime($history_data[$i]['end_date']) - strtotime($history_data[$i]['start_date']);
                        $temp = $second;
                        $total = (int)$second;
                        $hour = 0;
                        $minute = 0;
                        $sec = 0;
                        while ($total >= 3600) {
                            $total = $total - 3600;
                            $hour++;
                        }
                        while ($total >= 60) {
                            $total = $total - 60;
                            $minute++;
                        }
                        $second = $total;
                        //echo $hour.$minute.$second;

                        //计算速度
                        $speed = $distance / $temp * 3600;


                        //DateDiff("G", strtotime($history_data['start_date'], strtotime($history_data['end_date']))),
                        $result['runHistroyList'][] = array(
                            'runHistroyId' => $history_data[$i]['id'],
                            'runimeInfo' => date("Y-m-d", strtotime($history_data[$i]['start_date'])) . ' 星期' . $this->GetWeek(date('N', strtotime($history_data[$i]['start_date']))),
                            'startTime' => date('H:i', strtotime($history_data[$i]['start_date'])),
                            'endTime' => date('H:i', strtotime($history_data[$i]['end_date'])),
                            'usedTime' => $hour . ':' . $minute . ':' . $second,
                            'distance' => round($distance, 1),
                            'averageSpeed' => $gps_data[count($gps_data) - 1]['speed'],
                            'startLongitude' => $gps_data[0]['longitude'],
                            'startLatitude' => $gps_data[0]['latitude'],
                            'startAddress' => $gps_data[0]['address'],
                            'endLongitude' => $gps_data[count($gps_data) - 1]['longitude'],
                            'endLatitude' => $gps_data[count($gps_data) - 1]['latitude'],
                            'endAddress' => $gps_data[count($gps_data) - 1]['address']
                        );
                    }
                }
                echo fit_api(true, 0, '成功!', $result);
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//获取车辆行驶历史记录详细轨迹
    public
    function GetCarHistoryGpsInfoList()
    {
        $access_token = I('access_token');
        $car_id = I('car_id');
        $runHistroyId = I("runHistroyId");

        if (!$access_token || !$car_id || !$runHistroyId) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            //查询用户关联机车
            $user_car = M('user_car');

            $conditon = array(
                "user_id" => $userid,
                "car_id" => $car_id
            );
            $user_car_data = $user_car->where($conditon)->select();

            //这里判断默认car和关联car
            if ($user_car_data) {
                $run_history = M('run_history');

                $condition = array(
                    'id' => $runHistroyId
                );

                $history_data = $run_history->where($condition)->select();

                if (!$history_data) {
                    echo fit_api(true, 1, 'runHistroyId err!', "");
                    exit;
                }

                $runHistroyList = array();

                //查询时间段内的gps详细信息
                $gps = M('Gps');

                $condition = array(
                    "time" => array(array('EGT', $history_data[0]['start_date']), array('ELT', $history_data[0]['end_date']), 'AND'),
                    "car_id" => $car_id,
                    "longitude" => array(array('EGT', 0), array('ELT', 180), 'AND'),
                    "latitude" => array(array('EGT', 0), array('ELT', 90), 'AND')
                );
                $gps_data = $gps->where($condition)->order('time asc')->select();

                $condition = array(
                    "time" => array(array('EGT', $history_data[0]['start_date']), array('ELT', $history_data[0]['end_date']), 'AND'),
                    "car_id" => $car_id
                );
                $gps_distance_data = $gps->where($condition)->order('time asc')->select();

                $runList = array();
                if (count($gps_data) > 0) {
                    if (count($gps_distance_data) == 1) {
                        //计算距离
                        $distance = 0;

                        //计算速度
                        $speed = 0;

                        //计算时间
                        $second = strtotime($history_data[0]['end_date']) - strtotime($history_data[0]['start_date']);
                        $total = (int)$second;
                        $hour = 0;
                        $minute = 0;
                        $sec = 0;
                        while ($total >= 3600) {
                            $total = $total - 3600;
                            $hour++;
                        }
                        while ($total >= 60) {
                            $total = $total - 60;
                            $minute++;
                        }
                        $second = $total;
                        //echo $hour.$minute.$second;

                        $runList[0] = array(
                            'longitude' => $gps_data[0]['longitude'],
                            'latitude' => $gps_data[0]['latitude'],
                            'time' => $gps_data[0]['time']
                        );

                        $address1 = $this->GetAddress($gps_data[0]['longitude'], $gps_data[0]['latitude']);
                        $runHistroyList = array(
                            'runHistroyId' => $history_data[0]['id'],
                            'runimeInfo' => date("Y-m-d", strtotime($history_data[0]['start_date'])) . ' 星期' . $this->GetWeek(date('N', strtotime($history_data[0]['start_date']))),
                            'startTime' => date('H:i', strtotime($history_data[0]['start_date'])),
                            'endTime' => date('H:i', strtotime($history_data[0]['end_date'])),
                            'usedTime' => $hour . ':' . $minute . ':' . $second,
                            'distance' => $distance,
                            'averageSpeed' => $gps_data[0]['speed'],
                            'startLongitude' => $gps_data[0]['longitude'],
                            'startLatitude' => $gps_data[0]['latitude'],
                            'startAddress' => $address1,
                            'endLongitude' => $gps_data[0]['longitude'],
                            'endLatitude' => $gps_data[0]['latitude'],
                            'endAddress' => $address1,
                            'runList' => $runList
                        );
                    } else {
                        //计算距离
                        //$distance = GetDistance($gps_data[0]['latitude'], $gps_data[0]['longitude'], $gps_data[count($gps_data) - 1]['latitude'], $gps_data[count($gps_data) - 1]['longitude']);
                        $distance = $gps_distance_data[count($gps_distance_data) - 1]['mileage'] - $gps_distance_data[0]['mileage'];
                        //计算时间
                        $second = strtotime($history_data[0]['end_date']) - strtotime($history_data[0]['start_date']);
                        $temp = $second;
                        $total = (int)$second;
                        $hour = 0;
                        $minute = 0;
                        $sec = 0;
                        while ($total >= 3600) {
                            $total = $total - 3600;
                            $hour++;
                        }
                        while ($total >= 60) {
                            $total = $total - 60;
                            $minute++;
                        }
                        $second = $total;
                        //echo $hour.$minute.$second;

                        //计算速度
                        $speed = $distance / $temp * 3600;

                        $runList = array();

                        for ($i = 0; $i < count($gps_data); $i++) {
                            $runList[] = array(
                                'longitude' => $gps_data[$i]['longitude'],
                                'latitude' => $gps_data[$i]['latitude'],
                                'time' => $gps_data[$i]['time']
                            );
                        }

                        $address1 = $this->GetAddress($gps_data[0]['longitude'], $gps_data[0]['latitude']);
                        $address2 = $this->GetAddress($gps_data[count($gps_data) - 1]['longitude'], $gps_data[count($gps_data) - 1]['latitude']);
                        $runHistroyList = array(
                            'runHistroyId' => $history_data[0]['id'],
                            'runimeInfo' => date("Y-m-d", strtotime($history_data[0]['start_date'])) . ' 星期' . $this->GetWeek(date('N', strtotime($history_data[0]['start_date']))),
                            'startTime' => date('H:i', strtotime($history_data[0]['start_date'])),
                            'endTime' => date('H:i', strtotime($history_data[0]['end_date'])),
                            'usedTime' => $hour . ':' . $minute . ':' . $second,
                            'distance' => round($distance, 1),
                            'averageSpeed' => $gps_data[count($gps_data) - 1]['speed'],
                            'startLongitude' => $gps_data[0]['longitude'],
                            'startLatitude' => $gps_data[0]['latitude'],
                            'startAddress' => $address1,
                            'endLongitude' => $gps_data[count($gps_data) - 1]['longitude'],
                            'endLatitude' => $gps_data[count($gps_data) - 1]['latitude'],
                            'endAddress' => $address2,
                            'runList' => $runList
                        );
                    }
                } else {
                    $second = strtotime($history_data[0]['end_date']) - strtotime($history_data[0]['start_date']);
                    $total = (int)$second;
                    $hour = 0;
                    $minute = 0;
                    $sec = 0;
                    while ($total >= 3600) {
                        $total = $total - 3600;
                        $hour++;
                    }
                    while ($total >= 60) {
                        $total = $total - 60;
                        $minute++;
                    }
                    $second = $total;

                    $runList[0] = array(
                        'longitude' => '',
                        'latitude' => '',
                        'time' => ''
                    );

                    $runHistroyList = array(
                        'runHistroyId' => $history_data[0]['id'],
                        'runimeInfo' => date("Y-m-d", strtotime($history_data[0]['start_date'])) . ' 星期' . date('N', strtotime($history_data[0]['start_date'])),
                        'startTime' => date('H:i', strtotime($history_data[0]['start_date'])),
                        'endTime' => date('H:i', strtotime($history_data[0]['end_date'])),
                        'usedTime' => $hour . ':' . $minute . ':' . $second,
                        'distance' => '0',
                        'averageSpeed' => '0',
                        'startLongitude' => '',
                        'startLatitude' => '',
                        'startAddress' => '',
                        'endLongitude' => '',
                        'endLatitude' => '',
                        'endAddress' => '',
                        'runList' => $runList
                    );
                }

                echo fit_api(true, 0, '成功!', $runHistroyList);
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//得到时间段gps数据
    public
    function GetGpsByDate()
    {
        $access_token = I('access_token');
        $car_sn = I('car_sn');
        $starttime = I('starttime');
        $endtime = I('endtime');

        if ($access_token && $car_sn && ($starttime || $endtime)) {
            $login = M('Login');

            $condition['key'] = $access_token;
            $data = $login->where($condition)->select();

            if ($data) {
                $user_id = $data[0]["user_id"];

                $user = M('User');

                $condition = array(
                    "id" => $user_id
                );
                $data = $user->where($condition)->select();

                if ($data) {
                    $car_id = $data[0]["car_id"];

                    //对比car_sn
                    $car = M('Car');

                    $condition = array(
                        "id" => $car_id
                    );
                    $data = $car->where($condition)->select();

                    if ($data) {
                        $data_car_sn = $data[0]["car_sn"];

                        if ($data_car_sn == $car_sn) {
                            $gps_condition = "car_id = '" . $car_id . "'";

                            if ($starttime)
                                $gps_condition = $gps_condition . " and time >= '" . $starttime . "'";

                            if ($endtime)
                                $gps_condition = $gps_condition . " and time <= '" . $endtime . "'";

                            $db = M('Gps');

                            $data = $db->where($gps_condition)->field("o_direction,a_direction,longitude,latitude,mileage,speed")->select();

                            $ishave = 0;
                            if (count($data) > 0)
                                $ishave = 1;

                            $result = array(
                                "count" => count($data),
                                "ishave" => $ishave,
                                "gpsdata" => $data
                            );
                            echo fit_api(true, 0, '获得成功!', $result);
                        } else {
                            echo fit_api(true, 1, 'car_sn错误!', '');
                        }
                    } else {
                        echo fit_api(true, 1, '没有car!', '');
                    }
                } else {
                    echo fit_api(true, 1, '没有user!', '');
                }
            } else {
                echo fit_api(true, 99, 'access_token错误!', '');
            }
        } else {
            echo fit_api(true, 1, '参数为空!', '');
        }
    }

//得到机车固件信息
    public
    function GetCarRom()
    {
        $access_token = I('access_token');
        $car_sn = I('car_sn');

        if ($access_token && $car_sn) {
            $login = M('Login');

            $condition['key'] = $access_token;
            $data = $login->where($condition)->select();

            if ($data) {
                $user_id = $data[0]["user_id"];

                $user = M('User');

                $condition = array(
                    "id" => $user_id
                );
                $data = $user->where($condition)->select();

                if ($data) {
                    $car_id = $data[0]["car_id"];

                    //对比car_sn
                    $car = M('Car');

                    $condition = array(
                        "id" => $car_id
                    );
                    $data = $car->where($condition)->select();

                    if ($data) {
                        $data_car_sn = $data[0]["car_sn"];

                        if ($data_car_sn == $car_sn) {

                            $result = array(
                                "phone_number" => $data[0]["phone_number"],
                                "battery_number" => $data[0]["battery_number"],
                                "arm_ver" => $data[0]["arm_ver"],
                                "muc_ver" => $data[0]["muc_ver"],
                                "ctl_ver" => $data[0]["ctl_ver"],
                                "bms_ver" => $data[0]["bms_ver"]
                            );

                            echo fit_api(true, 0, '获得成功!', $result);
                        } else {
                            echo fit_api(true, 1, 'car_sn错误!', '');
                        }
                    } else {
                        echo fit_api(true, 1, '没有car!', '');
                    }
                } else {
                    echo fit_api(true, 1, '没有user!', '');
                }
            } else {
                echo fit_api(true, 99, 'access_token错误!', '');
            }
        } else {
            echo fit_api(true, 1, '参数为空!', '');
        }
    }

//获取车辆最近7天行驶数据
    public
    function GetWeekGpsData()
    {
        $access_token = I('access_token');
        $car_id = I('car_id');

        if (!$access_token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            //查询用户关联机车
            $user_car = M('user_car');

            $conditon = array(
                "user_id" => $userid,
                "car_id" => $car_id
            );
            $user_car_data = $user_car->where($conditon)->select();

            //这里判断默认car和关联car
            if ($user_car_data || !$car_id) {
                $gps = M('Gps');

                $time = date('Y-m-d 00:00:00', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));

                $data = $gps->query("select mileage,time from fit_gps where car_id='$car_id' and time > '$time' order by time");
                //$data = $gps->where($condition)->select();

                $result = array("runHistroyList" => array());

                if ($car_id) {
                    $index = 0;
                    $tempIndex = 0;

                    for ($i = 0; $i < 7; $i++) {
                        $day = date('Y-m-d', time() - ((date('w') == 0 ? 7 : date('w')) - $i - 1) * 24 * 3600);

                        $startMileage = -1;
                        $endMileage = 0;
                        for ($a = $index; $a < count($data); $a++) {
                            if ($data[$a]["time"] != "") {
                                if (substr($data[$a]["time"], 0, 10) == $day) {
                                    if ($startMileage == -1) {
                                        $startMileage = $data[$a]["mileage"];
                                    }
                                    $endMileage = $data[$a]["mileage"];

                                    //$distance += $data[$a]["mileage"];
                                } else {
                                    $tempIndex = $a;
                                    break;
                                }
                            }
                            $tempIndex = $a;
                        }
                        $index = $tempIndex;

                        if ($startMileage == -1) {
                            $startMileage = 0;
                        }

                        $rundis = $endMileage - $startMileage;
                        $rundis = round($rundis, 2);
                        if ($rundis < 0)
                            $rundis = 0;

                        $weekInfo = date('D', time() - ((date('w') == 0 ? 7 : date('w')) - $i - 1) * 24 * 3600);
                        $runDay = date('Y-m-d', time() - ((date('w') == 0 ? 7 : date('w')) - $i - 1) * 24 * 3600);

                        $result["runHistroyList"][] = array(
                            "weekInfo" => $weekInfo,
                            "runDistance" => $rundis,
                            "runDay" => $runDay
                        );
                    }
                }

                //获取未读信息和用户信息
                $msg = M('user_msg');

                $condition = array(
                    "user_id" => $userid,
                    "read" => 2
                );

                $msg_data = $msg->where($condition)->select();
                $result["unreadMsg"] = count($msg_data);

                $user = M('user');

                $condition = array(
                    "id" => $userid
                );

                $user_data = $user->where($condition)->select();

                $result["userInfo"] = array(
                    "userId" => $userid,
                    "nickName" => $user_data[0]["nick_name"],
                    "nickRealName" => $user_data[0]["name"],
                    "userPhone" => $user_data[0]["phone_number"],
                    "userHeadImgUrl" => $user_data[0]["head_pic"]
                );

                echo fit_api(true, 0, '成功!', $result);
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//选择车辆
    public
    function ChoiceCar()
    {
        $access_token = I('access_token');
        $car_id = I('car_id');

        if (!$access_token || !$car_id) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            //查询用户关联机车
            $user_car = M('user_car');

            $conditon = array(
                "user_id" => $userid,
                "car_id" => $car_id
            );
            $user_car_data = $user_car->where($conditon)->select();

            //这里判断默认car和关联car
            if ($user_car_data) {
                //更新新的car为默认
                $user = M('User');

                $user->car_id = $car_id;

                $condition = array(
                    "id" => $userid
                );
                $result = $user->where($condition)->save();

                echo fit_api(true, 0, '更新成功!', '');

            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//获取车辆信息（车辆管理）
    public
    function GetCarInfo()
    {
        $access_token = I('access_token');
        $car_id = I('car_id');

        $download_url = $this->website;

        if (!$access_token || !$car_id) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            //查询用户关联机车
            $user_car = M('user_car');


            $conditon = array(
                "user_id" => $userid,
                "car_id" => $car_id
            );
            $user_car_data = $user_car->where($conditon)->select();

            //这里判断默认car和关联car
            if ($user_car_data) {
                $car = M('Car');

                $condition = array(
                    "id" => $car_id
                );
                $data = $car->where($condition)->select();

                $lock = 1;
                if ($data[0]["locked"] == 0) {
                    $lock = 2;
                }

                $versionModel = M('version');
                $arm_versions = $versionModel->where(" type_id = 3 and car_class = " . $data[0]["car_class"])->order('id desc')->limit('1')->select();
                //echo $versionModel->getlastsql();exit;
                $mcu_versions = $versionModel->where(" type_id = 4 and car_class = " . $data[0]["car_class"])->order('id desc')->limit('1')->select();
                $car_versions = $versionModel->where(" type_id = 5 and car_class = " . $data[0]["car_class"])->order('id desc')->limit('1')->select();

                //取出车辆color
                $color = M('color');
                $color_con = array("id" => $data[0]["color"]);
                $color_data = $color->where($color_con)->select();

                $car_color = '';
                if ($color_data) {
                    $car_color = $color_data[0]["color"];
                }

                //取出车辆关联车型
                $carClass = M('cartclass');
                $class_con = array("id" => $data[0]["car_class"]);
                $class_data = $carClass->where($class_con)->select();
                $car_class = '';
                if ($class_data) {
                    $car_class = $class_data[0]["name"] . ' ' . $car_color;
                }

                $result = array(
                    "car_id" => $car_id,
                    "car_sn" => $data[0]["car_sn"],
                    "carImgUrl" => $data[0]["car_imgurl"] . '?id=' . time(),
                    "carSimpleName" => $data[0]["car_simple_name"],
                    "carName" => $car_class,
                    "frameNO" => $data[0]["car_frame_no"],
                    "engineNO" => $data[0]["car_engine_no"],
                    "carVersion" => $data[0]["ctl_ver"],
                    "carNewVersion" => count($car_versions) == 0 ? '' : $car_versions[0]["version_code"],
                    "carNewVersionUrl" => count($car_versions) == 0 ? '' : $download_url . $car_versions[0]["new_version_url"],
                    "carNewFileLength" => count($car_versions) == 0 ? '' : $car_versions[0]["version_size"],
                    "carNewfileMd5" => count($car_versions) == 0 ? '' : $car_versions[0]["file_md5"],
                    "armVersion" => $data[0]["arm_ver"],
                    "armNewVersion" => count($arm_versions) == 0 ? '' : $arm_versions[0]["version_code"],
                    "armNewVersionUrl" => count($arm_versions) == 0 ? '' : $download_url . $arm_versions[0]["new_version_url"],
                    "armNewFileLength" => count($arm_versions) == 0 ? '' : $arm_versions[0]["version_size"],
                    "armNewfileMd5" => count($arm_versions) == 0 ? '' : $arm_versions[0]["file_md5"],
                    "mcuVersion" => $data[0]["muc_ver"],
                    "mcuNewVersion" => count($mcu_versions) == 0 ? '' : $mcu_versions[0]["version_code"],
                    "mcuNewVersionUrl" => count($mcu_versions) == 0 ? '' : $download_url . $mcu_versions[0]["new_version_url"],
                    "mcuNewFileLength" => count($mcu_versions) == 0 ? '' : $mcu_versions[0]["version_size"],
                    "mcuNewfileMd5" => count($mcu_versions) == 0 ? '' : $mcu_versions[0]["file_md5"],
                    "bmsVersion" => $data[0]["bms_version"],
                    "gpsRefreshTime" => $data[0]["gps_refreshtime"],
                    "gsmRefreshTime" => $data[0]["gsm_refreshtime"],
                    "remoteLock" => $lock
                );
                echo fit_api(true, 0, '成功!', $result);

            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//车辆远程锁车开关控制
    public
    function RemoteLock()
    {
        $access_token = I('access_token');
        $car_id = I('car_id');
        $remoteLock = I('remoteLock');
        $phone_number = I('phone_number');
        $checkcode = I('checkcode');

        if (!$access_token || !$car_id || !$remoteLock) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            //检查验证码
            if (CheckCode($phone_number, $checkcode, 9) == 1) {
                //查询用户关联机车
                $user_car = M('user_car');

                $conditon = array(
                    "user_id" => $userid,
                    "car_id" => $car_id
                );
                $user_car_data = $user_car->where($conditon)->select();

                //这里判断默认car和关联car
                if ($user_car_data) {
                    $lock = 1;
                    if ($remoteLock == 2)
                        $lock = 0;

                    $car = M('Car');
                    $condition = array(
                        "id" => $car_id
                    );

                    $car_data = $car->where($condition)->select();
                    $iccid = $car_data[0]["iccid"];
                    $car_sn = $car_data[0]["car_sn"];
                    $freeze = $car_data[0]["freeze"];


                    $car->locked = $lock;
                    $result = $car->where($condition)->save();


                    //发送短信通知机车
                    if ($lock == 1) {                  //锁车
                        IccidSendSms($iccid, 'CAR_LOCK:' . $iccid . '&' . $car_sn . '$');
                    } else {
                        //如果车辆冻结则不触锁
                        if ($freeze == 1)
                            IccidSendSms($iccid, 'CAR_UNLOCK:' . $iccid . '&' . $car_sn . '$');
                        else {
                            echo fit_api(true, 1, '您的爱车已冻结，请联系厂商续费!', "");
                            exit;
                        }
                    }

                    echo fit_api(true, 0, '成功!', "");

                } else {
                    echo fit_api(true, 3, '非法车辆!', '');
                }
            } else {
                echo fit_api(true, 4, '验证码不正确!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//获取天气预报
    public
    function GetWeather()
    {
        $longitude = I('longitude');
        $latitude = I('latitude');

        if (!$longitude || !$latitude) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        //取出城市
        //例子http://api.map.baidu.com/geocoder/v2/?ak=PjtTnnvyjoVqI36gY8XQS4Im3CuIbb8c&location=31.582140,120.355320&output=json&pois=1
        $url = 'http://api.map.baidu.com/geocoder/v2/?ak=PjtTnnvyjoVqI36gY8XQS4Im3CuIbb8c&location=' . $latitude . ',' . $longitude . '&output=json&pois=1';

        $result = file_get_contents($url);

        $json_string = json_decode($result);

        if ($json_string->result->addressComponent->city) {

            $city = $json_string->result->addressComponent->city;

            //读取数据库缓存表，根据城市，判断最近更新时间，每6小时更新一次数据库
            $weather = M('weather');
            $condition = array(
                "city" => $city
            );
            $weather_data = $weather->where($condition)->select();

            if ($weather_data) {
                if (time() - $weather_data[0]["time"] > 3600 * 6) {
                    $url = 'http://op.juhe.cn/onebox/weather/query?cityname=' . $city . '&key=6db3e27a68e43dc316278b876764c4df';

                    $result = file_get_contents($url);
                    $json_string = json_decode($result);

                    $data = array(
                        'realtime' => $json_string->result->data->realtime
                    );

                    $weather->realtime = json_encode($json_string->result->data->realtime);
                    $weather->time = time();
                    $weather->where($condition)->save();
                } else {
                    $data = array(
                        'realtime' => json_decode($weather_data[0]["realtime"])
                    );
                }
            } else {
                //根据城市获取天气
                $url = 'http://op.juhe.cn/onebox/weather/query?cityname=' . $city . '&key=6db3e27a68e43dc316278b876764c4df';

                $result = file_get_contents($url);

                $json_string = json_decode($result);

                $data = array(
                    'realtime' => $json_string->result->data->realtime
                );

                $weather->city = $city;
                $weather->realtime = json_encode($json_string->result->data->realtime);
                $weather->time = time();
                $weather->add();
            }
            echo fit_api(true, 0, '成功!', $data);
        } else {
            echo fit_api(true, 2, '城市为空!', '');
        }
    }

//获取我的车辆列表
    public
    function GetUserCarList()
    {
        $access_token = I('access_token');

        if (!$access_token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $user_id = $login_data[0]["user_id"];

            $user = M('User');

            $condition = array(
                "id" => $user_id
            );

            $user_data = $user->where($condition)->select();

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

                        //取出车辆color
                        $color = M('color');
                        $color_con = array("id" => $car_data[0]["color"]);
                        $color_data = $color->where($color_con)->select();

                        $car_color = '';
                        if ($color_data) {
                            $car_color = $color_data[0]["color"];
                        }

                        //取出车辆关联车型
                        $carClass = M('cartclass');
                        $class_con = array("id" => $car_data[0]["car_class"]);
                        $class_data = $carClass->where($class_con)->select();
                        $car_class = '';
                        if ($class_data) {
                            $car_class = $class_data[0]["name"] . ' ' . $car_color;
                        }

                        $carList[] = array(
                            "car_id" => $car_data[0]["id"],
                            "car_sn" => $car_data[0]["car_sn"],
                            "carImgUrl" => $car_data[0]["car_imgurl"] . '?id=' . time(),
                            "carSimpleName" => $car_data[0]["car_simple_name"],
                            "carName" => $car_class,
                            "isDefault" => $default,
                            "bluetoochMac" => $car_data[0]["bluetoochMac"],
                            "bluetoochName" => $car_data[0]["bluetoochName"],
                            "bluetoochCommandPwd" => $car_data[0]["bluetoochCommandPwd"]
                        );
                    }
                }
            }

            $result = array(
                "carList" => $carList
            );

            echo fit_api(true, 0, '成功!', $result);

        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//设置车辆名称
    public
    function SetCarSimpleName()
    {
        $access_token = I('access_token');
        $car_id = I('car_id');
        $carSimpleName = I('carSimpleName');

        if (!$access_token || !$car_id || !$carSimpleName) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $user_id = $login_data[0]["user_id"];

            //查询用户关联机车
            $user_car = M('user_car');

            $conditon = array(
                "user_id" => $user_id,
                "car_id" => $car_id
            );

            $user_car_data = $user_car->where($conditon)->select();

            //这里判断默认car和关联car
            if ($user_car_data) {

                $car = M('Car');
                $condition = array(
                    "id" => $car_id
                );

                $car->car_simple_name = $carSimpleName;

                $result = $car->where($condition)->save();

                if ($result) {
                    echo fit_api(true, 0, '成功!', "");
                } else {
                    echo fit_api(true, 4, '失败!', "");
                }
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//车辆过户1（车主信息）
    public
    function CarTransferStep1()
    {
        $access_token = I('access_token');
        $phone_number = I('phone_number');
        $checkcode = I('checkcode');
        $customerName = I('customerName');
        $customerAddress = I('customerAddress');
        $car_id = I('car_id');
        $car_sn = I('car_sn');
        $carName = I('carName');

        if (!$access_token || !$car_id || !$phone_number || !$checkcode || !$customerName || !$car_sn || !$carName) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $user_id = $login_data[0]["user_id"];

            //检查手机号是否是用户注册手机号
            $user = M('user');

            $condition = array(
                "id" => $user_id,
                "user_name" => $phone_number
            );

            $user_data = $user->where($condition)->select();

            if (count($user_data) == 0) {
                echo fit_api(true, 1, '手机号不正确!', '');
                exit;
            }

            //检查验证码
            if (CheckCode($phone_number, $checkcode, 6) == 1) {
                //查询用户关联机车
                $user_car = M('user_car');

                $conditon = array(
                    "user_id" => $user_id,
                    "car_id" => $car_id
                );

                $user_car_data = $user_car->where($conditon)->select();

                //这里判断默认car和关联car
                if ($user_car_data) {

                    $car_transfer = M('car_transfer');
                    $transfer_count = $car_transfer->count();

                    $transfer_id = date('Ymd') . substr(strval($transfer_count + 100001), 1, 5);

                    //增加过户申请表
                    $car_transfer->Id = $transfer_id;
                    $car_transfer->car_id = $car_id;
                    $car_transfer->customer_name = $customerName;
                    $car_transfer->customer_address = $customerAddress;
                    $car_transfer->car_sn = $car_sn;
                    $car_transfer->car_name = $carName;
                    $car_transfer->user_id = $user_id;
                    $car_transfer->customer_number = $phone_number;
                    $result = $car_transfer->add();

                    if ($result) {
                        echo fit_api(true, 0, '成功!', array("transferOrderId" => $transfer_id));
                    } else {
                        echo fit_api(true, 5, '失败!', "");
                    }
                } else {
                    echo fit_api(true, 3, '非法车辆!', '');
                }

            } else {
                echo fit_api(true, 4, '验证码错误!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//车辆过户2（受让人信息）
    public
    function CarTransferStep2()
    {
        $access_token = I('access_token');
        $receivePhoneNumber = I('receivePhoneNumber');
        $checkcode = I('checkcode');
        $receiveName = I('receiveName');
        $car_id = I('car_id');
        $transferOrderId = I('transferOrderId');
        $receiveAddress = I('receiveAddress');


        if (!$access_token || !$car_id || !$receivePhoneNumber || !$checkcode || !$receiveName || !$transferOrderId || !$receiveAddress) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $user_id = $login_data[0]["user_id"];

            //检查验证码
            if (CheckCode($receivePhoneNumber, $checkcode, 7) == 1) {
                //查询用户关联机车
                $user_car = M('user_car');

                $conditon = array(
                    "user_id" => $user_id,
                    "car_id" => $car_id
                );

                $user_car_data = $user_car->where($conditon)->select();

                //这里判断默认car和关联car
                if ($user_car_data) {

                    $car_transfer = M('car_transfer');

                    $condition = array(
                        "Id" => $transferOrderId
                    );

                    //修改过户申请表
                    $car_transfer->receive_user_id = $user_id;
                    $car_transfer->receive_name = $receiveName;
                    $car_transfer->receive_address = $receiveAddress;
                    $car_transfer->receive_number = $receivePhoneNumber;
                    $car_transfer->time = date('Y-m-d H:i:s', time());
                    $result = $car_transfer->where($condition)->save();

                    if ($result) {
                        echo fit_api(true, 0, '成功!', "");
                    } else {
                        echo fit_api(true, 5, '失败!', "");
                    }
                } else {
                    echo fit_api(true, 3, '非法车辆!', '');
                }

            } else {
                echo fit_api(true, 4, '验证码错误!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//自助检测
    public
    function SelfCheck()
    {
        echo $this->mallwebsite . '/mobile/test_self';
    }

//车辆过户记录
    public
    function GetCarTransfer()
    {
        $access_token = I('access_token');

        if (!$access_token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $user_id = $login_data[0]["user_id"];

            $condition = array(
                "user_id" => $user_id,
                "receive_user_id" => $user_id,
                "_logic" => "or"
            );

            $car_transfer = M('car_transfer');
            $transfer_data = $car_transfer->where($condition)->order('time desc')->select();

            $result = array();

            for ($a = 0; $a < count($transfer_data); $a++) {
                $result[] = array(
                    "transferOrderId" => $transfer_data[$a]["Id"],
                    "phoneNumbe" => $transfer_data[$a]["customer_number"],
                    "customerName" => $transfer_data[$a]["customer_name"],
                    "customerAddress" => $transfer_data[$a]["customer_address"],
                    "carName" => $transfer_data[$a]["car_name"],
                    "carSn" => $transfer_data[$a]["car_sn"],
                    "receiveName" => $transfer_data[$a]["receive_name"],
                    "receiveAddress" => $transfer_data[$a]["receive_address"],
                    "receiveNumber" => $transfer_data[$a]["receive_number"],
                    "transferTime" => $transfer_data[$a]["time"]
                );
            }

            echo fit_api(true, 0, '成功', array("transferList" => $result));
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//失窃上报短信验证接口
    public
    function CarLostStep1()
    {
        $access_token = I('access_token');
        $phone_number = I('phone_number');
        $checkcode = I('checkcode');

        if (!$access_token || !$phone_number || !$checkcode) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $user_id = $login_data[0]["user_id"];

            //检查手机号是否是用户注册手机号
            $user = M('user');

            $condition = array(
                "id" => $user_id,
                "user_name" => $phone_number
            );

            $user_data = $user->where($condition)->select();

            if (count($user_data) == 0) {
                echo fit_api(true, 1, '手机号不正确!', '');
                exit;
            }

            //检查验证码
            if (CheckCode($phone_number, $checkcode, 8) == 1) {
                echo fit_api(true, 0, '验证码正确!', '');
            } else {
                echo fit_api(true, 4, '验证码不正确!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//失窃上报信息提交接口
    public
    function CarLostStep2()
    {
        $access_token = I('access_token');
        $phone_number = I('phone_number');
        $car_id = I('car_id');
        $customerName = I('customerName');
        $car_sn = I('car_sn');
        $carName = I('carName');
        $lossDate = I('lossDate');
        $lossAddress = I('lossAddress');
        $lossDesc = I('lossDesc');

        if (!$access_token || !$phone_number || !$car_id || !$customerName || !$car_sn || !$carName || !$lossDate) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $user_id = $login_data[0]["user_id"];

            $car_lost = M('car_lost');

            $car_lost->car_id = $car_id;
            $car_lost->user_id = $user_id;
            $car_lost->phone_number = $phone_number;
            $car_lost->customer_name = $customerName;
            $car_lost->car_sn = $car_sn;
            $car_lost->car_name = $carName;
            $car_lost->loss_date = $lossDate;
            $car_lost->loss_address = $lossAddress;
            $car_lost->loss_desc = $lossDesc;
            $car_lost->time = date('Y-m-d H:i:s', time());

            $result = $car_lost->add();

            if (false !== $result) {
                //更新机车状态为3
                $car = M('Car');
                $condition = array(
                    "id" => $car_id
                );
                $car->state = 3;
                $result = $car->where($condition)->save();

                echo fit_api(true, 0, 'success!', '');
            } else {
                echo fit_api(true, 1, 'fail!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//失窃上报记录列表
    public
    function CarLostList()
    {
        $access_token = I('access_token');

        if (!$access_token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $car_lost = M('car_lost');

            $car_lost_data = $car_lost->order('time desc')->select();

            $carLostList = array();

            for ($i = 0; $i < count($car_lost_data); $i++) {
                $carLostList[] = array(
                    "orderId" => $car_lost_data[$i]["Id"],
                    "phoneNumbe" => $car_lost_data[$i]["phone_number"],
                    "customerName" => $car_lost_data[$i]["customer_name"],
                    "carName" => $car_lost_data[$i]["car_name"],
                    "carSn" => $car_lost_data[$i]["car_sn"],
                    "lostInfo" => $car_lost_data[$i]["loss_desc"],
                    "lostAddress" => $car_lost_data[$i]["loss_address"],
                    "lostSubmitTime" => $car_lost_data[$i]["time"],
                    "lostDate" => $car_lost_data[$i]["loss_date"]
                );
            }
            echo fit_api(true, 0, '成功', array("carLostList" => $carLostList));
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//获取音效方案列表（用于获取smart、音效方案文件列表）
    public
    function GetFileList()
    {
        $access_token = I('access_token');
        $type = I('type');
        $currentPage = I('currentPage', 1);
        $pageSize = I('pageSize', 20);


        if (!$access_token || !$type) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $car_file = M('car_file');

            $str_conditon = 'type = ' . $type;

            if ($type == 2) {
                $str_conditon = 'type > 20 and type < 30';
            }

            if ($currentPage < 1) {
                $currentPage = 1;
            }

            $data = $car_file->where($str_conditon)->order('fileId desc')->limit(($currentPage - 1) * $pageSize . ',' . $pageSize)->select();

            $fileList = array();
//"fileCarUrl" => $data[$i]["file_car_url"],"fileCarLength" => $data[$i]["file_car_length"],
            for ($i = 0; $i < count($data); $i++) {
                $fileList[] = array(
                    "fileId" => $data[$i]["fileId"],
                    "fileName" => $data[$i]["file_name"],
                    "fileAppUrl" => $this->website . $data[$i]["file_app_url"],
                    "fileAppLength" => $data[$i]["file_app_length"],
                    "fileMd5" => $data[$i]["file_app_md5"],
                    "isBuiltIn" => $data[$i]["is_builtIn"]
                );
            }
            echo fit_api(true, 0, '成功', array("fileList" => $fileList));
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

//提交意见反馈
    public
    function UploadFeedback()
    {
        $access_token = I('access_token');
        $infoTitle = I('infoTitle');
        $info = I('info');
        $type = I('type');

        if (!$access_token || !$infoTitle || !$info) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $user_id = $login_data[0]["user_id"];

            $user = M('user');
            $condition = array(
                "id" => $user_id
            );
            $user_data = $user->where($condition)->select();

            $feedback = M('feedback');

            if (user_data) {
                $feedback->user_name = $user_data[0]["user_name"];
                $feedback->nick_name = $user_data[0]["nick_name"];
                $feedback->car_id = $user_data[0]["car_id"];

                $car = M('car');
                $condition = array(
                    "id" => $user_data[0]["car_id"]
                );
                $car_data = $car->where($condition)->select();

                if ($car_data) {
                    $feedback->car_name = $car_data[0]["car_name"];
                    $feedback->car_sn = $car_data[0]["car_sn"];
                    $feedback->ctl_ver = $car_data[0]["ctl_ver"];
                    $feedback->car_frame_no = $car_data[0]["car_frame_no"];
                    $feedback->car_engine_no = $car_data[0]["car_engine_no"];
                }
            }

            $feedback->title = $infoTitle;
            $feedback->content = $info;
            $feedback->user_id = $user_id;
            $feedback->create_at = date('Y-m-d H:i:s', time());

            $feedback->type = $type;

            $pic_file = "";
            $path = dirname(dirname(__FILE__));
            $path = substr($path, 0, strrpos($path, '/', 0));
            $path = substr($path, 0, strrpos($path, '/', 0)) . '/Public/uploads/';

            for ($i = 1; $i < 10; $i++) {
                if (I('picFile' . $i))
                    $file = $this->save_img(I('picFile' . $i));
                if ($file) {
                    if ($pic_file != "") {
                        $pic_file = $pic_file . "," . $this->website . '/Public/uploads/' . $file;
                    } else {
                        $pic_file = $this->website . '/Public/uploads/' . $file;
                    }
                }
            }

            $feedback->pic_file = $pic_file;
            $result = $feedback->add();

            if (false !== $result) {
                echo fit_api(true, 0, 'success!', '');
            } else {
                echo fit_api(true, 1, 'fail!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    public
    function save_img($image)
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

    public
    function hideCarHistoryGps()
    {
        $access_token = I('access_token');
        $runHistroyId = I('runHistroyId');

        $login = M('Login');
        $condition['key'] = $access_token;
        $login_data = $login->where($condition)->select();

        if ($login_data) {
            $run_history = M('run_history');

            $condition = array("id" => $runHistroyId);
            $result = $run_history->where($condition)->delete();

            if (false !== $result) {
                echo fit_api(true, 0, 'success!', '');
            } else {
                echo fit_api(true, 1, 'fail!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }
}