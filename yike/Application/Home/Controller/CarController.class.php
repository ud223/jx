<?php
namespace Home\Controller;
use Think\Controller;

class CarController extends Controller{
    //登录
    public function login() {
//        header( 'Access-Control-Allow-Origin:*' );

        $name = I('get.name');
        $password = I('get.password');

        $condition = array(
            'name' => $name,
            'password' => $password
        );

        $user = M('User');
        $data=$user->where($condition)->select();

        if($data) {
            $key = md5(uniqid().time().rand(10000000,99999999));
            $num = $this->insertLoginLog($key,$data[0]['id']);

            if($num>0){
                echo fit_api(true, 1, 'login success!', $key);
            }
            else{
                echo fit_api(true, 0, 'log is fail!', '');
            }
        }else{
            echo fit_api(true, 0, 'login is fail!', '');
        }
    }

    //增加登录记录
    public function insertLoginLog($key,$uid){
//        header( 'Access-Control-Allow-Origin:*' );

        $login = M('Login');

        $login->key =$key;
        $login->user_id =$uid;
        $login->login_time = date('Y-m-d H:i:s', time());
        $login->expire_time = date("Y-m-d H:i:s",strtotime("8 hour"));
        $login->is_expire = 2;
        return $login->add();
    }

    //上传机车信息
    public function uploadCar(){
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            echo fit_api(true, 0, 'no auth', ''); exit;
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

//        $phone_number = I('phone_number');
//        $battery_number = I('battery_number');
//        $arm_ver = I('arm_ver');
//        $muc_ver = I('muc_ver');
//        $ctl_ver = I('ctl_ver');
//        $bms_ver = I('bms_ver');
//        $car_sn = I('car_sn');
        
        //参数必填判断
        if(!$car_sn) {
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

        if ($car_sn != $auth) {
            echo fit_api(true, 0, 'auth fail', ''); exit;
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

        $condition = array(
            "car_sn" => $car_sn
        );

        $result = $car->where($condition)->save();

        if (false !== $result) {
            echo fit_api(true, 1, 'save success!', '');
        }
        else{
            echo fit_api(true, 0, 'save fail!', '');
        }
    }

    //上传机车状态信息
    public function uploadCarState(){
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            echo fit_api(true, 0, 'no auth', ''); exit;
        }

        $post_data = $GLOBALS['HTTP_RAW_POST_DATA'];
        //将JSON格式数据进行解码，解码后不是JSON数据格式，不可用echo直接输出
        $arr = json_decode($post_data, true);

        $tmp_auth = $_SERVER['HTTP_AUTHORIZATION'];

        $tmp_auth = str_replace('Basic', '', $tmp_auth);

        $auth = base64_decode($tmp_auth);

        $arr = $_POST;

        $car_state = $arr['car_state'];
        $powr_state = $arr['powr_state'];
        $car_sn = $arr['car_sn'];

//        $car_state = I('car_state');
//        $powr_state = I('powr_state');
//        $car_sn = I('car_sn');

        if(!$car_sn){
            echo fit_api(true, 0, '$car_sn parameters is imperfect!', ''); exit;
        }

        if (!$powr_state && $powr_state != 0){
            echo fit_api(true, 0, '$powr_state parameters is imperfect!', ''); exit;
        }

        if (!$car_state && $car_state != 0) {
            echo fit_api(true, 0, '$car_state parameters is imperfect!', ''); exit;
        }

        if (!$car_sn != $auth) {
            echo fit_api(true, 0, 'auth fail', ''); exit;
        }

        $db = M();
        $num = $db->query("select id  from fit_car where car_sn='".$car_sn."'");

        if($num){
            $car_id = $num[0]["id"];

            $state = M('Car_state');

            $state->car_state = $car_state;
            $state->powr_state = $powr_state;

            $condition = array(
                "car_id" => $car_id
            );

            $result = $state->where($condition)->save();

            if (false !== $result) {
                echo fit_api(true, 1, 'save success!', '');
            }
            else{
                echo fit_api(true, 0, 'save fail!', '');
            }
        }
        else{
            echo fit_api(true, 0, 'car_sn non-existent!', '');
        }
    }

    //上传机车电池状态信息
    public function uploadBatteryState(){
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            echo fit_api(true, 0, 'no auth', ''); exit;
        }

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

        if(!$car_sn) {
            echo fit_api(true, 0, '$car_sn parameters is imperfect!', ''); exit;
        }

        if (!$discharge_currnet && $discharge_currnet != 0) {
            echo fit_api(true, 0, '$discharge_currnet parameters is imperfect!', ''); exit;
        }

        if (!$charge_currnet && $charge_currnet != 0) {
            echo fit_api(true, 0, '$charge_currnet parameters is imperfect!', ''); exit;
        }

        if (!$filling_time && $filling_time != 0) {
            echo fit_api(true, 0, '$filling_time parameters is imperfect!', ''); exit;
        }

        if (!$temp && $temp != 0) {
            echo fit_api(true, 0, '$temp parameters is imperfect!', ''); exit;
        }

        if (!$soc_rt && $soc_rt != 0) {
            echo fit_api(true, 0, '$soc_rt parameters is imperfect!', ''); exit;
        }

        if ($car_sn != $auth) {
            echo fit_api(true, 0, 'auth fail', ''); exit;
        }

        if (!preg_match("/^\\d+$/",$discharge_currnet)){
            echo fit_api(true, 0, 'discharge_currnet format is err!', '');
            exit;
        }

        if (!preg_match("/^\\d+$/",$charge_currnet)){
            echo fit_api(true, 0, 'charge_currnet format is err!', '');
            exit;
        }

        $db = M();
        $b_num = $db->query("select battery_number from fit_car where car_sn='".$car_sn."'");

        if($b_num){
            $battery_number = $b_num[0]["battery_number"];
            $battery = M('Battery');

            $battery->discharge_currnet = $discharge_currnet;
            $battery->charge_currnet = $charge_currnet;
            $battery->filling_time = $filling_time;
            $battery->temp = $temp;
            $battery->soc_rt = $soc_rt;

            $condition = array(
                "battery_sn" => $battery_number
            );

            $result = $battery->where($condition)->save();

            if (false !== $result) {
                echo fit_api(true, 1, 'save success!', '');
            }
            else{
                echo fit_api(true, 0, 'save fail!', '');
            }
        }
        else{
            echo fit_api(true, 0, 'car_sn non-existent!', '');
        }
    }

