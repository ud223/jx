<?php
namespace Admin\Controller;
use Think\Controller;

class AdminController extends CommonController {
    //页面模型
    private $PageModel = "AdminUser";

    public function user(){
        
    }
    
    //修改管理员账号信息
    public function user_edit(){        
        
    }

    public function user_add(){
        
    }
    
    //删除管理员
    public function user_del(){
        $sIds = param("delid");
        $this->DelFromIDReturn($sIds, "auth_admin", "id");
        $this->DelFromID($sIds, "auth_group_access", "uid");
    }
}

