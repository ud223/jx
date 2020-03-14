<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller {
    protected $_PageTitle = ""; //页面标题
    protected $_PageCustomBtn = array();   //页面自定义功能按钮配置
    protected $_PageListAddUrl = "";   //列表页面的数据修改地址
    protected $_PageListEditUrl = "";   //列表页面的数据修改地址
    protected $_PageListDeleteUrl = "";   //列表页面的数据删除地址
    protected $_PageSearchUrl = "";   //列表页面搜索提交的地址
    protected $_PostUrl = "";   //页面表单提交地址
    protected $_PostBackUrl = "";
    protected $_MenuAddChild = false;   //能否添加多余2级的子菜单

    protected $_AdmStatus = array("禁用", "正常"); //状态字符
    protected $_Status = array("正常", "禁用"); //状态字符
    protected $_NewsStatus = array("已审核", "未审核", "禁用"); //新闻状态字符
    protected $_IsTop = array("正常", "置顶"); //置顶字符
    protected $_IsHead = array("正常", "头条"); //头条字符
    
    function _initialize() {
        $nSessionUID = get_s_id();
        $sReturlUrl = __MODULE__ . '/Login';

        //$nSessionUID = "";
        if(empty($nSessionUID)){
            $this->error('你还没有登录，无法登陆系统！',$sReturlUrl);
           // exit('<script>alertMsg.warn("你没有访问此页面的权限"); navTab.closeCurrentTab();</script>');
        }
        
        // 用户权限检查
        $Auth = new \Think\Auth();  
  
        //需要验证的规则列表,支持逗号分隔的权限规则或索引数组  
        $name = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;  
        
        //当前用户id  
        $uid = $nSessionUID;

        //分类  
        $type = MODULE_NAME;  
  
        //执行check的模式  
        $mode = 'url';  
  
        //'or' 表示满足任一条规则即通过验证;  
        //'and'则表示需满足所有规则才能通过验证  
        $relation = 'and';  

//        if (!$Auth->check($name, $uid, $type, $mode, $relation)) {
//            return $this->error('你没有访问此页面的权限！',$_SERVER['HTTP_REFERER']);
//        }
        
        set_s_auth($uid);
    }
    
    /**
     * 栏目列表页面模板
     */
    public function displayCategoryList($listData = array()) {
        //页面标题
        $this->assign("pagetitle", $this->_PageTitle);
        
        //页面按钮
        $this->assign("BtnCustom", $this->_PageCustomBtn);
        
        //列表页数据修改Url
        $this->assign("PageListAddUrl", $this->_PageListAddUrl);
        
        //列表页数据修改Url
        $this->assign("PageListEditUrl", $this->_PageListEditUrl);
        
        //列表页数据删除Url
        $this->assign("PageListDeleteUrl", $this->_PageListDeleteUrl);
        
        //能否添加多余2级的子菜单
        $this->assign("MenuAddChild", $this->_MenuAddChild);
        
        //列表数据
        $this->assign("listdata", $listData);
        
        $this->display('Template:CategoryList');
    }
    
    /**
     * 栏目添加页面
     */
    public function displayCategoryAdd($_cfg=array()) {
        //页面标题
        $this->assign("pagetitle", $this->_PageTitle);
        
        //页面按钮
        $this->assign("BtnCustom", $this->_PageCustomBtn);
        
        //表单提交按钮
        $this->assign("PostUrl", $this->_PostUrl);
        
        //表单提交成功后的返回地址
        $this->assign("PostBackUrl", $this->_PostBackUrl);

        //页面配置
        $this->assign("config", $_cfg);
        
        $this->display('Template:CategoryAdd');
    }
    
    /**
     * 新闻列表页面模板
     */
    public function displayNewsList($listData = array()) {
        //页面标题
        $this->assign("pagetitle", $this->_PageTitle);
        
        //页面按钮
        $this->assign("BtnCustom", $this->_PageCustomBtn);
        
        //列表页数据修改Url
        $this->assign("PageListAddUrl", $this->_PageListAddUrl);
        
        //列表页数据修改Url
        $this->assign("PageListEditUrl", $this->_PageListEditUrl);
        
        //列表页数据删除Url
        $this->assign("PageListDeleteUrl", $this->_PageListDeleteUrl);
        
        //列表页数据删除Url
        $this->assign("PageSearchUrl", $this->_PageSearchUrl);
        
        //列表数据
        $this->assign("listdata", $listData);
        
        $this->display('Template:NewsList');
    }
    
    /**
     * 栏目添加页面
     */
    public function displayNewsSet($_cfg=array()) {
        //页面标题
        $this->assign("pagetitle", $this->_PageTitle);
        
        //页面按钮
        $this->assign("BtnCustom", $this->_PageCustomBtn);
        
        //表单提交按钮
        $this->assign("PostUrl", $this->_PostUrl);
        
        //表单提交成功后的返回地址
        $this->assign("PostBackUrl", $this->_PostBackUrl);

        //页面配置
        $this->assign("config", $_cfg);
        
        $this->display('Template:NewsSet');
    }

    /**
     * 添加、修改、删除栏目类型数据， 执行的是存储过程
     * $_procname 存储过程名
     * $_param 存储过程参数，必须与数据库存储过程的参数顺序一致
     */
    protected function SetMenuData($_procname, $_param = array()){
        $exeProc = "call " . $_procname . "(". join(",", $_param) .")";
        $sResult = ExecProc($exeProc);
        
        if($sResult == 1000){
            if(strpos($_procname, "app") >= 0){
                $BttenCache = new \Btten\BttenCache();
                
                $BttenCache->SyncAppMenu();
                SyncAppMenu();
                SyncAppMenu_DLL();
            }
            
            if(strpos($_procname, "system") >= 0){
                $BttenCache = new \Btten\BttenCache();
                
                $BttenCache->SyncSystenNav();
                SyncSystemMenu();
            }

            return true;
        }else{
            return false;
        }
    }
    
    /*
     * 执行删除数据操作
     * $_tablename 数据所在的表名
     * $_where 删除条件
     */
    protected function DelData($_tablename, $_where){
        if(IsN($_where)){
            return false;
        }
        
        $model = M($_tablename);
        $sResult = $model->where($_where)->delete();

        return $sResult;
    }
    

    /*
     * 根据主键删除数据
     * $_ids 要删除数据的ID，多个ID用 , 隔开
     * $_tablename 数据所在的表名
     * $_pkname 主键字段名
     */
    protected function DelFromID($_ids,$_tablename, $_pkname){
        $DelData = explode(',', $_ids);

        $sWhere = "({$_pkname}=" . join(" or {$_pkname} =", $DelData) . ")";

        $return = $this->DelData($_tablename, $sWhere);
        
        if($return){
            SuccessInfo("数据删除成功");
        }else{
            ErrorInfo("数据可能已经被删除，请刷新页面后，在尝试删除。");
        }
    }
    
    /*
     * 根据主键删除数据
     * $_ids 要删除数据的ID，多个ID用 , 隔开
     * $_tablename 数据所在的表名
     * $_pkname 主键字段名
     */
    protected function DelFromIDReturn($_ids,$_tablename, $_pkname){
        $DelData = explode(',', $_ids);

        $sWhere = "({$_pkname}=" . join(" or {$_pkname} =", $DelData) . ")";

        $return = $this->DelData($_tablename, $sWhere);
        
        return $return;
    }
    
    /*
     * 根据主键删除数据
     * $_ids 要删除数据的ID，多个ID用 , 隔开
     * $_tablename 数据所在的表名
     * $_pkname 主键字段名
     */
    protected function SetData($_tablename, $_param, $_pkname="", $_where=""){
        if(IsN($_param)){
            return false;
        }
        
        $model = M($_tablename);
        $sReturn = "";
        
        if(IsN($_pkname)){
            $sReturn = $model->add($_param);
        }else{
            if(IsN($_where)){
                $sReturn = $model->save($_param);
            }else{
                $sReturn = $model->where($_where)->save($_param);
            }
        }
//        exit(showsql($model));
        return $sReturn;
    }
}