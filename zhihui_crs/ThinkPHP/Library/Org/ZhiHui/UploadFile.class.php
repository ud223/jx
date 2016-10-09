<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;


class UploadFile {
  private $clsPicture = null;
  private $clsFile = null;
  
  public $FilePathType = '';
  public $FileNameType = '';

  /**
   * 构造方法，用于实例化一个图片处理对象
   * @param string $type 要使用的类库，默认使用GD库
   */
  public function __construct(){
    $this->clsPicture = new \Org\ZhiHui\Picture();
    $this->clsFile = new \Org\ZhiHui\File();
  }

  //region 图片上传
  /**
   * todo: 检查表单提交的图片文件
   * @param      $_file_image
   * @param bool $_check_type
   *
   * @return array
   */
  public function CheckFormImage($_file_image, $_check_type=false){
    $ImageConfig = $this->GetUploadConfig();

    //检查文件大小
    if($_file_image["size"] > $ImageConfig["size"]){
      return ReturnError(L("图片文件大小不能超过{$ImageConfig["size_desc"]}"));
    }

    //region 检查文件格式
    $FileType = explode(",", $ImageConfig["type"]);
    $FoundTypeError = true;

    foreach($FileType as $key=>$val){
      if($_file_image["type"] === $val){
        $FoundTypeError = false;
        break;
      }
    }

    if($FoundTypeError){
      return ReturnError(L("只能上传{$ImageConfig["desc"]}格式的图片文件"));
    }
    //endregion 检查文件格式
    
    return ReturnCorrect();
  }

  /**
   * todo:上传图片
   * @param string $_otherfolder 额外的文件保存路径
   * @param array  $_arrwh 缩略图宽高数组
   * @param int    $_corp 裁剪方式
   *
   * @return array
   */
  public function UploadImage($_file_image, $_otherfolder='', $_arrwh=array(), $_corp = 1){
    $CheckResult = $this->CheckFormImage($_file_image);
    
    if($CheckResult["error"] != 0){
      return $CheckResult;
    }
    
    $ImageConfig = $this->GetUploadConfig();
    $sFileRoot = $this->GetFileSavePath();

    if(!empty($_otherfolder)){
      $_otherfolder = "/{$_otherfolder}/";
    }else{
      $_otherfolder = '/';
    }

    //文件保存路径
    $sSavePath = $sFileRoot["root"] . $sFileRoot["path"] . $_otherfolder;
    $this->Directory($sSavePath); //创建目录
    chmod($sSavePath,0777);

    //文件名
    $sFileName = time().mt_rand();

    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize = $ImageConfig["size"];// 设置附件上传大小
    $upload->exts = explode(',', $ImageConfig["desc"]);// 设置附件上传大小
    $upload->rootPath = $sFileRoot["root"];
    $upload->savePath = $sFileRoot["path"] . $_otherfolder; // 设置附件上传目录
    $upload->saveName = $sFileName;
    $upload->autoSub  = false;

    $info = $upload->upload();

    if(!$info) {
      return ReturnError(L("_UPLOAD_FAILURE_"), $upload->getError(), 903);
    }else{
      foreach($info as $key=>$val){
        if(!empty($_arrwh)){
          $this->SaveThumb($sSavePath, $info[$key]["savename"], $_arrwh, $_corp);
        }

        $ImageFilePath = $sSavePath.$val["savename"];
        $clsImage = new \Think\Image();
        $clsImage->open($ImageFilePath);

        $info[$key]["file"] = $sFileRoot["path"] . $_otherfolder . $val["savename"];
        $info[$key]["width"] = $clsImage->width();
        $info[$key]["height"] = $clsImage->height();
        $info[$key]["mime"] = $clsImage->mime();
      }
      
      return ReturnCorrect('', $info);
    }
  }

  /**
   * 保存缩略图
   * @access public
   * @param string $_path 原图路径
   * @param string $_filename 原图名称
   * @param array $_arrwh 需要保存的缩略图宽高数组
   * @return mixed
   */
  public function SaveThumb($_path, $_filename, $_arrwh, $_corp = 1){
    $clsImage = new \Think\Image();

    foreach($_arrwh as $key=>$val){
      $Result = $clsImage->open($_path . $_filename);
      $clsImage->thumb($val[0], $val[1], $_corp)->save($_path . $_filename . "!{$val[0]}_{$val[1]}.jpg");
    }
  }
  
  private function GetImagesInfo($_file_path, $_file_url_path, $_file_name, $_original_name="", $_file_size=0){
    $sFileFullPath = $_file_path . $_file_name;
    
    $clsImage = new \Think\Image();
    $clsImage->open($sFileFullPath);
    $ImageInfo["original_name"] = $_original_name;
    $ImageInfo["path"] = $_file_url_path;
    $ImageInfo["name"] = $_file_name;
    $ImageInfo["file"] = $_file_url_path. $_file_name;
    $ImageInfo["width"] = $clsImage->width();
    $ImageInfo["height"] = $clsImage->height();
    $ImageInfo["size"] = IsNum($_file_size, false, false)?$_file_size:0;
    $ImageInfo["type"] = $clsImage->type();
    $ImageInfo["mime"] = $clsImage->mime();
    
    return $ImageInfo;
  }
  //endregion 图片上传
  
  //region 文件上传
  /**
   * todo: 检查表单提交的图片文件
   * @param      $_file_image
   * @param bool $_check_type
   *
   * @return array
   */
  public function CheckFormFile($_file){
    $FileConfig = $this->GetUploadConfig();

    //检查文件大小
    if($_file["size"] > $FileConfig["size"]){
      return ReturnError(L("文件大小不能超过{$FileConfig["size_desc"]}"));
    }

    //region 检查文件格式
    $FileType = explode(",", $FileConfig["type"]);
    $FoundTypeError = true;

    foreach($FileType as $key=>$val){
      if($_file["type"] === $val){
        $FoundTypeError = false;
        break;
      }
    }

    if($FoundTypeError){
      return ReturnError(L("只能上传{$FileConfig["desc"]}格式的文件"));
    }
    //endregion 检查文件格式

    return ReturnCorrect();
  }

