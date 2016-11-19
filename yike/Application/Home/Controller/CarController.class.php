<?php
namespace Home\Controller;
use Think\Controller;

class CarController extends Controller
{
    //登录
    public function login()
    {
//        header( 'Access-Control-Allow-Origin:*' );

        $name = I('get.name');
        $password = I('get.password');

        $condition = array(
            'name' => $name,
            'password' => $password
        );

        $user = M('User');
        $data = $user->where($condition)->select();

        if ($data) {
            $key = md5(uniqid() . time() . rand(10000000, 99999999));
            $num = $this->insertLoginLog($key, $data[0]['id']);

            if ($num > 0) {
                echo fit_api(true, 1, 'login success!', $key);
            } else {
                echo fit_api(true, 0, 'log is fail!', '');
            }
        } else {
            echo fit_api(true, 0, 'login is fail!', '');
        }
    }

    //增加登录记录
    public function insertLoginLog($key, $uid)
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

    //上传机车信息
    public function uploadCar()
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

        $condition = array(
            "car_sn" => $car_sn
        );

        $result = $car->where($condition)->save();

        if (false !== $result) {
            echo fit_api(true, 1, 'save success!', '');
        } else {
            echo fit_api(true, 0, 'save fail!', '');
        }
    }

    //上传机车状态信息
    public function uploadCarState()
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

        $car_state = $arr['car_state'];
        $powr_state = $arr['powr_state'];
        $car_sn = $arr['car_sn'];

//        $car_state = I('car_state');
//        $powr_state = I('powr_state');
//        $car_sn = I('car_sn');

        if (!$car_sn) {
            echo fit_api(true, 0, '$car_sn parameters is imperfect!', '');
            exit;
        }

        if (!$powr_state && $powr_state != 0) {
            echo fit_api(true, 0, '$powr_state parameters is imperfect!', '');
            exit;
        }

        if (!$car_state && $car_state != 0) {
            echo fit_api(true, 0, '$car_state parameters is imperfect!', '');
            exit;
        }

        if (!$car_sn != $auth) {
            echo fit_api(true, 0, 'auth fail', '');
            exit;
        }

        $db = M();
        $num = $db->query("select id  from fit_car where car_sn='" . $car_sn . "'");

        if ($num) {
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
            } else {
                echo fit_api(true, 0, 'save fail!', '');
            }
        } else {
            echo fit_api(true, 0, 'car_sn non-existent!', '');
        }
    }

    //上传机车电池状态信息
    public function uploadBatteryState()
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

        if (!$car_sn) {
            echo fit_api(true, 0, '$car_sn parameters is imperfect!', '');
            exit;
        }

        if (!$discharge_currnet && $discharge_currnet != 0) {
            echo fit_api(true, 0, '$discharge_currnet parameters is imperfect!', '');
            exit;
        }

        if (!$charge_currnet && $charge_currnet != 0) {
            echo fit_api(true, 0, '$charge_currnet parameters is imperfect!', '');
            exit;
        }

        if (!$filling_time && $filling_time != 0) {
            echo fit_api(true, 0, '$filling_time parameters is imperfect!', '');
            exit;
        }

        if (!$temp && $temp != 0) {
            echo fit_api(true, 0, '$temp parameters is imperfect!', '');
            exit;
        }

        if (!$soc_rt && $soc_rt != 0) {
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
        $b_num = $db->query("select battery_number from fit_car where car_sn='" . $car_sn . "'");

        if ($b_num) {
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
            } else {
                echo fit_api(true, 0, 'save fail!', '');
            }
        } else {
            echo fit_api(true, 0, 'car_sn non-existent!', '');
        }
    }

    //上传机车gps信息
    public function uploadGps()
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
        $speed = $arr['speed'];
        $car_sn = $arr['car_sn'];
        $gps_state = $arr['gps_state'];
        $gsm_state = $arr['gsm_state'];

//        $o_direction = I('o_direction');
//        $a_direction = I('a_direction');
//        $longitude = I('longitude');
//        $latitude = I('latitude');
//        $time = I('time');
//        $mileage = I('mileage');
//        $speed = I('speed');
//        $car_sn = I('car_sn');

