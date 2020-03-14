<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/10/30
 * Time: 14:22
 */

namespace Qwadmin\Controller;


class UniversityController extends ComController
{
    public function index($p = 1) {
        $p = intval($p) > 0 ? $p : 1;
        $universityModel = D('university');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1 and is_delete = 0';

        $count = $universityModel->where($where)->count();

        $tmp_list = $universityModel->where($where)->order('time desc')->limit($offset . ',' . $pagesize)->select();


        $page = new\Think\Page($count,$pagesize);
        $show = $page->show();
        $this->assign('university',$tmp_list);
        $this->assign('page',$show);

        $this->display();
    }

    public function add() {
        $university = D('university')->where('id')->select();

        $this->assign('university', $university);

        $this->display('form');
    }

    public function edit($id = 0){
        $id = intval($id);
        $universityModel = D('university');
        $photoModel = D('photo');

        $item = $universityModel->where("id='$id'")->find();
        $photo_id = $item["img"];

        $photo = $photoModel->where("id='$photo_id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }
        $this->assign('photo',$photo);
        $this->assign('item', $item);
        $this->display('form');
    }

    public function update($id = 0){
        $id = intval($id);
        $photoModel = D('photo');
        // print_r($aid); exit;
        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;
        $data['describe'] = isset($_POST['describe']) ? $_POST['describe'] : false;
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
            D('university')->data($data)->where('id = ' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/university/index'));
        }
        else {
            $id = D('university')->data($data)->add();
            if ($id) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U('/qwadmin/university/index'));
            }
            else {
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

            $result = M('university')->where($map)->data($data)->save();

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