<?php
/**
 * Created by PhpStorm.
 * User: ssc
 * Date: 2018/10/14
 * Time: 17:28
 */

namespace Home\Controller;


class InformationController extends ComController
{
    public  function index(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $item =  D('information')->where("id='$id'")->find();

        $img_id = $item["img"];
        $img_photo = D('photo')->where("id='$img_id'")->find();
        $item['img_photo'] = $img_photo;

        $this->assign('item', $item);
        $this->display();
    }
    public  function home(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $item =  D('information')->where("id='$id'")->find();

        $img_id = $item["img"];
        $img_photo = D('photo')->where("id='$img_id'")->find();
        $item['img_photo'] = $img_photo;

        $this->assign('item', $item);
        $this->display();
    }
}