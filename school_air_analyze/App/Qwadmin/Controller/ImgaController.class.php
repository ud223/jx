<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/11/29
 * Time: 12:34
 */

namespace Qwadmin\Controller;


class ImgaController extends ComController
{
    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;
        $pictureModel = D('mail_img');

        $pagesize = 5;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where='1 = 1 ';

        $count=$pictureModel->where($where)->order("id desc")->count();

        $tmp_list=$pictureModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();

        //print_r($list); exit;

        $page = new\Think\Page($count,$pagesize);
        $page=$page->show();
        $this->assign('mail_img',$tmp_list);
        $this->assign('page',$page);

        $this->display();

    }

    public function add() {
        $mail_img = D('mail_img')->where('id ')->select();
        $this->assign('mail_img', $mail_img);

        $this->display('form');
    }

    public function edit($aid = 0){
        $aid = intval($aid);
        //
        $mail_imgModel = D('mail_img');
        $photoModel=D('photo');
        $item = $mail_imgModel->where("id='$aid'")->find();

        $photo_id = $item["name"];

        $photo = $photoModel->where("id='$photo_id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }

        $this->assign('photo',$photo);
        $this->assign('item', $item);
        $this->display('form');
    }

    public function update($aid = 0){
        $aid = intval($aid);
        $photoModel = D('photo');
        $name = I('post.name', '', 'strip_tags');

        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = $name;

        $result_photo = $photoModel->add($photo);
        // print_r($photo); exit;
        if (!$result_photo) {
            $this->success('图片上传失败');
        }

        $data['name'] =  $photo['id'];

        if ($aid) {
            D('mail_img')->data($data)->where('id=' . $aid)->save();
            addlog('编辑内容，ID：' . $aid);
            $this->success('恭喜！内容编辑成功！');
        }
        else {
            $aid = D('mail_img')->data($data)->add();
            if ($aid) {
                addlog('新增内容，ID：' . $aid);
                $this->success('恭喜！内容新增成功！');
            }
            else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

}