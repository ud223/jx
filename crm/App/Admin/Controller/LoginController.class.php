<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller {
    public function index(){
        $sAuthKey = get_s_auth();

        if(empty($sAuthKey)){
            $this->display();
        }else{
            $this->redirect('Index/index');
        }
    }
    
    public function login(){
        $sUserName = param("username");
        $sPassword = param("password");
        $sCode = param("code");
        
        $arrErrMsg = array();
        
        $Result = $this->CheckVerify($sCode);
        
        if(!$Result){
            array_push($arrErrMsg, "<p>验证码错误</p>");
        }
        
        if(empty($sUserName)){
            array_push($arrErrMsg, "<p>用户名为空</p>");
        }
        
        if(empty($sPassword)){
            array_push($arrErrMsg, "<p>密码为空</p>");
        }
        
        if(!empty($arrErrMsg)){
            ErrorReturn(join("",$arrErrMsg));
        }
        
        $sPassword = md5($sPassword);
        $sField = "a.`id` as `admid`, a.`username`, a.`truename`, a.`headimg`, a.`loginip`, a.`logintime`, a.`status` as `admstatus`, g.`id` as `groupid`, g.`title`, g.`status` as `groupstatus`";
        $model = M("auth_admin as a");
        $UserInfo = $model->join(array("btten_auth_group_access as ga on a.`id` = ga.`uid`","btten_auth_group as g on ga.`group_id` = g.`id`"))->field($sField)->where("a.`username` = '%s' and a.`pwd` = '%s'", $sUserName,$sPassword)->find();
        
        if(empty($UserInfo)){
            ErrorReturn("用户名或密码错误");
        }else{
            //判断用户状态和用户所在组状态，是否被锁定
            if($UserInfo["admstatus"] != 1 || $UserInfo["groupstatus"] != 1){
                ErrorReturn("帐号被锁定，请与管理员联系。");
            }
        }
        
        //设置登录用户的信息到Session中 Begin
        set_s_id($UserInfo["admid"]);
        set_s_name($UserInfo["username"]);
        set_s_gid($UserInfo["groupid"]);
        set_s_gname($UserInfo["title"]);
        set_s_headimg($UserInfo["headimg"]);
        set_s_ip($UserInfo["loginip"]);
        set_s_date($UserInfo["logintime"]);
        //设置登录用户的信息到Session中 End
        
        $sLoginIP = get_client_ip();
        
        $arrData = array(
            'id' => $UserInfo["admid"],
            'loginip' => $sLoginIP,
            'logintime' => time()
        );
        
        $model = M("auth_admin");
        $addResult = $model->save($arrData);
        
        SuccessReturn("登陆成功。");
    }
    
    public function logout(){
        set_s_auth(null);
        set_s_date(null);
        set_s_gid(null);
        set_s_gname(null);
        set_s_headimg(null);
        set_s_id(null);
        set_s_ip(null);
        set_s_name(null);

        SuccessReturn("退出成功");
    }
    
    public function CreateVerify(){
        $arrVerifyConfig = C('VERIFY');
        $Verify = new \Think\Verify($arrVerifyConfig);
        
        //不加这个，验证码可能会显示不了
        ob_clean(); 
        
        $Verify->entry();
    }
    
    //检查验证码
    private function CheckVerify($code){
        $arrVerifyConfig = C('VERIFY');
        $Verify = new \Think\Verify($arrVerifyConfig);
        
        return $Verify->check($code);
    }
}