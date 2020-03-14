<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/16/016
 * Time: 9:47
 */

namespace Home\Controller;

//use function PHPSTORM_META\elementType;
use Vendor\Page;

class AppController extends ComController
{

    /*************************
     * 用户注册api
     * ***********************/
    public function regsiter() {
        $token = isset($_GET['token']) ? $_GET['token'] : false;
        $phone = isset($_GET['phone']) ? $_GET['phone'] : false;
        $nick_name = isset($_GET['nick_name']) ? $_GET['nick_name'] : false;
        $password = isset($_GET['password']) ? $_GET['password'] : false;
        $identifying_code = isset($_GET['identifying_code']) ? $_GET['identifying_code '] : false;

        if (!$token) {
            echo ajax_api(true, '100', 'Error Token', false); exit;
        }

        if (!$phone) {
            echo ajax_api(true, '0', 'Phone number is empty', false); exit;
        }

        if (!$nick_name) {
            echo ajax_api(true, '0', 'NickName is empty', false); exit;
        }

        if (!$password) {
            echo ajax_api(true, '0', 'Password is empty', false); exit;
        }

//        if (!$identifying_code) {
//            echo ajax_api(true, '0', 'Identifying code is empty', false); exit;
//        }

//        $sms = M('sms')->where("phone = '{$phone}' and type = 1")->order("create_at desc")->limit(1)->find();
//
//        if (!$sms) {
//            echo ajax_api(true, '0', 'Please get Identifying code', false); exit;
//        }
//
//        if ($sms['code'] != $identifying_code) {
//            echo ajax_api(true, '0', 'Identifying code error', false); exit;
//        }

        $user = M('user')->where("phone='{$phone}'")->find();

        if ($user) {
            echo ajax_api(true, '0', 'The phone was regsiter', false); exit;
        }

        $userModel = M('user');

        $userModel->phone = $phone;
        $userModel->nick_name = $nick_name;
        $userModel->password = M5($password);

        $result = $userModel->add();

        if ($result) {
            echo ajax_api(true, '200', 'regsiter success', false);
        }
        else {
            echo ajax_api(true, '0', 'regsiter fail', false);
        }
    }

    /************************
     * 登录api
     * *********************/
    public function login() {
        $token = isset($_GET['token']) ? $_GET['token'] : false;
        $phone = isset($_GET['phone']) ? $_GET['phone'] : false;
//        $nick_name = isset($_GET['nick_name']) ? $_GET['nick_name'] : false;
        $password = isset($_GET['password']) ? $_GET['password'] : false;

        if (!$token) {
            echo ajax_api(true, '100', 'Error Token', false); exit;
        }

        if ($token != C("COMMON_TOKEN")) {
            echo ajax_api(true, '100', 'Token error', false); exit;
        }

        if (!$phone) {
            echo ajax_api(true, '0', 'Phone number is empty', false); exit;
        }

//        if (!$nick_name) {
//            echo ajax_api(true, '0', 'NickName is empty', false); exit;
//        }

        if (!$password) {
            echo ajax_api(true, '0', 'Password is empty', false); exit;
        }

        $user = M('user')->where("phone='{$phone}' and is_disable = 0")->find();

        if ($user) {
            if ($user['password'] == M5($password)) {
                echo ajax_api(true, '200', 'Login success', $user);
            }
            else {
                echo ajax_api(true, '0', 'Phone number or password error', false);
            }
        }
        else {
            echo ajax_api(true, '0', 'Phone number or password error', false);
        }
    }

    const API_SEND_URL='http://smssh1.253.com/msg/send/json'; //创蓝发送短信接口URL
    const API_ACCOUNT= 'N624456_N7518893'; // 创蓝API账号
    const API_PASSWORD= '7CmT8NcSv06d84';// 创蓝API密码

    /***********************
     * 获取验证短信
     * ********************/
    public function getSmsCode($type) {
        $token = isset($_GET['token']) ? $_GET['token'] : false;
        $phone = isset($_GET['phone']) ? $_GET['phone'] : false;
        $type = isset($_GET['phone']) ? $_GET['phone'] : 1;

        if (!$token) {
            echo ajax_api(true, '100', 'Token error', false);
            exit;
        }

        if ($token != C("COMMON_TOKEN")) {
            echo ajax_api(true, '100', 'Token error', false);
            exit;
        }

        if (!$phone) {
            echo ajax_api(true, '0', 'Phone number is empty', false);
            exit;
        }

        $smsModel = M('sms');

        $showtime = date("Y-m-d");
        $showtime1 = date("Y-m-d H:i");
        $showtime2 = date("Y-m-d H");

        $sms_count = $smsModel ->where("phone = '$phone' and date = '$showtime'")->count();

        if($sms_count == 10){
            echo ajax_api(true, '24', '抱歉你今天的短信次数已用完,请明天再试！！！', $sms_count);
            exit;
        }

        $data = array();
        $data['create_at'] = array('like', '%'.$showtime1.'%');
        $data['phone'] = $phone;

        $sms_count1 = $smsModel ->where($data)->count();

        if($sms_count1 == 2){
            echo ajax_api(true, '1', '抱歉请隔1分钟再试', $sms_count1);
            exit;
        }
        $data1 = array();
        $data1['create_at'] = array('like', '%'.$showtime2.'%');
        $data1['phone'] = $phone;

        $sms_count2 = $smsModel ->where($data1)->count();
        if($sms_count2 == 5){
            echo ajax_api(true, '60', '抱歉请隔一个小时再试！', $sms_count2);
            exit;
        }




//        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";

        $mobile_code = RandomNum(4, 1);

//        $post_data = "account=cf_tianshi&password=fit@angelhere&mobile=" . $phone . "&content=您的验证码为{S8}请在{S1}分钟内输入。感谢您对社区的支持，祝您生活愉快！");

        $time = 10;
        $msg = "您的验证码为{$mobile_code}请在{$time}分钟内输入。感谢您对社区的支持，祝您生活愉快！";

        //创蓝接口参数
        $postArr = array (
            'account'  =>  self::API_ACCOUNT,
            'password' => self::API_PASSWORD,
            'msg' => urlencode($msg),
            'phone' => $phone,
            'report' => true
        );
//        print_r($postArr); exit;
        //密码可以使用明文密码或使用32位MD5加密
        $gets = $this->curlPost(self::API_SEND_URL, $postArr);

        $smsModel->id = uniqid() . time() . rand(10000000, 99999999);
        $smsModel->phone = $phone;
        $smsModel->msg = "Your Identifying code is ：" . $mobile_code;
        $smsModel->code = $mobile_code;
        $smsModel->type = $type;
        $smsModel->create_at = date('Y-m-d H:i:s', time());
        $smsModel->date = date('Y-m-d', time());

        $result = $smsModel->add();

        header("Content-type:text/html; charset=UTF-8");

        if ($result) {
            echo ajax_api(true, 200, 'Identifying code is send', $gets);
            exit;
        } else {
            echo ajax_api(true, 100, 'Get Identifying code error', $gets);
            exit;
        }
    }

    public function informationQuery(){
        $informationModel = D('information');
        $company_informationModel=D('company_information');

        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $position = isset($_POST['position']) ? $_POST['position'] : false;
        $company = isset($_POST['company']) ? $_POST['company'] : false;
        $telephone = isset($_POST['telephone']) ? $_POST['telephone'] : false;
        $mailbox = isset($_POST['mailbox']) ? $_POST['mailbox'] : false;

        $data = array();

        if($name){
            $data['name'] = $name;
        }

        if($position){
            $data['position'] = $position;
        }
        if($company){
            $data['company'] = $company;
        }
        if($telephone){
            $data['telephone'] = $telephone;
        }
        if($mailbox){
            $data['mailbox'] = $mailbox; $data['mailbox'] = $mailbox;
        }

        $reco_list = $informationModel->where('is_delete=0')->where($data)->select();

        $list = array();

        foreach ($reco_list as $p) {
            $p['company_information'] = $company_informationModel->where("id={$p['company']}")->find();

            $list[] = $p;
        }

        echo ajax_api(true,200,'查询数据成功', $list);
    }

