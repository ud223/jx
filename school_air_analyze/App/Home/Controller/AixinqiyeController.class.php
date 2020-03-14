<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/11/1
 * Time: 13:28
 */

namespace Home\Controller;


class AixinqiyeController extends ComController
{
    public  function index(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $item =  D('aixinqiye')->where("id='$id'")->find();

        $img_id = $item["img"];
        $logo_id = $item["logo"];
        $img_photo = D('photo')->where("id='$img_id'")->find();
        $logo_photo = D('photo')->where("id='$logo_id'")->find();
        $item['img_photo'] = $img_photo;
        $item['logo_photo'] = $logo_photo;

        $this->assign('item', $item);
        $this->display();
    }

}