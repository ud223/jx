<?php
namespace Admin\Controller;
use Think\Controller;

class IndexController extends CommonController {
    public function index(){
        //页面数据配置 Begin
        //注意第一条中包含数组括号 [ ，最后一条包含数组括号 ]
        $arrPage = array();
        array_push($arrPage, '[{"control":"textbox", "id":"test_a", "name":"test_a", "title":"文本框", "tips":"文本框测试说明信息", "value":""}');
        array_push($arrPage, '{"control":"textarea", "id":"test_b", "name":"test_b", "title":"文本域", "tips":"文本域测试说明信息", "value":""}');
        array_push($arrPage, '{"control":"hidden", "id":"test_c", "name":"test_c", "title":"隐藏文本框", "tips":"隐藏文本框测试说明信息", "value":""}');
        array_push($arrPage, '{"control":"radio", "id":"test_d", "name":"test_d", "title":"单选按钮", "tips":"单选按钮测试说明信息", "value":"a", "option":[{"key":"选项一", "val":"a", "readonly":"false"},{"key":"选项二", "val":"b", "readonly":"false"}]}');
        array_push($arrPage, '{"control":"checkbox", "id":"test_e", "name":"test_e", "title":"多选框", "tips":"多选框测试说明信息", "value":"", "option":[{"key":"选项一", "val":"a", "readonly":"false"},{"key":"选项二", "val":"b", "readonly":"false"}]}');
        array_push($arrPage, '{"control":"dropdownlist", "id":"test_f", "name":"test_f", "title":"下拉框", "tips":"下拉框测试说明信息", "value":"", "option":[{"key":"选项一", "val":"a", "readonly":"false"},{"key":"选项二", "val":"b", "readonly":"false"}]}');
        array_push($arrPage, '{"control":"dateselect", "id":"test_g", "name":"test_g", "title":"日期选择", "tips":"日期选择测试说明信息", "value":""}');
        array_push($arrPage, '{"control":"daterange", "id":"test_h", "name":"test_h", "title":"日期范围", "tips":"日期范围测试说明信息", "value":""}');
        array_push($arrPage, '{"control":"upload_image", "id":"test_i", "name":"test_i", "title":"图片上传", "value":"", "extend":{"crop":3, "thumbsize":[{"width":164, "height":180},{"width":200, "height":200}]}}');
        array_push($arrPage, '{"control":"upload_file", "id":"test_j", "name":"test_j", "title":"文件上传", "value":""}');
        array_push($arrPage, '{"control":"kindeditor", "id":"test_k", "name":"test_k", "title":"文本编辑器", "tips":"文本编辑器测试说明信息", "value":"", "css":"last"}]');
        
        $sJsonConfig = join(",", $arrPage);
        //页面数据配置 End

        $arrPageConfig = json_decode($sJsonConfig, true);

        $this->assign("config",$arrPageConfig);
        $this->display();
    }
}