//        echo fit_api(true, 0, implode(' ', $arr), ''); exit;

        if (!$car_sn && $car_sn != 0) {
            echo fit_api(true, 0, 'car_sn parameters is imperfect!', '');
            exit;
        }

        if (!$o_direction && $o_direction != 0) {
            echo fit_api(true, 0, '$o_direction parameters is imperfect!', '');
            exit;
        }

        if (!$a_direction && $a_direction != 0) {
            echo fit_api(true, 0, '$a_direction parameters is imperfect!', '');
            exit;
        }

        if (!$longitude && $longitude != 0) {
            echo fit_api(true, 0, '$longitude parameters is imperfect!', '');
            exit;
        }

        if (!$latitude && $latitude != 0) {
            echo fit_api(true, 0, '$latitude parameters is imperfect!', '');
            exit;
        }

        if (!$time && $time != 0) {
            echo fit_api(true, 0, '$time parameters is imperfect!', '');
            exit;
        }

        if (!$mileage && $mileage != 0) {
            echo fit_api(true, 0, '$mileage parameters is imperfect!', '');
            exit;
        }

        if (!$speed && $speed != 0) {
            echo fit_api(true, 0, '$speed parameters is imperfect!', '');
            exit;
        }

        if ($car_sn != $auth) {
            echo fit_api(true, 0, 'auth fail! car_sn:' . $car_sn . '; auth:' . $auth, '');
            exit;
        }

        if (!$gps_state && $gps_state != 0) {
            echo fit_api(true, 0, '$gps_state parameters is imperfect!', '');
            exit;
        }

        if (!$gsm_state && $gsm_state != 0) {
            echo fit_api(true, 0, '$gsm_state parameters is imperfect!', '');
            exit;
        }

        if (!preg_match("/^\\d+(\.\\d+)?$/", $mileage)) {
            echo fit_api(true, 0, 'mileage format is err!', '');
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
            $gps->longitude = $longitude;
            $gps->latitude = $latitude;
            $gps->time = $time;
            $gps->mileage = $mileage;
            $gps->speed = $speed;
            $gps->gps_state = $gps_state;
            $gps->gsm_state = $gsm_state;

            $result = $gps->add();

            if (false !== $result) {
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

                $gps = M('Gps');
                $conditon = array(
                    "car_id" => $car_id
                );
                $gps_data = $gps->where($conditon)->select();

                $battery = M('Battery');
                $conditon = array(
                    "battery_sn" => $data[0]['battery_number']
                );
                $battery_data = $battery->where($conditon)->select();

                $batt = '';
                if($battery_data){
                    $batt = $battery_data[0]['soc_rt'];
                }

                $result = array(
                    "isLocked" => count($data) == 0 ? '': $data[0]["locked"],
                    "carImgUrl" => count($data) == 0 ? '': $data[0]["car_imgurl"],
                    "gpsRefreshTime" => count($data) == 0 ? '': $data[0]["gps_refreshtime"],
                    "gsmRefreshTime" => count($data) == 0 ? '': $data[0]["gsm_refreshtime"],
                    "remainderEnergy"=>$batt,

                    "longitude" => count($gps_data) == 0 ? '': $gps_data[0]["longitude"],
                    "latitude" => count($gps_data) == 0 ? '': $gps_data[0]["latitude"],
                    "refreshTime" => count($gps_data) == 0 ? '': $gps_data[0]["time"],
                    "address" => count($gps_data) == 0 ? '': $gps_data[0]["address"],
                    "remainderRange" => count($gps_data) == 0 ? '': $gps_data[0]["mileage"],
                    "gpsState" => count($gps_data) == 0 ? '': $gps_data[0]["gps_state"],
                    "gsmState" => count($gps_data) == 0 ? '': $gps_data[0]["gsm_state"],

                    "gpsRefreshTime" => count($data) == 0 ? '': $data[0]["gps_refreshtime"],
                    "gsmRefreshTime" => count($data) == 0 ? '': $data[0]["gsm_refreshtime"],
                    "advertiseImgUrl" => "",
                    "advertiseHtmlUrl " => ""
                );

                echo fit_api(true, 0, '成功!', $result);
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //获取车辆行驶历史记录
    public function GetCarHistoryGpsList()
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

                $history_data = $run_history->where($condition)->order('id desc')->limit(($currentPage - 1) * $pageSize + 1 . ',' . $pageSize)->select();

                $result = array('runHistroyList' =>array());
                for ($i = 0; $i < count($history_data); $i++) {
                    //查询时间段内的gps详细信息
                    $gps = M('Gps');

                    $condition = array(array('EGT', $history_data[$i]['start_date']), array('ELT', $history_data[$i]['end_date']), 'AND');
                    $gps_data = $gps->where($condition)->order('id asc')->select();

                    if (count($gps_data) > 0) {
                        if (count($gps_data) == 1) {
                            //计算距离
                            $distance = 0;

                            //计算速度
                            $speed = 0;

                            $result['runHistroyList'][] = array(
                                'runHistroyId' => $history_data[$i]['id'],
                                'runimeInfo' => date("Y-m-d", strtotime($history_data[$i]['start_date'])) . ' 星期' . date('N', strtotime($history_data[$i]['start_date'])),
                                'startTime' => date('H:i', strtotime($history_data[$i]['start_date'])),
                                'endTime' => date('H:i', strtotime($history_data[$i]['end_date'])),
                                'usedTime' => DateDiff("H:i:s", strtotime($history_data[$i]['start_date'], strtotime($history_data[$i]['end_date']))),
                                'distance' => $distance,
                                'averageSpeed' => $speed,
                                'startLongitude' => $gps_data[0]['longitude'],
                                'startLatitude' => $gps_data[0]['latitude'],
                                'startAddress' =>$gps_data[0]['address'],
                                'endLongitude' => $gps_data[0]['longitude'],
                                'endLatitude' => $gps_data[0]['latitude'],
                                'endAddress' =>$gps_data[0]['address']
                            );
                        } else {
                            //计算距离
                            $distance = GetDistance($gps_data[0]['latitude'], $gps_data[0]['longitude'], $gps_data[count($gps_data)-1]['latitude'], $gps_data[count($gps_data)-1]['longitude']);

                            //计算速度
                            $speed = $distance / DateDiff("G", strtotime($history_data[$i]['start_date'], strtotime($history_data[$i]['end_date'])));
                            //DateDiff("G", strtotime($history_data['start_date'], strtotime($history_data['end_date']))),
                            $result['runHistroyList'][] = array(
                                'runHistroyId' => $history_data[$i]['id'],
                                'runimeInfo' => date("Y-m-d", strtotime($history_data[$i]['start_date'])) . ' 星期' . date('N', strtotime($history_data[$i]['start_date'])),
                                'startTime' => date('H:i', strtotime($history_data[$i]['start_date'])),
                                'endTime' => date('H:i', strtotime($history_data[$i]['end_date'])),
                                'usedTime' => DateDiff("H:i:s", strtotime($history_data[$i]['start_date'], strtotime($history_data[$i]['end_date']))),
                                'distance' => $distance,
                                'averageSpeed' => $speed,
                                'startLongitude' => $gps_data[0]['longitude'],
                                'startLatitude' => $gps_data[0]['longitude'],
                                'startAddress' =>$gps_data[0]['address'],
                                'endLongitude' => $gps_data[count($gps_data)-1]['longitude'],
                                'endLatitude' => $gps_data[count($gps_data)-1]['longitude'],
                                'endAddress' =>$gps_data[count($gps_data)-1]['address']
                            );
                        }
                    }
                }
                echo fit_api(true, 0, '成功!', $result);
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //获取车辆行驶历史记录详细轨迹
    public function GetCarHistoryGpsInfoList()
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

                if(!$history_data){
                    echo fit_api(true, 0, '成功!', "");
                    exit;
                }

                $runHistroyList = array();

                    //查询时间段内的gps详细信息
                    $gps = M('Gps');

                    $condition = array(array('EGT', $history_data[0]['start_date']), array('ELT', $history_data[0]['end_date']), 'AND');
                    $gps_data = $gps->where($condition)->order('id asc')->select();

                    if (count($gps_data) > 0) {
                        if (count($gps_data) == 1) {
                            //计算距离
                            $distance = 0;

                            //计算速度
                            $speed = 0;

                            $runList = array(
                                'longitude' =>$gps_data[0]['longitude'],
                                'latitude' =>$gps_data[0]['latitude'],
                                'time' =>$gps_data[0]['time']
                            );

                            $runHistroyList[] = array(
                                'runHistroyId' => $history_data[0]['id'],
                                'runimeInfo' => date("Y-m-d", strtotime($history_data[0]['start_date'])) . ' 星期' . date('N', strtotime($history_data[0]['start_date'])),
                                'startTime' => date('H:i', strtotime($history_data[0]['start_date'])),
                                'endTime' => date('H:i', strtotime($history_data[0]['end_date'])),
                                'usedTime' => DateDiff("H:i:s", strtotime($history_data[0]['start_date'], strtotime($history_data[0]['end_date']))),
                                'distance' => $distance,
                                'averageSpeed' => $speed,
                                'startLongitude' => $gps_data[0]['longitude'],
                                'startLatitude' => $gps_data[0]['latitude'],
                                'startAddress' =>$gps_data[0]['address'],
                                'endLongitude' => $gps_data[0]['longitude'],
                                'endLatitude' => $gps_data[0]['latitude'],
                                'endAddress' =>$gps_data[0]['address'],
                                'runList'=>$runList
                            );
                        } else {
                            //计算距离
                            $distance = GetDistance($gps_data[0]['latitude'], $gps_data[0]['longitude'], $gps_data[count($gps_data)-1]['latitude'], $gps_data[count($gps_data)-1]['longitude']);

                            //计算速度
                            $speed = $distance / DateDiff("G", strtotime($history_data[0]['start_date'], strtotime($history_data[0]['end_date'])));

                            $runList=array();

                            for($i=0;$i<count($gps_data);$i++){
                                $runList[] = array(
                                    'longitude' =>$gps_data[$i]['longitude'],
                                    'latitude' =>$gps_data[$i]['latitude'],
                                    'time' =>$gps_data[$i]['time']
                                );
                            }
                            $runHistroyList[] = array(
                                'runHistroyId' => $history_data[0]['id'],
                                'runimeInfo' => date("Y-m-d", strtotime($history_data[0]['start_date'])) . ' 星期' . date('N', strtotime($history_data[0]['start_date'])),
                                'startTime' => date('H:i', strtotime($history_data[0]['start_date'])),
                                'endTime' => date('H:i', strtotime($history_data[0]['end_date'])),
                                'usedTime' => DateDiff("H:i:s", strtotime($history_data[0]['start_date'], strtotime($history_data[0]['end_date']))),
                                'distance' => $distance,
                                'averageSpeed' => $speed,
                                'startLongitude' => $gps_data[0]['longitude'],
                                'startLatitude' => $gps_data[0]['longitude'],
                                'startAddress' =>$gps_data[0]['address'],
                                'endLongitude' => $gps_data[count($gps_data)-1]['longitude'],
                                'endLatitude' => $gps_data[count($gps_data)-1]['longitude'],
                                'endAddress' =>$gps_data[count($gps_data)-1]['address'],
                                'runList'=>$runList
                            );
                        }
                    }

                echo fit_api(true, 0, '成功!', $runHistroyList);
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //得到时间段gps数据
    public function GetGpsByDate(){
        $access_token = I('access_token');
        $car_sn = I('car_sn');
        $starttime = I('starttime');
        $endtime = I('endtime');

        if($access_token && $car_sn && ($starttime || $endtime)){
            $login = M('Login');

            $condition['key'] = $access_token;
            $data = $login->where($condition)->select();

            if($data) {
                $user_id = $data[0]["user_id"];

                $user = M('User');

                $condition = array(
                    "id" => $user_id
                );
                $data = $user->where($condition)->select();

                if($data){
                    $car_id = $data[0]["car_id"];

                    //对比car_sn
                    $car = M('Car');

                    $condition = array(
                        "id" => $car_id
                    );
                    $data = $car->where($condition)->select();

                    if($data){
                        $data_car_sn = $data[0]["car_sn"];

                        if($data_car_sn == $car_sn){
                            $gps_condition = "car_id = '". $car_id . "'";

                            if($starttime)
                                $gps_condition = $gps_condition . " and time >= '". $starttime ."'";

                            if($endtime)
                                $gps_condition = $gps_condition . " and time <= '". $endtime ."'";

                            $db = M('Gps');

                            $data = $db->where($gps_condition)->field("o_direction,a_direction,longitude,latitude,mileage,speed")->select();

                            $ishave = 0;
                            if(count($data)>0)
                                $ishave = 1;

                            $result = array(
                                "count"=>count($data),
                                "ishave"=>$ishave,
                                "gpsdata"=>$data
                            );
                            echo fit_api(true, 0, '获得成功!', $result);
                        }
                        else{
                            echo fit_api(true, 1, 'car_sn错误!', '');
                        }
                    }
                    else{
                        echo fit_api(true, 1, '没有car!', '');
                    }
                }
                else{
                    echo fit_api(true, 1, '没有user!', '');
                }
            }
            else{
                echo fit_api(true, 1, 'access_token错误!', '');
            }
        }
        else{
            echo fit_api(true, 1, '参数为空!', '');
        }
    }

    //得到机车固件信息
    public function GetCarRom(){
        $access_token = I('access_token');
        $car_sn = I('car_sn');

        if($access_token && $car_sn){
            $login = M('Login');

            $condition['key'] = $access_token;
            $data = $login->where($condition)->select();

            if($data) {
                $user_id = $data[0]["user_id"];

                $user = M('User');

                $condition = array(
                    "id" => $user_id
                );
                $data = $user->where($condition)->select();

                if($data){
                    $car_id = $data[0]["car_id"];

                    //对比car_sn
                    $car = M('Car');

                    $condition = array(
                        "id" => $car_id
                    );
                    $data = $car->where($condition)->select();

                    if($data){
                        $data_car_sn = $data[0]["car_sn"];

                        if($data_car_sn == $car_sn){

                            $result = array(
                                "phone_number" => $data[0]["phone_number"],
                                "battery_number" => $data[0]["battery_number"],
                                "arm_ver" => $data[0]["arm_ver"],
                                "muc_ver" => $data[0]["muc_ver"],
                                "ctl_ver" => $data[0]["ctl_ver"],
                                "bms_ver" => $data[0]["bms_ver"]
                            );

                            echo fit_api(true, 0, '获得成功!', $result);
                        }
                        else{
                            echo fit_api(true, 1, 'car_sn错误!', '');
                        }
                    }
                    else{
                        echo fit_api(true, 1, '没有car!', '');
                    }
                }
                else{
                    echo fit_api(true, 1, '没有user!', '');
                }
            }
            else{
                echo fit_api(true, 1, 'access_token错误!', '');
            }
        }
        else{
            echo fit_api(true, 1, '参数为空!', '');
        }
    }

    //获取车辆最近7天行驶数据
    public  function GetWeekGpsData()
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

            $conditon=array(
                "user_id" => $userid,
                "car_id" => $car_id
            );
            $user_car_data = $user_car->where($conditon)->select();

            //这里判断默认car和关联car
            if ($user_car_data) {
                $gps = M('Gps');

                $condition = array(
                    "car_id" => $car_id
                );
                $data = $gps->where($condition)->select();

                $result = array("runHistroyList" => array());

                for ($i = 0; $i < 7; $i++) {
                    $distance = 0;
                    for ($a = 0; $a < count($data); $a++) {
                        if ($data[$a]["time"] != "") {
                            if (substr($data[$a]["time"], 0, 10) == date("Y-m-d", strtotime("-" . $i . " day"))) {
                                $distance += $data[$a]["mileage"];
                            }
                        }
                    }
                    $result["runHistroyList"][] = array(
                        "weekInfo" => date("N", strtotime("-" . $i . " day")),
                        "runDistance" => $distance,
                        "runDay" => date("Y-m-d", strtotime("-" . $i . " day"))
                    );
                }
                echo fit_api(true, 0, '成功!', $result);
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //选择车辆
    public function ChoiceCar(){
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

            $conditon=array(
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

                if ($result != false) {
                    echo fit_api(true, 0, '更新成功!', '');
                } else {
                    echo fit_api(true, 4, '更新失败!', '');
                }
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //获取车辆信息（车辆管理）
    public  function GetCarInfo()
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

            $conditon=array(
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

                $result = array(
                    "car_id"=>$car_id,
                    "car_sn"=>$data[0]["car_sn"],
                    "carImgUrl"=>$data[0]["car_imgurl"],
                    "carSimpleName"=>$data[0]["car_simple_name"],
                    "carName"=>$data[0]["car_name"],
                    "frameNO"=>$data[0]["car_frame_no"],
                    "engineNO"=>$data[0]["car_engine_no"],
                    "carVersion"=>$data[0]["car_version"],
                    "hasNewCarVersion"=>$data[0]["hasNewCarVersion"],
                    "gpsRefreshTime"=>$data[0]["gps_refreshtime"],
                    "gsmRefreshTime"=>$data[0]["gsm_refreshtime"]
                );

                echo fit_api(true, 0, '成功!', $result);
            } else {
                echo fit_api(true, 3, '非法车辆!', '');
            }
        } else {
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //获取天气预报
    public function GetWeather()
    {
        $longitude = I('longitude');
        $latitude = I('latitude');

        if (!$longitude || !$latitude) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        //取出城市
        //例子http://api.map.baidu.com/geocoder/v2/?ak=PjtTnnvyjoVqI36gY8XQS4Im3CuIbb8c&location=31.582140,120.355320&output=json&pois=1
         $url = 'http://api.map.baidu.com/geocoder/v2/?ak=PjtTnnvyjoVqI36gY8XQS4Im3CuIbb8c&location='.$latitude.','.$longitude.'&output=json&pois=1';

        $result = file_get_contents($url);

        $json_string = json_decode($result);

        if($json_string->result->addressComponent->city) {
            $city = $json_string->result->addressComponent->city;
            //根据城市获取天气
            $url='http://op.juhe.cn/onebox/weather/query?cityname='.$city.'&key=6db3e27a68e43dc316278b876764c4df';

            $result = file_get_contents($url);
            $json_string = json_decode($result);

            $data = array(
                'realtime'=>$json_string->result->data->realtime
            );

            echo fit_api(true, 0, '成功!', $data);
        }
        else{
            echo fit_api(true, 2, '城市为空!', '');
        }
    }

    //获取我的车辆列表
    public function GetUserCarList(){
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

            $conditon=array(
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
                            "carImgUrl" => $car_data[0]["car_imgurl"],
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
                "carList" => $carList
            );

            echo fit_api(true, 0, '成功!', $result);

        } else {
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //设置车辆名称
    public function SetCarSimpleName()
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
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //车辆过户1（车主信息）
    public function CarTransferStep1()
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
                    $car_transfer->id = $transfer_id;
                    $car_transfer->car_id = $car_id;
                    $car_transfer->customer_name = $customerName;
                    $car_transfer->customer_address = $customerAddress;
                    $car_transfer->car_sn = $car_sn;
                    $car_transfer->car_name = $carName;
                    $car_transfer->user_id = $user_id;
                    $result = $car_transfer->add();

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
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //车辆过户2（受让人信息）
    public function CarTransferStep2()
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
                        "id" => $transferOrderId
                    );

                    //修改过户申请表
                    $car_transfer->receive_user_id = $user_id;
                    $car_transfer->receive_name = $receiveName;
                    $car_transfer->receive_address = $receiveAddress;
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
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //自助检测
    public function SelfCheck(){
        echo 'http://mall.webetter100.com/mobile/test_self';
    }

    //车辆过户记录
    public function GetCarTransfer(){
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
            $transfer_data = $car_transfer->where($condition)->select();

            $result =array();

            for ($a = 0; $a < count($transfer_data); $a++) {
                //查询出让人手机号
                $user = M('user');
                $user_data = $user->where(array("id" => $transfer_data[a]["user_id"]))->select();

                $result[] = array(
                    "transferOrderId" =>$transfer_data[$a]["Id"],
                    "phoneNumbe" =>$user_data[0]["phone_number"],
                    "customerName" =>$transfer_data[$a]["customerName"],
                    "customerAddress" =>$transfer_data[$a]["customerAddress"],
                    "carName" =>$transfer_data[$a]["carName"],
                    "carSn" =>$transfer_data[$a]["carSn"],
                    "receiveName" =>$transfer_data[$a]["receive_name"],
                    "receiveAddress" =>$transfer_data[$a]["receive_address"],
                    "transferTime" =>$transfer_data[$a]["time"]
                );
            }

            echo fit_api(true, 0, '成功', array("transferList" => $result));
        } else {
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }

    //失窃上报短信验证接口
    public function CarLostStep1(){
        $access_token = I('access_token');
        $phone_number = I('phone_number');
        $checkcode = I('checkcode');

        if (!$access_token || !$phone_number || !$checkcode) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        //检查验证码
        if (CheckCode($phone_number, $checkcode, 8) == 1) {
            echo fit_api(true, 0, '验证码正确!', '');
        }
        else{
            echo fit_api(true, 4, '验证码不正确!', '');
        }
    }

    //失窃上报信息提交接口
    public function CarLostStep2(){
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

            $result = $car_lost->add();

            if (false !== $result) {
                echo fit_api(true, 1, 'save success!', '');
            } else {
                echo fit_api(true, 0, 'save fail!', '');
            }
        } else {
            echo fit_api(true, 2, 'access_token不正确!', '');
        }
    }
}