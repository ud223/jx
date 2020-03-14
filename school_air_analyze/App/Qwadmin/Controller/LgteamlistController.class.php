<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/11/1
 * Time: 10:11
 */

namespace Qwadmin\Controller;


class LgteamlistController extends ComController
{
    public function index($p = 1)
    {
        $p = intval($p) > 0 ? $p : 1;
        $lgteamlist_typeModel = D('lgteamlist_type');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1  and  is_delete = 0';

        $count = $lgteamlist_typeModel->where($where)->order("id desc")->count();

        $tmp_list = $lgteamlist_typeModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();


        $page = new\Think\Page($count, $pagesize);
        $page = $page->show();

        $this->assign('lgteamlist_type', $tmp_list);
        $this->assign('page', $page);

        $this->display();
    }

    public function add() {
        $lgteamlist_type = D('lgteamlist_type')->where('id')->select();

        $this->assign('lgteamlist_type', $lgteamlist_type);

        $this->display('form');
    }

    public function edit($id = 0){
        $id = intval($id);
        $lgteamlist_typeModel = D('lgteamlist_type');
        $photoModel=D('photo');
        $item = $lgteamlist_typeModel->where("id='$id'")->find();
        $photo_id = $item["img"];

        $photo = $photoModel->where("id='$photo_id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }
        $this->assign('photo', $photo);
        $this->assign('item', $item);
        $this->display('form');
    }

    public function update($id = 0){
        $id = intval($id);
        $photoModel = D('photo');

        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;
        $data['time'] = date('Y-m-d H:s:i', time());
        $img = I('post.img', '', 'strip_tags');

        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = $img;

        $result_photo = $photoModel->add($photo);
        // print_r($photo); exit;
        if (!$result_photo) {
            $this->success('图片上传失败');
        }

        $data['img'] =  $photo['id'];

        if ($id) {
            D('lgteamlist_type')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/lgteamlist/index'));
        } else {
            $id = D('lgteamlist_type')->data($data)->add();
            if ($id) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U('/qwadmin/lgteamlist/index'));
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function del(){
        $aids = isset($_REQUEST['aids']) ? $_REQUEST['aids'] : false;
        if ($aids) {
            if (is_array($aids)) {
                $aids = implode(',', $aids);
                $map['id'] = array('in', $aids);
            } else {
                $map = 'id=' . $aids;
            }

            if (M('lgteamlist_type')->where($map)->delete()) {
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