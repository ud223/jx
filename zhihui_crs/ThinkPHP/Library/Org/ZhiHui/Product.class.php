<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class Product {
  private $modProduct = null;
  private $modProductType = null;
  private $modProductSnapshots = null;

  /**
   * todo:产品详细内容缩略图配置
   * @var array
   */
  public $_ProductContentThumbConfig = array(
    "100" => array(100,100),
    "300" => array(300,300),
    "500" => array(500,500),
    "800" => array(800,800),
  );

  /**
   * todo: 支付方式
   * @var array
   */
  public $_PaymentType = array(
    "现金"
  );

  /**
   * todo: 缴费方式
   * @var array
   */
  public $_PayType = array(
    "期缴",
    "趸交",
    "期缴/趸交",
  );

  /**
   * todo: 附件类型
   * @var array
   */
  public $_AttachmentType = array(
    "image"=>"image",
    "file"=>"file",
  );
  
  function __construct() {
    $this->modProduct = M("product");
    $this->modProductType = M("product_type");
    $this->modProductSnapshots = M("product_snapshots");
  }
  
  //region 缓存
  //region 产品类别缓存
  /**
   * 获取产品类别
   * @param $_product_type_id 产品类别ID
   * @return mixed
   */
  public function GetProductTypeDetails($_product_type_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetProductTypeDetailsCache($_product_type_id);

      if(empty($Info)){
        $Info = $this->GetProductTypeDetailsDb($_product_type_id);
      }
    }else{
      $Info = $this->GetProductTypeDetailsDb($_product_type_id);
    }

    return $Info;
  }

  /**
   * 设置产品类别数据缓存
   * @param $_product_type_id 产品类别ID
   * @param array $_data 产品类别数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetProductTypeDetailsCache($_product_type_id, $_data=NULL){
    F($this->GetProductTypeDetailsCacheKey($_product_type_id), $_data);
  }

  /**
   * 获取产品类别数据缓存
   * @param $_product_type_id 产品类别ID
   * @return mixed
   */
  private function GetProductTypeDetailsCache($_product_type_id){
    return F($this->GetProductTypeDetailsCacheKey($_product_type_id));
  }

  /**
   * 获取产品类别缓存Key
   * @param $_product_type_id 产品类别ID
   * @return mixed
   */
  private function GetProductTypeDetailsCacheKey($_product_type_id){
    return "Product/Type/{$_product_type_id}";
  }

  /**
   * todo:从数据库中获取产品类别数据
   * @param $_product_type_id 产品类别ID
   *
   * @return mixed
   */
  private function GetProductTypeDetailsDb($_product_type_id){
    $ProductTypeDetails = array();

    if(!IsNum($_product_type_id, false, false)){
      return $ProductTypeDetails;
    }

    $sWhere = "id={$_product_type_id}";
    $ProductTypeDetails = $this->modProductType->where($sWhere)->find();

    if(!empty($ProductTypeDetails)){
      $this->SetProductTypeDetailsCache($_product_type_id, $ProductTypeDetails);
    }

    return $ProductTypeDetails;
  }
  //endregion 产品类别缓存

  //region 产品缓存
  /**
   * 获取产品
   * @param $_product_id 产品ID
   * @return mixed
   */
  public function GetProductDetails($_product_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetProductDetailsCache($_product_id);

      if(empty($Info)){
        $Info = $this->GetProductDetailsDb($_product_id);
      }
    }else{
      $Info = $this->GetProductDetailsDb($_product_id);
    }
    
    if(IsArray($Info)){
      $Info["store_id"] = trim($Info["store_id"], ',');
      
      if(IsNum($Info["type_id"], false, false)){
        $ProducTypeInfo = $this->GetProductTypeDetails($Info["type_id"]);

        $Info["type_name"] = $ProducTypeInfo["type_name"];
      }

      //region 附件信息
      if(IsNum($Info["attachment_id"], false, false)){
        switch($Info["attachment_type"]){
          case $this->_AttachmentType["image"]:
            $clsPicture = new \Org\ZhiHui\Picture();

            $FileInfo = $clsPicture->GetPictureDetails($Info["attachment_id"]);
            $FileSizeKB = $FileInfo["pic_size"]/1024;
            $FileSizeMB = $FileSizeKB/1024;

            $FileInfo["full_file"] = FullImageUrl($FileInfo["file"]);
            $FileInfo["icon"] = "";
            break;
            
          case $this->_AttachmentType["file"]:
            $clsFile = new \Org\ZhiHui\File();

            $FileInfo = $clsFile->GetFileDetails($Info["attachment_id"]);
            $FileSizeKB = $FileInfo["file_size"]/1024;
            $FileSizeMB = $FileSizeKB/1024;

            $FileInfo["full_file"] = FullImageUrl($FileInfo["file_path"]);

            //region 获取文件图标
            switch($FileInfo["file_ext"]){
              case "docx":
                $sIcon = "fa-file-word-o";
                break;

              case "doc":
                $sIcon = "fa-file-word-o";
                break;

              case "xls":
                $sIcon = "fa-file-excel-o";
                break;

              case "xlsx":
                $sIcon = "fa-file-excel-o";
                break;

              case "rar":
                $sIcon = "fa-file-zip-o";
                break;

              case "zip":
                $sIcon = "fa-file-zip-o";
                break;

              case "pdf":
                $sIcon = "fa-file-pdf-o";
                break;

              default:
                $sIcon = "fa-file-o";
                break;
            }

            $FileInfo["icon"] = $sIcon;
            //endregion 获取文件图标
            break;
        }

        $FileInfo["file_size_kb"] = round($FileSizeKB, 2);
        $FileInfo["file_size_mb"] = round($FileSizeMB, 2);

        $FileInfo["add_date"] = Time2FullDate($FileInfo["add_time"], "Y-m-d");

        $Info["attachment"] = $FileInfo;
      }
      //endregion 附件信息
    }

    return $Info;
  }

  /**
   * 设置产品数据缓存
   * @param $_product_id 产品ID
   * @param array $_data 产品数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetProductDetailsCache($_product_id, $_data=NULL){
    F($this->GetProductDetailsCacheKey($_product_id), $_data);
  }

  /**
   * 获取产品数据缓存
   * @param $_product_id 产品ID
   * @return mixed
   */
  private function GetProductDetailsCache($_product_id){
    return F($this->GetProductDetailsCacheKey($_product_id));
  }

  /**
   * 获取产品缓存Key
   * @param $_product_id 产品ID
   * @return mixed
   */
  private function GetProductDetailsCacheKey($_product_id){
    return "Product/Details/{$_product_id}";
  }

  /**
   * todo:从数据库中获取产品数据
   * @param $_product_id 产品ID
   *
   * @return mixed
   */
  private function GetProductDetailsDb($_product_id){
    $ProductDetails = array();

    if(!IsNum($_product_id, false, false)){
      return $ProductDetails;
    }

    $sWhere = "id={$_product_id}";
    $ProductDetails = $this->modProduct->where($sWhere)->find();

    if(!empty($ProductDetails)){
      $this->SetProductDetailsCache($_product_id, $ProductDetails);
    }

    return $ProductDetails;
  }
  //endregion 产品缓存

  //region 产品快照缓存
  /**
   * 获取产品
   * @param $_snapshots_id 产品快照ID
   * @return mixed
   */
  public function GetProductSnapshotsDetails($_snapshots_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetProductSnapshotsDetailsCache($_snapshots_id);

      if(empty($Info)){
        $Info = $this->GetProductSnapshotsDetailsDb($_snapshots_id);
      }
    }else{
      $Info = $this->GetProductSnapshotsDetailsDb($_snapshots_id);
    }

    if(IsArray($Info)){
      //region 附件信息
      if(IsNum($Info["attachment_id"], false, false)){
        switch($Info["attachment_type"]){
          case $this->_AttachmentType["image"]:
            $clsPicture = new \Org\ZhiHui\Picture();

            $FileInfo = $clsPicture->GetPictureDetails($Info["attachment_id"]);
            $FileSizeKB = $FileInfo["pic_size"]/1024;
            $FileSizeMB = $FileSizeKB/1024;

            $FileInfo["full_file"] = FullImageUrl($FileInfo["file"]);
            $FileInfo["icon"] = "";
            break;

          case $this->_AttachmentType["file"]:
            $clsFile = new \Org\ZhiHui\File();

            $FileInfo = $clsFile->GetFileDetails($Info["attachment_id"]);
            $FileSizeKB = $FileInfo["file_size"]/1024;
            $FileSizeMB = $FileSizeKB/1024;

            $FileInfo["full_file"] = FullImageUrl($FileInfo["file_path"]);

            //region 获取文件图标
            switch($FileInfo["file_ext"]){
              case "docx":
                $sIcon = "fa-file-word-o";
                break;

              case "doc":
                $sIcon = "fa-file-word-o";
                break;

              case "xls":
                $sIcon = "fa-file-excel-o";
                break;

              case "xlsx":
                $sIcon = "fa-file-excel-o";
                break;

              case "rar":
                $sIcon = "fa-file-zip-o";
                break;

              case "zip":
                $sIcon = "fa-file-zip-o";
                break;

              case "pdf":
                $sIcon = "fa-file-pdf-o";
                break;

              default:
                $sIcon = "fa-file-o";
                break;
            }

            $FileInfo["icon"] = $sIcon;
            //endregion 获取文件图标
            break;
        }

        $FileInfo["file_size_kb"] = round($FileSizeKB, 2);
        $FileInfo["file_size_mb"] = round($FileSizeMB, 2);

        $FileInfo["add_date"] = Time2FullDate($FileInfo["add_time"], "Y-m-d");

        $Info["attachment"] = $FileInfo;
      }
      //endregion 附件信息
    }

    return $Info;
  }

  /**
   * 设置产品数据缓存
   * @param $_snapshots_id 产品快照ID
   * @param array $_data 产品数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetProductSnapshotsDetailsCache($_snapshots_id, $_data=NULL){
    F($this->GetProductSnapshotsDetailsCacheKey($_snapshots_id), $_data);
  }

  /**
   * 获取产品数据缓存
   * @param $_snapshots_id 产品快照ID
   * @return mixed
   */
  private function GetProductSnapshotsDetailsCache($_snapshots_id){
    return F($this->GetProductSnapshotsDetailsCacheKey($_snapshots_id));
  }

  /**
   * 获取产品快照缓存Key
   * @param $_snapshots_id 产品快照ID
   * @return mixed
   */
  private function GetProductSnapshotsDetailsCacheKey($_snapshots_id){
    return "Product/Snapshots/{$_snapshots_id}";
  }

  /**
   * todo:从数据库中获取产品数据
   * @param $_snapshots_id 产品快照ID
   *
   * @return mixed
   */
  private function GetProductSnapshotsDetailsDb($_snapshots_id){
    $ProductSnapshotsDetails = array();

    if(!IsNum($_snapshots_id, false, false)){
      return $ProductSnapshotsDetails;
    }

    $sWhere = "id={$_snapshots_id}";
    $ProductSnapshotsDetails = $this->modProductSnapshots->where($sWhere)->find();

    if(!empty($ProductSnapshotsDetails)){
      $this->SetProductSnapshotsDetailsCache($_snapshots_id, $ProductSnapshotsDetails);
    }

    return $ProductSnapshotsDetails;
  }
  //endregion 产品快照缓存
  //endregion 缓存
  
  //region 产品类别
  /**
   * todo: 获取产品类别
   */
  public function ProductTypeOption(){
    $sField = "id";
    $sOrder = "id desc";
    $ProductTypeIdList = $this->modProductType->field($sField)->order($sOrder)->select();
    $ProductTypeOption = array();
    
    foreach($ProductTypeIdList as $key=>$val){
      $ProductTypeInfo = $this->GetProductTypeDetails($val["id"]);
      
      $OptionInfo["key"] = $ProductTypeInfo["type_name"];
      $OptionInfo["val"] = $ProductTypeInfo["id"];
      
      array_push($ProductTypeOption, $OptionInfo);
    }
    
    return $ProductTypeOption;
  }
  //endregion 产品类别
  
  //region 产品
  /**
   * todo: 获取产品option
   * @param $_field_key 字段名
   * @param $_field_val 字段值
   *
   * @return array
   */
  public function ProductOption($_field_key, $_field_val){
    $sField = "id";
    $sWhere = "status=1";
    $sOrder = "id desc";
    
    if(!IsN($_field_key) && IsNum($_field_val, false, false)){
      switch($_field_key){
        case "broker_company_id":
          $sWhere .= " and broker_company_id={$_field_val}";
          break;
        
        case "type_id":
          $sWhere .= " and type_id={$_field_val}";
          break;
      }
    }
    
    $ProductIdList = $this->modProduct->field($sField)->where($sWhere)->order($sOrder)->select();
    $ProductOption = array();

    foreach($ProductIdList as $key=>$val){
      $ProductInfo = $this->GetProductDetails($val["id"]);

      $OptionInfo["key"] = $ProductInfo["product_name"];
      $OptionInfo["val"] = $ProductInfo["id"];

      array_push($ProductOption, $OptionInfo);
    }

    return $ProductOption;
  }

  /**
   * todo: 增加产品销售数量
   * @param int $_product_id
   * @param int $_sell_number
   *
   * @return array
   */
  public function PlusProductSellNumber($_product_id, $_sell_number=1){
    if(!IsNum($_product_id, false, false)){
      return ReturnError(L("_PRODUCT_ID_ERROR_"));
    }else{
      $ProductInfo = $this->GetProductDetails($_product_id);

      if(!IsArray($ProductInfo)){
        return ReturnError(L("_PRODUCT_NOT_EXIST_"));
      }
    }
    
    if(!IsNum($_sell_number, false, false)){
      $_sell_number = 1;
    }

    $Result = $this->modProduct->where('id='.$ProductInfo["id"])->setInc('sell_number',$_sell_number);
    
    if($Result === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }
  //endregion 产品
}