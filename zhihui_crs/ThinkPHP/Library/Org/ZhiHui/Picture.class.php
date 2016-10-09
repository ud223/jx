<?php
/**
 * 商品分类
 * User: 淼
 * Date: 2015/6/3
 * Time: 11:04
 */
namespace Org\ZhiHui;

class Picture {
  public function __construct() {
    
  }

  //region 图片信息缓存
  /**
   * 获取图片信息
   * @param int $_pic_id 图片id
   * @return mixed
   */
  public function GetPictureDetails($_pic_id){
    if(GetCacheOnOff()){
      //使用缓存
      $PictureInfo = $this->GetPictureDetailsCache($_pic_id);

      if(empty($PictureInfo)){
        $PictureInfo = $this->GetPictureDetailsDb($_pic_id);
      }
    }else{
      $PictureInfo = $this->GetPictureDetailsDb($_pic_id);
    }

    return $PictureInfo;
  }

  /**
   * 设置图片信息数据缓存
   * @param int $_pic_id 图片id
   * @param array $_data 图片数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetPictureDetailsCache($_pic_id, $_data=NULL){
    F($this->GetPictureDetailsCacheKey($_pic_id), $_data);
  }

  /**
   * todo:格式化图片上传控件返回值字段数据
   * @param array $_data 图片信息数组
   * @param array $_wh 要返回的图片宽高数组
   *
   * @return array
   */
  public function FmtImageControlData($_data, $_wh=array()){
    $Result = array();

    if(!empty($_data)){
      $Result["img_id"] = $_data["id"];

      if(empty($_wh)){
        $sThumb = $_data["save_name"];
      }else{
        $sThumb = GetThumbImageNameIndex($_data["save_name"], $_wh);
      }

      $Result["img_url"] = FullImageUrl($_data["save_path"] . $sThumb, "images/");

    }

    return $Result;
  }

  /**
   * 获取图片数据缓存
   * @param int $_pic_id 图片id
   * @return mixed
   */
  private function GetPictureDetailsCache($_pic_id){
    return F($this->GetPictureDetailsCacheKey($_pic_id));
  }

  /**
   * 获取图片信息缓存Key
   * @param int $_pic_id 图片id
   * @return mixed
   */
  private function GetPictureDetailsCacheKey($_pic_id){
    return "Picture/Details/{$_pic_id}";
  }

  /**
   * 从数据库中获取图片信息数据
   * @param int $_pic_id 图片id
   * @return mixed
   */
  private function GetPictureDetailsDb($_pic_id){
    if(!IsNum($_pic_id, false, false)){
      return array();
    }
    
    $model = M("picture");
    $sWhere = "id={$_pic_id}";
    $PictureDetails = $model->where($sWhere)->find();

    if(!empty($PictureDetails)){

      $this->SetPictureDetailsCache($_pic_id, $PictureDetails);
    }

    return $PictureDetails;
  }
  //endregion 图片信息缓存
}