    //用户注册
    public function useradd(){
        $userModel = D('user');
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        $openid =isset($_POST['openid']) ? $_POST['openid'] : false;
        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $phone = isset($_POST['phone']) ? $_POST['phone'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$openid){
            echo ajax_api(true,'100','获取id失败',false);exit;
        }
        if(!$name){
            echo ajax_api(true,'100','获取姓名失败',false);exit;
        }
        if(!$phone){
            echo ajax_api(true,'100','获取电话失败',false);exit;
        }

        $user = array();
        $user['openid']=$openid;
        $user['is_delete']=$openid;

        $openid_photo = D('user')->where($user)->find();

        if($openid_photo['is_disable']=='1'){
            echo ajax_api(true,'88','用户已禁用',false);exit;
        }

        if($openid_photo){
            echo ajax_api(true,'100','openid已注册',false);exit;
        }

        $user_photo = D('user')->where("phone='$phone'")->find();

        if($user_photo){
            echo ajax_api(true,'100','电话号码已注册',false);exit;
        }

        $data = array();

        $data["id"] = build_order_no();
        $data['openid'] = $openid;
        $data['name'] = $name;
        $data['phone'] = $phone;
        $data['img']='2018092097485056';

        $result_userModel = $userModel->add($data);

        if ($result_userModel) {
            echo ajax_api(true,200,'注册成功');
        }else{
            echo ajax_api(true,100,'注册失败');
        }
    }

    //用户名修改
    public function user_name(){
        $openid =isset($_POST['openid']) ? $_POST['openid'] : false;
        $name =isset($_POST['name']) ? $_POST['name'] : false;

        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$openid)
        {
            echo ajax_api(true,'100','openid获取异常',false);exit;
        }
        if(!$name)
        {
            echo ajax_api(true,'100','姓名获取异常',false);exit;
        }

        $list=D('user')->where("openid='$openid'")->find();

        if($list['modify']=='1')
        {
            echo ajax_api(true,'100','姓名已经修改过',false);exit;
        }

        if($list['name']==$name)
        {
            echo ajax_api(true,'100','姓名不能重复',false);exit;
        }
        $data = array();
        $data['name'] =$name;
        $data['modify'] ='1';

        D('user')->data($data )->where('id=' . $list['id'])->save();
        echo ajax_api(true,'200','修改成功');exit;
    }

    //用户头像修改
    public function user_img(){
        $openid =isset($_POST['openid']) ? $_POST['openid'] : false;
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$openid)
        {
            echo ajax_api(true,'100','openid获取异常',false);exit;
        }

        $img=$_FILES;

        if(!$img)
        {
            echo ajax_api(true,'100','图片上传失败',false);exit;
        }

        $filename=$img['files']['name'];
        $tmp_name=$img['files']['tmp_name'];
        $name=get_ext($filename);
        $save_path = "/var/www/Intelligent_community/Public/uploads/". date("YmdHisfff", time()). '.'.$name;
        move_uploaded_file($tmp_name, $save_path);

        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = "/Public/uploads/". date("YmdHisfff", time()). '.'. $name;

        $photo_list= D('photo')->add($photo);

        if(!$photo_list)
        {
            echo ajax_api(true,'100','图片上传失败',false);exit;
        }

        $list=D('user')->where("openid='$openid'")->find();

        $data = array();
        $data['img'] =$photo['id'];

        D('user')->data($data )->where('id=' . $list['id'])->save();
        echo ajax_api(true,'200','修改成功');exit;
    }

    //用户信息获取
    public function user_openid(){

        $openid =isset($_POST['openid']) ? $_POST['openid'] : false;

        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$openid)
        {
            echo ajax_api(true,'100','openid获取失败',false);exit;
        }

        $user=D('user')->where("openid='$openid'")->find();
        if(!$user)
        {
            echo ajax_api(true,'88','用户信息为空',false);exit;
        }else{
            echo ajax_api(true,'200','查询成功',$user);exit;
        }
    }

    public function userQuery(){
        $openid =isset($_POST['openid']) ? $_POST['openid'] : false;

        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        $user=D('user')->where("openid='$openid'")->find();

        echo ajax_api(true,'200','查询成功',$user);exit;
    }

    //社区地图
    public function communitymapQuery(){
        $community_mapModel = D('community_map');

        $token = isset($_POST['token']) ? $_POST['token'] : false;
        if (!$token) {
            echo ajax_api(true, '100', 'Error Token', false);
            exit;
        }
        if ($token != C("COMMON_TOKEN")) {
            echo ajax_api(true, '100', 'Token error', false);
            exit;
        }

//        $lng = isset($_POST['lng']) ? $_POST['lng'] : false;
//        $lat = isset($_POST['lat']) ? $_POST['lat'] : false;


        $data = array();

        $data['is_delete'] = '0';

        $community_list = $community_mapModel->where($data)->select();

//        if (!$lng) {
//            echo ajax_api(true, '100', '经纬度无法获取', $community_list);
//            exit;
//        }
//        if (!$lat) {
//            echo ajax_api(true, '100', '经纬度无法获取', $community_list);
//            exit;
//        }
//
//        $list = array();
//
//        foreach ($community_list as $m) {
//            $m['distance'] = getDistance($m["latitude"], $m["longitude"], $lat, $lng);
//
//            $mi=$m['distance'];
//            if($mi>1000){
//                $m['distance'] =  sprintf("%.1f", $mi / 1000);
//                $m['unit'] = 'km';
//            }else{
//                $m['distance']=$mi;
//                $m['unit'] = 'm';
//            }
//
//            $list[] = $m;
//        }

        if ($community_list) {
            echo ajax_api(true, 200, '查询成功', $community_list);
        } else {
            echo ajax_api(true, 201, '查询失败');
        }
    }

    //社区超市
    public function communitymap_name_Query(){

        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        $data = array();
        $data['name'] = '社区超市 ';
        $data['is_delete'] = '0';

        $community_list = D('community_map')->where($data)->select();
        echo ajax_api(true,200,'查询成功', $community_list);
    }

    //便民通讯录
    public function mail_listQuery(){
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        $mail_listModel = D('mail_list');

        $data = array();

        $data['is_delete'] = '0';

        $mail_list = $mail_listModel->where($data)->select();

        echo ajax_api(true,200,'查询数据成功', $mail_list);
    }

    public function proposal_category(){
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }
        $proposal_categoryModel = D('proposal_category');

        $data = array();

        $data['is_delete'] = '0';

        $category = $proposal_categoryModel->where($data)->select();

        echo ajax_api(true,200,'查询数据成功', $category);
    }

    //居民建议箱
    public function proposaladd(){
        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $category = isset($_POST['category']) ? $_POST['category'] : false;
        $content = isset($_POST['content']) ? $_POST['content'] : false;
        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$openid){
            echo ajax_api(true,'90','用户信息获取异常',false);exit;
        }

        $user=D('user')->where("openid='$openid'")->find();

        if(!$user){
            echo ajax_api(true,'88','用户信息为空',false);exit;
        }

        if(!$title){
            echo ajax_api(true,'92','标题不能为空',false);exit;
        }
        if(!$category){
            echo ajax_api(true,'93','请选择分类',false);exit;
        }
        if(!$content){
            echo ajax_api(true,'94','详细内容不能为空',false);exit;
        }

        $img=$_FILES;
//        if($img){
//            echo ajax_api(true,'187','详细内容不能为空',$img);exit;
//        }else{
//            echo ajax_api(true,'189','详细内容不能为空',$img);exit;
//        }

        if($img){
            $filename=$img['files']['name'];
            $tmp_name=$img['files']['tmp_name'];
            $ext_name=get_ext($filename);
            $save_path = "/Public/uploads/".date("YmdHis", time()). '.'.$ext_name;
            move_uploaded_file($tmp_name, '/var/www/Intelligent_community/'. $save_path);

            $photo = array();
            $photo['id'] = build_order_no();
            $photo['img'] = $save_path;

            D('photo')->add($photo);

            $data = array();
            $data["id"] = build_order_no();
            $data["openid"] = $openid;
            $data["title"] = $title;
            $data["category"] = $category;
            $data["content"] = $content;
            $data["img"] =$photo['id'] ;
            $data['propose_time']=  date('Y-m-d H:i:s');

            $result = D('proposal')->data($data)->add();

            if ($result) {
                echo ajax_api(true,200,'提交成功',$data["id"]);
            }else{
                echo ajax_api(true,201,'提交失败');
            }
        }else{
            $data = array();
            $data["id"] = build_order_no();
            $data["openid"] = $openid;
            $data["title"] = $title;
            $data["category"] = $category;
            $data["content"] = $content;
            $data["img"] ='0' ;
            $data['propose_time']=  date('Y-m-d H:i:s');

            $result = D('proposal')->data($data)->add();

            if ($result) {
                echo ajax_api(true,200,'提交成功',$data["id"]);
            }else{
                echo ajax_api(true,201,'提交失败');
            }
        }
    }

    public function proposalsave(){
        $id = isset($_POST['id']) ? $_POST['id'] : false;

        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$id){
            echo ajax_api(true,'88','id为空',false);exit;
        }

        $img=$_FILES;

        $filename=$img['files']['name'];
        $tmp_name=$img['files']['tmp_name'];
        $ext_name = get_ext($filename);

        $save_path = "/Public/uploads/".build_order_no(). '.'.$ext_name;
        move_uploaded_file($tmp_name, '/var/www/Intelligent_community/'.$save_path);

        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = $save_path;

        D('photo')->add($photo);

        $item=D('proposal')->where("id='$id'")->find();
        $photo_id=$item['img'];
