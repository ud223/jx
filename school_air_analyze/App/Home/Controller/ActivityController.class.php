<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7/007
 * Time: 15:49
 */

namespace Home\Controller;

use Think\Controller;

class ActivityController extends Controller{

    public  function index(){
        $activity_idModel = D('activity_id');
        $activity_formModel = D('activity_form');
        $manyModel = D('many');

        $activity_controlModel = D('activity_control');
        $activity_journalModel = D('activity_journal');
        $photoModel = D('photo');
        $userModel = D('user');
        $community_activity_personnelmodel = D('community_activity_personnel');

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $openid = isset($_REQUEST['openid']) ? $_REQUEST['openid'] : false;

        $user=$user_item = $userModel->where("openid='$openid'")->find();

        $data=array();
        $data['id']=$id;
        $data['is_delete']=0;
        $item =  D('community_activity')->where($data)->find();

        if($openid){
            $personnel = array();
            $personnel['activity_id'] = $id;
            $personnel['openid'] = $openid;

            $personnel = $community_activity_personnelmodel->where($personnel)->find();

            $journal =$activity_journalModel ->where("openid = '$openid' and activity_id = '$id'")->count();
//            print_r($journal); exit;
            if($journal == 3 ){

                if($personnel){
                    $item['state1']=$personnel['state'];
                }else{
                    $item['state1']= '2';
                }

            }else{
                $item['state1']=$personnel['state'];
            }

        }else{
            $item['state1']= 0;
        }

        $item['openid']=$openid;
        $item['personnel']=$personnel;
        $item['user']=$user;

        $img_id = $item["logo"];
        $img_photo = $photoModel->where("id='$img_id'")->find();
        $item['img_photo'] = $img_photo;

        $img_id1 = $item["group_pictures"];
        $img_photo1 = $photoModel->where("id='$img_id1'")->find();
        $item['img'] = $img_photo1['img'];

        //print_r($item); exit;
        $integral_state=$item['integral_state'];
        $item['integral_state']=D('integral_state')->where("id='$integral_state'")->find();
        $item['time'] =date('m月d日 G:i ',strtotime($item['time']));

        $item['date']= strtotime(date('Y-m-d H:i:s', time()));
        $item['deadline'] =strtotime($item['deadline']);

        $activity_id = $activity_idModel ->where("activity_id = '$id'")->find();

        $activity_kj = $activity_id['id'];

        $activity_form = $activity_formModel ->where("activity_content = '$activity_kj' and dofaut = 0 and is_delete = 0")->select();

        $list = array();

        foreach ($activity_form as $p) {
            $p['type'] = $activity_controlModel->where("id={$p['type']}")->find();

            $list[] = $p;
        }

        $activity_form2 = $activity_formModel ->where("activity_content = '$activity_kj' and dofaut = 1 and is_delete = 0")->select();

        $itm = array();

        foreach ($activity_form2 as $p) {
            $tips = $p["tips"];
            $p['data']= $manyModel->where( "activity_form_id = '$tips' and is_delete = 0")->select();

            $itm[] =  $p;

        }

        $ren_data = $community_activity_personnelmodel->where("activity_id = '$id' and is_delete = 0")->order('time desc')->select();
        $int = $community_activity_personnelmodel->where("activity_id = '$id' and is_delete = 0")->count();

        $itmlist = array();

        foreach ($ren_data as $z) {

            $openid = $z["openid"];
            $user= $userModel->where( "openid = '$openid' and is_delete = 0")->find();
            $user_img = $user['img'];
            $z["img"] = $photoModel->where("id='$user_img'")->find();
            $time = $z["time"];
            if($time){
                $z["time"] = date("Y-m-d H:i:s", $z["time"]);
            }else{
                $z["time"] = '';
            }


            $z["int"] = $int;
            $int--;
            $itmlist[] =  $z;

        }
        $this->assign('ren_data', $itmlist);
        $this->assign('activity_form2', $itm);
        $this->assign('activity_form', $list);
        $this->assign('item', $item);
        $this->display();
    }

