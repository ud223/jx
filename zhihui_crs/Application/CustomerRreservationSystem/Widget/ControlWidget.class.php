<?php
namespace MsmwuAdminCms\Widget;
use Think\Controller;

class ControlWidget extends Controller {
  /**
   * todo:单图片上传控件
   * @param string $suffix id后缀
   * @param bool $is_multi 是否批量上传
   * @param int $is_line 是否显示分割线
   * @param int $is_must 是否显示必填标记
   * @param string $label_title label标题
   * @param string $desc 上传字段描述
   * @param string|array $url_val 图片路径 或 图片路径数组
   * @param array $data 图片数据数组 或 二维图片数据数组
   * @param array $config 上传参数配置
   * @param string $upload_url 上传图片处理路径
   * @param string $delete_url 删除图片处理路径
   * @param string $other_param 其它参数
   * @param string $js_callback js回调函数
   */
  public function UploadImage($suffix, $is_multi, $is_line, $is_must, $label_title, $desc, $url_val, $data, $config, $upload_url, $delete_url='', $other_param=0, $js_callback=''){
    $this->assign("Suffix", $suffix);
    $this->assign("IsLine", $is_line);
    $this->assign("IsMust", $is_must);
    $this->assign("LableTitle", $label_title);
    $this->assign("UpDesc", $desc);
    $this->assign("UrlValue", $url_val);
    $this->assign("ImageData", $data);
    $this->assign("ImgUpCfg", $config);
    $this->assign("UploadUrl", $upload_url);
    $this->assign("DeleteUrl", $delete_url);
    
    if(empty($other_param)){
      $this->assign("OtherParam", 0);
    }else{
      if(is_array($other_param)){
        $this->assign("OtherParam", json_encode($other_param));
      }else{
        $this->assign("OtherParam", $other_param);
      }
    }
    
    $this->assign("JsCallBackFunction", $js_callback);

    if($is_multi){
      $this->display("Control:upload_image_multi");
    }else{
      $this->display("Control:upload_image_single");
    }
    
  }
}