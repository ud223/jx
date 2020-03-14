<?php
/**
 * 日    期：2016-01-21
 * 版    本：1.0.0
 * 功能说明：前台控制器演示。
 *
 **/
namespace Home\Controller;

use Vendor\Page;

class AboutController extends ComController
{    
    public function index(){        
        //类目信息
        $zonglei = M('category')->where(array('id'=>'5'))->find();
        //子类
        $zilei = M('category')->where(array('pid'=>'5'))->order('o ASC')->select();
        $this->assign('zonglei', $zonglei);
           
        //子类类型
        $zileiid = isset($_GET['zid']) ? $_GET['zid'] : $zilei[0]['id'];
        $this->assign('zileiid',$zileiid);
        $zileiinfo = M('category')->where(array('id'=>$zileiid))->find();
        $where['sid'] = $zileiid;        
        if(($zileiid == '15') || ($zileiid == '16') || ($zileiid == '17')){ //公司团队列表显示
            $zileiinfo['zlinfo'] = M('article')->where($where)->order('o desc')->select();
        }else{
            $zileiinfo['zlinfo'] = M('article')->where($where)->order('o desc')->find();     
        } 
        $this->assign('zilei', $zilei);
        $this->assign('zileiinfo', $zileiinfo);        
        $this->display();
    } 

}