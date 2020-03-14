<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2019/3/4
 * Time: 10:34
 */

namespace Qwadmin\Controller;


class IntegralController extends ComController{
    public function index($p = 1) {

        $keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : false;

        $p = intval($p) > 0 ? $p : 1;
        $userModel = D('user');
        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $where = array();
        $where['name']= array('like', '%'.$keyword.'%');

        $count=$userModel->where($where)->count();

        $tmp_list=$userModel->where($where)->order('id desc')->limit($offset . ',' . $pagesize)->select();

//       print_r($tmp_list); exit;
        $page = new\Think\Page($count,$pagesize);
        $show=$page->show();
        $this->assign('user',$tmp_list);
        $this->assign('page',$show);

        $this->display();

    }

    public function edit($id = 0){
        $id = intval($id);
        $userModel = D('user');

        $item = $userModel->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }
        $this->assign('item', $item);
        $this->display('form');
    }
    public function update(){
        $userModel = D('user');
        $integralModel = D('integral');
        $logModel = D('log');

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $integral = isset($_POST['integral']) ? $_POST['integral'] : false;

        $list= $userModel->where('id=' . $id)->find();

        $ip = get_client_ip();

        $logo = '登录成功。';
        $log = $logModel->order('id desc')->where("ip = '$ip' and log = '$logo'")->find();

        $inte = array();
        $openid = $list['openid'];
        $inte['user_id'] = $openid;
        $inte['integral'] = $list['integral'];
        $inte['integral2'] =$integral;
        $time = strtotime(date('Y-m-d H:i:s', time()));
        $inte['time'] = $time ;
        $inte['ip'] = $ip;
        $inte['name'] = $log['name'];
        $inte['log_id'] = $log['id'];

        $integralModel->data($inte)->add();

        $data = array();
        $data['integral'] = $integral;

        if ($id) {
            $userModel->data($data)->where('id=' . $id)->save();

            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/integral/index'));
        } else {
            $this->error('抱歉，未知错误！');
        }
    }

    public function integral($p = 1){
        $p = intval($p) > 0 ? $p : 1;
        $integralModel = D('integral');
        $userModel = D('user');

        $t = time() - 3600 * 24 * 60;
        $integralModel->where("time < $t")->delete();//删除60天前的日志

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1';

        $count = $integralModel->where($where)->count();

        $tmp_list = $integralModel->order('id desc')->limit($offset . ',' . $pagesize)->select();

        $list = array();

        foreach ($tmp_list as $p) {
            $openid = $p['user_id'];
            $p['openid'] = $userModel->where("openid = '$openid'")->find();
            $list[] = $p;
        }
//        print_r($list); exit;
        $page = new\Think\Page($count,$pagesize);
        $show=$page->show();
        $this->assign('integral',$list);
        $this->assign('page',$show);

        $this->display();

    }

}