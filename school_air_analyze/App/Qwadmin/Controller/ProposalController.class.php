<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/14/014
 * Time: 16:05
 */

namespace Qwadmin\Controller;


class ProposalController extends ComController
{
    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;

        $categoryModel = D('proposal_category');

        $pagesize =5;#每页数量

        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $keyword =isset($_POST['$keyword']) ? $_POST['$keyword'] : false;

        $data = array();
        $data['result']='1';
        $data['is_delete']='0';

        $count = D('proposal')->where($data)->count();

        $proposal_list =  D('proposal')->order('propose_time desc')->limit($offset . ',' . $pagesize)->where($data)->select();

        $list = array();

        foreach ($proposal_list as $p) {
            $openid=$p['openid'];
            $p['user'] = D('user')->where("openid='$openid'")->find();
            $p['proposal_category'] = $categoryModel->where("id={$p['category']}")->find();
            $p['result'] = D('proposal_result')->where("id={$p['result']}")->find();
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

        $recosscc = D('proposal');

        $item = $recosscc->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }

        $result=D('proposal_result')->select();

        $this->assign('currentcategory',$item);
        $this->assign('proposal_result',$result);
        $this->display('form');
    }

    public function update(){

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $name = isset($_POST['name']) ? $_POST['name'] : false;

        $data = array();
        $data['name'] = $name;
        $data['result'] = '2';

        if($id){
            D('proposal')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！');
        }else {
            $this->error('抱歉，未知错误！');
        }
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

            $data['is_delete'] = '1';

            $result =D('proposal')->data($data)->where($map)->save();

            if ($result !== false) {
                addlog('删除内容，AID：' . $id);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    public function handle($p=1){
        $p = intval($p) > 0 ? $p : 1;

        $categoryModel = D('proposal_category');

        $pagesize =5;#每页数量

        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $keyword =isset($_POST['$keyword']) ? $_POST['$keyword'] : false;

        $data = array();
        $data['result']='2';
        $data['is_delete']='0';

        $count = D('proposal')->where($data)->count();

        $proposal_list =  D('proposal')->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();

        $list = array();

        foreach ($proposal_list as $p) {
            $openid=$p['openid'];
            $p['user'] = D('user')->where("openid='$openid'")->find();
            $p['proposal_category'] = $categoryModel->where("id={$p['category']}")->find();
            $p['result'] = D('proposal_result')->where("id={$p['result']}")->find();
            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }
    public function handle_edit($id = 0){
        $id = intval($id);

        $recosscc = D('proposal');

        $item = $recosscc->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }

        $result=D('proposal_result')->select();

        $this->assign('currentcategory',$item);
        $this->assign('proposal_result',$result);
        $this->display('handle_form');
    }
    public function handle_update(){

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $describe = isset($_POST['describe']) ? $_POST['describe'] : false;

        $data = array();
        $data['describe'] = $describe;
        $data['result'] = '4';

        if($id){
            D('proposal')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！');
        }else {
            $this->error('抱歉，未知错误！');
        }
    }

    public function inspect($p=1){
        $p = intval($p) > 0 ? $p : 1;

        $categoryModel = D('proposal_category');

        $pagesize =5;#每页数量

        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $keyword =isset($_POST['$keyword']) ? $_POST['$keyword'] : false;

        $data = array();
        $data['result']='4';
        $data['is_delete']='0';

        $count = D('proposal')->where($data)->count();

        $proposal_list =  D('proposal')->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();

        $list = array();

        foreach ($proposal_list as $p) {
            $openid=$p['openid'];
            $p['user'] = D('user')->where("openid='$openid'")->find();
            $p['proposal_category'] = $categoryModel->where("id={$p['category']}")->find();
            $p['result'] = D('proposal_result')->where("id={$p['result']}")->find();
            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }
    public function information_edit($id = 0){
        $id = intval($id);

        $item =  D('proposal')->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }

        $category_id = $item["category"];
        $category = D('proposal_category')->where("id='$category_id'")->find();
        $item['category_item'] = $category;

        $openid_id = $item["openid"];
        $user = D('user')->where("openid='$openid_id'")->find();
        $item['user_item'] = $user;

        $img = $item["img"];
        $imgs=explode(',',$img);

        $where='';
        foreach ($imgs as $i) {
            if($where!=''){
                $where=$where.','."'$i'";
            }else{
                $where=$where."'$i'";
            }
        }

        $img_list = D('photo')->where("id in ($where)")->select();

        $this->assign('currentcategory',$item);
        $this->assign('consult',$img_list);
        $this->display('information');
    }
    public function modify_edit($id = 0){
        $id = intval($id);

        $item = D('proposal')->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }

        $result=D('proposal_result')->select();

        $this->assign('currentcategory',$item);
        $this->assign('proposal_result',$result);
        $this->display('modify');
    }

    public function modify_update(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $result = isset($_POST['result']) ? $_POST['result'] : false;
        $describe = isset($_POST['describe']) ? $_POST['describe'] : false;

        $data = array();
        $data['name'] = $name;
        $data['result'] = $result;
        $data['describe'] = $describe;

        if($id){
            D('proposal')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！');
        }else {
            $this->error('抱歉，未知错误！');
        }
    }
    public function fail($p=1){
        $p = intval($p) > 0 ? $p : 1;

        $categoryModel = D('proposal_category');

        $pagesize =5;#每页数量

        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $keyword =isset($_POST['$keyword']) ? $_POST['$keyword'] : false;

        $data = array();
        $data['result']='3';
        $data['is_delete']='0';

        $count = D('proposal')->where($data)->count();

        $proposal_list =  D('proposal')->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();

        $list = array();

        foreach ($proposal_list as $p) {
            $openid=$p['openid'];
            $p['user'] = D('user')->where("openid='$openid'")->find();
            $p['proposal_category'] = $categoryModel->where("id={$p['category']}")->find();
            $p['result'] = D('proposal_result')->where("id={$p['result']}")->find();
            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function fail_edit($id = 0){
        $id = intval($id);

        $recosscc = D('proposal');

        $item = $recosscc->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }

        $result=D('proposal_result')->select();

        $this->assign('currentcategory',$item);
        $this->assign('proposal_result',$result);
        $this->display('fail_form');
    }
    public function fail_update(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $describe = isset($_POST['describe']) ? $_POST['describe'] : false;

        $data = array();
        $data['name'] = $name;
        $data['describe'] = $describe;
        $data['result'] = '3';

        if($id){
            D('proposal')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！');
        }else {
            $this->error('抱歉，未知错误！');
        }
    }

    public function again_update(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $name = isset($_POST['name']) ? $_POST['name'] : false;

        $data = array();
        $data['name'] = $name;
        $data['result'] = '2';

        if($id){
            D('proposal')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！');
        }else {
            $this->error('抱歉，未知错误！');
        }
    }

    public function again_edit($id = 0){
        $id = intval($id);

        $item = D('proposal')->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }

        $this->assign('currentcategory',$item);
        $this->display('again');
    }
}