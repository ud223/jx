<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/14/014
 * Time: 10:27
 */

namespace Qwadmin\Controller;


class RecoController extends ComController
{

    public function add()
    {
        $occupation=D('occupation')->select();
        $this->assign('occupation',$occupation);
        $this->display('form');
    }

    public function edit($aid = 0)
    {
        $aid = intval($aid);
        $recosscc = M('recommend');
        $article = $recosscc->where("aid='$aid'")->find();

        if (!$article) {
            $this->error('参数错误！');
        }

        $occupation=M('occupation')->select();
        $this->assign('occupation',$occupation);
        $this->assign('article', $article);
        $this->display('form');
    }

    public function index( $p = 1)
    {
        $p = intval($p) > 0 ? $p : 1;
        $consultModel = D('recommend');
        $occupationModel = D('occupation');


        $pagesize = 1;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1 ';
        $count = $consultModel->where($where)->count();

//        exit($offset . ',' . $pagesize);
        $reco_list = $consultModel->order('aid')->limit($offset . ',' . $pagesize)->select();
//        print_r($newsModel->getLastSql()); exit;
        $list = array();

        foreach ($reco_list as $p) {
            $p['occupation'] = $occupationModel->where("id={$p['oc']}")->find();
            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function del(){
        $aid = isset($_REQUEST['aid']) ? $_REQUEST['aid'] : false;

        if ($aid) {
            if (is_array($aid)){
                $aids = implode(',', $aid);
                $map['aid'] = array('in', $aid);
            } else {
                $map = 'aid=' . $aid;
            }

            $result=M('recommend')->where($map)->delete();

            if ($result !== false) {
                addlog('删除内容，AID：' . $aid);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    public function update($aid = 0,$act = null){
        if ($act == 'order') {
            $aid = I('post.aid', 0, 'intval');

            if (!$aid) {
                die('0');
            }

            $o = I('post.o', 0, 'intval');
            M('consult')->data(array('o' => $o))->where("aid='{$aid}'")->save();
            die('1');
        }

        $aid = intval($aid);
        $recommend_name = isset($_POST['recommend_name']) ? $_POST['recommend_name'] : false;
        $oc =isset($_POST['oc']) ? intval($_POST['oc']):0;
        $url = I('post.url', '', 'strip_tags');

        if(!$recommend_name){
            echo ajax_api(true,'100','推荐人姓名不能为空',false);exit;
        }
        if(!$url){
            echo ajax_api(true,'100','推荐人图片不能为空',false);exit;
        }

//        $data=array();
//        $aid = intval($aid);
//        $data['recommend_name'] = isset($_POST['recommend_name']) ? $_POST['recommend_name'] : false;
//        $data['propose'] = isset($_POST['propose']) ? $_POST['propose'] : false;
//        $data['url'] = I('post.url', '', 'strip_tags');
//        $data['oc'] = isset($_POST['oc']) ? intval($_POST['oc']):0;

        $data = array();
        $data['aid']=$aid;
        $data['recommend_name'] = $recommend_name;
        $data['oc'] = $oc;
        $data['url'] = $url;

        if ($aid)
        {
            $recommend = M('recommend')->data($data)->where('aid=' . $aid)->save();
            if ($recommend !== false) {
                echo ajax_api(true,200,'编辑成功');
            } else {
                echo ajax_api(true,200,'编辑失败','');
            }
        } else {
            $aid = M('recommend')->add($data);
            if ($aid !== false) {
                echo ajax_api(true,200,'添加成功');
            } else {
                echo ajax_api(true,200,'添加失败','');
            }
        }
    }
}