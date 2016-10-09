<?php
return array(
  'VERIFY' => array(
    'useImgBg' => true, //是否使用背景图片 默认为false
    'fontSize' => 36,   //验证码字体大小（像素） 默认为25
    'useCurve'=>false,   //是否使用混淆曲线 默认为true
    'useNoise'=>true,   //是否添加杂点 默认为true
    'imageW'=>300,    //验证码宽度 设置为0为自动计算
    'imageH'=>0,    //验证码高度 设置为0为自动计算
    'length' => 4,  //验证码位数
    'fontttf' => '4.ttf', //指定验证码字体 默认为随机获取
    //'useZh' => false,   //是否使用中文验证码
    //'codeSet' => '2345678abcdefghjkmnpqrstwxyz',
  ),
  
  //region 图片上传配置
  //产品详细内容图片上传的配置参数
  'UPLOAD_PRODUCT_CONTENT_IMAGE_CONFIG' => array(
    'size' => 5242880,//文件大小，单位：字节
    'size_desc' => '5M',
    'type' => 'image/jpeg,image/png', //允许上传的mini type
    'desc' => 'jpg,png', //运行上传的mini描述
  ),

  //用户证件照图片上传的配置参数
  'UPLOAD_CUSTOMER_IDCARD_IMAGE_CONFIG' => array(
    'size' => 5242880,//文件大小，单位：字节
    'size_desc' => '5M',
    'type' => 'image/jpeg,image/png', //允许上传的mini type
    'desc' => 'jpg,png', //运行上传的mini描述
  ),
  
  //用户证件照图片上传的配置参数
  'UPLOAD_ORDER_ATTACHMENT_CONFIG' => array(
    'size' => 5242880,//文件大小，单位：字节
    'size_desc' => '5M',
    'type' => 'application/zip,application/x-rar,application/x-rar-compressed,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/octet-stream,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document', //允许上传的mini type
    'desc' => 'zip,rar,doc,docx,xlsx,xls', //运行上传的mini描述
  ),
  //endregion 图片上传配置
);