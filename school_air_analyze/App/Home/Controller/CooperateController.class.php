<?php
/**
 * Created by PhpStorm.
 * User: ssc
 * Date: 2018/10/22
 * Time: 11:32
 */

namespace Home\Controller;

use Think\Controller;

class CooperateController  extends Controller
{
    public  function login(){
        $id = isset($_REQUEST['optid']) ? $_REQUEST['optid'] : false;
       // $id='2018111399505456';
        $this->assign('item', $id);
        $this->display();
    }

    public function update(){
        $id= isset($_REQUEST['optid']) ? $_REQUEST['optid'] : false;
        $usermane = isset($_REQUEST['usermane']) ? $_REQUEST['usermane'] : false;
        $password= isset($_REQUEST['password']) ? $_REQUEST['password'] : false;
       //
        $data=array();

        $data['usermane']=$usermane;
        $data['password']=$password;

        $list = D('community_cooperate_login')->where($data)->find();

        session_start();

        $uid =$list['id'];

        session('uid', $uid);

        if($list){

            if($list['type']=='1'){

                if (!$id) {
                    header('Location:/home/cooperate/leader_home.html?uid='. $list['id']);
                }

                if($id){

                   // $cooperate_list = D('community_cooperate')->order('time desc')->where("optid='$id'")->find();
                    header('Location:/home/cooperate/question.html?optid='. $id);


//                print_r($cooperate_list['openid']); exit;
//                    $cooperate_list['u']=$list;
//
//                    $openid=$cooperate_list['openid'];
//                    $cooperate_list['user'] = D('user')->where("openid='$openid'")->find();
//
//                    $cooperate_list['times']=date('Y年m月d日 G:i:s ',strtotime($cooperate_list['time']));
//
//                    $type=$cooperate_list['type'];
//                    $cooperate_list['types'] = D('community_cooperate_type')->where("id='$type'")->find();
//
//                    $img = $cooperate_list["img"];
//                    $imgs=explode(',',$img);
//
//                    $where='';
//                    foreach ($imgs as $i) {
//                        if($where!=''){
//                            $where=$where.','."'$i'";
//                        }else{
//                            $where=$where."'$i'";
//                        }
//                    }
//
//                    $img_list = D('photo')->where("id in ($where)")->select();
//
//                    $login_type = D('community_cooperate_login')->where("type='2'")->select();
//
//                    $this->assign('item', $cooperate_list);
//                    $this->assign('consult',$img_list);
//                    $this->assign('type',$login_type);
//                    $this->display('question');
//                }else{
//                    $data=array();
//                    $data['result']='1';
//                    $data['is_delete']='0';
//                    $cooperate =D('community_cooperate')->order('time desc')->where($data)->select();
//                   // print_r($cooperate); exit;
//                    $cooperate_list=array();
//
//                    foreach($cooperate as $p){
//                        $openid=$p['openid'];
//                        $p['user'] = D('user')->where("openid='$openid'")->find();
//                        $p['timel'] = date('Y年m月d日 G:i:s ',strtotime($p['time']));
//                        $p['cooperate_type'] = D('community_cooperate_type')->where("id='{$p['type']}'")->find();
//                        $p['cooperate_state'] = D('community_cooperate_state')->where("id='{$p['state']}'")->find();
//                        $cooperate_list[] = $p;
//                    }
//
//                    $this->assign('list', $list);
//                    $this->assign('consult',$cooperate_list);
//                    $this->display('leader_home');
                }
            }else{
                if (!$id) {
                    header('Location:/home/cooperate/personnel_home.html?uid='. $list['id']);
                }

                if($id){

                    header('Location:/home/cooperate/question_worker.html?optid='. $id);
//                    $data=array();
//                    $data['id']=$id;
//                    $data['result']='3';
//                    $data['name']=$list['name'];
//
//                    $cooperate_list = D('community_cooperate')->order('time desc')->where($data)->find();
////                print_r($cooperate_list['openid']); exit;
//                    $cooperate_list['u']=$list;
//
//                    $openid=$cooperate_list['openid'];
//                    $cooperate_list['user'] = D('user')->where("openid='$openid'")->find();
//
//                    $cooperate_list['times']=date('Y年m月d日 h:i:s ',strtotime($cooperate_list['time']));
//
//                    $type=$cooperate_list['type'];
//                    $cooperate_list['types'] = D('community_cooperate_type')->where("id='$type'")->find();
//
//                    $img = $cooperate_list["img"];
//                    $imgs=explode(',',$img);
//
//                    $where='';
//                    foreach ($imgs as $i) {
//                        if($where!=''){
//                            $where=$where.','."'$i'";
//                        }else{
//                            $where=$where."'$i'";
//                        }
//                    }
//
//                    $img_list = D('photo')->where("id in ($where)")->select();
//
//                    $login_type = D('community_cooperate_login')->where("type='2'")->select();
//
//                    $this->assign('item', $cooperate_list);
//                    $this->assign('consult',$img_list);
//                    $this->assign('type',$login_type);
//                    $this->display('question_worker');
//                }else{
//                    $data=array();
//                    $data['result']='3';
//                    $data['name']=$list['name'];
//                    $cooperate =D('community_cooperate')->order('time desc')->where("name='$data'")->select();
//
//                    $cooperate_list=array();
//
//                    foreach($cooperate as $p){
//                        $openid=$p['openid'];
//                        $p['user'] = D('user')->where("openid='$openid'")->find();
//                        $p['timel'] = date('Y年m月d日 G:i:s ',strtotime($p['time']));
//                        $p['cooperate_type'] = D('community_cooperate_type')->where("id='{$p['type']}'")->find();
//                        $p['cooperate_state'] = D('community_cooperate_state')->where("id='{$p['state']}'")->find();
//                        $cooperate_list[] = $p;
//                    }
//
//                    $this->assign('list', $list);
//                    $this->assign('consult',$cooperate_list);
//                    $this->display('personnel_home');
                }
            }
        }else{
            $this->error('账号或密码错误！');
        }
    }

