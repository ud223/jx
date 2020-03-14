<?php
/**
 * 日    期：2016-01-21
 * 版    本：1.0.0
 * 功能说明：新闻中心。
 *
 **/
namespace Home\Controller;

use Vendor\Page;

class NewsController extends ComController
{    
    public function index(){        
        //类目信息
        $zonglei = M('category')->where(array('id'=>'4'))->find();
        //子类        
        $zilei = M('category')->where(array('pid'=>'4'))->order('o asc')->select();
        $this->assign('zonglei', $zonglei);
        $page_size = 10;//新闻中心
        //子类类型
        $zileiid = isset($_GET['zid']) ? $_GET['zid'] : '4';
        $this->assign('zileiid',$zileiid);
        $zileiinfo = M('category')->where(array('id'=>$zileiid))->find();
        $where['sid'] = $zileiid;
        $count = M('article')->where($where)->count();
        $Page = new \Think\SelfPage($count, $page_size);
        $zileiinfo['zlinfo'] = M('article')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('t desc')->select();
        //$zileiinfo['zlinfo'] = M('article')->where($where)->order('o desc')->select();
        $show = $Page->show();

        $this->assign('page',$show);
        $this->assign('zilei', $zilei);
        $this->assign('zileiinfo', $zileiinfo);
        $this->display();
    }
    
    public function detail(){
        $aid = intval($_GET['id']);
        $article = M('article')->where('aid='.$aid)->find();        
        //查找父类信息
        $fulei = M('category')->where(array('id'=>$article['sid']))->find();
        $zonglei = M('category')->where(array('id'=>$fulei['pid']))->find(); 
        //子类
        $zilei = M('category')->where(array('pid'=>$zonglei['id']))->select();
        $this->assign('zilei', $zilei);
        
        $this->assign('article',$article);
        $this->assign('aid',$aid);
        $this->assign('zonglei',$zonglei);
        $this->assign('fulei',$fulei);
        $this -> display();
    }
    
}