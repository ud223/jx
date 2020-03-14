<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/19/019
 * Time: 21:39
 */

namespace Qwadmin\Controller;


class GuideController extends ComController
{

    public function index($p = 1){

        $p = intval($p) > 0 ? $p : 1;

        $guideModel = D('guide');
        $photoModel = D('photo');
        $guide_typeModel = D('guide_type');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            $data = array();
            $data['name']= array('like', '%'.$keyword.'%');
            $data['is_delete']=0;
        }else{
            $data = array();
            $data['is_delete']=0;
        }

        $count = $guideModel->where($data)->count();

        $img_list = $guideModel->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();

        $list = array();

        foreach ($img_list as $p) {
            $p['photo'] = $photoModel->where("id={$p['logo']}")->find();
            $p['type'] = $guide_typeModel->where("id={$p['type']}")->find();
            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function add() {
        $guide = D('guide')->where('id')->select();
        $guide_type = D('guide_type')->select();

        $this->assign('guide_type',$guide_type);
        $this->assign('guide', $guide);

        $this->display('form');
    }



    public function update($id){
        $file = array();
        //是否上传文件1
        if (!empty($_FILES['info_file']['tmp_name'])) {
            $filename=$_FILES['info_file']['name'];
            $tmp_name=$_FILES['info_file']['tmp_name'];
            $name=get_ext($filename);
            $pdf="pdf";
            $pdf1="PDF";
            if($name != $pdf and  $name != $pdf1){
                $this->error('抱歉，只准上传pdf类型文件');
            }

            //print_r($name); exit;
            $file_name = isset($_POST['file_name']) ? $_POST['file_name'] : false;

            $save_path = "/Public/uploads/". build_order_no() .'.'.$name;

            $file_result = move_uploaded_file($tmp_name, "/var/www/Intelligent_community" . $save_path);

            if (!$file_result) {
                $this->error('文件1上传失败'); exit;
            }

            $file['id'] = build_order_no();
            $file['file_name'] = $file_name;
            $file['src'] = $save_path;

            D('guide_fileu')->add($file);
        }
        else {
            $item = D('guide')->where("id='$id'")->find();
            if(!$item['file_1']){
                $file['id'] ='null';
            }else{
                $file['id'] = $item['file_1'];
            }
        }

        $file2 = array();

        //是否上传文件2
        if (!empty($_FILES['file']['tmp_name'])) {
            $file2name=$_FILES['file']['name'];
            $tmp2_name=$_FILES['file']['tmp_name'];
            $name2=get_ext($file2name);
            $pdf="pdf";
            $pdf1="PDF";
            if($name2 != $pdf and  $name2 != $pdf1){
                $this->error('抱歉，只准上传pdf类型文件');
            }
            $file2_name = isset($_POST['file2_name']) ? $_POST['file2_name'] : false;
            $save2_path = "/Public/uploads/". build_order_no(). $name2;

            $file2_result = move_uploaded_file($tmp2_name, "/var/www/Intelligent_community" . $save2_path);

            if (!$file2_result) {
                $this->error('文件2上传失败'); exit;
            }

            $file2['id'] = build_order_no();
            $file2['file_name'] = $file2_name;
            $file2['src'] = $save2_path;

            D('guide_fileu')->add($file2);
        }
        else {
            $item = D('guide')->where("id='$id'")->find();
            if(!$item['file_2']){
                $file2['id'] ='null';
            }else{
                $file2['id'] = $item['file_2'];
            }

        }

        $file3 = array();

        //是否上传文件2
        if (!empty($_FILES['C_file']['tmp_name'])) {
            $file3name=$_FILES['C_file']['name'];
            $tmp3_name=$_FILES['C_file']['tmp_name'];
            $name3=get_ext($file3name);
            $pdf="pdf";
            $pdf1="PDF";
            if($name3 != $pdf and  $name3 != $pdf1){
                $this->error('抱歉，只准上传pdf类型文件');
            }
            $file3_name = isset($_POST['file3_name']) ? $_POST['file3_name'] : false;
            $save3_path = "/Public/uploads/". build_order_no(). $name3;
            $file3_result = move_uploaded_file($tmp3_name, "/var/www/Intelligent_community" . $save3_path);



            if (!$file3_result) {
                $this->error('文件3上传失败'); exit;
            }

            $file3['id'] = build_order_no();
            $file3['file_name'] = $file3_name;
            $file3['src'] = $save3_path;

            D('guide_fileu')->add($file3);
        }
        else {
            $item = D('guide')->where("id='$id'")->find();
            if(!$item['file_3']){
                $file3['id'] ='null';
            }else{
                $file3['id'] = $item['file_3'];
            }

        }



        $file4 = array();

        //是否上传文件2
        if (!empty($_FILES['D_file']['tmp_name'])) {
            $file4name=$_FILES['D_file']['name'];
            $tmp4_name=$_FILES['D_file']['tmp_name'];
            $name4=get_ext($file4name);
            $pdf="pdf";
            $pdf1="PDF";
            if($name4 != $pdf and  $name4 != $pdf1){
                $this->error('抱歉，只准上传pdf类型文件');
            }
            $file4_name = isset($_POST['file4_name']) ? $_POST['file4_name'] : false;

            $save4_path = "/Public/uploads/". build_order_no(). $name4;

            $file4_result = move_uploaded_file($tmp4_name, "/var/www/Intelligent_community" . $save4_path);

            if (!$file4_result) {
                $this->error('文件4上传失败'); exit;
            }

            $file4['id'] = build_order_no();
            $file4['file_name'] = $file4_name;
            $file4['src'] = $save4_path;

            D('guide_fileu')->add($file4);
        }
        else {
            $item = D('guide')->where("id='$id'")->find();
            if(!$item['file_4']){
                $file4['id'] ='null';
            }else{
                $file4['id'] = $item['file_4'];
            }

        }

        $file5 = array();

        //是否上传文件2
        if (!empty($_FILES['E_file']['tmp_name'])) {
            $file5name=$_FILES['E_file']['name'];
            $tmp5_name=$_FILES['E_file']['tmp_name'];
            $name5=get_ext($file5name);
            $pdf="pdf";
            $pdf1="PDF";
            if($name5 != $pdf and  $name5 != $pdf1){
                $this->error('抱歉，只准上传pdf类型文件');
            }
            $file5_name = isset($_POST['file5_name']) ? $_POST['file5_name'] : false;
            $save5_path = "/Public/uploads/". build_order_no(). $name5;

            $file5_result = move_uploaded_file($tmp5_name, "/var/www/Intelligent_community" . $save5_path);

            if (!$file5_result) {
                $this->error('文件5上传失败'); exit;
            }

            $file5['id'] = build_order_no();
            $file5['file_name'] = $file5_name;
            $file5['src'] = $save5_path;

            D('guide_fileu')->add($file5);
        }
        else {
            $item = D('guide')->where("id='$id'")->find();
            if(!$item['file_5']){
                $file5['id'] ='null';
            }else{
                $file5['id'] = $item['file_5'];
            }

        }

        $file6 = array();

        //是否上传文件2
        if (!empty($_FILES['F_file']['tmp_name'])) {
            $file6name=$_FILES['F_file']['name'];
            $tmp6_name=$_FILES['F_file']['tmp_name'];

            $name6=get_ext($file6name);
            $pdf="pdf";
            $pdf1="PDF";
            if($name6 != $pdf and  $name6 != $pdf1){
                $this->error('抱歉，只准上传pdf类型文件');
            }
            $file6_name = isset($_POST['file6_name']) ? $_POST['file6_name'] : false;
            $save6_path = "/Public/uploads/". build_order_no(). $name6;

            $file6_result = move_uploaded_file($tmp6_name, "/var/www/Intelligent_community" . $save6_path);

            if (!$file6_result) {
                $this->error('文件6上传失败'); exit;
            }

            $file6['id'] = build_order_no();
            $file6['file_name'] = $file6_name;
            $file6['src'] = $save6_path;

            D('guide_fileu')->add($file6);
        }
        else {
            $item = D('guide')->where("id='$id'")->find();
            if(!$item['file_6']){
                $file6['id'] ='null';
            }else{
                $file6['id'] = $item['file_6'];
            }

        }

        $file7 = array();

        //是否上传文件2
        if (!empty($_FILES['G_file']['tmp_name'])) {
            $file7name=$_FILES['G_file']['name'];
            $tmp7_name=$_FILES['G_file']['tmp_name'];

            $name7=get_ext($file7name);
            $pdf="pdf";
            $pdf1="PDF";
            if($name7 != $pdf and  $name7 != $pdf1){
                $this->error('抱歉，只准上传pdf类型文件');
            }
            $file7_name = isset($_POST['file7_name']) ? $_POST['file7_name'] : false;
            $save7_path = "/Public/uploads/". build_order_no(). $name7;

            $file7_result = move_uploaded_file($tmp7_name, "/var/www/Intelligent_community" . $save7_path);

            if (!$file7_result) {
                $this->error('文件7上传失败'); exit;
            }

            $file7['id'] = build_order_no();
            $file7['file_name'] = $file7_name;
            $file7['src'] = $save7_path;

            D('guide_fileu')->add($file7);
        }
        else {
            $item = D('guide')->where("id='$id'")->find();
            if(!$item['file_7']){
                $file7['id'] ='null';
            }else{
                $file7['id'] = $item['file_7'];
            }

        }

        $file8 = array();

        //是否上传文件2
        if (!empty($_FILES['H_file']['tmp_name'])) {
            $file8name=$_FILES['H_file']['name'];
            $tmp8_name=$_FILES['H_file']['tmp_name'];

            $name8=get_ext($file8name);
            $pdf="pdf";
            $pdf1="PDF";
            if($name8 != $pdf and  $name8 != $pdf1){

                $this->error('抱歉，只准上传pdf类型文件');
            }
            $file8_name = isset($_POST['file8_name']) ? $_POST['file8_name'] : false;
            $save8_path = "/Public/uploads/". build_order_no(). $name8;

            $file8_result = move_uploaded_file($tmp8_name, "/var/www/Intelligent_community" . $save8_path);

            if (!$file8_result) {
                $this->error('文件8上传失败'); exit;
            }

            $file8['id'] = build_order_no();
            $file8['file_name'] = $file8_name;
            $file8['src'] = $save8_path;

            D('guide_fileu')->add($file8);
        }
        else {
            $item = D('guide')->where("id='$id'")->find();
            if(!$item['file_8']){
                $file8['id'] ='null';
            }else{
                $file8['id'] = $item['file_8'];
            }

        }

        $file9 = array();

        //是否上传文件2
        if (!empty($_FILES['I_file']['tmp_name'])) {
            $file9name=$_FILES['I_file']['name'];
            $tmp9_name=$_FILES['I_file']['tmp_name'];
            $name9=get_ext($file9name);
            $pdf="pdf";
            $pdf1="PDF";
            if($name9 != $pdf and  $name9 != $pdf1){

                $this->error('抱歉，只准上传pdf类型文件');
            }
            $file9_name = isset($_POST['file9_name']) ? $_POST['file9_name'] : false;
            $save9_path = "/Public/uploads/". build_order_no(). $name9;

            $file9_result = move_uploaded_file($tmp9_name, "/var/www/Intelligent_community" . $save9_path);

            if (!$file9_result) {
                $this->error('文件9上传失败'); exit;
            }

            $file9['id'] = build_order_no();
            $file9['file_name'] = $file9_name;
            $file9['src'] = $save9_path;

            D('guide_fileu')->add($file9);
        }
        else {
            $item = D('guide')->where("id='$id'")->find();
            if(!$item['file_9']){
                $file9['id'] ='null';
            }else{
                $file9['id'] = $item['file_9'];
            }

        }

        $file10 = array();

        //是否上传文件2
        if (!empty($_FILES['A_file']['tmp_name'])) {
            $file10name=$_FILES['A_file']['name'];
            $tmp10_name=$_FILES['A_file']['tmp_name'];
            $name10=get_ext($file10name);

            $pdf="pdf";
            $pdf1="PDF";

            if($name10 != $pdf and  $name10 != $pdf1){

                $this->error('抱歉，只准上传pdf类型文件');
            }
            $file10_name = isset($_POST['file10_name']) ? $_POST['file10_name'] : false;
            $save10_path = "/Public/uploads/". build_order_no(). $name10;

            $file10_result = move_uploaded_file($tmp10_name, "/var/www/Intelligent_community" . $save10_path);

            if (!$file10_result) {
                $this->error('文件10上传失败'); exit;
            }

            $file10['id'] = build_order_no();
            $file10['file_name'] = $file10_name;
            $file10['src'] = $save10_path;

            D('guide_fileu')->add($file10);
        }
        else {
            $item = D('guide')->where("id='$id'")->find();
            if(!$item['file_10']){
                $file10['id'] ='null';
            }else{
                $file10['id'] = $item['file_10'];
            }

        }


        //$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $phone = isset($_POST['phone']) ? $_POST['phone'] : false;
        $workshift = isset($_POST['workshift']) ? $_POST['workshift'] : false;
        $introduce = isset($_POST['introduce']) ? $_POST['introduce'] : false;
        $type = isset($_POST['type']) ? intval($_POST['type']):0;
        $logo= I('post.logo', '', 'strip_tags');

        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = $logo;

        $result_photo = D('photo')->add($photo);

        if (!$result_photo) {
            $this->error('图片上传失败'); exit;
        }
        // print_r($file['id']); exit;
        $guide = array();

        $guide['name'] = $name;
        $guide['file_1'] = $file['id'];
        $guide['file_2'] =  $file2['id'];
        $guide['file_3'] =  $file3['id'];
        $guide['file_4'] =  $file4['id'];
        $guide['file_5'] =  $file5['id'];
        $guide['file_6'] =  $file6['id'];
        $guide['file_7'] =  $file7['id'];
        $guide['file_8'] =  $file8['id'];
        $guide['file_9'] =  $file9['id'];
        $guide['file_10'] =  $file10['id'];
        $guide['phone'] = $phone;
        $guide['workshift'] = $workshift;
        $guide['introduce'] = $introduce;
        $guide['logo'] =  $photo['id'];
        $guide['type'] =  $type;

        if ($id) {
            D('guide')->data($guide)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/guide/index'));
        } else {
            $result = D('guide')->data($guide)->add();
            if ($result) {
                $item = D('guide')->where("id='$result'")->find();

                if ($item) {
                    $this->error('添加成功！',U('/qwadmin/guide/index'));
                }
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function edit($id = 0)
    {
        $id = intval($id);

        $item = D('guide')->where("id='$id'")->find();
        $guide_type = D('guide_type')->select();

        if (!$item) {
            $this->error('参数错误！');
        }

        $photo_id = $item["logo"];

        $photo = D('photo')->where("id='$photo_id'")->find();
        $item['logo_photo'] = $photo;

        $file_1_id = $item["file_1"];
        $file_2_id = $item["file_2"];
        $file_3_id = $item["file_3"];
        $file_4_id = $item["file_4"];
        $file_5_id = $item["file_5"];
        $file_6_id = $item["file_6"];
        $file_7_id = $item["file_7"];
        $file_8_id = $item["file_8"];
        $file_9_id = $item["file_9"];
        $file_10_id = $item["file_10"];


        $file_1 = D('guide_fileu')->where("id='$file_1_id'")->find();

        $file_2 = D('guide_fileu')->where("id='$file_2_id'")->find();

        $file_3 = D('guide_fileu')->where("id='$file_3_id'")->find();
        $file_4 = D('guide_fileu')->where("id='$file_4_id'")->find();
        $file_5 = D('guide_fileu')->where("id='$file_5_id'")->find();
        $file_6 = D('guide_fileu')->where("id='$file_6_id'")->find();
        $file_7 = D('guide_fileu')->where("id='$file_7_id'")->find();
        $file_8 = D('guide_fileu')->where("id='$file_8_id'")->find();
        $file_9 = D('guide_fileu')->where("id='$file_9_id'")->find();
        $file_10 = D('guide_fileu')->where("id='$file_10_id'")->find();

//        $item['file_3'] =$file_1;
//
//        $item['file_4'] =$file_2;

        //print_r($item); exit;


//        $this->assign('logo',$photo);
        $this->assign('file_1',$file_1);
        $this->assign('file_2',$file_2);
        $this->assign('file_3',$file_3);
        $this->assign('file_4',$file_4);
        $this->assign('file_5',$file_5);
        $this->assign('file_6',$file_6);
        $this->assign('file_7',$file_7);
        $this->assign('file_8',$file_8);
        $this->assign('file_9',$file_9);
        $this->assign('file_10',$file_10);
        $this->assign('guide_type',$guide_type);
        $this->assign('currentcategory',$item);
        $this->display('form');
    }

    public function del(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        if ($id) {
            if (is_array($id)) {
                $ids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();

            $data['is_delete'] = '1';

            $result =M('guide')->where($map)->data($data)->save();

            if ($result !== false) {
                addlog('删除内容，AID：' . $id);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        }else {
            $this->error('参数错误！');
        }
    }

}