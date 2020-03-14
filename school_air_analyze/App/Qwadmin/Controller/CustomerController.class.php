<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/2/002
 * Time: 11:28
 */

namespace Qwadmin\Controller;


class CustomerController extends ComController
{

    public function index( $p = 1){
        $p = intval($p) > 0 ? $p : 1;
        $customerModel=D('customer');
        $levelModel=D('level');

        $pagesize = 1;
        $offset = $pagesize * ($p - 1);
        $where = '1 = 1 ';
        $count = $customerModel->where($where)->count();
        $reco_list = $customerModel->order('id')->limit($offset . ',' . $pagesize)->select();

        $list = array();

        foreach ($reco_list as $p) {
            $p['level'] = $levelModel->where("id={$p['level_id']}")->find();

            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display();
    }

    public function del(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        if ($id) {
            if (is_array($id)){
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $result=M('customer')->where($map)->delete();

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