    public  function personnel_home(){
       // $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : false;
        $uid = session('uid') ;

        $list = D('community_cooperate_login')->where("id='$uid'")->find();

        $data=array();
        $data['is_delete']='0';
//        $data['result']='3';
        $data['name'] = $list['id'];
//        $data['state']='1';

        $cooperate =D('community_cooperate')->order('time desc')->where($data)->select();
//        exit(D('community_cooperate')->getLastSql());
        $cooperate_list=array();

        foreach($cooperate as $p){
            $openid=$p['openid'];

            $p['user'] = D('user')->where("openid='$openid'")->find();
            $p['timel'] = date('Y年m月d日 G:i:s ',strtotime($p['time']));
            $p['cooperate_type'] = D('community_cooperate_type')->where("id='{$p['type']}'")->find();
            $p['cooperate_state'] = D('community_cooperate_state')->where("id='{$p['state']}'")->find();

            $cooperate_list[] = $p;
        }
        $this->assign('list', $list);
        $this->assign('consult',$cooperate_list);
        $this->display('');
    }

    public  function leader_home(){
        //$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : false;
        $uid = session('uid') ;
        $list=D('community_cooperate_login')->where("id='$uid'")->find();

        $data=array();
//        $data['result']='1';
        $data['is_delete']='0';

        $cooperate = D('community_cooperate')->order('time desc')->where($data)->select();
        $cooperate_list=array();

        foreach($cooperate as $p){
            $openid=$p['openid'];

            $p['user'] = D('user')->where("openid='$openid'")->find();
            $p['timel'] = date('Y年m月d日 G:i:s ',strtotime($p['time']));
            $p['cooperate_type'] = D('community_cooperate_type')->where("id='{$p['type']}'")->find();
            $p['cooperate_state'] = D('community_cooperate_state')->where("id='{$p['state']}'")->find();

            $cooperate_list[] = $p;
        }

        $this->assign('list', $list);
        $this->assign('consult',$cooperate_list);
        $this->display('');
    }

    const API_SEND_URL='http://smssh1.253.com/msg/send/json'; //创蓝发送短信接口URL
    const API_ACCOUNT= 'N624456_N7518893'; // 创蓝API账号
    const API_PASSWORD= '7CmT8NcSv06d84';// 创蓝API密码

    public  function question(){
        $community_cooperate_stateModel = D('community_cooperate_state');
        $community_cooperate_login = D('community_cooperate_login');

        $id = isset($_REQUEST['optid']) ? $_REQUEST['optid'] : false;
       // $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : false;
        $uid = session('uid') ;

        $cooperate_list = D('community_cooperate')->where("optid='$id'")->find();

        $cooperate_list['u'] = $community_cooperate_login->where("id='$uid'")->find();

        $openid=$cooperate_list['openid'];

        $cooperate_list['user'] = D('user')->where("openid='$openid'")->find();

        $cooperate_list['times'] = date('Y年m月d日 G:i:s ',strtotime($cooperate_list['time']));

        $type=$cooperate_list['type'];
        $cooperate_list['types'] = D('community_cooperate_type')->where("id='$type'")->find();

        $img = $cooperate_list["img"];
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

        $login_type = D('community_cooperate_login')->where("type='2'")->select();

        $state = $cooperate_list['state'];
        $list_state = $community_cooperate_stateModel ->where("id = '$state'")->find();
       // $cooperate_list['state'] = $list_state['name'];

        $name = $cooperate_list['name'];

        $list_name = $community_cooperate_login->where("id = '$name'")->find();
      //  $cooperate_list['name'] = $list_name['name'];
        $this->assign('list_state', $list_state);
        $this->assign('list_name', $list_name);
        $this->assign('item', $cooperate_list);
        $this->assign('consult',$img_list);
        $this->assign('type',$login_type);
        $this->display('');
    }

