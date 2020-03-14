<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2019/4/12
 * Time: 11:08
 */

namespace Qwadmin\Controller;


class TexamineController extends ComController{
    public function activity_index($p=1){
        $active_stateModel=D('active_state');
        $integral_stateModel=D('integral_state');
        $active_ynModel = D('active_yn');
        $p = intval($p) > 0 ? $p : 1;

        $pagesize = 20;
        $offset = $pagesize * ($p - 1);

        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            $data = array();
            $data['title']= array('like', '%'.$keyword.'%');;
            $data['is_delete']=0;
            $data['active_state1']=0;

        }else{
            $data = array();
            $data['is_delete']=0;
            $data['active_state1']=0;

        }

        $count = D('community_activity')->where($data)->count();

        $tmp_list = D('community_activity')->order('id desc')->limit($offset . ',' . $pagesize)->where($data)->select();

        $list = array();

        foreach ($tmp_list as $p) {
            $p['active_state'] = $active_stateModel->where("id={$p['active_state']}")->find();
            $p['integral_state'] = $integral_stateModel->where("id={$p['integral_state']}")->find();
            $p['scene'] = $active_ynModel->where("id={$p['scene']}")->find();

            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function edit($id = 0){
        $id = intval($id);
        $item = D('community_activity')->where("id='$id'")->find();
        $active_stateModel = D('active_state');
        $integral_stateModel = D('integral_state');
        $active_ynModel = D('active_yn');

        if (!$item) {
            $this->error('参数错误！');
        }
        $photo_id = $item["logo"];
        $photo = D('photo')->where("id='$photo_id'")->find();
        $item['logo_photo'] = $photo['img'];

        $group_pictures = $item["group_pictures"];
        $photo1 = D('photo')->where("id='$group_pictures'")->find();
        $item["group_pictures"] = $photo1["img"];

        $active_state_id = $item['active_state'];
        $active_state = $active_stateModel ->where("id = $active_state_id ")->find();
        $item['active_state'] = $active_state['name'];

        $integral_state_id = $item['integral_state'];
        $integral_state = $integral_stateModel ->where("id = $integral_state_id ")->find();
        $item['integral_state'] = $integral_state['name'];

        $this->assign('currentcategory',$item);
        $this->display('activity_form');
    }

    public function del(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();
            $data['active_state1'] = '1';


            $result =M('community_activity')->data($data)->where($map)->save();

            if ($result !== false) {
                addlog('审核内容，AID：' . $id);
                $this->success('恭喜，活动审核成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    public function information_index($p = 1){
        $p = intval($p) > 0 ? $p : 1;

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            $data = array();
            $data['title']= array('like', '%'.$keyword.'%');
            $data['is_delete']=0;
            $data['steta']=0;
        }else{
            $data = array();
            $data['is_delete']=0;
            $data['steta']=0;
        }

        $count = D('information')->where($data)->count();

        $list =  D('information')->order('time desc')->limit($offset . ',' . $pagesize)->where($data)->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function information_edit($id = 0){
        $id = intval($id);

        $item = D('information')->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }
        $photo_id = $item["img"];
        $photo = D('photo')->where("id='$photo_id'")->find();

        $item['logo_photo'] = $photo['img'];

        $this->assign('currentcategory',$item);
        $this->display('information_form');
    }

    public function del1(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();
            $data['steta'] = '1';


            $result =M('information')->data($data)->where($map)->save();

            if ($result !== false) {
                addlog('审核内容，AID：' . $id);
                $this->success('恭喜，审核成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    public function partymember_index($p = 1){
        $p = intval($p) > 0 ? $p : 1;
        $community_partymemberModel = D('community_partymember');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1  and  is_delete = 0 and steta = 0 ';

        $count = $community_partymemberModel->where($where)->order("time desc")->count();

        $tmp_list = $community_partymemberModel->where($where)->order("time desc")->limit($offset . ',' . $pagesize)->select();

        $page = new\Think\Page($count, $pagesize);
        $page = $page->show();

        $this->assign('community_partymember', $tmp_list);
        $this->assign('page', $page);

        $this->display();
    }

    public function partymember_edit($id = 0){
        $id = intval($id);
        $community_partymemberModel = D('community_partymember');
        $photoModel=D('photo');
        $item = $community_partymemberModel->where("id='$id'")->find();
        $photo_id = $item["img"];

        $photo = $photoModel->where("id='$photo_id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }
        $this->assign('photo', $photo);
        $this->assign('item', $item);
        $this->display('partymember_form');
    }

    public function del2(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $community_partymemberModel = D('community_partymember');
        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();
            $data['steta'] = '1';


            $result =$community_partymemberModel->data($data)->where($map)->save();

            if ($result !== false) {
                addlog('审核内容，AID：' . $id);
                $this->success('恭喜，审核成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

}