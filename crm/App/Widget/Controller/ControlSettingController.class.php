<?php
namespace Widget\Controller;
use Think\Controller;

class ControlSettingController extends Controller {
    public function textbox($_cfg=array()){
        $arrHtml = array();
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label  class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 只读 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<select name="'. $_cfg["prefix"] .'setting" data-type="readonly" class="sidebar-option form-control input-large">');

        array_push($arrHtml, '<option value="false"');
        if($_cfg["readonly"] == "false"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>否</option>');
        
        array_push($arrHtml, '<option value="true"');
        if($_cfg["readonly"] == "true"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>是</option>');
        array_push($arrHtml, '</select>');
        array_push($arrHtml, '<span class="help-block">是否作为只读文本框使用</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label  class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 类型 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<select name="'. $_cfg["prefix"] .'setting" data-type="type" class="sidebar-option form-control input-large">');

        array_push($arrHtml, '<option value="text"');
        if($_cfg["type"] == "text"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>文本</option>');
        
        array_push($arrHtml, '<option value="password"');
        if($_cfg["type"] == "password"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>密码</option>');
        array_push($arrHtml, '</select>');
        array_push($arrHtml, '<span class="help-block">是否作为只读文本框使用</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        echo join("", $arrHtml);
    }
    
    public function textarea($_cfg=array()){
        $arrHtml = array();
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 行数 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="rows" type="text" class="form-control"  placeholder="" value="'. $_cfg["rows"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。相当于控件的高度</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        echo join("", $arrHtml);
    }
    
    public function option($_cfg=array()){
        $arrHtml = array();
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 选项 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<textarea name="'. $_cfg["prefix"] .'setting" data-type="option" class="form-control" rows="5">'. $_cfg["option"] .'</textarea>');
        array_push($arrHtml, '<span class="help-block">格式：选项名称|选项值。用回车分隔多个键值对</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- PHP函数创建选项 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="phpfunction" type="text" class="form-control"  placeholder="" value="'. $_cfg["phpfunction"] .'">');
        array_push($arrHtml, '<span class="help-block">php调用的函数。例如：TestFunction()，php函数必须放在Common/function.php文件中</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        echo join("", $arrHtml);
    }
    
    public function kindeditor($_cfg=array()){
        $arrHtml = array();
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 行数 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="rows" type="text" class="form-control"  placeholder="" value="'. $_cfg["rows"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。相当于控件的高度</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label  class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 功能按钮 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<textarea name="'. $_cfg["prefix"] .'setting" data-type="items" class="form-control" rows="3">'. $_cfg["items"] .'</textarea>');
        array_push($arrHtml, '<span class="help-block">可为空。详细的功能按钮配置，参考：http://kindeditor.net/docs/option.html#items</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 图片上传地址 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="uploadJson" type="text" class="form-control"  placeholder="" value="'. $_cfg["uploadJson"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 上传图片的参数名 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="filePostName" type="text" class="form-control"  placeholder="" value="'. $_cfg["filePostName"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。必须与上传图片方法中的参数名相同。</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label"> 扩展属性 -- 显示网络图片选项卡 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<select name="'. $_cfg["prefix"] .'setting" data-type="allowImageRemote" class="sidebar-option form-control input-large">');
        array_push($arrHtml, '<option value="false"');
        if($_cfg["allowImageRemote"] == "false"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>否</option>');
        
        array_push($arrHtml, '<option value="true"');
        if($_cfg["allowImageRemote"] == "true"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>是</option>');
        array_push($arrHtml, '</select>');
        array_push($arrHtml, '<span class="help-block"></span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
                
        echo join("", $arrHtml);
    }
    
    public function upload_image($_cfg=array()){
        $arrHtml = array();

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 图片上传地址 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="url" type="text" class="form-control"  placeholder="" value="'. $_cfg["url"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。填写 控制器/动作</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 自动提交 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<select name="'. $_cfg["prefix"] .'setting" data-type="auto" class="sidebar-option form-control input-large">');
        
        array_push($arrHtml, '<option value="true"');
        if($_cfg["auto"] == "true"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>是</option>');
        
        array_push($arrHtml, '<option value="false"');
        if($_cfg["auto"] == "false"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>否</option>');
        
        array_push($arrHtml, '</select>');
        array_push($arrHtml, '<span class="help-block">可为空。</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 多选模式 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<select name="'. $_cfg["prefix"] .'setting" data-type="multi" class="sidebar-option form-control input-large">');
        
        array_push($arrHtml, '<option value="true"');
        if($_cfg["multi"] == "true"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>是</option>');
        
        array_push($arrHtml, '<option value="false"');
        if($_cfg["multi"] == "false"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>否</option>');

        array_push($arrHtml, '</select>');
        array_push($arrHtml, '<span class="help-block"></span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 图片文件大小 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="fileSizeLimit" type="text" class="form-control"  placeholder="" value="'. $_cfg["fileSizeLimit"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。单位：字节</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 允许的类型 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="fileTypeExts" type="text" class="form-control"  placeholder="" value="'. $_cfg["fileTypeExts"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。默认值：jpg|jpeg|png|gif</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 类型说明 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="fileTypeDesc" type="text" class="form-control"  placeholder="" value="'. $_cfg["fileTypeDesc"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。默认值：image/png, image/gif, image/jpg, image/jpeg</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 缩略图裁剪方式 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="crop" type="text" class="form-control"  placeholder="" value="'. $_cfg["crop"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空，默认值：3。缩略图裁剪方式：1 等比例缩放类型；2 缩放后填充类型 ; 3 居中裁剪类型；4 左上角裁剪类型； 5 右下角裁剪类型；6 固定尺寸缩放类型；</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 缩略图尺寸 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<textarea name="'. $_cfg["prefix"] .'setting" data-type="thumbsize" class="form-control" rows="5">'. $_cfg["thumbsize"] .'</textarea>');
        array_push($arrHtml, '<span class="help-block">格式：宽*高。用回车分隔多个键值对</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 保存的文件夹名 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="fieldname" type="text" class="form-control"  placeholder="" value="'. $_cfg["fieldname"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。默认值：defalut</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        echo join("", $arrHtml);
    }
    
    public function upload_image_cut($_cfg=array()){
        $arrHtml = array();

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 图片上传地址 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="url" type="text" class="form-control"  placeholder="" value="'. $_cfg["url"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。填写 控制器/动作</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 自动提交 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<select name="'. $_cfg["prefix"] .'setting" data-type="auto" class="sidebar-option form-control input-large">');
        
        array_push($arrHtml, '<option value="true"');
        if($_cfg["auto"] == "true"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>是</option>');
        
        array_push($arrHtml, '<option value="false"');
        if($_cfg["auto"] == "false"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>否</option>');
        
        array_push($arrHtml, '</select>');
        array_push($arrHtml, '<span class="help-block">可为空。</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 允许的类型 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="fileTypeExts" type="text" class="form-control"  placeholder="" value="'. $_cfg["fileTypeExts"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。默认值：jpg|jpeg|png|gif</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 类型说明 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="fileTypeDesc" type="text" class="form-control"  placeholder="" value="'. $_cfg["fileTypeDesc"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。默认值：image/png, image/gif, image/jpg, image/jpeg</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 缩略图尺寸 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<textarea name="'. $_cfg["prefix"] .'setting" data-type="thumbsize" class="form-control" rows="5">'. $_cfg["thumbsize"] .'</textarea>');
        array_push($arrHtml, '<span class="help-block">格式：宽*高。多个尺寸用回车分隔，宽高最好是按比例减少。例如：100*50; 50*25</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 保存的文件夹名 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="fieldname" type="text" class="form-control"  placeholder="" value="'. $_cfg["fieldname"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。默认值：defalut</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        echo join("", $arrHtml);
    }
    
    public function upload_file($_cfg=array()){
        $arrHtml = array();
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 图片上传地址 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="url" type="text" class="form-control"  placeholder="" value="'. $_cfg["url"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 自动提交 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<select name="'. $_cfg["prefix"] .'setting" data-type="auto" class="sidebar-option form-control input-large">');
        
        array_push($arrHtml, '<option value="true"');
        if($_cfg["auto"] == "true"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>是</option>');
        
        array_push($arrHtml, '<option value="false"');
        if($_cfg["auto"] == "false"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>否</option>');
        
        array_push($arrHtml, '</select>');
        array_push($arrHtml, '<span class="help-block">可为空。</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 多选模式 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<select name="'. $_cfg["prefix"] .'setting" data-type="multi" class="sidebar-option form-control input-large">');
        
        array_push($arrHtml, '<option value="true"');
        if($_cfg["multi"] == "true"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>是</option>');
        
        array_push($arrHtml, '<option value="false"');
        if($_cfg["multi"] == "false"){
            array_push($arrHtml, 'selected="selected"');
        }
        array_push($arrHtml, '>否</option>');

        array_push($arrHtml, '</select>');
        array_push($arrHtml, '<span class="help-block"></span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 图片文件大小 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="fileSizeLimit" type="text" class="form-control"  placeholder="" value="'. $_cfg["fileSizeLimit"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。单位：字节</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 允许的类型 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="fileTypeExts" type="text" class="form-control"  placeholder="" value="'. $_cfg["fileTypeExts"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。默认值：jpg|jpeg|png|gif</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');

        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 保存的文件夹名 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="fieldname" type="text" class="form-control"  placeholder="" value="'. $_cfg["fieldname"] .'">');
        array_push($arrHtml, '<span class="help-block">可为空。默认值：defalut</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        echo join("", $arrHtml);
    }
    
    public function ganged($_cfg=array()){
        $arrHtml = array();
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 数据源 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="phpfunction" type="text" class="form-control"  placeholder="" value="'. $_cfg["phpfunction"] .'">');
        array_push($arrHtml, '<span class="help-block">数据源php函数。例如：TestFunction()，php函数必须放在Common/function.php文件中</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 数组Key </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="arraykey" type="text" class="form-control"  placeholder="" value="'. $_cfg["arraykey"] .'">');
        array_push($arrHtml, '<span class="help-block">数据源数组的下标Key</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 父级字段 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="parent" type="text" class="form-control"  placeholder="" value="'. $_cfg["parent"] .'">');
        array_push($arrHtml, '<span class="help-block"></span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- 子级字段 </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="child" type="text" class="form-control"  placeholder="" value="'. $_cfg["child"] .'">');
        array_push($arrHtml, '<span class="help-block">指定数据库表的哪个字段触发联动操作。多个关联字段用 , 分隔</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        echo join("", $arrHtml);
    }
    
    public function select_back($_cfg=array()){
        $arrHtml = array();
        
        array_push($arrHtml, '<div class="form-group ">');
        array_push($arrHtml, '<label class="col-md-3 control-label" style="text-align: right;"> 扩展属性 -- URL </label>');
        array_push($arrHtml, '<div class="col-md-9">');
        array_push($arrHtml, '<input name="'. $_cfg["prefix"] .'setting" data-type="url" type="text" class="form-control"  placeholder="" value="'. $_cfg["url"] .'">');
        array_push($arrHtml, '<span class="help-block">填写 控制器/动作</span>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        echo join("", $arrHtml);
    }
}