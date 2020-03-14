<?php
namespace Admin\Widget;
use Think\Controller;

class ControlWidget extends Controller {
    //基本配置
    private $public_config = array(
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
            "title"=>"",    //标题
            "tips"=>"", //提示文字
            "value"=>"",    //控件默认值
            "icon"=>false,  //是否使用图标，true false
            "icon_style"=>"",   //图片样式。参考html/ui_buttons.html
            "icon_align"=>"left",   //图标位置。left right
            "inside_text"=>"",  //文本框内部文本
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);
        
        $this->assign('textbox',$config);
        $this->display('Control/textbox');
    }
    
    /*
     * 文本域控件
    */
    public function textarea($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_textarea"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_textarea"),
            "title"=>"",    //标题
            "tips"=>"", //提示文字
            "value"=>"",    //控件默认值
            "rows"=>3,  //文本域行数，相当于高度
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('textarea',$config);
        $this->display('Control/textarea');
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
        $this->display('Control/hidden');
    }
    
    /*
     * 单选按钮组
    */
    public function radio($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_radio"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_radio"),
            "title"=>"",    //标题
            "tips"=>"", //提示文字
            "value"=>"",    //控件默认值
            //选项
            "option"=>array(),  //array(array("key"=>"下拉框选项一", "val"=>"xa"), array("key"=>"下拉框选项二", "val"=>"xb"))
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('radio',$config);
        $this->display('Control/radio');
    }
    
    /*
     * 多选按钮组
    */
    public function checkbox($_cfg=array()){
        //echo print_r($_cfg);
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_checkbox"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_checkbox"),
            "title"=>"多选按钮",    //标题
            "tips"=>"测试多选按钮提示信息", //提示文字
            "value"=>"a",    //控件默认值
            //选项
            "option"=>array(),  //array(array("key"=>"下拉框选项一", "val"=>"xa"), array("key"=>"下拉框选项二", "val"=>"xb"))
        );

        $config = array_merge($this->public_config, $def_config, $_cfg);
        //echo print_r($config);
        $this->assign('checkbox',$config);
        $this->display('Control/checkbox');
    }
    
    /*
     * 下拉框
    */
    public function dropdownlist($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_dropdownlist"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_dropdownlist"),
            "title"=>"",    //标题
            "tips"=>"", //提示文字
            "value"=>"a",    //控件默认值
            //选项
            "option"=>array(),  //array(array("key"=>"下拉框选项一", "val"=>"xa"), array("key"=>"下拉框选项二", "val"=>"xb"))
        );
        
        //exit(json_encode($def_config["option"]));
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('ddl',$config);
        $this->display('Control/dropdownlist');
    }
    
    /*
     * 日期选择
    */
    public function dateselect($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_dateselect"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_dateselect"),
            "title"=>"",    //标题
            "tips"=>"", //提示文字
            "value"=>date("Y-m-d",time()),    //控件默认值
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('dateselect',$config);
        $this->display('Control/dateselect');
    }
    
    /*
     * 日期范围选择
    */
    public function daterange($_cfg=array()){
        $sDefDate = date("Y/m/d",time());
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_daterange"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_daterange"),
            "title"=>"",    //标题
            "tips"=>"", //提示文字
            "value"=>"{$sDefDate} - {$sDefDate}",    //控件默认值
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('daterange',$config);
        $this->display('Control/daterange');
    }
    
    /*
     * 图片上传
    */
    public function uploadify_image($_cfg=array()){
        $cfgImageUpload = GetImageConfig();
        if((int)$cfgImageUpload['size'] >= 1024){
            $sImgSizeTip = ((int)$cfgImageUpload['size'] / 1024) . "MB";
        }else{
            $sImgSizeTip = $cfgImageUpload['size'] . "KB";
        }
        
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_uploadify_image"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_uploadify_image"),
            "title"=>"",    //标题
            "tips"=>"允许上传 {$cfgImageUpload['desc']} 格式的图片，图片大小不能超过{$sImgSizeTip}。", //提示文字
            "value"=>"",    //控件默认值
                    
            //uploadify配置
            "buttonImage"=>__ROOT__ . "/Public/res/admin/js/uploadify/browse_image.jpg",  //按钮图片路径
            "swf"=>__ROOT__ . "/Public/res/admin/js/uploadify/uploadify.swf",   
            "uploader"=>__MODULE__ . "/Public/UploadImage", //图片提交路径
            "auto"=>"true",   //自动提交
            "multi"=>"true", //是否允许多文件选择
            "width"=>97,    //宽度
            "height"=>34,   //高度
            "removeTimeout"=>0, //移除页面项的时间
            "buttonCursor"=>"pointer",   //按钮鼠标样式
            "fileSizeLimit"=>"{$cfgImageUpload['size']}KB",    //上传的文件大小
            "fileTypeDesc"=>"{$cfgImageUpload['desc']}", //上传文件类型说明
            "fileTypeExts"=>"{$cfgImageUpload['type']}", //上传文件类型
            "method"=>"post",    //提交方式
            "queueSizeLimit"=>100,  //每次可选择的文件数量，不能超出uploadLimit的值
            "progressData"=>"percentage",   //显示进度和上传速度
            "uploadLimit"=>100,  //最多允许上传的文件数
            "formData"=>"",//额外的提交数据
            "crop"=>3, //缩略图裁剪方式，1 等比例缩放类型；2 缩放后填充类型 ; 3 居中裁剪类型；4 左上角裁剪类型； 5 右下角裁剪类型；6 固定尺寸缩放类型；
            "thumbsize"=>array(array("width"=>164, "height"=>180), array("width"=>300, "height"=>200)), //返回第一个缩率图信息
            "fieldname"=>"default", //分类文件夹
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('uploadify_image',$config);
        $this->display('Control/uploadify_image');
    }
    
    /*
     * 文件上传
    */
    public function uploadify_file($_cfg=array()){
        $cfgFileUpload = GetFileConfig();
        
        if((int)$cfgFileUpload['size'] >= 1024){
            $sFileSizeTip = ((int)$cfgFileUpload['size'] / 1024) . "MB";
        }else{
            $sFileSizeTip = $cfgFileUpload['size'] . "KB";
        }
        
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_uploadify_file"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_uploadify_file"),
            "title"=>"",    //标题
            "tips"=>"允许上传 {$cfgFileUpload['desc']} 格式的文件，文件大小不能超过{$sFileSizeTip}。", //提示文字
            "value"=>"",    //控件默认值
                    
            //uploadify配置
            "buttonImage"=>__ROOT__ . "/Public/res/admin/js/uploadify/browse_file.jpg",  //按钮图片路径
            "swf"=>__ROOT__ . "/Public/res/admin/js/uploadify/uploadify.swf",   
            "uploader"=>__MODULE__ . "/Public/UploadFile", //图片提交路径
            "auto"=>"true",   //自动提交
            "multi"=>"true", //是否允许多文件选择
            "width"=>97,    //宽度
            "height"=>34,   //高度
            "removeTimeout"=>0, //移除页面项的时间
            "buttonCursor"=>"pointer",   //按钮鼠标样式
            "fileSizeLimit"=>"{$cfgFileUpload['size']}KB",    //上传的文件大小
            "fileTypeDesc"=>"{$cfgFileUpload['desc']}", //上传文件类型说明
            "fileTypeExts"=>"{$cfgFileUpload['type']}", //上传文件类型
            "method"=>"post",    //提交方式
            "queueSizeLimit"=>100,  //每次可选择的文件数量，不能超出uploadLimit的值
            "progressData"=>"percentage",   //显示进度和上传速度
            "uploadLimit"=>100,  //最多允许上传的文件数
            "formData"=>"",//额外的提交数据
            "fieldname"=>"default", //分类文件夹
        );
            
        $config = array_merge($this->public_config, $def_config, $_cfg);
        
        $this->assign('uploadify_file',$config);
        $this->display('Control/uploadify_file');
    }
    
    /*
     * KindEditor文本编辑器
    */
    public function kindeditor($_cfg=array()){
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_kindeditor"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_kindeditor"),
            "title"=>"",    //标题
            "tips"=>"", //提示文字
            "rows"=>15,  //文本域行数，相当于高度
            "value"=>"",    //控件默认值
            
            //kindeditor配置
            //按钮配置
            "items"=>"'source', '|', 'undo', 'redo', '|', 'cut', 'copy', 'paste', '|', 'plainpaste', 'wordpaste', '|' , 'quickformat', 'clearhtml', 'selectall', 'removeformat', 'hr', 'anchor', 'link', 'unlink' ,'image', '/', 'formatblock', 'fontname', 'fontsize', 'forecolor', 'hilitecolor', 'bold', '|' , 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', '|', 'subscript', 'superscript', 'italic', 'underline', 'strikethrough'",
            "uploadJson"=>__MODULE__ . "/Public/KindEditorUploadImage/fieldname/kindeditor",
            "filePostName"=>"Filedata", //上传图片的post名称，与UploadImage方法内参数名对应
            "allowImageRemote"=>"true",    //显示网络图片选项卡
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('kindeditor',$config);
        $this->display('Control/kindeditor');
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
            "title"=>"",    //标题
            "tips"=>"只能上传 {$sImgDesc} 格式的文件，每个文件不能超过 {$sImageSizeTip}。", //提示文字
            "rows"=>15,  //文本域行数，相当于高度
            "value"=>"",    //控件默认值
            
            "url"=>__MODULE__ . "/Public/UploadImage", //图片提交路径
            "auto"=>"true",   //自动提交
            "multi"=>"true", //是否允许多文件选择4
            "fileSizeLimit"=>"{$cfgImageUpload['size']}",    //上传的文件大小
            "fileTypeExts"=>"{$cfgImageUpload['type']}", //上传文件类型
            "fileTypeDesc"=>"{$cfgImageUpload['desc']}", //上传文件类型说明
            "crop"=>3, //缩略图裁剪方式，1 等比例缩放类型；2 缩放后填充类型 ; 3 居中裁剪类型；4 左上角裁剪类型； 5 右下角裁剪类型；6 固定尺寸缩放类型；
            "thumbsize"=>array(array("width"=>164, "height"=>180), array("width"=>300, "height"=>200)), //返回第一个缩率图信息
            "fieldname"=>"default", //分类文件夹
        );

        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('upload_image',$config);
        $this->display('Control/upload_image');
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
            "title"=>"",    //标题
            "tips"=>"只能上传 {$cfgFileUpload['desc']} 格式的文件，每个文件不能超过 {$sFileSizeTip}。", //提示文字
            "rows"=>15,  //文本域行数，相当于高度
            "value"=>"",    //控件默认值
            
            "url"=>__MODULE__ . "/Public/UploadFile", //图片提交路径
            "auto"=>"true",   //自动提交
            "multi"=>"false", //是否允许多文件选择4
            "fileSizeLimit"=>"{$cfgFileUpload['size']}",    //上传的文件大小
            "fileTypeExts"=>"{$cfgFileUpload['type']}", //上传文件类型
            "fileTypeDesc"=>"{$cfgFileUpload['desc']}", //上传文件类型说明
            "fieldname"=>"default", //分类文件夹
        );

        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('upload_file',$config);
        $this->display('Control/upload_file');
    }
    
    /*
     * 系统栏目下拉框
    */
    public function system_category($_cfg=array()){
        $arrMenu = GetSystemMenuCache();
        
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_system_category"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_system_category"),
            "title"=>"",    //标题
            "tips"=>"", //提示文字
            "value"=>"",    //控件默认值
            //选项
            "option"=>$arrMenu,
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);
   
        $this->assign('system_category',$config);
        $this->display('Control/system_category');
    }
    
    /*
     * 应用栏目下拉框
    */
    public function app_category($_cfg=array()){
        $arrMenu = GetAppMenuCache();
        
        $def_config = array(
            "id"=> strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_app_category"),
            "name"=>strtolower(MODULE_NAME . "_" . CONTROLLER_NAME . "_" . ACTION_NAME . "_app_category"),
            "title"=>"",    //标题
            "tips"=>"", //提示文字
            "value"=>"",    //控件默认值
            //选项
            "option"=>$arrMenu,
        );
        
        $config = array_merge($this->public_config, $def_config, $_cfg);

        $this->assign('app_category',$config);
        $this->display('Control/app_category');
    }
}
