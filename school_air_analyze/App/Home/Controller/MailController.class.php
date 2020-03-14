<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2019/4/23
 * Time: 13:10
 */

namespace Home\Controller;


class MailController extends ComController{
    public  function index(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $item =  D('mail')->where("id='$id'")->find();

        $this->assign('item', $item);
        $this->display();
    }

}