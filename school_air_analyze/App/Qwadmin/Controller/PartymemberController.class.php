<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/11/5
 * Time: 11:29
 */

namespace Qwadmin\Controller;


class PartymemberController extends ComController
{
    public function index($p = 1)
    {
        $p = intval($p) > 0 ? $p : 1;
        $community_partymemberModel = D('community_partymember');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1  and  is_delete = 0';

        $count = $community_partymemberModel->where($where)->order("time desc")->count();

        $tmp_list = $community_partymemberModel->where($where)->order("time desc")->limit($offset . ',' . $pagesize)->select();

        $page = new\Think\Page($count, $pagesize);
        $page = $page->show();

        $this->assign('community_partymember', $tmp_list);
        $this->assign('page', $page);

        $this->display();
    }

    public function add() {
        $community_partymember = D('community_partymember')->where('id')->select();

        $this->assign('community_partymember', $community_partymember);

        $this->display('form');
    }

    public function edit($id = 0){
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
        $this->display('form');
    }

    public function update($id = 0){
        $id = intval($id);
        $photoModel = D('photo');

        $data['title'] = isset($_POST['title']) ? $_POST['title'] : false;
        $data['subtitle'] = isset($_POST['subtitle']) ? $_POST['subtitle'] : false;
        $data['content'] = isset($_POST['content']) ? $_POST['content'] : false;
        $data['time'] = date('Y-m-d H:s:i', time());
        $img = I('post.img', '', 'strip_tags');
        $data['steta'] = 0;

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
            D('community_partymember')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/partymember/index'));
        } else {
            $id = D('community_partymember')->data($data)->add();
            if ($id) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U('/qwadmin/partymember/index'));
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function del()
    {
        $aids = isset($_REQUEST['aids']) ? $_REQUEST['aids'] : false;

        if ($aids) {
            if (is_array($aids)) {
                $aids = implode(',', $aids);
                $map['id'] = array('in', $aids);
            } else {
                $map = 'id=' . $aids;
            }

            $data['is_delete'] = 1;

            $result = M('community_partymember')->where($map)->data($data)->save();

            if ($result !== false) {
                addlog('删除内容，AID：' . $aids);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    public function fz($id=0){
        $id = intval($id);

        $partymemberModel = D('community_partymember');
        $currentcategory = $partymemberModel->where("id='$id'")->find();

        if (!$currentcategory) {
            $this->error('参数错误！');
        }

        $QR= $currentcategory['id'];
        $currentcategory['id'] = "	https://lgcommunity.webetter100.com/home/partymember/index/id/$QR.html";

        $this->assign('currentcategory', $currentcategory);
        $this->display('ssc');
    }


}