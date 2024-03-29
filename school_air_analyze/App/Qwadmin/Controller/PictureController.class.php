<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/11/22
 * Time: 12:19
 */

namespace Qwadmin\Controller;


class pictureController extends ComController
{
    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;
        $pictureModel = D('picture');

        $pagesize = 5;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where='1 = 1 ';

        $count=$pictureModel->where($where)->order("id desc")->count();

        $tmp_list=$pictureModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();

        //print_r($list); exit;

        $page = new\Think\Page($count,$pagesize);
        $page=$page->show();
        $this->assign('picture',$tmp_list);
        $this->assign('page',$page);

        $this->display();

    }

    public function add() {
        $picture = D('picture')->where('id ')->select();
        $this->assign('picture', $picture);

        $this->display('form');
    }

    public function edit($aid = 0){
        $aid = intval($aid);
        //
        $pictureModel = D('picture');
        $photoModel=D('photo');
        $item = $pictureModel->where("id='$aid'")->find();

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
            D('picture')->data($data)->where('id=' . $aid)->save();
            addlog('编辑内容，ID：' . $aid);
            $this->success('恭喜！内容编辑成功！');
        }
        else {
            $aid = D('picture')->data($data)->add();
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