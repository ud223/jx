<?php
/**
 * Created by PhpStorm.
 * User: ssc
 * Date: 2018/10/10
 * Time: 10:14
 */

namespace Qwadmin\Controller;


class CommodityController extends ComController
{
    public function index($p=1){
        $p = intval($p) > 0 ? $p : 1;

        $pagesize =5;
        $offset = $pagesize * ($p - 1);

        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            $data = array();
            $data['name']=$keyword;
            $data['is_delete']=0;
        }else{
            $data = array();
            $data['is_delete']=0;
        }

        $count = D('commodity')->where($data)->count();

        $list = D('commodity')->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function update(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;
        $data['integral'] = isset($_POST['integral']) ? $_POST['integral'] : false;

        if ($id) {
            D('commodity')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！');
        } else {
            $data["id"] = build_order_no();

            $result = D('commodity')->data($data)->add();
            if ($result) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function edit($id = 0)
    {
        $id = intval($id);

        $currentcategory = D('commodity')->where("id='$id'")->find();

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

            $result =M('commodity')->where($map)->data($data)->save();

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
    public function generate($id=0){
        $id = intval($id);

        $currentcategory = D('commodity')->where("id='$id'")->find();

        if (!$currentcategory) {
            $this->error('参数错误！');
        }

        $this->assign('currentcategory', $currentcategory);
        $this->display('qrcode');
    }

}