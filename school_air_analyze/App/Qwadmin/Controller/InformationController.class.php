<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12/012
 * Time: 10:13
 */

namespace Qwadmin\Controller;


class InformationController extends ComController
{
    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;

        $pagesize = 20;#每页数量
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

        $count = D('information')->where($data)->count();

        $list =  D('information')->order('time desc')->limit($offset . ',' . $pagesize)->where($data)->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }
    public function update(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $data['title'] = isset($_POST['title']) ? $_POST['title'] : false;
        $data['introduce'] = isset($_POST['introduce']) ? $_POST['introduce'] : false;
        $data['detailed_introduce'] = isset($_POST['detailed_introduce']) ? $_POST['detailed_introduce'] : false;
        $data['time']=date('Y-m-d H:s:i', time());
        $img = I('post.img', '', 'strip_tags');
        $data['steta'] = 0;

        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = $img;

        $result_photo = D('photo')->add($photo);

        if (!$result_photo) {
            $this->success('图片上传失败');
        }

        $data['img'] =  $photo['id'];

        if ($id) {
            D('information')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/information/index'));
        } else {
            $data["id"] = build_order_no();

            $result = D('information')->data($data)->add();
            if ($result) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U('/qwadmin/information/index'));
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function edit($id = 0){
        $id = intval($id);

        $item = D('information')->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }
        $photo_id = $item["img"];
        $photo = D('photo')->where("id='$photo_id'")->find();

        $item['logo_photo'] = $photo;

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

            $result =D('information')->data($data)->where($map)->save();

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

    public function fz($id=0){
        $id = intval($id);

        $informationModel = D('information');
        $currentcategory = $informationModel->where("id='$id'")->find();

        if (!$currentcategory) {
            $this->error('参数错误！');
        }

        $QR= $currentcategory['id'];
        $currentcategory['id'] = "	https://lgcommunity.webetter100.com/home/Information/index/id/$QR.html";

        $this->assign('currentcategory', $currentcategory);
        $this->display('ssc');
    }
}