//        $photo=D('photo')->where("id='$photo_id'")->find();

        $data=array();
//        $data['img']="/Public/uploads/". date("YmdHisfff", time()). '.'.$name.'|'.$photo['img'];
        $data['img']=$photo_id.','.$photo['id'];

        $result=D('proposal')->data($data)->where("id={$item['id']}")->save();

        if ($result) {
            echo ajax_api(true,200,'添加图片成功',$item['id']);
        }else{
            echo ajax_api(true,201,'添加图片成功');
        }
    }

//居民建议箱查询信息
    public function proposal(){
//        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;
//        $token =isset($_POST['token']) ? $_POST['token'] : false;
//        if(!$token){
//            echo ajax_api(true,'100','Error Token',false);exit;
//        }
//        if($token!=C("COMMON_TOKEN")){
//            echo ajax_api(true,'100','Token error',false);exit;
//        }
//        if(!$openid){
//            echo ajax_api(true,'100','获取用户信息异常',false);exit;
//        }
        $openid='oU-Gk5KVuwP5EGImkCY2JsNVfAD0';
        $data=array();
        $data['openid']=$openid;
        $data['is_delete']='0';

        $proposal_list = D('proposal')->where($data)->select();

        if(!$proposal_list){
            echo ajax_api(true,'100','用户没有添加建议',false);exit;
        }

        $list = array();

        foreach ($proposal_list as $p) {

            $img = $p['img'];
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


           echo ajax_api(true,'100','用户没有添加建议', D('photo')->getLastSql());exit;
            echo ajax_api(true,'100','用户没有添加建议',$img_list);exit;

            $p['photo']=$img_list;
            $p['time1'] =date('m月d日 G:i ',strtotime($p['propose_time']));
            $p['openid'] = D('user')->where("openid='{$p['openid']}'")->find();
            $p['proposal_category'] = D('proposal_category')->where("id={$p['category']}")->find();

            $list[] = $p;
        }

        echo ajax_api(true,200,'查询数据成功', $list);
    }
//单条居民建议信息
    public function proposalid(){
        $id = isset($_POST['id']) ? $_POST['id'] : false;

        if(!$id){
            echo ajax_api(true,'100','ID为空',false);exit;
        }

//        $token =isset($_POST['token']) ? $_POST['token'] : false;
//        if(!$token){
//            echo ajax_api(true,'99','Error Token',false);exit;
//        }
//        if($token!=C("COMMON_TOKEN")){
//            echo ajax_api(true,'98','Token error',false);exit;
//        }

        $data=array();
        $data['id']=$id;
        $data['is_delete']='0';

        $proposal_list = D('proposal')->where($data)->select();
        $list = array();

        foreach ($proposal_list as $p) {

            $img = $p['img'];
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
//            echo ajax_api(true,'100','用户没有添加建议', D('photo')->getLastSql());exit;
//            echo ajax_api(true,'100','用户没有添加建议',$img_list);exit;
            $p['photo']=$img_list;
            $p['time1'] =date('m月d日 G:i ',strtotime($p['propose_time']));
            $p['openid'] = D('user')->where("openid='{$p['openid']}'")->find();
            $p['proposal_category'] = D('proposal_category')->where("id={$p['category']}")->find();

            $list[] = $p;
        }

        echo ajax_api(true,200,'查询数据成功', $list);
    }

    //办事指南查询
    public function guide(){
        $token =isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        $guideModel = D('guide');
        $photoModel = D('photo');
        $guide_typeModel = D('guide_type');

        $list = array();

        $list_type = $guide_typeModel->select();

        foreach ($list_type as $p) {
            $item = array();

            $type = $p['id'];

            $item['text'] = $p['name'];

            $guidey_list = $guideModel->where("type='$type'")->select();

            $menu_list = array();

            foreach ($guidey_list as $t) {
                $t['photo'] = $photoModel->where("id={$t['logo']}")->find();

                $t['guide_fileu1'] = D('guide_fileu')->where("id={$t['file_1']}")->find();
                $t['guide_fileu2'] = D('guide_fileu')->where("id={$t['file_2']}")->find();
                $t['guide_fileu3'] = D('guide_fileu')->where("id={$t['file_3']}")->find();
                $t['guide_fileu4'] = D('guide_fileu')->where("id={$t['file_4']}")->find();
                $t['guide_fileu5'] = D('guide_fileu')->where("id={$t['file_5']}")->find();
                $t['guide_fileu6'] = D('guide_fileu')->where("id={$t['file_6']}")->find();
                $t['guide_fileu7'] = D('guide_fileu')->where("id={$t['file_7']}")->find();
                $t['guide_fileu8'] = D('guide_fileu')->where("id={$t['file_8']}")->find();
                $t['guide_fileu9'] = D('guide_fileu')->where("id={$t['file_9']}")->find();
                $t['guide_fileu10'] = D('guide_fileu')->where("id={$t['file_10']}")->find();

                $menu_list[] = $t;
            }

            $item['menus']= $menu_list;

            $list[] = $item;
        }

        echo ajax_api(true,200,'查询数据成功', $list);

    }
    //openid获取
    public function get_openid(){
        $code = isset($_GET['code']) ? $_GET['code'] : false;
//        $code='0330i8l30lXNiD1cIuo306JUk300i8lF';

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wx125b224740239c6b&secret=cb4c3ef70a3425e41ee849ed3c7cad2e&js_code={$code}&grant_type=authorization_code" ;  //api接口地址

        $result=request_get($url);

        $array = json_decode($result,TRUE);
        $openid=$array['openid'];

        if(!$openid){
            echo ajax_api(true,'100','openid获取失败',false);exit;
        }

        $user = array();
        $user['openid'] = $openid;
        $user['is_delete'] = '0';

        $user_item =  D('user')->where($user)->select();

//        if(!$user_item){
//            $data['openid'] = $openid;
//
//            echo ajax_api(true,'101','用户信息无效!',$data);exit;
//        }

        $data = array();

        foreach ($user_item as $p) {
            $img_id = $p['img'];

            if ($img_id) {
                $p['photo'] = D('photo')->where("id={$p['img']}")->find();
            }
            else {
                $p['photo'] = '';
            }

            $data['list'] = $p;
        }

        $data['openid'] = $openid;

        if ($user_item) {
            echo ajax_api(true,200,'查询成功', $data);
        }else{
            echo ajax_api(true,201,'查询失败', $data);
        }
    }
    //案例反馈下拉框
    public function case_feedback_category(){
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }
        $data = array();

        $data['is_delete'] = '0';

        $category = D('case_feedback_category')->where($data)->select();

        echo ajax_api(true,200,'查询数据成功', $category);
    }

    //案例反馈
    public function case_feedbackadd(){
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $category = isset($_POST['category']) ? $_POST['category'] : false;
        $content = isset($_POST['content']) ? $_POST['content'] : false;
        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;

        if(!$openid){
            echo ajax_api(true,'90','用户信息获取异常',false);exit;
        }

        $user=D('user')->where("openid='$openid'")->find();

        if(!$user){
            echo ajax_api(true,'91','用户信息为空',false);exit;
        }

        if(!$title){
            echo ajax_api(true,'92','标题不能为空',false);exit;
        }
        if(!$category){
            echo ajax_api(true,'93','请选择分类',false);exit;
        }
        if(!$content){
            echo ajax_api(true,'94','详细内容不能为空',false);exit;
        }

        $img=$_FILES;

        if($img){
            $filename=$img['files']['name'];
            $tmp_name=$img['files']['tmp_name'];
            $name=get_ext($filename);
            $save_path = "/var/www/Intelligent_community/Public/uploads/". date("YmdHisfff", time()). '.'.$name;
            move_uploaded_file($tmp_name, $save_path);

            $photo = array();
            $photo['id'] = build_order_no();
            $photo['img'] = "/Public/uploads/". date("YmdHisfff", time()). '.'. $name;

            D('photo')->add($photo);

            $data = array();
            $data["id"] = build_order_no();
            $data["openid"] = $openid;
            $data["title"] = $title;
            $data["category"] = $category;
            $data["content"] = $content;
            $data["img"] =$photo['id'] ;
            $data["time"] = date('Y-m-d H:i:s');

//            $data['propose_time']=  date('Y-m-d H:i:s');

            $result = D('case_feedback')->data($data)->add();

            if ($result) {
                echo ajax_api(true,200,'提交成功',$data["id"]);
            }else{
                echo ajax_api(true,201,'提交失败');
            }
        }else{
            $data = array();
            $data["id"] = build_order_no();
            $data["openid"] = $openid;
            $data["title"] = $title;
            $data["category"] = $category;
            $data["content"] = $content;
            $data["img"] ='0' ;
            $data["time"] = date('Y-m-d H:i:s');

//            $data['propose_time']=  date('Y-m-d H:i:s');

            $result = D('case_feedback')->data($data)->add();

            if ($result) {
                echo ajax_api(true,200,'提交成功',$data["id"]);
            }else{
                echo ajax_api(true,201,'提交失败');
            }
        }

    }

    public function case_feedbacksave() {
        $id = isset($_POST['id']) ? $_POST['id'] : false;

        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$id){
            echo ajax_api(true,'88','id为空',false);exit;
        }

        $img=$_FILES;

        $filename=$img['files']['name'];
        $tmp_name=$img['files']['tmp_name'];
        $name=get_ext($filename);

        $save_path = "/var/www/Intelligent_community/Public/uploads/". date("YmdHisfff", time()). '.'.$name;
        move_uploaded_file($tmp_name, $save_path);

        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = "/Public/uploads/". date("YmdHisfff", time()). '.'. $name;

        D('photo')->add($photo);

        $item=D('case_feedback')->where("id='$id'")->find();
        $photo_id=$item['img'];
