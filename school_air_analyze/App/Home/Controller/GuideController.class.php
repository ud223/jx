<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6/006
 * Time: 10:50
 */

namespace Home\Controller;

use Think\Controller;

class GuideController extends Controller
{

    public  function index($id = 0){

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;//

        $item =  D('guide')->where("id='$id'")->find();

        $fileu_id = $item["file_1"];
        $fileu = D('guide_fileu')->where("id='$fileu_id'")->find();
        $item['file_1_photo'] = $fileu;


        $fileu2_id = $item["file_2"];
        $fileu2 = D('guide_fileu')->where("id='$fileu2_id'")->find();
        $item['file_2_photo'] = $fileu2;

        $fileu3_id = $item["file_3"];
        $fileu3 = D('guide_fileu')->where("id='$fileu3_id'")->find();
        $item['file_3_photo'] = $fileu3;

        $fileu4_id = $item["file_4"];
        $fileu4 = D('guide_fileu')->where("id='$fileu4_id'")->find();
        $item['file_4_photo'] = $fileu4;

        $fileu5_id = $item["file_5"];
        $fileu5 = D('guide_fileu')->where("id='$fileu5_id'")->find();
        $item['file_5_photo'] = $fileu5;

        $fileu6_id = $item["file_6"];
        $fileu6 = D('guide_fileu')->where("id='$fileu6_id'")->find();
        $item['file_6_photo'] = $fileu6;

        $fileu7_id = $item["file_7"];
        $fileu7 = D('guide_fileu')->where("id='$fileu7_id'")->find();
        $item['file_7_photo'] = $fileu7;

        $fileu8_id = $item["file_8"];
        $fileu8 = D('guide_fileu')->where("id='$fileu8_id'")->find();
        $item['file_8_photo'] = $fileu8;

        $fileu9_id = $item["file_9"];
        $fileu9 = D('guide_fileu')->where("id='$fileu9_id'")->find();
        $item['file_9_photo'] = $fileu9;

        $fileu10_id = $item["file_10"];
        $fileu10 = D('guide_fileu')->where("id='$fileu10_id'")->find();
        $item['file_10_photo'] = $fileu10;

        $this->assign('item', $item);
        $this->display();
    }

}