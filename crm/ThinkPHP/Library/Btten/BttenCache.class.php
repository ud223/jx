<?php
namespace Btten;

/**
 * 系统缓存
 */
class BttenCache {
    protected $config = array(
        "groupid"=>0,
    );
    
    /**
     * 架构方法 设置参数
     * @access public     
     * @param  array $config 配置参数
     */    
    public function __construct($cfg=array()){
        if(empty($cfg)){
            $cfg["groupid"] = get_s_gid();
        }else{
            if(!is_numeric($cfg["groupid"])){
                $cfg["groupid"] = get_s_gid();
            }
        }
        
        $cfg["system_cache_key"] = "menu/leftmenu_{$cfg["groupid"]}";
        
        $this->config = array_merge($this->config, $cfg);
    }
    
    //公用方法--------------------------------------------------------------------------------------
     /**
     * 删除所有系统缓存
     * @access public     
     * @param  array $config 配置参数
     */  
    public function DelSystemCache($_key=""){
        if(!empty($_key)){
            F($_key, NULL);
            return;
        }
        
        $arrConfig = C('CACHE_CONFIG');
        $nCount = sizeof($arrConfig);
        
        foreach ($arrConfig as $key => $value) {
            F($value, NULL);
        }
    }

    //左侧系统栏目菜单--------------------------------------------------------------------------------------
    
    /**
     * 获取系统菜单
     * @access public     
     * @param  array $config 配置参数
     */  
    public function GetSystemNav(){
        return $this->SystemNavHtml();
    }

    /**
     * 同步系统菜单数据
     * @access public     
     * @param  array $config 配置参数
     */  
    public function SyncSystenNav(){
        $model = M("auth_group");
        $GroupNav = $model->field("menuids")->where("id={$this->config["groupid"]}")->find();
        
        $model = M("system_category");
        $NavList = $model->where("menuid in ({$GroupNav["menuids"]})")->select();
        
        $nCount = sizeof($NavList);
        $arrMainMenu = array();

        for($i=0; $i<$nCount; $i++){
            if((int)$NavList[$i]["parentid"] === 1){
                array_push($arrMainMenu, $NavList[$i]);
            }else{
                continue;
            }
        }

        $nMainCount = sizeof($arrMainMenu);

        for($i=0; $i<$nMainCount; $i++){
            $arrSubMenu = array();

            for($k=0; $k<$nCount; $k++){
                if((int)$NavList[$k]["parentid"] === 1){
                    continue;
                }else{
                    if($NavList[$k]["parentid"] == $arrMainMenu[$i]["menuid"]){
                        array_push($arrSubMenu, $NavList[$k]);
                    }else{
                        continue;
                    }
                }
            }

            $arrMainMenu[$i]["sub"] = $arrSubMenu;
        }
        
        $this->SetNavCache($this->config["system_cache_key"], $arrMainMenu);

        return $arrMainMenu;
    }
    
    //设置系统菜单缓存
    private function SetNavCache($_CacheKey, $_CacheContent){
        if(empty($_CacheKey)){
            return;
        }
        
        F($_CacheKey,NULL);
        F($_CacheKey,$_CacheContent);
    }
    
    //获取系统菜单缓存
    private function GetNavCache($_CacheKey){   
        $arrMenu = F($_CacheKey);

        if(empty($arrMenu)){
            $arrMenu = $this->SyncSystenNav();
        }

        return $arrMenu;
    }
    
    //设置系统菜单HTML代码
    private function SystemNavHtml(){
        $_mainid = param("navmid");
        $_subid = param("navsid");
        $_mainappid = param("navappmid");
        $_subappid = param("navappsid");
        
        $arrHtml = array();
        
        array_push($arrHtml, '<li style="height:1px; font-size:0; margin:0; padding:0;"></li>');
        
        //如果没有栏目id，则把系统首页设为激活状态
        if(empty($_mainid) && empty($subid) && empty($_mainappid) && empty($_subappid)){
            array_push($arrHtml, '<li class="active">');
        }else{
            array_push($arrHtml, '<li class="">');
        }
        
        array_push($arrHtml, '<a href="' . __MODULE__ . '/Index/index">');
        array_push($arrHtml, '<i class="icon-home"></i> ');
        array_push($arrHtml, '<span class="title">系统首页</span>');
        array_push($arrHtml, '</a>');
        array_push($arrHtml, '</li>');

        $NavList = $this->GetNavCache($this->config["system_cache_key"]);
        
        if(!empty($NavList)){
            $nCount = sizeof($NavList);
            for($i=0; $i<$nCount; $i++){
                if($NavList[$i]["status"] == 1){
                    continue;
                }

                //设定父栏目激活状态
                if($_mainid == $NavList[$i]["menuid"]){
                    $sCssName = "active";
                }else{
                    $sCssName = "";
                }

                //父栏目 Begin
                array_push($arrHtml, '<li class="'. $sCssName .'">');
                array_push($arrHtml, '<a href="javascript:;">');
                array_push($arrHtml, '<span class="title">'. $NavList[$i]["menu_name"] .'</span>');
                array_push($arrHtml, '<span class="arrow "></span>');
                array_push($arrHtml, '</a>');

                //子栏目 Begin
                if((int)$NavList[$i]["child"] > 0){
                    array_push($arrHtml, '<ul class="sub-menu">');

                    $SubMenuList = $NavList[$i]["sub"];
                    $nSubCount = sizeof($SubMenuList);

                    for($j=0; $j<$nSubCount; $j++){
                        if($SubMenuList[$j]["status"] == 1){
                            continue;
                        }

                        //设定子栏目激活状态
                        if($_subid == $SubMenuList[$j]["menuid"]){
                            $sCssName = "active";
                        }else{
                            $sCssName = "";
                        }

                        array_push($arrHtml, '<li class="'. $sCssName .'">');

                        //设置栏目链接
                        if($SubMenuList[$j]["url"] == "#" || $SubMenuList[$j]["url"] == "javascript:void(0);"){
                            $sChildMenuUrl = $SubMenuList[$j]["url"];
                        }else{
                            $sChildMenuUrl = __MODULE__ . '/' . $SubMenuList[$j]["url"] . '/navmid/' . $SubMenuList[$j]["parentid"] . '/navsid/' . $SubMenuList[$j]["menuid"];
                        }

                        array_push($arrHtml, '<a href="' . $sChildMenuUrl .'">'. $SubMenuList[$j]["menu_name"] .'</a>');
                        array_push($arrHtml, '</li>');
                    }

                    array_push($arrHtml, '</ul>');
                }
                //子栏目 End

                array_push($arrHtml, '</li>');
                //父栏目End

            }
        }
        
        $sHtml = join('',$arrHtml);

        return $sHtml;
    }
    
