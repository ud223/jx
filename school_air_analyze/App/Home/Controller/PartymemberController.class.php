<?php
/**
 * Created by PhpStorm.
 * User: ssc
 * Date: 2018/10/19
 * Time: 16:09
 */

namespace Home\Controller;

use Think\Controller;

class PartymemberController extends Controller
{
    public  function index(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $item =  D('community_partymember')->where("id='$id'")->find();
//11
        $img_id = $item["img"];
        $img_photo = D('photo')->where("id='$img_id'")->find();
        $item['img_photo'] = $img_photo;

        $this->assign('item', $item);
        $this->display();
    }
}