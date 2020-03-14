<?php
//region 设置登录帐号类型 cookies
/**
 * 设置登录帐号类型Cookies
 *
 * @param $_account_type 帐号状态
 *
 * @internal param array $_arrinfo 管理员信息
 */
function SetLoginAccountTypeCookies($_account_type){
  cookie(CookiesLoginAccountTypeKey(), $_account_type, array('expire'=>time() + 3600));
}

/**
 * 获取登录帐号类型Cookies
 *
 * @return mixed
 * @internal param array $_arrinfo 管理员信息
 */
function GetLoginAccountTypeCookeis(){
  return cookie(CookiesLoginAccountTypeKey());
}

/**
 * 清除登录帐号类型Cookies
 *
 * @param $_admin_id
 *
 * @internal param array $_arrinfo 管理员信息
 */
function ClearLoginAccountTypeCookies(){
  cookie(CookiesLoginAccountTypeKey(), null);
}

/**
 * 获取登录帐号类型Cookies key
 *
 * @return string
 * @internal param array $_arrinfo 管理员信息
 */
function CookiesLoginAccountTypeKey(){
  return GetCookiePrefix() . "AccountType";
}
//endregion 设置登录帐号类型 cookies

//region 管理员ID cookies
/**
 * 设置管理员Cookies
 *
 * @param $_account_id
 *
 * @internal param array $_arrinfo 管理员信息
 */
function SetAccountIDCookies($_account_id){
  cookie(CookiesAccountIDKey(), $_account_id, array('expire'=>time() + 3600));
}

/**
 * 设置管理员Cookies
 *
 * @param $_admin_id
 *
 * @internal param array $_arrinfo 管理员信息
 */
function ClearAccountIDCookies(){
  cookie(CookiesAccountIDKey(), null);
}

/**
 * 获取管理员Cookies
 *
 * @return mixed
 * @internal param array $_arrinfo 管理员信息
 */
function GetAccountIDCookeis(){
  return cookie(CookiesAccountIDKey());
}

/**
 * 获取管理员ID的Cookies key
 *
 * @return string
 * @internal param array $_arrinfo 管理员信息
 */
function CookiesAccountIDKey(){
  return GetCookiePrefix() . "AccountID";
}
//endregion 管理员ID cookies

/**
 * 标示类值显示样式
 * @param int $_val 样式类型：1，信息；2，主要信息；3，成功的；4.警告；5.危险；
 * @return string
 */
function FmtValueStyle($_val, $_str, $_othercss=''){
  $sCss = "";

  switch ($_val){
    case 1 :
      $sCss = "label-info";
      break;

    case 2 :
      $sCss = "label-primary";
      break;

    case 3 :
      $sCss = "label-success";
      break;

    case 4 :
      $sCss = "label-warning";
      break;

    case 5 :
      $sCss = "label-danger";
      break;

    case 6 :
      $sCss = "label-white";
      break;

    default :
      $sCss = "label-default";
      break;
  }

  return '<span class="label '. $sCss . ' ' . $_othercss .'">'. $_str .'</span>';
}

function GetProductContentImgCfg(){
  return C('UPLOAD_PRODUCT_CONTENT_IMAGE_CONFIG');
}

function GetProductAttachmentImgCfg(){
  return C('UPLOAD_PRODUCT_ATTACHMENT_IMAGE_CONFIG');
}

function GetProductAttachmentFileCfg(){
  return C('UPLOAD_PRODUCT_ATTACHMENT_FILE_CONFIG');
}

function GetCustomerIdCardImgCfg(){
  return C('UPLOAD_CUSTOMER_IDCARD_IMAGE_CONFIG');
}

function GetOrderAttachmentCfg(){
  return C('UPLOAD_ORDER_ATTACHMENT_CONFIG');
}

