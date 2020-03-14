<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/20/020
 * Time: 18:00
 */

namespace Qwadmin\Controller;


class UserinformationController extends ComController
{
    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;

        $userModel = D('user');
        $photoModel = D('photo');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            $data = array();
            $data[$field] = array('like', '%'.$keyword.'%');
            $data['is_delete'] = '0';
            $data['is_disable'] = '0';

        }else{
            $data = array();
            $data['is_delete'] = '0';
            $data['is_disable'] = '0';
        }

        $where='1 = 1 and is_delete = 0';
        $count = $userModel->where($where)->count();


        $user_list = $userModel->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();

        $list = array();

        foreach ($user_list as $p) {
            $p['photo'] = $photoModel->where("id={$p['img']}")->find();
            $p['$image_arr']=explode("|",$p['photo']['img']);
            $list[] = $p;

        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('count', $count);
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

            $result =M('user')->data($data)->where($map)->save();

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

    public function add()
    {
        $sex=D('sex')->select();
        $this->assign('sex',$sex);
        $this->display('form');
    }

    public function update(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;
        $data['account_number'] = isset($_POST['account_number']) ? $_POST['account_number'] : false;
        $data['id_number'] = isset($_POST['id_number']) ? $_POST['id_number'] : false;
//        $data['u_sex'] = isset($_POST['u_sex']) ? intval($_POST['u_sex']) : 0;
//        $data['nativeplace'] = isset($_POST['nativeplace']) ? $_POST['nativeplace'] : false;
//        $data['nation'] = isset($_POST['nation']) ? $_POST['nation'] : false;
//        $data['birth'] = isset($_POST['birth']) ? $_POST['birth'] : false;
//        $data['address'] = isset($_POST['address']) ? $_POST['address'] : false;
//        $data['id_card_no'] = isset($_POST['id_card_no']) ? $_POST['id_card_no'] : false;
//        $data['account_number'] = isset($_POST['account_number']) ? $_POST['account_number'] : false;
//        $data['password'] = isset($_POST['name']) ? $_POST['password'] : false;
        $data['phone'] = isset($_POST['phone']) ? $_POST['phone'] : false;
//        $img = I('post.img', '', 'strip_tags');

//        $photo = array();
//        $photo['id'] = build_order_no();
//        $photo['img'] = $img;
//        $result_photo = D('photo')->add($photo);
//
//        if (!$result_photo) {
//            $this->success('图片上传失败');
//        }
//
//        $data['img'] =  $photo['id'];

        if ($id) {
            D('user')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/userinformation/index'));
        } else {
//            $data["id"] = build_order_no();
//
//            $result = D('user')->data($data)->add();
//            if ($result) {
//                addlog('新增内容，ID：' . $id);
//                $this->success('恭喜！内容新增成功！');
//            } else {
                $this->error('抱歉，未知错误！');
//            }
        }
    }

    public function edit($id = 0)
    {
        $id = intval($id);
        $userModel = D('user');

        $item = $userModel->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }
        $photo_id = $item["img"];
        $photo = D('photo')->where("id='$photo_id'")->find();

        $item['logo_photo'] = $photo;

        $sex=D('sex')->select();
        $this->assign('sex',$sex);
        $this->assign('currentcategory',$item);
        $this->display('form');
    }

    public function disable(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $data['is_disable'] ='1';

        if ($id) {
            D('user')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！用户禁用成功！');
        } else {
                $this->error('抱歉，未知错误！');
        }
    }
}