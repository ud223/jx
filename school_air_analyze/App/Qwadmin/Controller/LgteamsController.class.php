<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/11/1
 * Time: 10:34
 */

namespace Qwadmin\Controller;


class LgteamsController extends ComController
{
    public function index($p = 1)
    {
        $p = intval($p) > 0 ? $p : 1;
        $lgteamsModel = D('lgteams');
        $lgteamlist_typeModel = D('lgteamlist_type');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1  and  is_delete = 0';

        $count = $lgteamsModel->where($where)->order("id desc")->count();

        $tmp_list = $lgteamsModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();

        $list = array();

        foreach ($tmp_list as $p) {
            $p['type'] = $lgteamlist_typeModel->where("id={$p['type']}")->find();
            $list[] = $p;
        }
        $page = new\Think\Page($count, $pagesize);
        $page = $page->show();

        $this->assign('lgteams', $list);
        $this->assign('page', $page);

        $this->display();
    }

    public function add() {
        $lgteams = D('lgteams')->where('id')->select();
        $lgteamlist_type = D('lgteamlist_type')->select();

        $this->assign('lgteamlist_type',$lgteamlist_type);
        $this->assign('lgteams', $lgteams);

        $this->display('form');
    }

    public function edit($id = 0){
        $id = intval($id);
        $lgteamsModel = D('lgteams');
        $photoModel=D('photo');
        $item = $lgteamsModel->where("id='$id'")->find();
        $lgteamlist_type = D('lgteamlist_type')->select();
        $photo_id = $item["img"];

        $photo = $photoModel->where("id='$photo_id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }
        $this->assign('lgteamlist_type', $lgteamlist_type);
        $this->assign('photo', $photo);
        $this->assign('item', $item);
        $this->display('form');
    }
    public function update($id = 0){
        $id = intval($id);
        $photoModel = D('photo');

        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;
        $data['name_vice'] = isset($_POST['name_vice']) ? $_POST['name_vice'] : false;
        $data['content'] = isset($_POST['content']) ? $_POST['content'] : false;
        $data['type'] = isset($_POST['type']) ? intval($_POST['type']):0;
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
            D('lgteams')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/lgteams/index'));
        } else {
            $id = D('lgteams')->data($data)->add();
            if ($id) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U('/qwadmin/lgteams/index'));
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function del(){
        $id = isset($_REQUEST['aids']) ? $_REQUEST['aids'] : false;

        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();

            $data['is_delete'] = '1';

            $result =D('lgteams')->data($data)->where($map)->save();

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
}