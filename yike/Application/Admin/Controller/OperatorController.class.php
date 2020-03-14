<?php
namespace Admin\Controller;

use Think\Controller;

class OperatorController extends Controller
{
    public function login()
    {
        $this->display();
    }

    public function login1()
    {
        $username = I('username');
        $password = I('password');

        $Operator = D('operator');

        $condigion = array(
            "user_name" => $username,
            "password" => md5($password)
        );

        $data = $Operator->where($condigion)->select();

        if ($data && $data[0]["enable"] == 1) {
            echo fit_api(true, 200, '登录成功!', $data[0]["id"]);
        } else {
            if (count($data) == 0)
                echo fit_api(true, 0, '用户名或密码错误!', 0);
            else {
                if ($data[0]["enable"] == 0)
                    echo fit_api(true, 0, '用户被锁定!', 0);
                else {
                    echo fit_api(true, 0, '错误!', 0);
                }
            }
        }
    }

    public function car_list()
    {
        $car_sn = I('car_sn');
        $carModel = D('car');

        if ($car_sn) {
            $condition = array("car_sn" => $car_sn);
            $count = $carModel->where($condition)->count();
        }
        //else
        //   $count = $carModel->count();

        $p = getpage($count, 20);

        if ($car_sn)
            $list = $carModel->query("select a.*,b.name from fit_car a left join fit_operator b on a.operator_id = b.id where a.car_sn ='" . $car_sn . "' order by a.id limit " . $p->firstRow . ',' . $p->listRows);
        //else
        // $list = $carModel->query("select a.*,b.name from fit_car a left join fit_operator b on a.operator_id = b.id order by a.id limit " . $p->firstRow . ',' . $p->listRows);

        $this->assign('data', array(
            'list' => $list
        ));

        $this->assign('page', $p->show()); // 赋值分页输出

        $this->display();
    }

    public function car_freelog_list()
    {
        $car_sn = I('car_sn');
        $freelogModel = D('freelog');

        if ($car_sn) {
            $count = $freelogModel->query("select a.time as free_time,a.freeze,b.id,b.car_sn,b.car_frame_no,c.name from fit_freelog a left join fit_car b on a.car_id = b.id left join fit_user c on a.operator_id = c.id where b.car_sn = '" . $car_sn . "'");
        }

        $p = getpage(count($count), 20);

        if ($car_sn)
            $list = $freelogModel->query("select a.time as free_time,a.freeze,b.id,b.car_sn,b.car_frame_no,c.name from fit_freelog a left join fit_car b on a.car_id = b.id left join fit_operator c on a.operator_id = c.id where b.car_sn = '" . $car_sn . "' order by a.time desc limit " . $p->firstRow . ',' . $p->listRows);
        // else
        //    $list = $freelogModel->query("select a.time as free_time,a.freeze,b.id,b.car_sn,b.car_frame_no,c.name from fit_freelog a left join fit_car b on a.car_id = b.id left join fit_operator c on a.operator_id = c.id  order by a.id limit " . $p->firstRow . ',' . $p->listRows);

        $this->assign('data', array(
            'list' => $list
        ));

        $this->assign('page', $p->show()); // 赋值分页输出

        $this->display();
    }

    //解冻
    public function car_del()
    {
        $id = I('id');
        $operator_id = I('operator_id');

        $carModel = D('car');

        $condition = array("id" => $id);

        $carModel->freeze = 1;
        $carModel->locked = 0;
        $carModel->operator_id = $operator_id;
        $carModel->free_time = date('Y-m-d H:i:s', time());

        $result = $carModel->where($condition)->save();

        $free_logModel = D('freelog');
        $free_logModel->operator_id = $operator_id;
        $free_logModel->time = date('Y-m-d H:i:s', time());
        $free_logModel->freeze = 1;
        $free_logModel->car_id = $id;
        $result = $free_logModel->add();

        $car = M('Car');
        $condition = array(
            "id" => $id
        );

        $car_data = $car->where($condition)->select();
        $iccid = $car_data[0]["iccid"];
        $car_sn = $car_data[0]["car_sn"];

        //发送短信通知机车
        IccidSendSms($iccid, 'CAR_UNLOCK:' . $iccid . '&' . $car_sn . '$');

        if (false !== $result || 0 !== $result) {
            $result = true;
        } else {
            $result = false;
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', '');
        else
            echo fit_api(true, 0, '保存失败', '');
    }

    //冻结
    public function car_edit()
    {
        $id = I('id');
        $operator_id = I('operator_id');

        $carModel = D('car');

        $condition = array("id" => $id);

        $carModel->freeze = 0;
        $carModel->locked = 1;
        $carModel->operator_id = $operator_id;
        $carModel->free_time = date('Y-m-d H:i:s', time());

        $result = $carModel->where($condition)->save();

        $free_logModel = D('freelog');
        $free_logModel->operator_id = $operator_id;
        $free_logModel->time = date('Y-m-d H:i:s', time());
        $free_logModel->freeze = 0;
        $free_logModel->car_id = $id;
        $result = $free_logModel->add();

        $car = M('Car');
        $condition = array(
            "id" => $id
        );

        $car_data = $car->where($condition)->select();
        $iccid = $car_data[0]["iccid"];
        $car_sn = $car_data[0]["car_sn"];

        //发送短信通知机车
        IccidSendSms($iccid, 'CAR_LOCK:' . $iccid . '&' . $car_sn . '$');

        if (false !== $result || 0 !== $result) {
            $result = true;
        } else {
            $result = false;
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', '');
        else
            echo fit_api(true, 0, '保存失败', '');
    }

    public function car_map()
    {
        $id = I('id');

        $gpsModel = M('gps');
        //查找最后一条有效经纬度数据
        $conditon = array(
            "longitude" => array('LT', 180),
            "latitude" => array('LT', 90),
            "car_id" => $id
        );
        $gps_validate_data = $gpsModel->where($conditon)->order('time desc')->limit('1')->select();

        if ($gps_validate_data) {
            $this->assign('lng', $gps_validate_data[0]["longitude"]);
            $this->assign('lat', $gps_validate_data[0]["latitude"]);
            $this->assign('time', $gps_validate_data[0]["time"]);

        } else {
            $this->assign('lng', 116.397428);
            $this->assign('lat', 39.90923);
        }

        $this->display();
    }

    /* 退出登录 */
    public function logout()
    {
        if (is_login()) {
            D('Member')->logout();
            session('[destroy]');
            $this->success('退出成功！', U('Operator/login'));
        } else {
            $this->redirect('Operator/login');
        }
    }
}