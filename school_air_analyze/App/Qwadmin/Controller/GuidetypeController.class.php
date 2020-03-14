<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/11/30
 * Time: 10:32
 */

namespace Qwadmin\Controller;


class GuidetypeController extends ComController
{
    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;
        $guide_typeModel = D('guide_type');

        $pagesize = 5;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where='1 = 1 ';

        $count=$guide_typeModel->where($where)->order("id desc")->count();

        $tmp_list=$guide_typeModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();

        //print_r($list); exit;

        $page = new\Think\Page($count,$pagesize);
        $page=$page->show();
        $this->assign('guide_type',$tmp_list);
        $this->assign('page',$page);

        $this->display();

    }

    public function add() {
        $guide_type = D('guide_type')->where('id ')->select();
        $this->assign('guide_type', $guide_type);

        $this->display('form');
    }

    public function edit($aid = 0){
        $aid = intval($aid);
        //
        $guide_typeModel = D('guide_type');

        $item = $guide_typeModel->where("id='$aid'")->find();


        if (!$item) {
            $this->error('参数错误！');
        }


        $this->assign('item', $item);
        $this->display('form');
    }

    public function update($aid = 0){
        $aid = intval($aid);
        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;

        if ($aid) {
            D('guide_type')->data($data)->where('id=' . $aid)->save();
            addlog('编辑内容，ID：' . $aid);
            $this->success('恭喜！内容编辑成功！');
        }
        else {
            $aid = D('guide_type')->data($data)->add();
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