//        $photo=D('photo')->where("id='$photo_id'")->find();

        $data=array();
//        $data['img']="/Public/uploads/". date("YmdHisfff", time()). '.'.$name.'|'.$photo['img'];
        $data['img']=$photo_id.','.$photo['id'];

        $result=D('case_feedback')->data($data)->where("id={$item['id']}")->save();

        if ($result) {
            echo ajax_api(true,200,'添加图片成功',$photo_id);
        }else{
            echo ajax_api(true,201,'添加图片成功');
        }
    }

    public function enterprise_recruit(){
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }
        $data = array();

        $data['is_delete'] = '0';

        $recruit = D('enterprise_recruit')->where($data)->select();

        $list = array();

        foreach ($recruit as $p) {
            $p['photo'] = D('photo')->where("id={$p['img']}")->find();
            $list[] = $p;
        }

        echo ajax_api(true,200,'查询数据成功', $list);
    }

    public function enterprise_dispute(){
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        $data = array();

        $data['is_delete'] = '0';

        $dispute = D('enterprise_dispute')->where($data)->select();

        $list = array();

        foreach ($dispute as $p) {
            $p['photo'] = D('photo')->where("id={$p['img']}")->find();
            $list[] = $p;
        }

        echo ajax_api(true,200,'查询数据成功', $list);
    }

    public function community_activity(){
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        $data = array();

        $data['is_delete'] = '0';
        $data['active_state1'] = '1';

        $activity = D('community_activity')->where($data)->order("id desc")->select();

        $list = array();

        foreach ($activity as $p) {
            $p['time1'] =date('m月d日 G:i ',strtotime($p['time']));
            $p['logo'] = D('photo')->where("id={$p['logo']}")->find();
            $p['integral_state'] = D('integral_state')->where("id={$p['integral_state']}")->find();
            $list[] = $p;
        }

        echo ajax_api(true,200,'查询数据成功', $list);
    }

    public function commodity(){
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }
        $id =isset($_POST['id']) ? $_POST['id'] : false;
        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;

        if(!$id){
            echo ajax_api(true,'100','id为空',false);exit;
        }
        if(!$openid){
            echo ajax_api(true,'100','openID为空',false);exit;
        }

        $commodity = D('commodity')->where("id='$id'")->find();

        if(!$commodity){
            echo ajax_api(true,'101','已下架此商品',false);exit;
        }

        $user = D('user')->where("openid='$openid'")->find();

        if(!$user){
            echo ajax_api(true,'100','用户没有注册',false);exit;
        }

        if($user['integral']<$commodity['integral']){
            echo ajax_api(true,'102','你的积分不足',false);exit;
        }

        $data= array();

        $data['integral']=$user['integral'] -$commodity['integral'];

        D('user')->data($data )->where('id=' . $user['id'])->save();

        $record = array();
        $record['id'] = build_order_no();
        $record['commodity_id'] = $id;
        $record['openid'] = $openid;
        $record['time']=  date('Y-m-d H:i:s');

        $list= D('exchange_record')->add($record);

        if ($list) {
            echo ajax_api(true,200,'兑换成功');
        }else{
            echo ajax_api(true,201,'兑换失败');
        }
    }
    //商品查询
    public function commodity_Query(){
        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$openid){
            echo ajax_api(true,'100','获取用户信息异常',false);exit;
        }

        $exchange_record = D('exchange_record')->where("openid='$openid'")->select();
        if(!$exchange_record)
        {
            echo ajax_api(true,'100','无记录');exit;
        }
        $list = array();

        foreach ($exchange_record as $p) {
            $p['time1'] =date('m月d日 G:i:s ',strtotime($p['time']));
            $p['commodity'] = D('commodity')->where("id={$p['commodity_id']}")->find();
            $list[] = $p;
        }
        echo ajax_api(true,'200','查询成功',$list);exit;
    }

    public function activity_personnel(){
        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;
        $token =isset($_POST['token']) ? $_POST['token'] : false;
        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$openid){
            echo ajax_api(true,'100','获取用户信息异常',false);exit;
        }

        $data=array();
        $data['openid']=$openid;

        $cpersonnel = D('community_activity_personnel')->where($data)->select();

        if(!$cpersonnel){
            echo ajax_api(true,'100','用户没有参加活动',false);exit;
        }

        $list = array();

        foreach ($cpersonnel as $p) {
            $p['photo'] = D('community_activity')->where("id={$p['activity_id']}")->find();
            $p['state'] = D('community_activity_personnel_state')->where("id={$p['state']}")->find();
            $list[] = $p;
        }

        echo ajax_api(true,'200','查询成功',$list);exit;
    }

    public function random($length = 6 , $numeric = 0) {
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        if($numeric) {
            $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
            $max = strlen($chars) - 1;
            for($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }

    public function Post($curlPost,$url) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);

        return $return_str;
    }

    public function sms_valid() {
        $phone =isset($_GET['phone']) ? $_GET['phone'] : false;
        $openid =isset($_GET['openid']) ? $_GET['openid'] : false;
        $token =isset($_GET['token']) ? $_GET['token'] : false;

        if(!$token){
            echo ajax_api(true,'102','Error Token',$phone);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'99','Token error',false);exit;
        }

        if(!$openid){
            echo ajax_api(true,'96','openid为空',false);exit;
        }

        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";

        if (!$phone) {
            echo ajax_api(true,'98','没有获取到手机号码',false);exit;
        }

        $mobile_code = $this->random(4,1);

//        $post_data = "account=cf_tianshi&password=fit@angelhere&mobile=". $phone ."&content=".rawurlencode("您的验证码是：".$mobile_code);
//
//        header("Content-type:text/html; charset=UTF-8");

        //密码可以使用明文密码或使用32位MD5加密
//        $gets =  $this->Post($post_data, $target);

        $time = 10;
        $msg = "您的验证码为{$mobile_code}请在{$time}分钟内输入。感谢您对社区的支持，祝您生活愉快！";

        //创蓝接口参数
        $postArr = array (
            'account'  =>  self::API_ACCOUNT,
            'password' => self::API_PASSWORD,
            'msg' => urlencode($msg),
            'phone' => $phone,
            'report' => true
        );
