<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/3/003
 * Time: 14:32
 */

namespace Qwadmin\Controller;


class EnterpriserecruitController extends ComController
{
    public  function index($p=1){

        $p = intval($p) > 0 ? $p : 1;

        $recruitModel = D('enterprise_recruit');
        $photoModel = D('photo');

        $userModel = D('user');

        $pagesize =5;#每页数量

        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            $data = array();
            $data['title']=$keyword;
            $data['is_delete']=0;
        }else{
            $data = array();
            $data['is_delete']=0;
        }

        $count = $recruitModel->where($data)->count();

        $recruit_list = $recruitModel->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();

        $list = array();

        foreach ($recruit_list as $p) {
            $p['photo'] = $photoModel->where("id={$p['img']}")->find();
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

        $data['title'] = isset($_POST['title']) ? $_POST['title'] : false;
        $data['phone'] = isset($_POST['phone']) ? $_POST['phone'] : false;
        $data['introduce'] = isset($_POST['introduce']) ? $_POST['introduce'] : false;
        $logo= I('post.logo', '', 'strip_tags');
        $img = I('post.img', '', 'strip_tags');

        $data_logo= array();
        $data_logo['id'] = build_order_no();
        $data_logo['img'] = $logo;
        $photo_logo=D('photo')->add($data_logo);

        if (!$photo_logo) {
            $this->success('图片上传失败');
        }
        $data_img= array();
        $data_img['id'] = build_order_no();
        $data_img['img'] = $img;
        $photo_img=D('photo')->add($data_img);

        if (!$photo_img) {
            $this->success('图片上传失败');
        }
        $data['logo'] =$data_logo['id'];
        $data['img'] =$data_img['id']
        ;
        if ($id) {
            D('enterprise_recruit')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！');
        } else {
            $data["id"] = build_order_no();

            $result = D('enterprise_recruit')->data($data)->add();
            if ($result) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function add(){
        $this->display('form');
    }

    public function edit($id = 0){
        $id = intval($id);

        $item = D('enterprise_recruit')->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }

        $logo_id = $item["logo"];
        $logo_photo = D('photo')->where("id='$logo_id'")->find();
        $item['logo_photo'] = $logo_photo;

        $img_id = $item["img"];
        $img_photo = D('photo')->where("id='$img_id'")->find();
        $item['img_photo'] = $img_photo;

        $this->assign('currentcategory',$item);
        $this->display('form');

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

            $result =D('enterprise_recruit')->data($data)->where($map)->save();

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