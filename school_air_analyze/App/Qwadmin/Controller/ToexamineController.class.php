<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27/027
 * Time: 16:26
 */

namespace Qwadmin\Controller;


class ToexamineController extends ComController
{
    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;

        $proposalModel = D('proposal');
        $categoryModel = D('proposal_category');
        $resultModel = D('proposal_result');
        $userModel = D('user');
        $photoModel = D('photo');

        $pagesize =5;#每页数量

        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $data = array();
        $data['result']='2';
        $data['is_delete']='0';

        $count = D('proposal')->where($data)->count();

        $proposal_list =  D('proposal')->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();


        $list = array();

        foreach ($proposal_list as $p) {
            $openid=$p['openid'];
            $p['user'] = $userModel->where("openid='$openid'")->find();

            $p['proposal_category'] = $categoryModel->where("id={$p['category']}")->find();

            $p['proposal_result'] = $resultModel->where("id={$p['result']}")->find();
            $photo=$p['photo'];
            $p['photo'] = $photoModel->where("id='$photo'")->find();

            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
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

            $result =M('proposal')->data($data)->where($map)->save();

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
        $result = isset($_POST['result']) ? $_POST['result'] : false;
        $describe = isset($_POST['describe']) ? $_POST['describe'] : false;
        $img = I('post.img', '', 'strip_tags');

        $photo = array();
        $photo['id'] = build_order_no();


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
}