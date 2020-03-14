<?php
/**
 * Created by PhpStorm.
 * User: ssc
 * Date: 2018/6/15
 * Time: 16:56
 */

namespace Home\Controller;

use Think\Controller;

class HouseController extends Controller
{
    public function index(){

        $houseModel = D('house');

        $house = $houseModel->where('id')->select();

        $this->assign('data', array(
            'house' => $house
        ));

        $this->display();
    }


    public function detail($id = 0) {

        $id = intval($id);
        $houseModel = D('house');
        $LayerModel = D('layer');
        $roomModel = D('room');
        $sroomModel = D('sroom');
        $garageModel = D('garage');
        $recommendModel = D('recommend');
        $occupationModel = D('occupation');

        $item = $houseModel->where("id='$id'")->find();

        $layer = $LayerModel->where("id={$item['layer_id']}")->find();
        $room = $roomModel->where("id={$item['room_id']}")->find();
        $sroom = $sroomModel->where("id={$item['sroom_id']}")->find();
        $garage = $garageModel->where("id={$item['garage_id']}")->find();
        $recommend = $recommendModel->where("aid={$item['recommend_id']}")->find();
        $occupation = $occupationModel->where("id={$item['occupation_id']}")->find();

        if (!$item) {
            $this->error('参数错误！');
        }
        $this->assign('occupation', $occupation);
        $this->assign('recommend', $recommend);
        $this->assign('garage', $garage);
        $this->assign('sroom', $sroom);
        $this->assign('room', $room);
        $this->assign('layer', $layer);
        $this->assign('item', $item);

        $this->display();

    }


    public function save_house_new(){
        $consultModel=M('consult');

        $name = isset($_POST['name'])?$_POST['name'] : false;
        $phone = isset($_POST['phone'])?$_POST['phone'] : false;
        $query = isset($_POST['query'])?$_POST['query'] : false;

        if (!$name) {
            echo ajax_api(true, '100', '姓名不能为空', false) ; exit;
        }
        if (!$phone) {
            echo ajax_api(true, '100', '电话不能为空', false) ; exit;
        }
        if (!$query) {
            echo ajax_api(true, '100', '意见不能为空', false) ; exit;
        }
        $data = array();
        $data['name'] = $name;
        $data['phone'] = $phone;
        $data['query'] = $query;

        $consult = $consultModel->add($data);

        if ($consult !== false) {
            echo ajax_api(true, 200, '提交成功', '');
        }
        else {
            echo ajax_api(true, 200, '提交失败', '');
        }
    }
}
