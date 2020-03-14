<?php

/**
 *
 * 日    期：2016-01-21
 * 版    本：1.0.0
 * 功能说明：前台公用控制器。
 *
 * */

namespace Home\Controller;
use Think\Controller;

class ComController extends Controller {

    public function _initialize() {
        //查询出导航栏目信息
        $catelistdao = M('category')->where(array('pid' => '0','is_dao' => '0'))->order('o desc')->select();
        foreach ($catelistdao as $key => $value) {
            $catelistdao[$key]['sonlist'] = M('category')->where(array('pid' => $value['id'],'is_dao' => '0'))->order('o ASC')->select();
        }
        $this->assign("catelistdao", $catelistdao);
        C(setting());
        $links = M('links')->limit(6)->select();
        $this->assign('links', $links);
        $flash = M('flash')->limit(3)->select();
        $this->assign('flash', $flash);
        
        //查找出所有栏目的信息
        $indexinfo = M('category')->where(array('id'=>'1'))->find();
        $indexinfo1 = M('category')->where(array('id'=>'2'))->find();
        $indexinfo2 = M('category')->where(array('id'=>'3'))->find();
        $indexinfo3 = M('category')->where(array('id'=>'4'))->find();
        $indexinfo4 = M('category')->where(array('id'=>'5'))->find();
        $indexinfo5 = M('category')->where(array('id'=>'19'))->find();
        $logo = M('logo')->where("id=1")->find();


        $this->assign('indexinfo', $indexinfo);
        $this->assign('indexinfo1', $indexinfo1);
        $this->assign('indexinfo2', $indexinfo2);
        $this->assign('indexinfo3', $indexinfo3);
        $this->assign('indexinfo4', $indexinfo4);
        $this->assign('indexinfo5', $indexinfo5);
        $this->assign("logo", $logo);


        //查找作品子类图片
        $this->filmcate=M("zuopin")->where(array('id'=>1))->getField("logo");
        $this->wangcate=M("zuopin")->where(array('id'=>2))->getField("logo");
        $this->adcate=M("zuopin")->where(array('id'=>3))->getField("logo");
        $this->yugaocate=M("zuopin")->where(array('id'=>4))->getField("logo");
        
    }
}