//        print_r($postArr); exit;
        //密码可以使用明文密码或使用32位MD5加密
        $gets = $this->curlPost(self::API_SEND_URL, $postArr);

        if(!$mobile_code){
            echo ajax_api(true,'97','验证码发送失败',false);exit;
        }

        $data = array();
        $data['id'] = build_order_no();
        $data['phone'] = $phone;
        $data['openid'] = $openid;
        $data['mobile_code'] = $mobile_code;
        $data['time']=  date('Y-m-d H:i:s');

        $phone_proving = D('phone_proving')->add($data);

        echo ajax_api(true,'200','验证短信已发送');exit;
    }
//注册
    public function phone_proving() {
        $openid =isset($_GET['openid']) ? $_GET['openid'] : false;
        $mobile_code =isset($_GET['mobile_code']) ? $_GET['mobile_code'] : false;
        $token =isset($_GET['token']) ? $_GET['token'] : false;
        $name =isset($_GET['name']) ? $_GET['name'] : false;
        $account_number =isset($_GET['account_number']) ? $_GET['account_number'] : false;
        $phone =isset($_GET['phone']) ? $_GET['phone'] : false;
        $address =isset($_GET['address']) ? $_GET['address'] : false;
//        echo ajax_api(true,'100','Error Token',$openid);exit;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if(!$openid){
            echo ajax_api(true,'99','用户信息为空',false);exit;
        }

        if(!$mobile_code){
            echo ajax_api(true,'98','验证码为空',false);exit;
        }

        if(!$name){
            echo ajax_api(true,'97','姓名不能为空',false);exit;
        }
        if(!$phone){
            echo ajax_api(true,'95','电话号码不能为空',false);exit;
        }

        $user_info = D('user')->where("openid = '$openid' and is_delete = 0 ")->find();

        if ($user_info['is_disable'] == '1') {
            echo ajax_api(true, '88', '用户已禁用', false);
            exit;
        }

        if ($user_info) {
            echo ajax_api(true, '100', 'openid已注册', false);
            exit;
        }

        $user_phone = D('user')->where("phone='$phone'")->find();

        if ($user_phone) {
            echo ajax_api(true, '100', '电话号码已注册', false);
            exit;
        }

//        $openid='undefined';
//        $mobile_code='6712';
        $data = array();
        $data['phone'] = $phone;
        $data['code'] = $mobile_code;

        $sms = D('sms')->where($data)->find();

        if(!$sms)
        {
            echo ajax_api(true,'94','验证码无效',false);exit;
        }

        $user = array();
        $user['id'] = build_order_no();
        $user['openid'] = $openid;
        $user['name'] = $name;
        $user['phone'] = $phone;
        $user['address'] = $address;
        $user['account_number'] = $account_number;
        $user['time'] = date('Y-m-d H:i:s');
        $user['img']='2018092097485056';

        $list =D('user')->add($user);

        if($list){
            echo ajax_api(true,'200','注册成功', $user);exit;
        }else{
            echo ajax_api(true,'201','注册失败');exit;
        }
    }
//电话修改
    public function phone_modify() {
        $openid =isset($_GET['openid']) ? $_GET['openid'] : false;
        $mobile_code =isset($_GET['mobile_code']) ? $_GET['mobile_code'] : false;
        $token =isset($_GET['token']) ? $_GET['token'] : false;
        $phone =isset($_GET['phone']) ? $_GET['phone'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if(!$openid){
            echo ajax_api(true,'99','用户信息为空',false);exit;
        }

        if(!$mobile_code){
            echo ajax_api(true,'98','验证码为空',false);exit;
        }

        if(!$phone){
            echo ajax_api(true,'97','电话号码不能为空',false);exit;
        }

        $data = array();
        $data['phone'] = $phone;
        $data['code'] = $mobile_code;

        $phone_proving = D('sms')->where($data)->find();

//        $diff = time() - strtotime($phone_proving['time']);

        if(!$phone_proving)
        {
            echo ajax_api(true,'96','验证验证码无效码为空',false);exit;
        }

        $user = array();

        $user['phone'] = $phone;

        $result = D('user')->data($user)->where("openid='$openid'")->save();

        if($result !== false){
            echo ajax_api(true,'200','修改成功');exit;
        }else{
            echo ajax_api(true,'201','修改失败');exit;
        }
    }

    //资讯
    public function information() {
        $token =isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        $data=array();
        $data['is_delete']='0';
        $data['steta']='1';

        $information=D('information')->where($data)->order("id desc")->select();

        $list = array();

        foreach ($information as $p) {

            $p['logo'] = D('photo')->where("id={$p['img']}")->find();
            $list[] = $p;
        }

        echo ajax_api(true,'200','查询成功',$list);exit;
    }
//主页图片
    public function homepage() {
        $token =isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        $data=array();
        $data['sort']='1';
        $data['is_delete']='0';

        $homepage=D('homepage')->order('orde')->where($data)->select();

        $list = array();

        foreach ($homepage as $p) {
            $p['photo'] = D('photo')->where("id={$p['photo']}")->find();
            $list[] = $p;
        }

        echo ajax_api(true,'200','查询成功',$list);exit;
    }
    //站内信
    public function mail() {
        $token =isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        $data=array();
        $data['is_delete']='0';

        $mail=D('mail')->where($data)->order('time desc')->select();

        $list = array();
        foreach ($mail as $p) {
            $p['time1'] =date('m月d日 G:i ',strtotime($p['time']));
            $list[] = $p;
        }

        echo ajax_api(true,'200','查询成功',$list);exit;
    }

    //党员之家查询
    public function community_partymember() {
        $token =isset($_GET['token']) ? $_GET['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'99','Token error',false);exit;
        }

        $partymember_list=D('community_partymember')->where('is_delete = 0 and steta = 1')->order("id desc")->select();

        $list=array();

        foreach ($partymember_list as $p){
            $p['photo']=D('photo')->where("id={$p['img']}")->find();
            $list[]=$p;
        }

        if($list){
        echo ajax_api(true,'200','查询成功',$list);exit;
        }else{
            echo ajax_api(true,'201','查询失败',$list);exit;
        }
    }

    //共建社区分类查询
    public function community_cooperate_type() {
        $token = isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'99','Token error',false);exit;
        }

        $list=D('community_cooperate_type')->select();

        if($list){
            echo ajax_api(true,'200','查询成功',$list);exit;
        }else{
            echo ajax_api(true,'201','查询失败',$list);exit;
        }
    }

    //共建社区
    public function community_cooperate() {
        $communityCooperateModel = D('community_cooperate');

        $token = isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'99','Token error',false);exit;
        }

        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;
        $type = isset($_POST['type']) ? $_POST['type'] : false;
        $content = isset($_POST['content']) ? $_POST['content'] : false;
        $phone = isset($_POST['phone']) ? $_POST['phone'] : false;
        $img = $_FILES;

        if(!$openid){
            echo ajax_api(true,'95','用户信息为空',false);exit;
        }

        $user = D('user')->where("openid='$openid'")->find();

        if(!$user){
            echo ajax_api(true,'98','用户信息为空',false);exit;
        }
        if($user['is_disable']=='1'){
            echo ajax_api(true,'150','用户被禁用',false);exit;
        }
        if(!$content){
            echo ajax_api(true,'96','内容为空',false);exit;
        }
        if (!$type) {
            echo ajax_api(true, '93', '请选择分类', false);
            exit;
        }
        if (!$phone) {
            echo ajax_api(true, '94', '电话为空', false);
            exit;
        }

        if($img){
            $filename=$img['files']['name'];
            $tmp_name=$img['files']['tmp_name'];
            $ext_name=get_ext($filename);
            $save_path = "/Public/uploads/".build_order_no(). '.'.$ext_name;
            move_uploaded_file($tmp_name, '/var/www/lg_community/'. $save_path);

            $photo = array();
            $photo['id'] = build_order_no();
            $photo['img'] = $save_path;

            D('photo')->add($photo);

            $data = array();

            $data["optid"] = build_order_no();
            $data["openid"] = $openid;
            $data["type"] = $type;
            $data["phone"] = $phone;
            $data["content"] = $content;
            $data["img"] = $photo['id'];
            $data['time']=  date('Y-m-d H:i:s');

            $result = $communityCooperateModel->data($data)->add();

            if ($result) {
                $community_cooperate_loginModel = D("community_cooperate_login");

                $operate = $community_cooperate_loginModel->where("type=1 and is_delete=0 and manage='$type'")->find();

                $msg = "您好, 你有一条待处理的工作, 请点击登录处理http://lgcommunity.webetter100.com/home/cooperate/login?optid={$data["optid"]}";

                //创蓝接口参数
                $postArr = array (
                    'account'  =>  self::API_ACCOUNT,
                    'password' => self::API_PASSWORD,
                    'msg' => urlencode($msg),
                    'phone' => $operate['phone'],
                    'report' => true
                );

                //密码可以使用明文密码或使用32位MD5加密
                $gets = $this->curlPost(self::API_SEND_URL, $postArr);

                echo ajax_api(true,200,'提交成功',$data["optid"]);
            }else{
                echo ajax_api(true,201,'提交失败'. $communityCooperateModel->getLastSql());
            }
        }else{
            $data = array();
            $data["optid"] = build_order_no();
            $data["openid"] = $openid;
            $data["type"] = $type;
            $data["content"] = $content;
            $data["phone"] = $phone;
            $data["img"] = '0';
            $data['time']=  date('Y-m-d H:i:s');

            $result = $communityCooperateModel->data($data)->add();

            if ($result) {
                $community_cooperate_loginModel = D("community_cooperate_login");

                $operate = $community_cooperate_loginModel->where("type=1 and is_delete=0 and manage='$type'")->find();

                $msg = "您好, 你有一条待处理的工作, 请点击登录处理http://lgcommunity.webetter100.com/home/cooperate/login?optid={$data["optid"]}";

                //创蓝接口参数
                $postArr = array (
                    'account'  =>  self::API_ACCOUNT,
                    'password' => self::API_PASSWORD,
                    'msg' => urlencode($msg),
                    'phone' => $operate['phone'],
                    'report' => true
                );

                //密码可以使用明文密码或使用32位MD5加密
                $gets = $this->curlPost(self::API_SEND_URL, $postArr);

//                print_r($operate['phone']);exit;

                echo ajax_api(true,200,'提交成功', $data["optid"]);
            }else{
                echo ajax_api(true,201,'提交失败'. $communityCooperateModel->getLastSql());
            }
        }
    }

    //共建社区多张图片
    public function community_cooperate_img() {
        $token = isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'99','Token error',false);exit;
        }

        $id = isset($_POST['id']) ? $_POST['id'] : false;

        if(!$id){
            echo ajax_api(true,'98','ID获取异常',false);exit;
        }

        $img = $_FILES;

        $filename = $img['files']['name'];
        $tmp_name = $img['files']['tmp_name'];
        $ext_name = get_ext($filename);
        $save_path = "/Public/uploads/".build_order_no(). '.'.$ext_name;
        move_uploaded_file($tmp_name, '/var/www/lg_community/'. $save_path);

        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = $save_path;

        D('photo')->add($photo);

        $item = D('community_cooperate')->where("optid='{$id}'")->find();

        $photo_id = $item['img'];

        $data = array();

        $data['img'] = $photo_id.','.$photo['id'];
