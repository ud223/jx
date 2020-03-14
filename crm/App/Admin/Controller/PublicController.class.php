<?php
namespace Admin\Controller;
use Think\Controller;

class PublicController extends Controller {
    public function index(){
        $this->display();
    }
    
    /*
     * 保存上传图片
     */
    public function UploadImage(){
        $nCropType = param("crop");
        $sFieldName = param("fieldname");
        $arrThumbSize = json_decode($_POST["thumbsize"], true);

        $sImgRootPath = DATA_ROOT_PATH;
        
        if(empty($sFieldName)){
            $sFieldPath = DEFAULT_IMAGES_PATH;
        }else{
            $sFieldPath = UPLOAD_IMAGES_PATH . '/' . $sFieldName;
        }
        
        $arrImgCfg = GetImageConfig();
        
        $config = array(
            'maxSize' => (int)$arrImgCfg['size'],
            'exts' => explode('|',$arrImgCfg['type']),
            'subName' => array(),
            'rootPath' => $sImgRootPath,
            'saveExt' => 'jpg',
            'savePath' => $sFieldPath . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/',
        );
        
        $sSavePath = $config["rootPath"]. $config["savePath"] ;
        Directory($sSavePath); //创建目录
        chmod($sSavePath,0777);	
        
        $upload = new \Think\Upload($config);  // 实例化上传类
        
        $info = $upload->upload();
        
         if(!$info) {
            // 上传错误提示错误信息    
            //$this->error($upload->getError());
            ErrorInfo($upload->getError());
        }else{
            $arrImageInfo = $info["Filedata"];
            
            $sImagePath = $sImgRootPath . $arrImageInfo["savepath"] . $arrImageInfo["savename"];
            
            $image = new \Think\Image();
            $image->open($sImagePath);

            $arrImageInfo["width"] = $image->width();
            $arrImageInfo["height"] = $image->height();
            $arrImageInfo["mime"] = $image->mime();
            
            $addFile = $this->SaveImageToDb($arrImageInfo);

            $arrReturn["imageid"] = $addFile;
            $arrReturn["image"] = $arrImageInfo["savename"];
            $arrReturn["path"] = $arrImageInfo["savepath"];
            $arrReturn["width"] = $arrImageInfo["width"];
            $arrReturn["height"] = $arrImageInfo["height"];
            $arrReturn["url"] = $arrImageInfo["savepath"] . $arrImageInfo["savename"];
            
            //创建缩略图 Begin
            if(!empty($arrThumbSize)){
                $arrThumb = $this->CreateThumb($arrImageInfo, $nCropType, $arrThumbSize, $sImgRootPath, $sPrefix);
                
                if(!empty($arrThumb)){
                    $arrReturn["thumb_image"] = $arrThumb["savename"];
                    $arrReturn["thumb_path"] = $arrThumb["savepath"];
                    $arrReturn["thumb_width"] = $arrThumb["width"];
                    $arrReturn["thumb_height"] = $arrThumb["height"];
                    $arrReturn["thumb_type"] = $arrThumb["type"];
                    $arrReturn["thumb_mime"] = $arrThumb["mime"];
                    $arrReturn["thumb_size"] = $arrThumb["size"];
                    $arrReturn["thumb_url"] = $arrThumb["savepath"] . $arrThumb["savename"]; 
                }
            }
            //创建缩略图 End
            
            exit(json_encode($arrReturn));
        }
    }
    
    /*
     * 保存上传文件
     */
    public function UploadFile(){
        $sFieldName = param("fieldname");
        
        $sFileRootPath = DATA_ROOT_PATH;
        
        if(empty($sFieldName)){
            $sFieldPath = DEFAULT_FILES_PATH;
        }else{
            $sFieldPath = UPLOAD_FILES_PATH . '/' . $sFieldName;
        }
        
        $arrFileCfg = GetFileConfig();
        
        $config = array(
            'maxSize' => (int)$arrFileCfg['size'] * 1024,
            'exts' => explode('|',$arrFileCfg['type']),
            'subName' => array(),
            'rootPath' => $sFileRootPath,
            'savePath' => $sFieldPath . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/',
        );
        
        $sSavePath = $config["rootPath"]. $config["savePath"] ;
        Directory($sSavePath); //创建目录
        chmod($sSavePath,0777);	
        
        $upload = new \Think\Upload($config);  // 实例化上传类
        
        $info = $upload->upload();
        
        if(!$info) {
            // 上传错误提示错误信息
            ErrorInfo($upload->getError());
        }else{
            $arrFileInfo = $info["Filedata"];
            $addFile = $this->SaveFileToDb($arrFileInfo);
            
            $arrReturn = array(
                "fileid"=>$addFile,
                "name"=>$arrFileInfo["name"],
                "size"=>$arrFileInfo["size"],
                "ext"=>$arrFileInfo["ext"],
                "savename"=>$arrFileInfo["savename"],
                "savepath"=>$arrFileInfo["savepath"],
            );
            
            exit(json_encode($arrReturn));
        }
    }
    
