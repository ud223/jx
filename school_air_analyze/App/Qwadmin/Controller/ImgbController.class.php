<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/11/29
 * Time: 12:40
 */

namespace Qwadmin\Controller;


class ImgbController extends ComController
{
    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;
        $map_imgModel = D('map_img');

        $pagesize = 5;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where='1 = 1 ';

        $count=$map_imgModel->where($where)->order("id desc")->count();

        $tmp_list=$map_imgModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();

        //print_r($list); exit;

        $page = new\Think\Page($count,$pagesize);
        $page=$page->show();
        $this->assign('map_img',$tmp_list);
        $this->assign('page',$page);

        $this->display();

    }

    public function add() {
        $map_img = D('map_img')->where('id ')->select();
        $this->assign('map_img', $map_img);

        $this->display('form');
    }

    public function edit($aid = 0){
        $aid = intval($aid);
        //
        $map_imgModel = D('map_img');
        $photoModel=D('photo');
        $item = $map_imgModel->where("id='$aid'")->find();

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
            D('map_img')->data($data)->where('id=' . $aid)->save();
            addlog('编辑内容，ID：' . $aid);
            $this->success('恭喜！内容编辑成功！');
        }
        else {
            $aid = D('map_img')->data($data)->add();
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