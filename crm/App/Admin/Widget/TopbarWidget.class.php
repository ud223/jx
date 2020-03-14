<?php
namespace Admin\Widget;
use Think\Controller;

class TopbarWidget extends Controller {
    public function index(){
        $sAdminName = get_s_name();
        $HeadImg = json_decode(get_s_headimg(), true);
        
        if(empty($HeadImg)){
            $sAdmHead = "res/admin/assets/img/avatar_small.png";
        }else{
            $sAdmHead = "{$HeadImg[0]["thumb_path"]}thumb_29_29_{$HeadImg[0]["image"]}";
        }

        $arrAdmInfo = array(
            "id"=>  get_s_id(),
            "name"=> get_s_name(),
            "headimg"=>  $sAdmHead,
            "groupid"=>  get_s_gid(),
            "groupname"=>  get_s_gname(),
            "ip"=>  get_s_ip(),
            "date"=>  get_s_date(),
        );

        $this->assign('adminfo',$arrAdmInfo);
        $this->display('Topbar:index');
    }
}