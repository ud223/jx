<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21/021
 * Time: 9:52
 */

namespace Qwadmin\Controller;


class HomemanagerController extends ComController
{
    public function index($p = 1){

        $p = intval($p) > 0 ? $p : 1;

        $imgModel = D('homepage');
        $sortModel = D('sort');
        $typeModel = D('type');
        $photoModel = D('photo');

        $pagesize =5;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {

            $where = " name like '%{$keyword}%'";
        }

        $data = array();

        $data['is_delete'] = '0';

        $count = $imgModel->where($where)->where($data)->count();


        $img_list =D('homepage')->order('orde')->limit($offset . ',' . $pagesize)->where($where)->where($data)->select();

        $list = array();

        foreach ($img_list as $p) {
            $p['sort'] = $sortModel->where("id={$p['sort']}")->find();
            $p['type'] = $typeModel->where("id={$p['type']}")->find();
            $p['photo'] = $photoModel->where("id={$p['photo']}")->find();
            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function update(){

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $urc = isset($_POST['urc']) ? $_POST['urc'] : false;
        $type = isset($_POST['type']) ? $_POST['type'] : false;
        $sort = isset($_POST['sort']) ? $_POST['sort'] : false;
        $img= I('post.img', '', 'strip_tags');
        $order = isset($_POST['order']) ? $_POST['order'] : false;

        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = $img;

        $result_photo = D('photo')->add($photo);

        if (!$result_photo) {
            $this->success('图片上传失败');
        }

        $data = array();
        $data['name'] = $name;
        $data['urc'] = $urc;
        $data['type'] = $type;
        $data['sort'] = $sort;
        $data['photo'] =  $photo['id'];
        $data['order'] =  $order;
//        print_r($type); exit;
        if ($id) {
            D('homepage')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/homemanager/index'));
        } else {
            $data['id'] = build_order_no();
            $result = D('homepage')->add($data);

            if ($result) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U('/qwadmin/homemanager/index'));
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function add()
    {
        $type=D('type')->select();
        $sort=D('sort')->select();
        $this->assign('type',$type);
        $this->assign('sort',$sort);
        $this->display('form');
    }

    public function edit($id = 0)
    {
        $id = intval($id);
        $recosscc = M('homepage');
        $item = $recosscc->where("id='$id'")->find();


        if (!$item) {
            $this->error('参数错误！');
        }

        $photo_id = $item["photo"];

        $photo = D('photo')->where("id='$photo_id'")->find();

        $item['logo_photo'] = $photo;

        $type=D('type')->select();
        $sort=D('sort')->select();

        $this->assign('type',$type);
        $this->assign('sort',$sort);
        $this->assign('currentcategory', $item);
        $this->display('form');
    }

    public function del(){
        $aids = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        if ($aids) {
            if (is_array($aids)) {
                $aids = implode(',', $aids);
                $map['id'] = array('in', $aids);
            } else {
                $map = 'id=' . $aids;
            }
            if (M('homepage')->where($map)->delete()) {
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