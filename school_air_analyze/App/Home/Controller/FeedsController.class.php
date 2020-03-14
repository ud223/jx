<?php
/**
 * 日    期：2016-01-21
 * 版    本：1.0.0
 * 功能说明：前台控制器演示。
 *
 **/
namespace Home\Controller;

use Vendor\Page;

class FeedsController extends ComController
{    
    public function index(){        
        //类目信息
        $zonglei = M('category')->where(array('id'=>'19'))->find();
        //子类
        $zilei = M('category')->where(array('pid'=>'19'))->order('o ASC')->select();
        $this->assign('zonglei', $zonglei);
           
        //子类类型
        $zileiid = isset($_GET['zid']) ? $_GET['zid'] : '19';
        $this->assign('zileiid',$zileiid);
        $zileiinfo = M('category')->where(array('id'=>$zileiid))->find();  
        $where['sid'] = $zileiid;
        $zileiinfo['zlinfo'] = M('article')->where($where)->order('t desc')->find();     
        $this->assign('zilei', $zilei);
        $this->assign('zileiinfo', $zileiinfo);        
        $this->display();
    } 

}