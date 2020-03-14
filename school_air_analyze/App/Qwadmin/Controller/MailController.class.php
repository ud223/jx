<?php
/**
 * Created by PhpStorm.
 * User: ssc
 * Date: 2018/10/18
 * Time: 11:26
 */

namespace Qwadmin\Controller;


class MailController extends ComController
{

    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;

        $pagesize =5;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            $data = array();
            $data['title']= array('like', '%'.$keyword.'%');
            $data['is_delete']=0;
        }else{
            $data = array();
            $data['is_delete']=0;
        }

        $count = D('mail')->where($data)->count();

        $list = D('mail')->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();

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

            $result =M('mail')->data($data)->where($map)->save();

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
    public function edit($id = 0)
    {
        $id = intval($id);

        $item = D('mail')->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }
        $this->assign('currentcategory',$item);
        $this->display('form');
    }
    public function update(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $data['title'] = isset($_POST['title']) ? $_POST['title'] : false;
        $data['content'] = isset($_POST['content']) ? $_POST['content'] : false;

        if ($id) {
            D('mail')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/mail/index'));
        } else {
            $data["id"] = build_order_no();
            $data["time"] = date('Y-m-d H:i:s');
            $result = D('mail')->data($data)->add();
            if ($result) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U('/qwadmin/mail/index'));
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }
}