//        echo ajax_api(true,201,'添加图片失败', $data); exit;
        $result = D('community_cooperate')->data($data)->where("optid='{$id}'")->save();

        if ($result !== false) {
            echo ajax_api(true,200,'添加图片成功',$item['id']);
        }else{
            echo ajax_api(true,201,'添加图片失败');
        }
    }

    //共建社区信息全查询
    public function community_cooperate_select() {
        $token = isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'99','Token error',false);exit;
        }

        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;

        if(!$openid){
            echo ajax_api(true,'98','openid为空',false);exit;
        }

        $data=array();
        $data['openid']=$openid;
        $data['is_delete']='0';

        $cooperate = D('community_cooperate')->where($data)->order("time desc")->select();

        $list = array();

        foreach ($cooperate as $p){
            $p['cooperate_type']=D('community_cooperate_type')->where("id={$p['type']}")->find();
            $p['time1'] =date('Y年m月d日 G:i ',strtotime($p['time']));
            $p['cooperate_state']=D('community_cooperate_state')->where("id={$p['state']}")->find();

            $list[]=$p;
        }

        if($list){
            echo ajax_api(true,'200','查询成功',$list);exit;
        }else{
            echo ajax_api(true,'201','查询失败',$list);exit;
        }
    }

    //共建社区信息全查询
    public function community_cooperate_find() {
        $token = isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'99','Token error',false);exit;
        }

        $id =isset($_POST['id']) ? $_POST['id'] : false;

        if(!$id){
            echo ajax_api(true,'98','ID为空',false);exit;
        }

//        $id=1223131;
        $data=array();

        $data['optid'] = $id;
        $data['is_delete']='0';

        $p = D('community_cooperate')->where($data)->find();



//        $list=array();

