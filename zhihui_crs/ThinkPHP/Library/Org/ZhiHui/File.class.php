<?php
/**
 * 文件类
 * User: 淼
 * Date: 2015/6/3
 * Time: 11:04
 */
namespace Org\ZhiHui;

class File {
  public function __construct() {
    
  }

  //region 文件信息缓存
  /**
   * 获取文件信息
   * @param int $_file_id 文件id
   * @return mixed
   */
  public function GetFileDetails($_file_id){
    if(GetCacheOnOff()){
      //使用缓存
      $FileInfo = $this->GetFileDetailsCache($_file_id);

      if(empty($FileInfo)){
        $FileInfo = $this->GetFileDetailsDb($_file_id);
      }
    }else{
      $FileInfo = $this->GetFileDetailsDb($_file_id);
    }

    return $FileInfo;
  }

  /**
   * 设置文件信息数据缓存
   * @param int $_file_id 文件id
   * @param array $_data 文件数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetFileDetailsCache($_file_id, $_data=NULL){
    F($this->GetFileDetailsCacheKey($_file_id), $_data);
  }

  /**
   * 获取文件数据缓存
   * @param int $_file_id 文件id
   * @return mixed
   */
  private function GetFileDetailsCache($_file_id){
    return F($this->GetFileDetailsCacheKey($_file_id));
  }

  /**
   * 获取文件信息缓存Key
   * @param int $_file_id 文件id
   * @return mixed
   */
  private function GetFileDetailsCacheKey($_file_id){
    return "File/Details/{$_file_id}";
  }

  /**
   * 从数据库中获取文件信息数据
   * @param int $_file_id 文件id
   * @return mixed
   */
  private function GetFileDetailsDb($_file_id){
    if(!IsNum($_file_id, false, false)){
      return array();
    }
    
    $model = M("file");
    $sWhere = "id={$_file_id}";
    $FileDetails = $model->where($sWhere)->find();

    if(!empty($FileDetails)){

      $this->SetFileDetailsCache($_file_id, $FileDetails);
    }

    return $FileDetails;
  }
  //endregion 文件信息缓存
}
