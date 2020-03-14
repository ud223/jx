<?php
/**
 * 日    期：2016-01-21
 * 版    本：1.0.0
 * 功能说明：前台控制器演示。
 *
 **/
namespace Home\Controller;

use Vendor\Page;

class IndexController extends ComController
{
    public function index(){ 
        //mogul视频
        $wheremogul['sid'] = '2';
        $mogul = M('article')->where($wheremogul)->order('t desc')->select();
        $this->assign('mogul',$mogul);
        //mogul分类信息
        $wheremogullei['id'] = '2';
        $wheremogulleiinfo = M('article')->where($wheremogullei)->find();
        $this->assign('wheremogulleiinfo',$wheremogulleiinfo);
        
        //作品分类信息
        $wherezuoplei['id'] = '3';
        $wherezuoleiinfo = M('article')->where($wherezuoplei)->find();
        $this->assign('wherezuoleiinfo',$wherezuoleiinfo);
        
        //作品最新上传的视频
        $workall['sid'] = array('in','10,11,12,13');
        $wordknew = M('article')->where($workall)->order('t desc')->find();
        $this->assign('wordknew',$wordknew);
        
        //mogul所有视频
        $wheremogulall['sid'] = array('in','6,7,8,9');
        $mogulall = M('article')->where($wheremogulall)->order('t desc')->select();
        $this->assign('mogulall',$mogulall);
        
        //首页大图mogul和作品
        $this->mogulbigpic=M('zuopin')->find(5);
        $this->zuopinbigpic=M('zuopin')->find(6);

        //作品的第一个子栏目
        $wherework['sid'] = '10';
        $works = M('article')->where($wherework)->order('t desc')->select();
        $this->assign('works',$works);
        
        //合作资源
        $wherezy['sid'] = '16';
        $hezuoziyuan = M('article')->where($wherezy)->order('t desc')->select();
        $this->assign('hezuoziyuan',$hezuoziyuan);
        
        //作品的子栏目       
        $zuopzilei = M('category')->where(array('pid'=>'3'))->order('o desc')->select();
        $this->assign('zuopzilei',$zuopzilei);


//        print_r($zuopzilei); exit;
        
        $this->display();
    }   
    
    public function search(){ 
        if(IS_POST){
            $pianming = $_POST['pian'];
            $cond['title']=array('like','%'.$pianming.'%');
            $allvideo = M('article')->where($cond)->select();
            $this->assign('allvideo',$allvideo);
        }
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