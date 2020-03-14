<?php
namespace Admin\Widget;
use Think\Controller;

class NavWidget extends Controller {
    /*
     * 创建左侧导航栏目
     * @param int $_mainid 父栏目ID
     * @param int $subid 子栏目ID
    */
    public function leftmenu(){
        $BttenCache = new \Btten\BttenCache();
        $Menu = $BttenCache->GetSystemNav();
        $AppMenu = $BttenCache->GetAppMenu();
        $NavAppmid = param("navappmid");
        $this->assign('menu',$Menu);
        $this->assign('AppMenu',$AppMenu);
        $this->assign('navappmid',$NavAppmid);
        $this->display('Nav:leftmenu');
    }
    
    /*
     * 创建页面主体部分的头部导航路径节点
    */
    public function menuroot(){
        $_mainid = param("navmid");
        $_subid = param("navsid");
        $_mainappid = param("navappmid");
        $_subappid = param("navappsid");

        $model = M("system_category");
        $MenuList = $model->where("menuid={$_mainid} or menuid={$_subid}")->order("left_val")->select();
        
        if(empty($MenuList)){
            $model = M("app_menu");
            $sWhere = "";
            
            if(!empty($_subappid)){
                $sWhere = "menuid={$_mainappid} or menuid={$_subappid}";
            }else{
                $sWhere = "menuid={$_mainappid}";
            }
            
            $MenuList = $model->where($sWhere)->order("left_val")->select();
        }
        
        $page_title = "系统首页";
        
        $main_name = $MenuList[0]["menu_name"];
        $sub_name = $MenuList[1]["menu_name"];
        
        if(!empty($sub_name)){
            $page_title = $sub_name;
        }else{
            $page_title = $main_name;
        }
        
        $this->assign('main_name',$main_name);
        $this->assign('sub_name',$sub_name);
        $this->assign('page_title',$page_title);
        $this->display('Nav:menuroot');
    }
}