//        foreach ($cooperate as $p){
//            $img = $p['img'];
//            $imgs=explode(',',$img);
//
//            $where='';
//            foreach ($imgs as $i) {
//                if($where!=''){
//                    $where=$where.','."'$i'";
//                }else{
//                    $where=$where."'$i'";
//                }
//            }
//
//            $img_list = D('photo')->where("id in ($where)")->select();
//
//            $p['photo']=$img_list;
//            $openid=$p['openid'];
//            $login=$p['login_id'];
//            $p['cooperate_login']=D('community_cooperate_login')->where("id='$login'")->find();
//            $p['user']=D('user')->where("openid='$openid'")->find();
//            $p['cooperate_type']=D('community_cooperate_type')->where("id={$p['type']}")->find();
//            $p['time1'] =date('Y年m月d日 G:i ',strtotime($p['time']));
//            $p['timea'] =date('Y年m月d日 G:i ',strtotime($p['allot_time']));
//            $p['times'] =date('Y年m月d日 G:i ',strtotime($p['solve_time']));
//            $p['timesc'] =date('Y年m月d日 G:i ',strtotime($p['score_time']));
//            $p['cooperate_state']=D('community_cooperate_state')->where("id={$p['state']}")->find();
//            $list[]=$p;
//        }

        if($p){
            $img = $p['img'];
            $imgs = explode(',',$img);

            $where='';
            foreach ($imgs as $i) {
                if($where!=''){
                    $where=$where.','."'$i'";
                }
                else{
                    $where=$where."'$i'";
                }
            }

            $img_list = D('photo')->where("id in ($where)")->select();

            $p['photo']=$img_list;
            $openid=$p['openid'];
            $login=$p['login_id'];

            $cooperate_login = D('community_cooperate_login')->where("id='$login'")->find();

            if($cooperate_login){
                $p['cooperate_login'] = $cooperate_login;
            }else{
                $p['cooperate_login'] = "";
            }

            $p['user']=D('user')->where("openid='$openid'")->find();
            $p['cooperate_type']=D('community_cooperate_type')->where("id={$p['type']}")->find();

           $logo = $p['img2'];

           if($logo){
               $img2 = D('photo')->where("id = '$logo'")->find();
               $p['img2'] = $img2['img'];
           }else{
               $p['img2'] = '';
           }

            if ($p['name'] != '')
            {
                $oper = D("community_cooperate_login")->where("id={$p['name']}")->find();

                if ($oper)
                    $p['name'] = $oper['name'];
            }

            $state = (int)$p['result'];

            $p['time1'] =date('Y年m月d日 G:i ',strtotime($p['time']));

            if ($state > 1)
                $p['timea'] =date('Y年m月d日 G:i ',strtotime($p['allot_time']));

            if ($state > 3)
                $p['times'] =date('Y年m月d日 G:i ',strtotime($p['solve_time']));

            if ($state > 5)
                $p['timesc'] =date('Y年m月d日 G:i ',strtotime($p['score_time']));

            $p['cooperate_state'] = D('community_cooperate_state')->where("id={$p['state']}")->find();

            $solve_time = $p['solve_time'];

            $p['timecd'] = date("Y年m月d日 G:i",strtotime("+5 day",strtotime("$solve_time")));


            echo ajax_api(true,'200','查询成功',$p);exit;
        }else{
            echo ajax_api(true,'201','查询失败',false);exit;
        }
    }
    //共建社区处理成功评分
    public function community_cooperate_score() {
        $token = isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'99','Token error',false);exit;
        }

        $score = isset($_POST['score']) ? $_POST['score'] : false;

        $id = isset($_POST['id']) ? $_POST['id'] : false;

        if(!$score){
            echo ajax_api(true,'98','评分为空',false);exit;
        }
        if(!$id){
            echo ajax_api(true,'97','ID为空',false);exit;
        }

        $data=array();
        $data['score'] = $score;
        $data['result'] = '5';
        $data['score_time'] = date('Y-m-d H:i:s');
        $data['state'] = '5';
        $data['result'] = 6;

        $result = D('community_cooperate')->data($data)->where("optid='$id'")->save();

        $cooperate = D('community_cooperate')->where("optid='$id'")->find();

        $data['time'] = date('Y年m月d日 G:i ',strtotime($data['score_time']));

        if($result){
            $openid = $cooperate['openid'];

            $user = D("user")->where("openid ='{$openid}'")->find();

            $user_data = array();

            $user_data['integral'] = ((int)$user['integral']) + 20;

            D("user")->where("openid ='{$openid}'")->save($user_data);

            echo ajax_api(true,'200','修改积分成功', $data);exit;
        }else{
            echo ajax_api(true,'201','修改积分失败');exit;
        }
    }

    //共建社区处理失败
    public function community_cooperate_fail(){
        $community_cooperateModel = D('community_cooperate');

        $token = isset($_POST['token']) ? $_POST['token'] : false;
        $id = isset($_POST['id']) ? $_POST['id'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }
        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'99','Token error',false);exit;
        }

        if(!$id){
            echo ajax_api(true,'98','ID为空',false);exit;
        }

        $item = $community_cooperateModel->where("optid = '$id'")->find();
        $type =  $item['type'];

        $data=array();
        $data["optid"] = build_order_no();
        $data['openid']=$item['openid'];
        $data['type'] = $type;
        $data['img'] = $item['img'] ;
        $data['time'] = date('Y-m-d H:i:s');
        $data['content'] = $item['content'];
        $data['oid_id'] = $item['optid'];

        $cooperate = $community_cooperateModel->data($data)->add();

        if ($cooperate) {
            $community_cooperate_loginModel = D("community_cooperate_login");

            $operate = $community_cooperate_loginModel->where("type=1 and is_delete=0 and manage='$type'")->find();

            $msg = "您好, 你有一条待处理的工作, 请点击登录处理http://lgcommunity.webetter100.com/home/cooperate/login?optid={$data["optid"]}";

            //创蓝接口参数
            $postArr = array(
                'account' => self::API_ACCOUNT,
                'password' => self::API_PASSWORD,
                'msg' => urlencode($msg),
                'phone' => $operate['phone'],
                'report' => true
            );

            //密码可以使用明文密码或使用32位MD5加密
            $gets = $this->curlPost(self::API_SEND_URL, $postArr);

            echo ajax_api(true,200,'提交成功', $data);
        }else{
            echo ajax_api(true,201,'提交失败' );
        }
    }

    //义工号修改
    public function user_account_number(){
        $userModel = D('user');
        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;
        $account_number = isset($_POST['account_number']) ? $_POST['account_number'] : false;
        $token = isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$openid)
        {
            echo ajax_api(true,'100','openid获取异常',false);exit;
        }

        if(!$account_number)
        {
            echo ajax_api(true,'100','义工号获取异常',false);exit;
        }

        $list = $userModel->where("openid = '$openid'")->find();

        if($list['account_number']==$account_number)
        {
            echo ajax_api(true,'100','义工号不能重复',false);exit;
        }
        $data = array();

        $data['account_number'] = $account_number;

        D('user')->data($data )->where('id=' . $list['id'])->save();

        echo ajax_api(true,'200','修改成功');exit;
    }

    //外联大学
    public function university() {
        $universityModel = D('university');

        $token =isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        $data=array();
        $data['is_delete']='0';

        $university = $universityModel->where($data)->order('time desc')->select();

        $list = array();

        foreach ($university as $p) {

            $p['img'] = D('photo')->where("id={$p['img']}")->find();
            $list[] = $p;
        }

        echo ajax_api(true,'200','查询成功',$list);exit;
    }

  //社团类型
    public function lgteamlist_type() {
        $lgteamlist_typeModel=D('lgteamlist_type');
        $token =isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        $data=array();
        $data['is_delete']='0';

        $lgteamlist_type = $lgteamlist_typeModel->where($data)->select();

        $list = array();

        foreach ($lgteamlist_type as $p) {

            $p['img'] = D('photo')->where("id={$p['img']}")->find();
            $list[] = $p;
        }

        echo ajax_api(true,'200','查询成功',$list);exit;
    }

//社团列表
    public function lgteams() {
        $lgteamlist_typeModel=D('lgteamlist_type');
        $lgteamsModel = D('lgteams');

        $token =isset($_POST['token']) ? $_POST['token'] : false;
        $type =isset($_POST['type']) ? $_POST['type'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$type)
        {
            echo ajax_api(true,'100','类型异常',false);exit;
        }

        $data=array();
        $data['is_delete']='0';
        $data['type']=$type;

        $lgteams = $lgteamsModel->where($data)->order('time desc')->select();

        $list = array();

        foreach ($lgteams as $p) {
            $p['type'] = $lgteamlist_typeModel->where("id={$p['type']}")->find();
            $p['img'] = D('photo')->where("id={$p['img']}")->find();
            $list[] = $p;
        }

        echo ajax_api(true,'200','查询成功',$list);exit;
    }

    //爱心企业/地图
    public function aixinqiye() {
        $aixinqiyeModel=D('aixinqiye');

        $token =isset($_POST['token']) ? $_POST['token'] : false;
//        $lng =isset($_POST['lng']) ? $_POST['lng'] : false;
//        $lat = isset($_POST['lat']) ? $_POST['lat'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }



        $data=array();
        $data['is_delete']='0';

        $aixinqiye = $aixinqiyeModel->where($data)->order("id desc")->select();


//        if(!$lng){
//            echo ajax_api(true,'100','经纬度无法获取',$aixinqiye);exit;
//        }
//        if(!$lat){
//            echo ajax_api(true,'100','经纬度无法获取',$aixinqiye);exit;
//        }

//        $list = array();
//
//        foreach ($aixinqiye as $p) {
//            $p['img'] = D('photo')->where("id={$p['img']}")->find();
//            $p['distance']=getDistance($p["latitude"],$p["longitude"],$lng,$lat);
//
//            $mi=$p['distance'];
//
//            if($mi>1000){
//
//                $p['distance'] =  sprintf("%.1f", $mi / 1000);
//
//                $p['unit'] = 'km';
//
//            }else{
//
//                $p['distance']=$mi;
//
//                $p['unit'] = 'm';
//
//            }
//
//            $list[] = $p;
//        }

        echo ajax_api(true,'200','查询成功',$aixinqiye);exit;
    }

//爱心企业
    public function aixinqiye_home() {
        $aixinqiyeModel=D('aixinqiye');

        $token =isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        $data=array();
        $data['is_delete']='0';

        $aixinqiye = $aixinqiyeModel->where($data)->order("id desc")->select();


        $list = array();

        foreach ($aixinqiye as $p) {
            $p['img'] = D('photo')->where("id={$p['img']}")->find();
            $list[] = $p;
        }

        echo ajax_api(true,'200','查询成功',$list);exit;
    }

