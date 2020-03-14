<?php
/**
 * 日    期：2016-01-21
 * 版    本：1.0.0
 * 功能说明：Mogul。
 *
 **/
namespace Home\Controller;

use Vendor\Page;

class MogulController extends ComController
{    
    public function index(){        
        //类目信息
        $zonglei = M('category')->where(array('id'=>'2'))->find();
        //子类
        $zilei = M('category')->where(array('pid'=>'2'))->order('o desc')->select();
        $this->assign('zonglei', $zonglei);
        $page_size = 12;        
        //子类类型
        $zileiid = isset($_GET['zid']) ? $_GET['zid'] : $zilei[0]['id'];
        $this->assign('zileiid',$zileiid);
        $zileiinfo = M('category')->where(array('id'=>$zileiid))->find();
        $where['sid'] = $zileiid;
        $count = M('article')->where($where)->count();
        $Page = new \Think\SelfPage($count, $page_size);
        //$zileiinfo['zlinfo'] = M('article')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('o desc')->select();
        $zileiinfo['zlinfo'] = M('article')->where($where)->order('t desc')->select();

        $show = $Page->show();

        $this->assign('page',$show);
        $this->assign('zilei', $zilei);
        $this->assign('zileiinfo', $zileiinfo);
        // $this->assign('zonglei', $zonglei);
        $this->display();
    }
    
    public function detail(){
        $aid = intval($_GET['id']);
        $article = M('article')->where('aid='.$aid)->find();        
        //查找父类信息
        $fulei = M('category')->where(array('id'=>$article['sid']))->find();
        $zonglei = M('category')->where(array('id'=>$fulei['pid']))->find(); 
        
        //查找该父类的文章
        $fuleiinfo = M('article')->where(array('sid'=>$fulei['id']))->order('t desc')->select(); 
        $this->assign('fuleiinfo',$fuleiinfo);
        
        //子类
        $zilei = M('category')->where(array('pid'=>$zonglei['id']))->order('o desc')->select();
        $this->assign('zilei', $zilei);
        
        $this->assign('zileiid',$fulei['id']);
        
        $this->assign('article',$article);
        $this->assign('aid',$aid);
        $this->assign('zonglei',$zonglei);
        $this->assign('fulei',$fulei);
        $this -> display();
    }
    
}