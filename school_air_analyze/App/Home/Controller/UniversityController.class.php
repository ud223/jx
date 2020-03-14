<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/10/31
 * Time: 11:26
 */

namespace Home\Controller;


class UniversityController extends ComController
{
    public  function index(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $item =  D('university')->where("id='$id'")->find();

        $img_id = $item["img"];
        $img_photo = D('photo')->where("id='$img_id'")->find();
        $item['img_photo'] = $img_photo;

        $this->assign('item', $item);
        $this->display();
    }

}