<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/11/1
 * Time: 13:01
 */

namespace Qwadmin\Controller;


class AixinqiyeController extends ComController{
    public function index($p = 1)
    {
        $p = intval($p) > 0 ? $p : 1;
        $aixinqiyesModel = D('aixinqiye');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1  and  is_delete = 0';

        $count = $aixinqiyesModel->where($where)->order("id desc")->count();

        $tmp_list = $aixinqiyesModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();

        $page = new\Think\Page($count, $pagesize);
        $page = $page->show();

        $this->assign('aixinqiye', $tmp_list);
        $this->assign('page', $page);

        $this->display();
    }

    public function add() {
        $aixinqiye = D('aixinqiye')->where('id')->select();

        $this->assign('aixinqiye', $aixinqiye);

        $this->display('form');
    }

    public function edit($id = 0){
        $id = intval($id);
        $aixinqiyeModel = D('aixinqiye');
        $photoModel=D('photo');
        $item = $aixinqiyeModel->where("id='$id'")->find();

        $photo_id = $item["img"];
        $logo_id = $item["logo"];



        $photo = $photoModel->where("id='$photo_id'")->find();
        $logo = $photoModel->where("id='$logo_id'")->find();

        //print_r($logo); exit;


        if (!$item) {
            $this->error('参数错误！');
        }

        $this->assign('logo', $logo);
        $this->assign('photo', $photo);
        $this->assign('item', $item);
        $this->display('form');
    }

    public function update($id = 0){
        $id = intval($id);
        $photoModel = D('photo');

        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;
        $data['name_vice'] = isset($_POST['name_vice']) ? $_POST['name_vice'] : false;
        $data['address'] = isset($_POST['address']) ? $_POST['address'] : false;
        $data['phone'] = isset($_POST['phone']) ? $_POST['phone'] : false;
        $data['content'] = isset($_POST['content']) ? $_POST['content'] : false;
        $data['longitude'] = isset($_POST['longitude']) ? $_POST['longitude'] : false;
        $data['latitude'] = isset($_POST['latitude']) ? $_POST['latitude'] : false;
        $data['time'] = date('Y-m-d H:s:i', time());
        $img = I('post.img', '', 'strip_tags');
        $logo = I('post.logo', '', 'strip_tags');

        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = $img;

        $result_photo = $photoModel->add($photo);
        // print_r($photo); exit;
        if (!$result_photo) {
            $this->success('图片上传失败');
        }

        $photo1 = array();
        $photo1['id'] = build_order_no();
        $photo1['img'] = $logo;

        $logo_photo = $photoModel->add($photo1);
        // print_r($photo); exit;
        if (!$logo_photo) {
            $this->success('图片上传失败');
        }

        $data['logo'] =  $photo1['id'];
        $data['img'] =  $photo['id'];

        if ($id) {
            D('aixinqiye')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/aixinqiye/index'));
        } else {
            $id = D('aixinqiye')->data($data)->add();
            if ($id) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U('/qwadmin/aixinqiye/index'));
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

            $result = M('aixinqiye')->where($map)->data($data)->save();

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




}