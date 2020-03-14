<?php
namespace Widget\Controller;
use Think\Controller;

class ControlController extends Controller {
    //基本配置
    private $public_config = array(
        "title"=>"",    //标题
        "tips"=>"", //提示文字
        "title_width"=>3,    //title宽度，1-12。vertical模式下有效
        "input_width"=>9,    //控件宽度，12 - title_width = input_width。vertical模式下有效
        "title_align"=>"right",  //title文字对齐方式。left center right。vertical模式下有效
        "style"=>"",    //控件的style属性
        "required"=>false,  //必填项
    );

    /*
     * 文本框控件
    */
    public function textbox($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_textbox"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_textbox"),
            "value"=>"",    //控件默认值

            //扩展属性
            "extend"=>array(
                //是否只读
                "readonly"=>"false", 
                "type"=>"text",
            ),
        );
        
        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);
        
        $this->assign('textbox',$config);
        $this->display(T('Widget@Control/textbox'));
    }
    
    /*
     * 文本域控件
    */
    public function textarea($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_textarea"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_textarea"),
            "value"=>"",    //控件默认值
            
            //扩展属性
            "extend"=>array(
                //只读属性配置
                "readonly"=>"false", 
                
                //行数配置
                "rows"=>3,   //文本域行数，相当于高度
            ),
        );
        
        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('textarea',$config);
        $this->display(T('Widget@Control/textarea'));
    }
    
    /*
     * 隐藏文本框
    */
    public function hidden($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_hidden"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_hidden"),
            "value"=>"",    //控件默认值
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('hidden',$config);
        $this->display(T('Widget@Control/hidden'));
    }
    
    /*
     * 单选按钮组
    */
    public function radio($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_radio"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_radio"),
            "value"=>"",    //控件默认值
            "option"=>  $this->FmtOption($_cfg),  //array(array("key"=>"是", "val"=>"true", "readonly"=>"false"))
            
            //扩展属性
            "extend"=>array(
                //选项配置
                "items"=>array(),
                
                //PHP函数名
                "fun"=>"",   
            ),
        );
   
        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('radio',$config);
        $this->display(T('Widget@Control/radio'));
    }
    
    /*
     * 多选按钮组
    */
    public function checkbox($_cfg=array()){
        //echo print_r($_cfg);
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_checkbox"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_checkbox"),
            "value"=>"",    //控件默认值
            "option"=>$this->FmtOption($_cfg),  //array(array("key"=>"是", "val"=>"true", "readonly"=>"false"))

            //扩展属性
            "extend"=>array(
                //选项配置
                "items"=>"",
                
                //PHP函数名
                "fun"=>"",   
            ),
        );
        
        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);
        
        $this->assign('checkbox',$config);
        $this->display(T('Widget@Control/checkbox'));
    }
    
    /*
     * 下拉框
    */
    public function dropdownlist($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_dropdownlist"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_dropdownlist"),
            "value"=>"",    //控件默认值
            "option"=>$this->FmtOption($_cfg),  //array(array("key"=>"是", "val"=>"true"))
            
            //扩展属性
            "extend"=>array(
                //只读属性配置
                "readonly"=>"false", 
                
                //选项配置
                "items"=>"",
                
                //文本域行数，相当于高度
                "fun"=>"",   
            ),
        );
        
        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('ddl',$config);
        $this->assign('ddl_setting',$setting_config);
        
        $this->display(T('Widget@Control/dropdownlist'));
    }
    
    /*
     * 日期选择
    */
    public function dateselect($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_dateselect"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_dateselect"),
            "value"=>date("Y-m-d",time()),    //控件默认值
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('dateselect',$config);
        $this->display(T('Widget@Control/dateselect'));
    }
    
    /*
     * 日期范围选择
    */
    public function daterange($_cfg=array()){
        $sDefDate = date("Y/m/d",time());
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_daterange"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_daterange"),
            "value"=>"{$sDefDate} - {$sDefDate}",    //控件默认值
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('daterange',$config);
        $this->display(T('Widget@Control/daterange'));
    }
    
    /*
     * KindEditor文本编辑器
    */
    public function kindeditor($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_kindeditor"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_kindeditor"),
            "rows"=>15,  //文本域行数，相当于高度
            "value"=>"",    //控件默认值
            
            //扩展属性
            "extend"=>array(
                //按钮配置
                "items"=>"'source', '|', 'undo', 'redo', '|', 'cut', 'copy', 'paste', '|', 'plainpaste', 'wordpaste', '|' , 'quickformat', 'clearhtml', 'selectall', 'removeformat', 'hr', 'anchor', 'link', 'unlink' ,'image', '/', 'formatblock', 'fontname', 'fontsize', 'forecolor', 'hilitecolor', 'bold', '|' , 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', '|', 'subscript', 'superscript', 'italic', 'underline', 'strikethrough'",
                "uploadJson"=>"Public/KindEditorUploadImage",   //图片上传地址
                "fieldname"=>"kindeditor",
                "filePostName"=>"Filedata", //上传图片的post名称
                "allowImageRemote"=>"true",     //显示网络图片选项卡
            ),   
        );

        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('kindeditor',$config);
        $this->display(T('Widget@Control/kindeditor'));
    }
    
    public function upload_image($_cfg=array()){
        $cfgImageUpload = GetImageConfig();
        
        $sImgDesc = str_replace("image/","",$cfgImageUpload['desc']);

        $nSize = (int)$cfgImageUpload['size'] / 1024;
        if($nSize >= 1024){
            $sImageSizeTip = ($nSize / 1024) . "MB";
        }else{
            $sImageSizeTip = $nSize . "KB";
        }
        
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_uploadimg"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_uploadimg"),
            "tips"=>"只能上传 {$sImgDesc} 格式的文件，每个文件不能超过 {$sImageSizeTip}。", //提示文字
            "value"=>"",    //控件默认值
                    
            //扩展属性
            "extend"=>array(
                "url"=>"Public/UploadImage",    //图片上传地址
                "auto"=>"true", //自动提交
                "multi"=>"true",    //允许选择多个图片
                "fileSizeLimit"=>"{$cfgImageUpload['size']}",   //上传文件大小，单位字节
                "fileTypeExts"=>"{$cfgImageUpload['type']}",    //上传文件类型
                "fileTypeDesc"=>"{$cfgImageUpload['desc']}",    //上传文件类型说明      
                "crop"=>3,  //缩略图裁剪方式，1 等比例缩放类型；2 缩放后填充类型 ; 3 居中裁剪类型；4 左上角裁剪类型； 5 右下角裁剪类型；6 固定尺寸缩放类型；
                "thumbsize"=>array(),   //缩略图尺寸
                "fieldname"=>"default", //图片保存文件夹
            ),  
        );
        
        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $_cfg["extend"]["thumbsize"] = $this->FmtThumbSize($_cfg["extend"]["thumbsize"]);
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('upload_image',$config);
        $this->display(T('Widget@Control/upload_image'));
    }
    
    public function upload_file($_cfg=array()){
        $cfgFileUpload = GetFileConfig();
        $nSize = (int)$cfgFileUpload['size'] / 1024;
        if($nSize >= 1024){
            $sFileSizeTip = ($nSize / 1024) . "MB";
        }else{
            $sFileSizeTip = $nSize . "KB";
        }

        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_uploadfile"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_uploadfile"),
            "tips"=>"只能上传 {$cfgFileUpload['desc']} 格式的文件，每个文件不能超过 {$sFileSizeTip}。", //提示文字
            "value"=>"",    //控件默认值
                    
            //扩展属性
            "extend"=>array(
                "url"=>"Public/UploadFile", //图片提交路径
                "auto"=>"true",   //自动提交
                "multi"=>"false", //是否允许多文件选择4
                "fileSizeLimit"=>"{$cfgFileUpload['size']}",    //上传的文件大小
                "fileTypeExts"=>"{$cfgFileUpload['type']}", //上传文件类型
                "fileTypeDesc"=>"{$cfgFileUpload['desc']}", //上传文件类型说明
                "fieldname"=>"default", //分类文件夹
            ),
        );

        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('upload_file',$config);
        $this->display(T('Widget@Control/upload_file'));
    }
    
    public function upload_image_cut($_cfg=array()){
        $cfgImageUpload = GetImageConfig();
        
        $sImgDesc = str_replace("image/","",$cfgImageUpload['desc']);
        
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_upload_image_cut"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_upload_image_cut"),
            "tips"=>"只能上传 {$sImgDesc} 格式的文件。", //提示文字
            "value"=>"",    //控件默认值
                    
            //扩展属性
            "extend"=>array(
                "url"=>"Public/UploadImageCut",    //图片上传地址
                "auto"=>"true", //自动提交
                "multi"=>"false",    //允许选择多个图片
                "fileSizeLimit"=>"{$cfgImageUpload['size']}",   //上传文件大小，单位字节
                "fileTypeExts"=>"{$cfgImageUpload['type']}",    //上传文件类型
                "fileTypeDesc"=>"{$cfgImageUpload['desc']}",    //上传文件类型说明      
                "crop"=>3,  //缩略图裁剪方式，1 等比例缩放类型；2 缩放后填充类型 ; 3 居中裁剪类型；4 左上角裁剪类型； 5 右下角裁剪类型；6 固定尺寸缩放类型；
                "thumbsize"=>array(),   //缩略图尺寸
                "fieldname"=>"default", //图片保存文件夹
            ),  
        );
        
        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $_cfg["extend"]["thumbsize"] = $this->FmtThumbSize($_cfg["extend"]["thumbsize"]);
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('upload_image_cut',$config);
        $this->display(T('Widget@Control/upload_image_cut'));
    }
    
    /*
     * 系统栏目下拉框
    */
    public function system_category($_cfg=array()){
        $arrMenu = GetSystemMenuCache();
        
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_system_category"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_system_category"),
            "value"=>"",    //控件默认值
            
            //扩展属性
            "extend"=>array(
                //选项
                "option"=>$arrMenu,
            ),
        );
        