    public function KindEditorUploadImage(){
        $nCropType = param("crop");
        $sFieldName = param("fieldname");
        $arrThumbSize = json_decode($_POST["thumbsize"], true);

        $sImgRootPath = DATA_ROOT_PATH;
        
        if(empty($sFieldName)){
            $sFieldPath = DEFAULT_IMAGES_PATH;
        }else{
            $sFieldPath = UPLOAD_IMAGES_PATH . '/' . $sFieldName;
        }
        
        $arrImgCfg = GetImageConfig();
        
        $config = array(
            'maxSize' => (int)$arrImgCfg['size'],
            'exts' => explode('|',$arrImgCfg['type']),
            'subName' => array(),
            'rootPath' => $sImgRootPath,
            'saveExt' => 'jpg',
            'savePath' => $sFieldPath . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/',
        );
        
        $sSavePath = $config["rootPath"]. $config["savePath"] ;
        Directory($sSavePath); //创建目录
        chmod($sSavePath,0777);	
        
        $upload = new \Think\Upload($config);  // 实例化上传类
        
        $info = $upload->upload();
        
         if(!$info) {
            // 上传错误提示错误信息    
            //$this->error($upload->getError());
            ErrorInfo($upload->getError());
        }else{
            $arrImageInfo = $info["Filedata"];
            
            $sImagePath = $sImgRootPath . $arrImageInfo["savepath"] . $arrImageInfo["savename"];
            
            $image = new \Think\Image();
            $image->open($sImagePath);

            $arrImageInfo["width"] = $image->width();
            $arrImageInfo["height"] = $image->height();
            $arrImageInfo["mime"] = $image->mime();
            
            $addFile = $this->SaveImageToDb($arrImageInfo);

            $arrReturn["url"] = SITE_URL . '/Public' . $arrImageInfo["savepath"] . $arrImageInfo["savename"];
            $arrReturn["error"] = 0;    //兼容KindEditor图片上传
            
            exit(json_encode($arrReturn));
        }
    }
    
    /*
     * 保存图片文件信息到数据库
     */
    private function SaveImageToDb($_fileinfo){
        $arrData = array(
            "filename"=>$_fileinfo["name"],
            "type"=>$_fileinfo["type"],
            "size"=>$_fileinfo["size"],
            "width"=>$_fileinfo["width"],
            "height"=>$_fileinfo["height"],
            "key"=>$_fileinfo["key"],
            "mime"=>$_fileinfo["mime"],
            "ext"=>$_fileinfo["ext"],
            "md5"=>$_fileinfo["md5"],
            "sha1"=>$_fileinfo["sha1"],
            "savename"=>$_fileinfo["savename"],
            "savepath"=>$_fileinfo["savepath"],
            "addtime"=>time(),
            "admid"=>get_s_id(),
        );

        $model = M("upload_images");
        return $model->add($arrData);
    }
    
    /*
     * 保存文件信息到数据库
     */
    private function SaveFileToDb($_fileinfo){
        $arrData = array(
            "filename"=>$_fileinfo["name"],
            "type"=>$_fileinfo["type"],
            "size"=>$_fileinfo["size"],
            "key"=>$_fileinfo["key"],
            "ext"=>$_fileinfo["ext"],
            "md5"=>$_fileinfo["md5"],
            "sha1"=>$_fileinfo["sha1"],
            "savename"=>$_fileinfo["savename"],
            "savepath"=>$_fileinfo["savepath"],
            "addtime"=>time(),
            "admid"=>get_s_id(),
        );

        $model = M("upload_files");
        return $model->add($arrData);
    }

    /*
     * 创建缩略图
     * IMAGE_THUMB_SCALE = 1; 等比例缩放类型
     * IMAGE_THUMB_FILLED = 2; 缩放后填充类型
     * IMAGE_THUMB_CENTER = 3; 居中裁剪类型
     * IMAGE_THUMB_NORTHWEST = 4; 左上角裁剪类型
     * IMAGE_THUMB_SOUTHEAST = 5; 右下角裁剪类型
     * IMAGE_THUMB_FIXED = 6; 固定尺寸缩放类型
     * 
     * $_imgInfo 原图信息
     * $_crop 缩略图裁剪类型
     * $_thumbsize 缩略图宽高 
     * $_rootpath 原图固定路径
     * $_sprefix 缩略图前缀
     */
    public function CreateThumb($_imgInfo, $_crop, $_thumbsize, $_rootpath){
        $image = new \Think\Image();
        //源图片路径
        $sImagePath = $_rootpath . $_imgInfo["savepath"];
        
        //缩略图保存路径
        $sThumbSavePath = $sImagePath . "thumb/";
        
        Directory($sThumbSavePath); //创建目录

        //裁剪方式
        $nType = $_crop;
        
        $nCount = sizeof($_thumbsize);
        $arrReturn = array();
        
        for($i=0; $i<$nCount; $i++){
            $arrSize = $_thumbsize[$i];
            $sPrefix = "thumb_{$arrSize["width"]}_{$arrSize["height"]}_";
            $saveName = $sPrefix . $_imgInfo["savename"];
            $sThumbFilePath = $sThumbSavePath . $saveName;
            
            //裁剪图片操作
            $image->open($sImagePath . $_imgInfo["savename"]);
            $image->thumb($arrSize["width"], $arrSize["height"], $nType)->save($sThumbFilePath);
            
            $info = $image->open($sThumbFilePath);
            
            $arrThumbInfo = array(
                'savename'=>$saveName,
                'savepath'=>$_imgInfo["savepath"] . "thumb/",
                'width'=>$image->width(),
                'height'=>$image->height(),
                'type'=>$image->type(),
                'mime'=>$image->mime(),
                'size'=>filesize($sThumbFilePath),
            );
            
            $arrData = array_merge($_imgInfo, $arrThumbInfo); 
            $this->SaveFileToDb($arrData);
            
            array_push($arrReturn, $arrThumbInfo);
        }

        return $arrReturn[0];
    }
}