    public function edit(){
        $community_activity_informationModel = D("community_activity_information");
        $community_activityModel = D('community_activity');
        $community_activity_personnelModel = D('community_activity_personnel');
        $community_sceneModel = D('community_scene');
        $activity_journalModel = D('activity_journal');
        $manyModel = D('many');
        $userModel = D('user');

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $openid = isset($_REQUEST['openid']) ? $_REQUEST['openid'] : false;
        $information = isset($_REQUEST['information']) ? $_REQUEST['information'] : false;
//        $deadline = isset($_REQUEST['deadlin']) ? $_REQUEST['deadlin'] : false;
        $date = strtotime(date('Y-m-d H:i:s', time()));

        $json2Array = json_decode($information, true);

        $community_scene = $community_sceneModel->where("activity_id='$id'and is_delete = 0")->select();

        $activity_list = $community_activityModel->where("id='$id'")->find();
        $time = strtotime($activity_list['time']);
        $deadline = strtotime($activity_list['deadline']);

        $user_list = $userModel->where("openid='$openid'")->find();

        $user = array();
        $user['activity_id'] = $id;
        $user['openid'] = $openid;

        $personnel = $community_activity_informationModel->where($user)->find();

        $int = 1;
        //报名是否到
        if($date < $time){
            echo ajax_api(true, 100, '抱歉，报名时间未到');exit;
        }
        //报名是否截至
        if($date > $deadline){
            echo ajax_api(true, 100, '抱歉，报名时间已截止');exit;
        }
        //重复报名
        if($personnel){
            echo ajax_api(true, 100, '抱歉，您已报名');exit;
        }
        //报名人数已满
        $participate_number = $activity_list['participate_number'];
        $limit_number = $activity_list['limit_number'];

        if($participate_number >= $limit_number){
            echo ajax_api(true, 100, '抱歉，报名人数已满');exit;
        }
        //唯一性判断
        foreach ($json2Array as $p) {
            if($p['code']){
                if ($p['is_only'] == '1'){
                    $code = $p['code'];
                    $dinahua = $p['valuer'];
                    $title = $p['title'];

                    $dh =  $community_activity_informationModel->where("activity_id = '$id' and code = '$code' and value = '$dinahua' ")->find();

                    if($dh){
                        echo ajax_api(true, 100, "抱歉，已存在$title");exit;
                    }

                }
            }
        }
        //开始事物
        $community_activity_informationModel->startTrans();

        $integral_state = $activity_list['integral_state'];
        //消耗积分报名
        if($integral_state == 1) {
            //判断用户积分是否足够
            if ($user_list['integral'] < $activity_list['integral']) {
                echo ajax_api(true, 100, '抱歉，您的积分不足');
                exit;
            }
        }

        //存入用户数据库
        foreach ($json2Array as $p) {
            if (!$p['code']) {
                echo ajax_api(true, 100, '抱歉，提交数据丢失！');
                exit;
            }

            $list = array();

            $list['id'] = build_order_no();
            $list['code'] = $p['code'];
            $title = $p['title'];
            //文本框存入title，value
            if ($title) {
                $list['title'] = $title;
                $list['value'] = $p['valuer'];
            }
            else {
                $valuer = $p['valuer'];
                $many = $manyModel->where("id = '$valuer'")->find();
                $list['value'] = $many['name'];
                $list['title'] = $p['dofaut'];
            }

            $list['activity_id'] = $id;
            $list['openid'] = $openid;
            $list['create_at'] = date('Y-m-d H:s:i', time());

            $r = $community_activity_informationModel->data($list)->add();

            if (!$r) {
                $community_activity_informationModel->rollback();

                echo ajax_api(true, 100, '参加失败');
                exit;
            }
        }

        //活动人数加1
        $activity = array();

        $activity['participate_number'] = $int + $activity_list['participate_number'];

        $r = $community_activityModel->data($activity)->where('id=' . $id)->save();

        if ($r !== false)
        {

        }
        else {
            $community_activity_informationModel->rollback();

            echo ajax_api(true, 100, '参加失败'); exit;
        }

        $scene = $activity_list['scene'];

        if($scene == 1){

            foreach ($community_scene as $C) {
                $data3 = array();
                $data3['id'] = build_order_no();
                $data3['activity_id'] = $C['id'];
                $data3['openid'] = $openid;
                $data3['name'] = $user_list['name'];
                $data3['time'] = strtotime(date('Y-m-d H:i:s', time()));

                $H = $community_activity_personnelModel->data($data3)->add();

                if (!$H) {
                    $community_activity_informationModel->rollback();

                    echo ajax_api(true, 100, '参加失败');
                    exit;
                }
            }
        }

        //数据存入签到表
        $data = array();
        $data['id'] = build_order_no();
        $data['activity_id'] = $id;
        $data['openid'] = $openid;
        $data['name'] = $user_list['name'];
        $data['time'] = strtotime(date('Y-m-d H:i:s', time()));

        $serc = $community_activity_personnelModel->data($data)->add();


        if ($serc) {
            $community_activity_informationModel->commit();

            $data4 =array();
            $data4['id'] = build_order_no();
            $data4['activity_id'] = $id;
            $data4['openid'] = $openid;
            $data4['time'] = date('Y-m-d H:s:i', time());

            $activity_journalModel->add($data4);

            echo ajax_api(true, 200, '参加成功');exit;
        } else {
            echo ajax_api(true, 100, '参加失败');exit;
        }
    }

