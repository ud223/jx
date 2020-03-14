<?php
namespace Admin\Controller;
use Think\Controller;

class UserAddressController extends CommonController {
    //页面模型
    private $PageModel = "UserAddress";
    
    public function index(){
        
    }
    
    public function add(){
        
    }
    
    public function edit(){
        
    }
    
    public function del(){
        $sIds = param("delid");
        $this->DelFromID($sIds, "user_address", "id");
    }
}