    //左侧应用栏目菜单--------------------------------------------------------------------------------------
        
    public function GetAppMenu(){
        return $this->AppMenuHtml();
    }
    
    public function SyncAppMenu(){
        F($this->GetAppMenuCacheDir(), NULL);

        $arrReturn = array();

        $model = M("app_menu");
        $ParentMenuList = $model->where('`parentid` = 1 and `status` = 0')->select();
        $nParentCount = sizeof($ParentMenuList);

        for($i=0; $i<$nParentCount; $i++){
            $SubMenuList = $model->where("`parentid` = {$ParentMenuList[$i]["menuid"]} and `status` = 0")->select();
            
            $ParentMenuList[$i]["sub"] = $SubMenuList;
            
            $nSubCount = sizeof($SubMenuList);

            for($k=0; $k<$nSubCount; $k++){
                
            }
        }
        
        $this->SetNavCache($this->GetAppMenuCacheDir(), $ParentMenuList);
        return $ParentMenuList;
    }   
    
    private function AppMenuHtml(){
        $_mainid = param("navappmid");
        $_subid = param("navappsid");
        
        $arrHtml = array();

        $NavList = $this->GetAppMenuCache($this->GetAppMenuCacheDir());
        
        if(!empty($NavList)){
            $nCount = sizeof($NavList);
            for($i=0; $i<$nCount; $i++){
                if($NavList[$i]["status"] == 1){
                    continue;
                }
                
                //设定父栏目激活状态
                if($_mainid == $NavList[$i]["menuid"]){
                    $sCssName = "active";
                }else{
                    $sCssName = "";
                }

                //子栏目 Begin
                if((int)$NavList[$i]["child"] > 0){
                    //父栏目 Begin
                    array_push($arrHtml, '<li class="'. $sCssName .'">');
                    array_push($arrHtml, '<a href="javascript:;">');
                    array_push($arrHtml, '<span class="title">'. $NavList[$i]["menu_name"] .'</span>');
                    array_push($arrHtml, '<span class="arrow "></span>');
                    array_push($arrHtml, '</a>');
                
                    array_push($arrHtml, '<ul class="sub-menu">');

                    $SubMenuList = $NavList[$i]["sub"];
                    
                    $nSubCount = sizeof($SubMenuList);

                    for($j=0; $j<$nSubCount; $j++){
                        if($SubMenuList[$j]["status"] == 1){
                            continue;
                        }

                        //设定子栏目激活状态
                        if($_subid == $SubMenuList[$j]["menuid"]){
                            $sCssName = "active";
                        }else{
                            $sCssName = "";
                        }

                        array_push($arrHtml, '<li class="'. $sCssName .'">');

                        //设置栏目链接
                        if($SubMenuList[$j]["url"] == "#" || $SubMenuList[$j]["url"] == "javascript:void(0);"){
                            $sChildMenuUrl = $SubMenuList[$j]["url"];
                        }else{
                            $sChildMenuUrl = __MODULE__ . '/'. $SubMenuList[$j]["url"] .'/navappmid/' . $SubMenuList[$j]["parentid"] . '/navappsid/' . $SubMenuList[$j]["menuid"];
                        }

                        array_push($arrHtml, '<a href="' . $sChildMenuUrl .'">'. $SubMenuList[$j]["menu_name"] .'</a>');
                        array_push($arrHtml, '</li>');
                    }

                    array_push($arrHtml, '</ul>');
                }else{
                    //设置栏目链接
                    if($NavList[$i]["url"] == "#" || $NavList[$i]["url"] == "javascript:void(0);"){
                        $sParentMenuUrl = $NavList[$i]["url"];
                    }else{
                        $sParentMenuUrl = __MODULE__ . '/'. $NavList[$i]["url"] .'/navappmid/' . $NavList[$i]["menuid"];
                    }

                    array_push($arrHtml, '<li class="'. $sCssName .'">');
                    array_push($arrHtml, '<a href="' . $sParentMenuUrl . '">');
                    array_push($arrHtml, '<span class="title">'. $NavList[$i]["menu_name"] .'</span>');
                    array_push($arrHtml, '</a>');
                    array_push($arrHtml, '</li>');
                }
                //子栏目 End

                //array_push($arrHtml, '</li>');
                //父栏目End

            }
        }
        
        $sHtml = join('',$arrHtml);

        return $sHtml;
    }

    private function GetAppMenuCache($_CacheKey){   
        $arrMenu = F($_CacheKey);

        if(empty($arrMenu)){
            $arrMenu = $this->SyncAppMenu();
        }

        return $arrMenu;
    }
    
    private function GetAppMenuCacheDir(){
        return C('CACHE_CONFIG.LEFT_APP_MENU_DIR');
    }
    
}