    public function jump(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $openid = isset($_REQUEST['openid']) ? $_REQUEST['openid'] : false;

        $data=array();
        $data['id']=$id;
        $data['is_delete']=0;

        $item =  D('community_activity')->where($data)->find();
        $item['openid']=$openid;
        $img_id = $item["img"];
        $img_photo = D('photo')->where("id='$img_id'")->find();
        $item['img_photo'] = $img_photo;

        $item['time'] =date('m月d日 G:i ',strtotime($item['time']));

        $this->assign('item', $item);
        $this->display('index');
    }
    const API_SEND_URL='http://smssh1.253.com/msg/send/json'; //创蓝发送短信接口URL
    const API_ACCOUNT= 'N624456_N7518893'; // 创蓝API账号
    const API_PASSWORD= '7CmT8NcSv06d84';// 创蓝API密码
    public function cancel(){
        $community_activityModel = D('community_activity');
        $community_activity_informationModel = D('community_activity_information');
        $community_activity_personnelModel =D('community_activity_personnel');
        $community_sceneModel = D('community_scene');

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $openid = isset($_REQUEST['openid']) ? $_REQUEST['openid'] : false;

        $activity = $community_activityModel->where("id = '$id'")->find();
        $phone = $activity['phone'];
        $community_scene = $community_sceneModel->where("activity_id = '$id'")->select();

        $participate_number = $activity['participate_number'];

        $int = 1;

        $data = array();
        $data['participate_number'] = $participate_number - $int;
        $community_activityModel->data($data)->where("id = '$id'")->save();



        $community_activity_informationModel->where(" activity_id = '$id' and openid = '$openid'")->delete();



        $scene = $activity['scene'];

        if($scene == 1){
            foreach ($community_scene as $C) {
                $id1 =$C['id'];


                $community_activity_personnelModel->where("activity_id = '$id1' and openid = '$openid'")->delete();
            }
        }

        $list = $community_activity_personnelModel->where("activity_id = '$id' and openid = '$openid'")->delete();

        if($list){
            $msg = "您有一条幸福龙光小程序活动人员取消报名通知请注意查收!";

            //创蓝接口参数
            $postArr = array (
                'account'  =>  self::API_ACCOUNT,
                'password' => self::API_PASSWORD,
                'msg' => urlencode($msg),
                'phone' => $phone,
                'report' => true
            );
            //密码可以使用明文密码或使用32位MD5加密
            $gets = $this->curlPost(self::API_SEND_URL, $postArr);
            echo ajax_api(true,'200','退出成功',$list);exit;
        }else{
            echo ajax_api(true,'201','未知错误',$data);exit;
        }
    }
    public function wx(){
        $community_activityModel = D('community_activity');
        $photo = D("photo");
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $openid = isset($_REQUEST['openid']) ? $_REQUEST['openid'] : false;

        $item = $community_activityModel ->where("id = $id ")->find();

        $img_id = $item["group_pictures"];
        $img_photo = $photo->where("id='$img_id'")->find();
        $item['group_pictures'] = $img_photo['img'];
        $item['openid'] = $openid;

        $this->assign('item', $item);
        $this->display();

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