//住址
    public function user_address(){
        $userModel=D('user');
        $openid =isset($_POST['openid']) ? $_POST['openid'] : false;
        $address =isset($_POST['address']) ? $_POST['address'] : false;
        $token =isset($_POST['token']) ? $_POST['token'] : false;

        if(!$token){
            echo ajax_api(true,'100','Error Token',false);exit;
        }

        if($token!=C("COMMON_TOKEN")){
            echo ajax_api(true,'100','Token error',false);exit;
        }

        if(!$openid)
        {
            echo ajax_api(true,'100','openid获取异常',false);exit;
        }

        if(!$address)
        {
            echo ajax_api(true,'100','义工号获取异常',false);exit;
        }

        $list=$userModel->where("openid='$openid'")->find();

        $data = array();

        $data['address'] =$address;

        D('user')->data($data)->where('id=' . $list['id'])->save();

        echo ajax_api(true,'200','修改成功');exit;
    }

    public function  oid(){
        $token = isset($_POST['token']) ? $_POST['token'] : false;
        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;

        if (!$token) {
            echo ajax_api(true, '100', 'Error Token', false);
            exit;
        }
        if ($token != C("COMMON_TOKEN")) {
            echo ajax_api(true, '99', 'Token error', false);
            exit;
        }

        if(!$openid){
            echo ajax_api(true,'90','用户信息获取异常',false);exit;
        }

        $user = D('user')->where("openid='$openid'")->find();

        if (!$user) {
            echo ajax_api(true, '91', '用户信息为空', false);
            exit;
        }
    }

    public function  activity_Print(){
        $token = isset($_POST['token']) ? $_POST['token'] : false;

        if (!$token) {
            echo ajax_api(true, '100', 'Error Token', false);
            exit;
        }
        if ($token != C("COMMON_TOKEN")) {
            echo ajax_api(true, '99', 'Token error', false);
            exit;
        }

        $active_pictureModel= D('active_picture');
        $photoModel = D('photo');

        $item = $active_pictureModel->where('id')->order("id desc")->find();

        $id = $item['name'];

        $item['name'] = $photoModel->where("id='$id'")->select();

        if($item){
            echo ajax_api(true, '200', '查询成功', $item);
        }else{
            echo ajax_api(true, '201', '查询失败', '');
        }
    }

    public function  picture(){
        $pictureModel = D('picture');
        $photoModel = D('photo');

        $token = isset($_POST['token']) ? $_POST['token'] : false;

        if (!$token) {
            echo ajax_api(true, '100', 'Error Token', false);
            exit;
        }
        if ($token != C("COMMON_TOKEN")) {
            echo ajax_api(true, '99', 'Token error', false);
            exit;
        }

        $item = $pictureModel->where('id')->order("id desc")->find();

        $id = $item['name'];

        $item['name']=$photoModel->where("id='$id'")->select();

        if($item){
            echo ajax_api(true, '200', '查询成功', $item);
        }else{
            echo ajax_api(true, '201', '查询失败', '');
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

    public function imgb(){
        $mail_imgModel = D('mail_img');
        $photoModel = D('photo');

        $token = isset($_POST['token']) ? $_POST['token'] : false;

        if (!$token) {
            echo ajax_api(true, '100', 'Error Token', false);
            exit;
        }
        if ($token != C("COMMON_TOKEN")) {
            echo ajax_api(true, '99', 'Token error', false);
            exit;
        }

        $item = $mail_imgModel->where('id')->order("id desc")->find();

        $id = $item['name'];

        $item['name']=$photoModel->where("id='$id'")->select();

        if($item){
            echo ajax_api(true, '200', '查询成功', $item);
        }else{
            echo ajax_api(true, '201', '查询失败', '');
        }
    }

    //现场报名
    public function scanning(){
        $community_activityModel = D('community_activity');
        $community_sceneModel = D('community_scene');
        $community_activity_personnelModel = D('community_activity_personnel');
        $userModel = D('user');

        $token = isset($_POST['token']) ? $_POST['token'] : false;
        $id = isset($_POST['id']) ? $_POST['id'] : false;
        $openid = isset($_POST['openid']) ? $_POST['openid'] : false;

        if (!$token) {
            echo ajax_api(true, '100', 'Error Token', false);
            exit;
        }
        if ($token != C("COMMON_TOKEN")) {
            echo ajax_api(true, '100', 'Token error', false);
            exit;
        }
        if (!$id) {
            echo ajax_api(true, '100', 'Error Token', false);
            exit;
        }
        if (!$openid) {
            echo ajax_api(true, '100', 'Error Token', false);
            exit;
        }

        $activity = $community_activityModel->where("id = '$id'")->find();

        $scene = $community_sceneModel->where("id = '$id'")->find();

        if (!$activity && !$scene) {
            echo ajax_api(true, '197', '二维码无效', false);
            exit;
        }

        $activity_personnel = $community_activity_personnelModel->where(" openid = '$openid'and activity_id = '$id'")->find();

        if($activity_personnel['state1'] == 1){
            echo ajax_api(true, 198, '您已签到');exit;
        }

        if($activity_personnel['state1'] == 2){
            echo ajax_api(true, 202, '您未参加此活动');exit;
        }

        $list = array();
        $list['state1'] = 1 ;

        $activity_personnel2 = $community_activity_personnelModel->where(" openid = '$openid'and activity_id = '$id'")->data($list)->save();

        if(!$activity_personnel2){
            echo ajax_api(true, 199, '您未参加此活动');exit;
        }

        $item1 = $userModel->where("openid = '$openid'")->find();
        $state = $activity['integral_state'];

        $state1 = $scene['integral_state'];

        if($state1 == 1){
            $data = array();
            $data['integral'] = $item1['integral'] - $scene['integral'];

            $userModel->where("openid = '$openid'")->data($data)->save();

            $data1 = array();
            $data1['actual_number'] = $data1['actual_number'] + 1;
            $community_sceneModel->where("id = '$id'")->data($data1)->save();

            echo ajax_api(true, 200, '签到成功', $scene['integral']);exit;
        }

        if($state1 == 2) {
            $data = array();
            $data['integral'] = $item1['integral'] + $scene['integral'];
            $userModel->where("openid = '$openid'")->data($data)->save();

            $data1 = array();
            $data1['actual_number'] = $data1['actual_number'] + 1;
            $community_sceneModel->where("id = '$id'")->data($data1)->save();

            echo ajax_api(true, 201, '签到成功', $scene['integral']);exit;
        }

        if($state == 1){
            $data = array();
            $data['integral'] = $item1['integral'] - $activity['integral'];
            $userModel->where("openid = '$openid'")->data($data)->save();
            echo ajax_api(true, 200, '签到成功', $activity['integral']);exit;
        }

        if($state == 2) {
            $data = array();
            $data['integral'] = $item1['integral'] + $activity['integral'];
            $userModel->where("openid = '$openid'")->data($data)->save();
            echo ajax_api(true, 201, '签到成功', $activity['integral']);exit;
        }


    }

    public function communitymapQuery2(){
        $community_mapModel = D('community_map');

        $token = isset($_POST['token']) ? $_POST['token'] : false;
        if (!$token) {
            echo ajax_api(true, '100', 'Error Token', false);
            exit;
        }
        if ($token != C("COMMON_TOKEN")) {
            echo ajax_api(true, '100', 'Token error', false);
            exit;
        }

        $lng = isset($_POST['lng']) ? $_POST['lng'] : false;
        $lat = isset($_POST['lat']) ? $_POST['lat'] : false;


        $data = array();

        $data['is_delete'] = '0';

        $community_list = $community_mapModel->where($data)->select();

        if (!$lng) {
            echo ajax_api(true, '100', '经纬度无法获取', $community_list);
            exit;
        }
        if (!$lat) {
            echo ajax_api(true, '100', '经纬度无法获取', $community_list);
            exit;
        }

        $list = array();

        foreach ($community_list as $m) {
            $m['distance'] = getDistance($m["latitude"], $m["longitude"], $lat, $lng);

            $mi=$m['distance'];
            if($mi>1000){
                $m['distance'] =  sprintf("%.1f", $mi / 1000);
                $m['unit'] = 'km';
            }else{
                $m['distance']=$mi;
                $m['unit'] = 'm';
            }

            $list[] = $m;
        }

        if ($community_list) {
            echo ajax_api(true, 200, '查询成功', $list);
        } else {
            echo ajax_api(true, 201, '查询失败');
        }
    }

    public function aixinqiye2() {
        $aixinqiyeModel=D('aixinqiye');

        $token = isset($_POST['token']) ? $_POST['token'] : false;
        if (!$token) {
            echo ajax_api(true, '100', 'Error Token', false);
            exit;
        }
        if ($token != C("COMMON_TOKEN")) {
            echo ajax_api(true, '100', 'Token error', false);
            exit;
        }

        $lng = isset($_POST['lng']) ? $_POST['lng'] : false;
        $lat = isset($_POST['lat']) ? $_POST['lat'] : false;


        $data = array();

        $data['is_delete'] = '0';

        $community_list = $aixinqiyeModel->where($data)->select();

        if (!$lng) {
            echo ajax_api(true, '100', '经纬度无法获取', $community_list);
            exit;
        }
        if (!$lat) {
            echo ajax_api(true, '100', '经纬度无法获取', $community_list);
            exit;
        }

        $list = array();

        foreach ($community_list as $m) {
            $m['distance'] = getDistance($m["latitude"], $m["longitude"], $lat, $lng);

            $mi=$m['distance'];
            if($mi>1000){
                $m['distance'] =  sprintf("%.1f", $mi / 1000);
                $m['unit'] = 'km';
            }else{
                $m['distance']=$mi;
                $m['unit'] = 'm';
            }

            $list[] = $m;
        }

        if ($community_list) {
            echo ajax_api(true, 200, '查询成功', $list);
        } else {
            echo ajax_api(true, 201, '查询失败');
        }

    }

}
