<?php
namespace CustomerRreservationSystem\Widget;
use Think\Controller;

class PageWidget extends \CustomerRreservationSystem\Controller\CommonController {
  //页面头部包含的文件
  public function  pageHeadInclude(){
    $this->display('Widget:page_head_include');
  }

  //页面底部包含的文件
  public function  pageFootInclude(){
    $this->display('Widget:page_foot_include');
  }

  public function pageMeta(){
    $this->display('Widget:page_meta');
  }
  
  public function pageTitle(){
    $this->display('Widget:page_title');
  }

  public function pageLang(){
    $this->display('Widget:page_lang');
  }

  public function bodyCss(){
    $this->display('Widget:body_css');
  }
  
  //region Wap
  //页面头部包含的文件
  public function wapPageHeadInclude(){
    $this->display('Widget:wap_page_head_include');
  }
  
  //页面底部包含的文件
  public function wapPageFootInclude(){
    $this->display('Widget:wap_page_foot_include');
  }
  
  public function wapPageMeta(){
    $this->display('Widget:wap_page_meta');
  }
  
  public function wapPageTitle(){
    $this->display('Widget:wap_page_title');
  }
  //endregion Wap
}