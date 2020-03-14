<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/15/015
 * Time: 10:28
 */

namespace Qwadmin\Controller;


class ConsuController extends ComController
{
    public function index($p = 1)
    {        $p = intval($p) > 0 ? $p : 1;

        $newsModel = D('consult');

        $pagesize = 8;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1 ';
        $count = $newsModel->where($where)->count();

        $reco_list = $newsModel->order('id')->limit($offset . ',' . $pagesize)->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $reco_list);
        $this->assign('page', $page);
        $this->display();

    }
    public function del()
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }
            if (M('consult')->where($map)->delete()) {
                addlog('删除内容，AID：' . $id);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('参数错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }
}