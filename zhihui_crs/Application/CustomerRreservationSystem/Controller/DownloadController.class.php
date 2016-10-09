<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class DownloadController extends CommonController {
  private $clsFile = null;
  private $clsPicture = null;
  
  function __construct() {
    parent::__construct();

    $this->clsFile = new \Org\ZhiHui\File();
    $this->clsPicture = new \Org\ZhiHui\Picture();
  }

  /**
   * todo: 输出原始文件名的文件流
   * @return bool
   */
  public function OriginalNameFile(){
    $nFileID = RR("file_id");
    
    if(!IsNum($nFileID, false, false)){
      return false;
    }
    
    $FileInfo = $this->clsFile->GetFileDetails($nFileID);
    
    if(!IsArray($FileInfo)){
      return false;
    }

    $filename = SITE_PATH . "/{$FileInfo["file_path"]}";
    $out_filename = $FileInfo["original_name"];

    @header('Accept-Ranges: bytes');
    @header('Accept-Length: ' . filesize($filename));

    @header('Content-Transfer-Encoding: binary');
    @header('Content-type: application/octet-stream');
    @header('Content-Disposition: attachment; filename=' . $out_filename);
    @header('Content-Type: application/octet-stream; name=' . $out_filename);

    $file = @fopen($filename, "r");
    echo @fread($file, @filesize($filename));
    @fclose($file);
    exit;
  }

  /**
   * todo: 输出原始文件名的文件流
   * @return bool
   */
  public function OriginalNameImage(){
    $nImageID = RR("image_id");

    if(!IsNum($nImageID, false, false)){
      return false;
    }

    $ImageInfo = $this->clsPicture->GetPictureDetails($nImageID);

    if(!IsArray($ImageInfo)){
      return false;
    }

    $filename = SITE_PATH . "/{$ImageInfo["file"]}";
    $out_filename = $ImageInfo["original_name"];

    @header('Accept-Ranges: bytes');
    @header('Accept-Length: ' . filesize($filename));

    @header('Content-Transfer-Encoding: binary');
    @header('Content-type: application/octet-stream');
    @header('Content-Disposition: attachment; filename=' . $out_filename);
    @header('Content-Type: application/octet-stream; name=' . $out_filename);

    $file = @fopen($filename, "r");
    echo @fread($file, @filesize($filename));
    @fclose($file);
    exit;
  }
}