    //上传机车gps信息
    public function uploadGps(){
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            echo fit_api(true, 0, 'no auth', ''); exit;
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
        $speed = $arr['speed'];
        $car_sn = $arr['car_sn'];

//        $o_direction = I('o_direction');
//        $a_direction = I('a_direction');
//        $longitude = I('longitude');
//        $latitude = I('latitude');
//        $time = I('time');
//        $mileage = I('mileage');
//        $speed = I('speed');
//        $car_sn = I('car_sn');

//        echo fit_api(true, 0, implode(' ', $arr), ''); exit;

        if(!$car_sn && $car_sn != 0) {
            echo fit_api(true, 0, 'car_sn parameters is imperfect!', ''); exit;
        }

        if (!$o_direction && $o_direction != 0) {
            echo fit_api(true, 0, '$o_direction parameters is imperfect!', ''); exit;
        }

        if (!$a_direction && $a_direction != 0) {
            echo fit_api(true, 0, '$a_direction parameters is imperfect!', ''); exit;
        }

        if (!$longitude && $longitude != 0) {
            echo fit_api(true, 0, '$longitude parameters is imperfect!', ''); exit;
        }

        if (!$latitude && $latitude != 0) {
            echo fit_api(true, 0, '$latitude parameters is imperfect!', ''); exit;
        }

        if (!$time && $time != 0) {
            echo fit_api(true, 0, '$time parameters is imperfect!', ''); exit;
        }

        if (!$mileage && $mileage != 0) {
            echo fit_api(true, 0, '$mileage parameters is imperfect!', ''); exit;
        }

        if (!$speed && $speed != 0) {
            echo fit_api(true, 0, '$speed parameters is imperfect!', ''); exit;
        }

        if ($car_sn != $auth) {
            echo fit_api(true, 0, 'auth fail! car_sn:'.$car_sn.'; auth:'. $auth, ''); exit;
        }

        if (!preg_match("/^\\d+(\.\\d+)?$/",$mileage)){
            echo fit_api(true, 0, 'mileage format is err!', '');
            exit;
        }

        $db = M();
        $num = $db->query("select id  from fit_car where car_sn='".$car_sn."'");

        if($num){
            $car_id = $num[0]["id"];

            $num = $db->query("select count(id) as num from fit_gps");//生成id
            $num = $num[0]["num"] + 1;
            while(strlen($num) <6){
                $num='0'.$num;
            }

            $gps = M('Gps');

            $gps->id = uniqid().time().rand(10000000,99999999).$num;
            $gps->car_id = $car_id;
            $gps->o_direction = $o_direction;
            $gps->a_direction = $a_direction;
            $gps->longitude = $longitude;
            $gps->latitude = $latitude;
            $gps->time = $time;
            $gps->mileage = $mileage;
            $gps->speed = $speed;

            $result = $gps->add();

            if (false !== $result) {
                echo fit_api(true, 1, 'save success!', '');
            }
            else{
                echo fit_api(true, 0, 'save fail!', '');
            }
        }
        else{
            echo fit_api(true, 0, 'car_sn non-existent!', '');
        }
    }
} 