<?php
/**
 * Created by PhpStorm.
 * User: ssc
 * Date: 2018/6/13
 * Time: 21:08
 */

namespace Qwadmin\Controller;


class HouseController extends ComController
{
    public function index($p = 1) {
        $p = intval($p) > 0 ? $p : 1;
        $houseModel = D('house');
        $layerModel = D('layer');
        $roomModel = D('room');
        $sroomModel =D('sroom');
        $garageModel = D('garage');
        $recommenModel = D('recommend');
        $occupationModel = D('occupation');

        $pagesize = 1;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where='1 = 1  ';

        $count=$houseModel->where($where)->count();

        $tmp_list=$houseModel->order('id')->limit($offset . ',' . $pagesize)->select();

        $list = array();

        foreach ($tmp_list as $p) {
            $p['layer'] = $layerModel->where("id={$p['layer_id']}")->find();
            $p['room'] = $roomModel->where("id={$p['room_id']}")->find();
            $p['sroom'] = $sroomModel->where("id={$p['sroom_id']}")->find();
            $p['garage'] = $garageModel->where("id={$p['garage_id']}")->find();
            $p['recommend'] = $recommenModel->where("aid={$p['recommend_id']}")->find();
            $p['occupation'] = $occupationModel->where("id={$p['occupation_id']}")->find();

            $list[] = $p;
        }
//        print_r($list); exit;

        $page = new\Think\Page($count,$pagesize);
        $show=$page->show();
        $this->assign('house',$list);
        $this->assign('page',$show);

        $this->display();

    }
    //新增跳转
    public function add() {
        $house = D('house')->where('id')->select();
        $layer = D('layer')->select();
        $room = D('room')->select();
        $sroom=D('sroom')->select();
        $garage=D('garage')->select();
        $occupation =D('occupation')->select();
        $recommend =D('recommend')->select();

        $this->assign('garage',$garage);
        $this->assign('sroom',$sroom);
        $this->assign('room', $room);
        $this->assign('layer', $layer);
        $this->assign('recommend',$recommend);
        $this->assign('occupation',$occupation);
        $this->assign('house', $house);

        $this->display('form');
    }
  //编辑跳转
    public function edit($id = 0){
        $id = intval($id);
        $houseModel = D('house');
        $layer = D('layer')->select();
        $room = D('room')->select();
        $sroom=D('sroom')->select();
        $garage=D('garage')->select();

        $item = $houseModel->where("id='$id'")->find();
        $occupation =D('occupation')->select();
        $recommend =D('recommend')->select();

        if (!$item) {
            $this->error('参数错误！');
        }

        $this->assign('garage',$garage);
        $this->assign('sroom',$sroom);
        $this->assign('room', $room);
        $this->assign('layer', $layer);
        $this->assign('recommend',$recommend);
        $this->assign('occupation',$occupation);
        $this->assign('item', $item);
        $this->display('form');
    }

    public function update($id = 0){
        $id = intval($id);

        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;
        $data['type'] = isset($_POST['type']) ? $_POST['type'] :false;
        $data['size'] = isset($_POST['size']) ? $_POST['size'] : false;
        $data['layer_id'] = isset($_POST['layer_name']) ? intval($_POST['layer_name']):0;
        $data['room_id'] = isset($_POST['room_id']) ? intval($_POST['room_id']):0;
        $data['sroom_id'] = isset($_POST['sroom_id']) ? intval($_POST['sroom_id']):0;
        $data['garage_id'] = isset($_POST['garage_id']) ? intval($_POST['garage_id']):0;
        $data['money'] = isset($_POST['money']) ? $_POST['money'] : false;
        $data['occupation_id'] = isset($_POST['occupation_id']) ? intval($_POST['occupation_id']):0;
        $data['recommend_id'] = isset($_POST['recommend_id']) ? intval($_POST['recommend_id']):0;
        $data['propose'] = isset($_POST['propose']) ? $_POST['propose'] : false;
        $data['img'] = I('post.img', '', 'strip_tags');
        $data['list'] = I('post.list', '', 'strip_tags');

        if ($id) {
            D('house')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！');
        } else {
            $id = D('house')->data($data)->add();
            if ($id) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }
    //删除
    public function del(){
        $aids = isset($_REQUEST['aids']) ? $_REQUEST['aids'] : false;
        if ($aids) {
            if (is_array($aids)) {
                $aids = implode(',', $aids);
                $map['id'] = array('in', $aids);
            } else {
                $map = 'id=' . $aids;
            }
            if (M('house')->where($map)->delete()) {
                addlog('删除内容，AID：' . $aids);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('参数错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }
}