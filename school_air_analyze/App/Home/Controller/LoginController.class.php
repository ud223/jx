<?php
/**
 * Created by PhpStorm.
 * User: ssc
 * Date: 2018/10/20
 * Time: 14:39
 */

namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller
{
    public  function index(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $this->assign('item', $id);
        $this->display();
    }

    public function update(){
        $id= isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $usermane = isset($_REQUEST['usermane']) ? $_REQUEST['usermane'] : false;
        $password= isset($_REQUEST['password']) ? $_REQUEST['password'] : false;

        $data=array();
        $data['usermane']=$usermane;
        $data['password']=$password;

        $list=D('community_cooperate_login')->where($data)->find();

        if($list){
            if($list['type']=='1'){
                if($id){
                    $cooperate_list = D('community_cooperate')->where("id='$id'")->find();
//                print_r($cooperate_list['openid']); exit;

                    $openid=$cooperate_list['openid'];
                    $cooperate_list['user'] = D('user')->where("openid='$openid'")->find();

                    $cooperate_list['times']=date('Y年m月d日 G:i:s ',strtotime($cooperate_list['time']));

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

                    $this->assign('item', $cooperate_list);
                    $this->assign('consult',$img_list);
                    $this->assign('type',$login_type);
                    $this->display('form');
                }else{

                }
            }else{
                if($id){

                }else{

                }
            }
        }else{
            $this->error('登陆失败,账号或密码错误！');
        }
    }

    public function edit()
    {
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $id = isset($_GET['id']) ? $_GET['id'] : '';

        if($field=='请分配负责处理工作人员')
        {
            $this->error('请分配负责处理工作人员！');
        }

        $data=array();
        $data['name']=$field;
        $data['allot_time']=date('Y-m-d H:i:s');

        if($id){
            D('community_cooperate')->where("id='$id'")->save($data);

            $list=D('community_cooperate')->where("is_delete='0'")->select();

            $this->assign('consult',$list);
            $this->display('home');
        }else{
            $this->error('提交成功失败');
        }
    }
    public function home(){
///
    }
}