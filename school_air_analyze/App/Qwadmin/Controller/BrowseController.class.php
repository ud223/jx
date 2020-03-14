<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/12/5
 * Time: 19:37
 */

namespace Qwadmin\Controller;


class BrowseController extends ComController
{
    public function index($p = 1){
        $p = intval($p) > 0 ? $p : 1;
        $community_cooperateModel = D('community_cooperate');
        $community_cooperate_stateModel = D('community_cooperate_state');
        $community_cooperate_typeModel = D('community_cooperate_type');
        $fenModel = D('fen');
        $userModel = D('user');


        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1 ';

        $count = $community_cooperateModel->where($where)->order("optid desc")->count();

        $tmp_list = $community_cooperateModel->where($where)->order("time desc")->limit($offset . ',' . $pagesize)->select();

        $list = array();

        foreach ($tmp_list as $p) {
            $p['state'] = $community_cooperate_stateModel->where("id={$p['state']}")->find();
            $p['type'] = $community_cooperate_typeModel->where("id={$p['type']}")->find();
            $openid = $p['openid'];
            $p['user'] = $userModel->where("openid = '$openid'")->find();
            $p['fen'] = $fenModel->where("id={$p['score']}")->find();

            $list[] = $p;
        }


        $page = new\Think\Page($count, $pagesize);
        $page = $page->show();

        $this->assign('community_cooperate', $list);
        $this->assign('page', $page);

        $this->display();
    }

    public function edit($id = 0){
        $id = intval($id);
        //
        $community_cooperateModel = D('community_cooperate');
        $community_cooperate_stateModel = D('community_cooperate_state');
        $community_cooperate_typeModel = D('community_cooperate_type');
        $userModel = D('user');

        $item =  $community_cooperateModel->where("optid='$id'")->find();
      //  print_r($item); exit;
        if (!$item) {
            $this->error('参数错误！');
        }
        $openid = $item['openid'];
        $user = $userModel->where("openid='$openid'")->find();
        $item['openid'] = $user['name'];

        $type_id = $item['type'];
        $type = $community_cooperate_typeModel->where("id='$type_id'")->find();
        $item['type'] = $type['name'];

        $state_id = $item['state'];
        $state= $community_cooperate_stateModel->where("id='$state_id'")->find();
        $item['state'] = $state['name'];

        $img = $item["img"];
        $imgs=explode(',',$img);

        $where='';

        foreach ($imgs as $i) {
            if($where!=''){
                $where=$where.','."'$i'";
            }else{
                $where=$where."'$i'";
            }
        }

        $img_list = D('photo')->where("id in ($where)")->select();


        $this->assign('item',$item);
        $this->assign('consult',$img_list);
        $this->display('form');
    }

}