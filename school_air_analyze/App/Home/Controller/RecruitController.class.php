<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/5/005
 * Time: 9:39
 */

namespace Home\Controller;

use Think\Controller;

class RecruitController extends Controller
{
    public  function index(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $item =  D('enterprise_recruit')->where("id='$id'")->find();

        $img_id = $item["img"];
        $img_photo = D('photo')->where("id='$img_id'")->find();
        $item['img_photo'] = $img_photo;

        $this->assign('item', $item);
        $this->display();
    }
}