//        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
//        exitp($_cfg["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);
   
        $this->assign('system_category',$config);
        $this->display(T('Widget@Control/system_category'));
    }
    
    /*
     * 系统栏目下拉框
    */
    public function system_category_all($_cfg=array()){
        $model = M("system_category");
        $arrMenu = $model->where("`left_val` > 1 and `dept` > 0")->order("`left_val`, `order`")->select();
        
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_system_category_all"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_system_category_all"),
            "value"=>"",    //控件默认值
            
            //扩展属性
            "extend"=>array(
                //选项
                "option"=>$arrMenu,
            ),
        );
        
        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);
   
        $this->assign('system_category_all',$config);
        $this->display(T('Widget@Control/system_category_all'));
    }
    
    /*
     * 应用栏目下拉框
    */
    public function app_category($_cfg=array()){
        $arrMenu = GetAppMenuCache();
        
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_app_category"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_app_category"),

            "value"=>"",    //控件默认值
            //扩展属性
            "extend"=>array(
                //选项
                "option"=>$arrMenu,
            ),
        );
        
        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('app_category',$config);
        $this->display(T('Widget@Control/app_category'));
    }
    
    /*
     * 联动选择
    */
    public function ganged($_cfg=array()){
         $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_region_area"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_region_area"),
            "value"=>"",    //控件默认值
            
            //扩展属性
            "extend"=>array(
                //是否只读
                "parent"=>"",
                "child"=>"",
                "phpfunction"=>"",
                "arraykey"=>""
            ),
        );

        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);
        $DataSource ="[]";
        
        if(!empty($config["extend"]["phpfunction"])){
            eval('$DataSource = ' . $config["extend"]["phpfunction"] . ';');
            
            if(!empty($config["extend"]["arraykey"])){
                $this->assign('DataSource', json_encode($DataSource[$config["extend"]["arraykey"]]));
            }else{
                $this->assign('DataSource', json_encode($DataSource));
            }
        }
        
        $this->assign('ganged',$config);
        $this->display(T('Widget@Control/ganged'));
    }
    
    /*
     * 查找带回
    */
    public function select_back($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_select_back"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_select_back"),
            "value"=>"",    //控件默认值
            
            //扩展属性
            "extend"=>array(
                //是否只读
                "url"=>"",
            ),
        );

        $_cfg["extend"] = $this->MergeExtend($_cfg["extend"], $def_config["extend"]);
        $config = array_merge($this->public_config, $def_config, $_cfg);
        
        $this->assign('select_back',$config);
        $this->display(T('Widget@Control/select_back'));
    }
    
    /*
     * 格式化选项
    */
    private function FmtOption($_arr){
        $_strjson = $_arr["extend"]["option"];
        $_funname = $_arr["extend"]["phpfunction"];
        
        if(empty($_strjson) && empty($_funname)){
            return "";
        }

        $ArrOption  = array();

        //判断人工输入的Option格式选项
        if(!empty($_strjson)){
            $ArrOption = explode(';', $_strjson);

            $nOptionCount = sizeof($ArrOption);

            for($i=0; $i<$nOptionCount; $i++){
                $arrItem = explode('|', $ArrOption[$i]);

                $ArrOption[$i] = array("key"=>$arrItem[0], "val"=>$arrItem[1]);
            }
        }
        
        //判断有没有配置函数
        if(!empty($_funname)){
            //执行函数
            eval('$arrFunOption = ' . $_funname . ';');
            
            if(!empty($arrFunOption)){
                //合并数组
                $ArrOption = array_merge($ArrOption,$arrFunOption);
            }
        }
        
        return $ArrOption;
    }
    
    /*
     * 合并扩展数组
     * $_mainArr 主数组
     * $_minorArr 次要数组
    */
   private function MergeExtend($_mainArr, $_minorArr){
       foreach($_mainArr as $key=>$val) {
            if(IsN($val)){
                $_mainArr[$key] = $_minorArr[$key];
            }
        }

        return $_mainArr;
   }
   
   /*
    * 格式化缩略图尺寸数组
    */
   private function FmtThumbSize($_sizestr){
       if(empty($_sizestr)){
           return array();
       }
       
       //29*29;64*64;100*100
       
       $arrThumb = explode(";", $_sizestr);

       $nCount = sizeof($arrThumb);
       
       $arrReturn = array();
       
       for($i=0; $i<$nCount; $i++){
           $arrWH = explode("*", $arrThumb[$i]);
           
           array_push($arrReturn, array("width"=>$arrWH[0], "height"=>$arrWH[1]));
       }
       
       return $arrReturn;
   }
}