  /**
   * todo:上传图片
   * @param string $_otherfolder 额外的文件保存路径
   * @param array  $_arrwh 缩略图宽高数组
   * @param int    $_corp 裁剪方式
   *
   * @return array
   */
  public function UploadFile($_file, $_otherfolder=''){
    $CheckResult = $this->CheckFormFile($_file);

    if($CheckResult["error"] != 0){
      return $CheckResult;
    }

    $FileConfig = $this->GetUploadConfig();
    $sFileRoot = $this->GetFileSavePath();

    if(!empty($_otherfolder)){
      $_otherfolder = "/{$_otherfolder}/";
    }else{
      $_otherfolder = '/';
    }

    //文件保存路径
    $sSavePath = $sFileRoot["root"] . $sFileRoot["path"] . $_otherfolder;
    $this->Directory($sSavePath); //创建目录
    chmod($sSavePath,0777);

    //文件名
    $sFileName = time().mt_rand();

    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize = $FileConfig["size"];// 设置附件上传大小
    $upload->exts = explode(',', $FileConfig["desc"]);// 设置附件上传大小
    $upload->rootPath = $sFileRoot["root"];
    $upload->savePath = $sFileRoot["path"] . $_otherfolder; // 设置附件上传目录
    $upload->saveName = $sFileName;
    $upload->autoSub  = false;

    $info = $upload->upload();

    if(!$info) {
      return ReturnError(L("_UPLOAD_FAILURE_"), $upload->getError(), 903);
    }else{
      foreach($info as $key=>$val){
        $info[$key]["file"] = $sFileRoot["path"] . $_otherfolder . $val["savename"];
      }

      return ReturnCorrect('', $info);
    }
  }
  //endregion 文件上传

  //region 辅助方法
  /**
   * 检查文件夹路径，不存在则创建
   * @access public
   * @param string $dir 文件夹路径
   * @return mixed
   */
  public function Directory($dir){
    return is_dir($dir) or ($this->Directory(dirname($dir)) and mkdir($dir, 0777));
  }
  
  /**
   * 文件保存路径
   * @access public
   * @param string $_base64 图片文件的base64字符串
   * @param string $_savepath 保存的路径
   * @return mixed
   */
  private function GetFileSavePath(){
    $sType = strtolower($this->FilePathType);
    $sSavePath = '';

    switch($sType){
      //商品详情图
      case "product_content" :
        $sSavePath = "Public/images/product/content";
        break;
      case "customer_idcard" :
        $sSavePath = "Public/images/customer/idcard";
        break;
      case "order_attachment" :
        $sSavePath = "Public/upload/file/order/attachment";
        break;
    }

    return array('root'=>SITE_PATH . "/", 'path'=>$sSavePath);
  }

  /**
   * 获取上传配置
   * @access public
   * @param string $dir 文件夹路径
   * @return mixed
   */
  private function GetUploadConfig(){
    $sType = strtolower($this->FilePathType);
    $Config = '';
    switch($sType){
      case "product_content" :
        $Config = GetProductContentImgCfg();
        break;

      case "customer_idcard" :
        $Config = GetCustomerIdCardImgCfg();
        break;

      case "order_attachment" :
        $Config = GetOrderAttachmentCfg();
        break;
    }

    return $Config;
  }
  //endregion 辅助方法
  
  //region 数据库操作
  /**
   * todo:保存图片信息到数据库
   *
   * @param $_info 图片信息
   *
   * @return array
   */
  public function SaveImgInfoToDb($_info){
    $model = M("picture");
    
    $nTime = time();
    
    $SaveData["original_name"] = $_info["original_name"];
    $SaveData["save_name"] = $_info["name"];
    $SaveData["save_path"] = $_info["path"];
    $SaveData["file"] = $_info["file"];
    $SaveData["file_ext"] = strtolower($_info["ext"]);
    $SaveData["pic_size"] = $_info["size"];
    $SaveData["pic_width"] = $_info["width"];
    $SaveData["pic_height"] = $_info["height"];
    $SaveData["pic_mime"] = $_info["mime"];
    $SaveData["pic_type"] = $_info["type"];
    $SaveData["add_time"] = $nTime;
    $SaveData["update_time"] = $nTime;
    
    $Result = $model->add($SaveData);
    
    if($Result === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }else{
      $this->clsPicture->GetPictureDetails($Result);
      
      return ReturnCorrect("", $Result);
    }
  }

  /**
   * todo:保存文件信息到数据库
   *
   * @param $_info 文件信息
   *
   * @return array
   */
  public function SaveFileInfoToDb($_info){
    $model = M("file");

    $nTime = time();

    $SaveData["original_name"] = $_info["original_name"];
    $SaveData["save_name"] = $_info["name"];
    $SaveData["save_path"] = $_info["path"];
    $SaveData["file_path"] = $_info["file"];
    $SaveData["file_ext"] = strtolower($_info["ext"]);
    $SaveData["file_size"] = $_info["size"];
    $SaveData["file_type"] = $_info["type"];
    $SaveData["add_time"] = $nTime;
    $SaveData["update_time"] = $nTime;

    $Result = $model->add($SaveData);

    if($Result === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }else{
      $this->clsFile->GetFileDetails($Result);

      return ReturnCorrect("", $Result);
    }
  }
  //endregion 数据库操作
}