    public  function question_worker(){
        $community_cooperate_stateModel = D('community_cooperate_state');
        $community_cooperate_login = D('community_cooperate_login');
        $id = isset($_REQUEST['optid']) ? $_REQUEST['optid'] : false;
       // $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : false;
        $uid = session('uid') ;

        $cooperate_list = D('community_cooperate')->where("optid='$id'")->find();
//                print_r($cooperate_list['openid']); exit;

        $cooperate_list['u']=D('community_cooperate_login')->where("id='$uid'")->find();

        $openid=$cooperate_list['openid'];
        $cooperate_list['user'] = D('user')->where("openid='$openid'")->find();

        $cooperate_list['times']=date('Y年m月d日 G:i:s ',strtotime($cooperate_list['time']));

        $type=$cooperate_list['type'];
        $cooperate_list['types'] = D('community_cooperate_type')->where("id='$type'")->find();

        $state = $cooperate_list['state'];
        $list_state = $community_cooperate_stateModel ->where("id = '$state'")->find();
        $cooperate_list['state'] = $list_state['name'];

        $name = $cooperate_list['name'];

        $list_name = $community_cooperate_login->where("id = '$name'")->find();
        $cooperate_list['name'] = $list_name['name'];



        $img = $cooperate_list["img"];
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

        $state_type = D('community_cooperate_state')->where('state = 1')->select();

        $this->assign('item', $cooperate_list);
        $this->assign('consult',$img_list);
        $this->assign('type',$state_type);
        $this->assign('uid', $uid);

        $this->display('');
    }

    public function edit(){
        $id = isset($_REQUEST['optid']) ? $_REQUEST['optid'] : false;
        $field = isset($_REQUEST['field']) ? $_REQUEST['field'] : false;
        $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : false;

        if($field =='0') {
            echo ajax_api(true,100,'请选择分配人'); exit;
        }
        else{
            $data = array();

            $data['login_id'] = $uid;
            $data['name'] = $field;
            $data['result'] = '3';
            $data['state'] = 4;
            $data['allot_time'] =  date('Y-m-d H:s:i', time());

            $cooperate = D('community_cooperate')->where("optid='{$id}'")->save($data);

            if($cooperate !== false){
                $community_cooperate_loginModel = D("community_cooperate_login");

                $operate = $community_cooperate_loginModel->where("id = $field")->find();

                $msg = "您好, 你有一条待处理的工作, 请点击登录处理http://lgcommunity.webetter100.com/home/cooperate/login?optid={$id}";

                //创蓝接口参数
                $postArr = array (
                    'account'  =>  self::API_ACCOUNT,
                    'password' => self::API_PASSWORD,
                    'msg' => urlencode($msg),
                    'phone' => $operate['phone'],
                    'report' => true
                );
//                echo ajax_api(true,100,'请选择分配人', $operate['phone']); exit;
                //密码可以使用明文密码或使用32位MD5加密
                $gets = $this->curlPost(self::API_SEND_URL, $postArr);

                echo ajax_api(true,200,'提交成功',$gets);
            }
            else{
                echo ajax_api(true,100,'提交失败');
            }
        }
    }

    public function worker_edit(){
        $show_pic = I('info_file');

        $photo = array();

        if ($show_pic) {
            $s = base64_decode(str_replace('data:image/jpeg;base64,', '', $show_pic));

            $pic_name = $save_path = "/Public/uploads/".build_order_no(). '.jpg';

            $save_path = '/var/www/lg_community' . $pic_name;

            $file_count = file_put_contents($save_path, $s);

            if (!$file_count) {
                echo fit_api(true, 0, '文件保存失败!', '');

                exit;
            }

            $photo['id'] = build_order_no();
            $photo['img'] = $pic_name;
            $photo['time'] =  date('Y-m-d H:s:i', time());

            $result = D('photo')->add($photo);

            if($result){

            }else{
                echo ajax_api(true,100,'图片提交失败');
            }
        }
        else {
            $photo['id'] = '';
        }

        $id = isset($_REQUEST['optid']) ? $_REQUEST['optid'] : false;
        $field = isset($_REQUEST['field']) ? $_REQUEST['field'] : false;
        $conclusion = isset($_REQUEST['conclusion']) ? $_REQUEST['conclusion'] : false;

        $data = array();

        $data['state'] = $field;
        $data['result'] = 5;
        $data['conclusion'] = $conclusion;
        $data['solve_time'] =  date('Y-m-d H:s:i', time());
        $data['img2'] = $photo['id'];

        $cooperate =D('community_cooperate')->where("optid='$id'")->save($data);

        if($cooperate){
//            $list = D('community_cooperate')->where("is_delete='0'")->select();
//
//            $this->assign('consult',$list);
            echo ajax_api(true,200,'提交成功');
        }else{
            echo ajax_api(true,100,'提交失败');
        }
    }

    /**
     * 通过CURL发送HTTP请求
     * @param string $url  //请求URL
     * @param array $postFields //请求参数
     * @return mixed
     *
     */
    private function curlPost($url,$postFields){
        $postFields = json_encode($postFields);

        $ch = curl_init ();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'   //json版本需要填写  Content-Type: application/json;
            )
        );
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,60);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close ( $ch );
        return $result;
    }
}