<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/14/014
 * Time: 10:32
 */

namespace Qwadmin\Controller;


class MaillistController extends ComController
{
    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;

        $mailModel = D('mail_list');

        $pagesize = 20;#每页数量

        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            $data = array();
            $data['name']= array('like', '%'.$keyword.'%');
            $data['is_delete']=0;
        }else{
            $data = array();
            $data['is_delete']=0;
        }

        $count = $mailModel->count();

        $list = $mailModel->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function update(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;
        $data['address'] = isset($_POST['address']) ? $_POST['address'] : false;
        $data['phone'] = isset($_POST['phone']) ? $_POST['phone'] : false;
        $data['longitude'] = isset($_POST['longitude']) ? $_POST['longitude'] : false;
        $data['latitude'] = isset($_POST['latitude']) ? $_POST['latitude'] : false;

        if ($id) {
            D('mail_list')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/maillist/index'));
        } else {
            $result = D('mail_list')->data($data)->add();
            if ($result) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U('/qwadmin/maillist/index'));
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function edit($id = 0)
    {
        $id = intval($id);
        $recosscc = M('mail_list');
        $currentcategory = $recosscc->where("id='$id'")->find();

        if (!$currentcategory) {
            $this->error('参数错误！');
        }

        $this->assign('currentcategory', $currentcategory);
        $this->display('form');
    }

    public function del(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        if ($id) {
            if (is_array($id)) {
                $ids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();

            $data['is_delete'] = '1';

            $result =M('mail_list')->where($map)->data($data)->save();

            if ($result !== false) {
                addlog('删除内容，AID：' . $id);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        }else {
            $this->error('参数错误！');
        }
    }

}