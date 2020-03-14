<?php
/**
 * Created by PhpStorm.
 * User: ssc
 * Date: 2018/10/8
 * Time: 9:44
 */

namespace Qwadmin\Controller;


class ActivityController extends ComController
{
    public function index($p=1){
        $active_stateModel=D('active_state');
        $integral_stateModel=D('integral_state');
        $active_ynModel = D('active_yn');
        $p = intval($p) > 0 ? $p : 1;

        $pagesize = 20;
        $offset = $pagesize * ($p - 1);

        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            $data = array();
            $data['title']= array('like', '%'.$keyword.'%');;
            $data['is_delete']=0;
        }else{
            $data = array();
            $data['is_delete']=0;
        }

        $count = D('community_activity')->where($data)->count();

        $tmp_list = D('community_activity')->order('id desc')->limit($offset . ',' . $pagesize)->where($data)->select();

        $list = array();

        foreach ($tmp_list as $p) {
            $p['active_state'] = $active_stateModel->where("id={$p['active_state']}")->find();
            $p['integral_state'] = $integral_stateModel->where("id={$p['integral_state']}")->find();
            $p['scene'] = $active_ynModel->where("id={$p['scene']}")->find();

            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function add(){
        $activity_controlModel = D('activity_control');
        $community_activity = D('community_activity')->where('id')->select();
        $active_state = D('active_state')->select();
        $integral_state = D('integral_state')->select();
        $active_yn = D('active_yn')->select();
        $activity_control = $activity_controlModel ->select();

        $this->assign('active_yn',$active_yn);
        $this->assign('activity_control',$activity_control);
        $this->assign('integral_state',$integral_state);
        $this->assign('active_state',$active_state);
        $this->assign('community_activity', $community_activity);

        $this->display('form');
    }

    public function update(){
        $activity_idModel = D('activity_id');
        $manyModel = D('many');

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $title= isset($_POST['title']) ? $_POST['title'] : false;
        $time= isset($_POST['time']) ? $_POST['time'] : false;
        $deadline= isset($_POST['deadline']) ? $_POST['deadline'] : false;
        $limit_number= isset($_POST['limit_number']) ? $_POST['limit_number'] : false;
        $participate_number= isset($_POST['participate_number']) ? $_POST['participate_number'] : false;
        $integral= isset($_POST['integral']) ? $_POST['integral'] : false;
        $introduce= isset($_POST['introduce']) ? $_POST['introduce'] : false;
        $active_state = isset($_POST['active_state']) ? intval($_POST['active_state']):0;
        $integral_state = isset($_POST['integral_state']) ? intval($_POST['integral_state']):0;
        $phone= isset($_POST['phone']) ? $_POST['phone'] : false;
//        $scene = isset($_POST['scene']) ? intval($_POST['scene']):0;

        $json = isset($_POST['data_json']) ? $_POST['data_json'] : false;

        //$students=json_decode($json);

        //var_dump($json2Array);

        $logo= I('post.logo', '', 'strip_tags');
        $group_pictures= I('post.group_pictures', '', 'strip_tags');

        $photo1 = array();
        $photo1['id'] = build_order_no();
        $photo1['img'] = $group_pictures;

        $result_photo1 = D('photo')->add($photo1);

        if (!$result_photo1) {
            $this->error('群图片上传失败'); exit;
        }


        $photo = array();
        $photo['id'] = build_order_no();
        $photo['img'] = $logo;

        $result_photo = D('photo')->add($photo);

        if (!$result_photo) {
            $this->error('logo上传失败'); exit;
        }

        $data=array();
        $data['title']=$title;
        $data['time']=$time;
        $data['deadline']=$deadline;
        $data['limit_number']=$limit_number;
        $data['participate_number']=$participate_number;
        $data['integral_state']=$integral_state;
        $data['integral']=$integral;
        $data['active_state']=$active_state;
        $data['introduce']=$introduce;
        $data['active_state1'] = 0;
        $data['phone']=$phone;

        $data['logo']=$photo['id'];
        $data['group_pictures']=$photo1['id'];

        if($group_pictures){
            $data['scene'] = 1;
        }

        if ($id) {
            D('community_activity')->data($data)->where('id=' . $id)->save();
            echo ajax_api(true,'201','编辑成功');exit;
        } else {
            $data["id"] = build_order_no();
            $result = D('community_activity')->data($data)->add();
            $item = array();
            $item['id'] = build_order_no();
            $item['activity_id'] = $data["id"];
            $item['time'] = date('Y-m-d H:s:i', time());

            $activity_idModel->data($item)->add();

            $json2Array = json_decode($json,true);

            foreach ($json2Array as $p) {
                if($p){
                    $list = array();

                    $list['checked'] = $p['checked'];
                    $list['is_only'] = $p['checkedwy'];
                    $list['code'] = $p['code'];
                    $list['title'] = $p['title'];
                    $list['type'] = $p['type'];
                    $list['sort'] = $p['sort'];
                    $list['length'] = $p['length'];
                    $list['activity_id'] = $data["id"];
                    $list['time'] = date('Y-m-d H:s:i', time());
                    $list['activity_content'] = $item['id'];
                    $content = $p['selection'];

                    if($p['type'] == 4 ||$p['type'] == 3){
                        $list['tips'] = build_order_no();

                        $activity_form_id = $list['tips'];

                        $list['dofaut'] = 1;

                        foreach($content as $t){
                            $tips = array();
                            if(!$t){
                                $tips['name'] = 'delete';
                                $tips['is_delete'] = 1;
                            }else{
                                $tips['name'] = $t;
                            }
                            $tips['activity_form_id'] = $activity_form_id;
                            $tips['time'] = date('Y-m-d H:s:i', time());
                            $tips['activity_id'] =$data["id"];

                            $manyModel->data($tips)->add();

                        }

                    }else{
                        $list['tips'] = 1;
                    }

                    D('activity_form')->add($list);
                }

            }

            if ($result) {
                echo ajax_api(true,'201','添加成功');exit;
            } else {
                $this->error('抱歉，未知错误！');
            }

        }
    }

    public function edit($id = 0){
        $id = intval($id);
        $item = D('community_activity')->where("id='$id'")->find();
        $active_state = D('active_state')->select();
        $integral_state = D('integral_state')->select();
        $active_yn = D('active_yn')->select();

        if (!$item) {
            $this->error('参数错误！');
        }
        $photo_id = $item["logo"];
        $photo = D('photo')->where("id='$photo_id'")->find();
        $item['logo_photo'] = $photo;

        $group_pictures = $item["group_pictures"];

        $photo1 = D('photo')->where("id='$group_pictures'")->find();

        $item["group_pictures"] = $photo1["img"];

        $this->assign('active_yn',$active_yn);
        $this->assign('integral_state',$integral_state);
        $this->assign('active_state',$active_state);
        $this->assign('currentcategory',$item);
        $this->display('form');
    }

    public function del(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();

            $data['is_delete'] = '1';

            $result =M('community_activity')->data($data)->where($map)->save();

            if ($result !== false) {
                addlog('删除内容，AID：' . $id);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }
    public function query($p=1){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $p = intval($p) > 0 ? $p : 1;

        $pagesize =10;
        $offset = $pagesize * ($p - 1);

        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            if ($field == 'name') {
                $data = array();
                $data['name'] = array('like', '%'.$keyword.'%');
                $data['is_delete'] = 0;
                $data['activity_id']=$id;
            }
        }else{
            $data = array();
            $data['activity_id']=$id;
            $data['is_delete']= 0 ;
        }


        $count = D('community_activity_personnel')->where($data)->count();

        $list_1 = D('community_activity_personnel')->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();
        $list = array();

        foreach ($list_1 as $p) {
            $openid=$p['openid'];

            $user = D('user')->where("openid='$openid'")->find();
            $p['name']=$user['name'];
            $p['id_number']=$user['id_number'];
            $p['phone']=$user['phone'];
            $p['state1'] = D('hd_state')->where("id={$p['state1']}")->find();

            $list[] = $p;
        }
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function qdel(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $community_activity_informationModel = D('community_activity_information');
        $community_activityModel = D('community_activity');
        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $int = 1;

            $item = M('community_activity_personnel')->where("id = $id")->find();

            $openid = $item['openid'];
            $activity_id = $item['activity_id'];

            $activity = $community_activityModel->where("id = $activity_id")->find();
            $participate_number = $activity['participate_number'];

            $community_activity_informationModel->where(" activity_id = '$activity_id' and openid = '$openid'")->delete();

            $result =M('community_activity_personnel')->where($map)->delete();

            $data = array();
            $data['participate_number'] = $participate_number - $int;
            $community_activityModel->data($data)->where("id = '$activity_id'")->save();

            if ($result !== false) {
                addlog("活动:'{$activity['title']}'删除报名人员，AID：'$openid'");
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }
    public function export_excel(){
        $community_activity = D("community_activity");
        $community_activity_informationmodel = D("community_activity_information");
        $activity_formModel = D('activity_form');
        $userModel = D('user');
        $community_activity_personnelModel = D('community_activity_personnel');

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        require_once '/var/www/lg_community/ThinkPHP/Library/Org/Excel/PHPExcel.php';

        $excel = new \PHPExcel();

        $excel->getProperties()->setCreator("Puppy");
        $excel->getProperties()->setTitle('User');
        $excel->getProperties()->setSubject("testForExportExcel");
        $excel->setActiveSheetIndex(0);
        $excel->setActiveSheetIndex()->setTitle("Excel");

        $activity_form = $activity_formModel->where("activity_id='$id' and is_delete = 0")->select();

        $ZM = array("B1", "C1", "D1", "E1", "F1", "G1", "H1", "I1", "J1", "K1", "L1", "M1", "N1", "O1", "P1", "Q1", "R1", "S1");

        $w = 0;

        $ddd = array();
        //print_r($activity_form); exit;
        foreach ($activity_form as $C) {
            $title = $C['title'];
            $excel->getActiveSheet()->setCellValue($ZM[$w], $C['title']);

            $ddd[] = $title;

            $w++;
        }
        $excel->getActiveSheet()->setCellValue("A1", "报名提交人");

        $ZM2 = array("B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S");

        foreach ($ddd as $u){
            $user = $community_activity_informationmodel->where("activity_id='$id' and title = '$u' and is_delete = 0 ")->order('openid desc,create_at desc')->select();
            $n = 2;

            foreach ($user as $t){
                $openid = $t['openid'];
                $user = $community_activity_personnelModel->where("openid = '$openid' and activity_id='$id' ")->find();

                $excel->getActiveSheet()->setCellValue("A$n",$user['name']);
                $n++;
            }
            //
            break;
        }

        $P = 0;
        $sss = array();
        foreach ($ddd as $u){
            $user = $community_activity_informationmodel->where("activity_id='$id' and title = '$u' and is_delete = 0 ")->order('openid desc,create_at desc')->select();
            $i = 2;

            foreach ($user as $t){
                $qqq = $ZM2[$P].$i;
                $sfz = $t['title'];

                if(strpos("$sfz",'身份证') !== false){
                    $excel->getActiveSheet()->setCellValue($qqq,"'".$t['value']);
                }else{
                    $excel->getActiveSheet()->setCellValue($qqq,$t['value']);
                }
                $i++;

            }
            $sss[] = $user;
            $P++;
        }

//        echo ajax_api(true,'201','编辑成功',$sss);exit;
        $activity=$community_activity->where("id='$id'")->find();

        $filename = $activity['title'];

        header('Content-Type: application/vnd.ms-excel');

        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');

        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel5($excel);
        $objWriter->save('php://output');

    }
    public function control($p = 1){
        $p = intval($p) > 0 ? $p : 1;

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $active_ynModel = D('active_yn');
        $activity_formModel = D('activity_form');
        $activity_controlModel = D('activity_control');
        $community_activityModel = D('community_activity');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where="1 = 1 and activity_id = '$id' and is_delete = 0";

        $count=$activity_formModel->where($where)->order("id desc")->count();

        $tmp_list=$activity_formModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();

        $list = array();

        foreach ($tmp_list as $p) {
            $p['checked'] = $active_ynModel->where("id={$p['checked']}")->find();
            $p['type'] = $activity_controlModel->where("id={$p['type']}")->find();
            $p['activity_id'] = $community_activityModel->where("id={$p['activity_id']}")->find();

            $list[] = $p;
        }

        //print_r($list); exit;

        $page = new\Think\Page($count,$pagesize);
        $page=$page->show();
        $this->assign('id',$id);
        $this->assign('activity_form',$list);
        $this->assign('page',$page);

        $this->display();
    }

    public function control_add(){
        $activity_id = isset($_REQUEST['activity_id']) ? $_REQUEST['activity_id'] : false;

        $activity_formModel = D('activity_form');
        $activity_controlModel = D('activity_control');
        $active_ynModel = D('active_yn');

        $activity_form = $activity_formModel->where("id")->find();

        $active_yn = $active_ynModel->select();
        $activity_control = $activity_controlModel->select();

        // print_r($item); exit;
        $this->assign('activity_id',$activity_id);
        $this->assign('activity_control',$activity_control);
        $this->assign('active_yn',$active_yn);
        $this->assign('activity_form',$activity_form);
        $this->display('control_form');
    }

    public function control_edit($id = 0){
        $id = intval($id);

        $activity_formModel = D('activity_form');
        $activity_controlModel = D('activity_control');
        $active_ynModel = D('active_yn');
        $item = $activity_formModel->where("id='$id'")->find();
        $activity_id = $item['activity_id'];

        $active_yn = $active_ynModel->select();
        $activity_control = $activity_controlModel->select();

        if (!$item) {
            $this->error('参数错误！');
        }
        // print_r($item); exit;
        $this->assign('activity_id',$activity_id);
        $this->assign('activity_control',$activity_control);
        $this->assign('active_yn',$active_yn);
        $this->assign('item',$item);
        $this->display('control_form');
    }

    public function control_update($id = 0){
        $id = intval($id);
        $community_activity_informationModel = D('community_activity_information');
        $community_activity_personnelModel = D('community_activity_personnel');
        $activity_formModel = D('activity_form');
        $activity_idModel = D('activity_id');
        $manyModel = D('many');
        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $type = isset($_POST['type']) ? intval($_POST['type']):0;
        $checked = isset($_POST['checked']) ? intval($_POST['checked']):0;
        $tips = isset($_POST['tips']) ? $_POST['tips'] : false;
        $activity_id = isset($_POST['activity_id']) ? $_POST['activity_id'] : false;
        $time = date('Y-m-d H:s:i', time());

        if($type ==0){
            $this->error('抱歉，请选择字段类型');
        }

        $activity_content = $activity_idModel->where("activity_id = '$activity_id'")->find();

        $data = array();
        $data['activity_id'] = $activity_id;
        $data['title'] = $title;
        $data['type'] = $type;
        $data['checked'] = $checked;
        $data['sort'] = 1;
        $data['time'] = $time;
        $data['length'] = 50;
        $data['activity_content'] = $activity_content['id'];

        if ($id) {
            if($type == 1|| $type == 2){
                $data['dofaut'] = 0;
                $data['tips'] = 1;
                $manyModel->where("activity_form_id = '$tips'")->delete();

            }
            if($tips == 1){
                if($type == 3|| $type == 4){
                    $data['dofaut'] = 1;
                    $data['tips'] = build_order_no();
                }
            }


            $activity_formModel->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            if($type == 3|| $type == 4){
                $this->success('恭喜！内容编辑成功！');exit;
            }
            $this->success('恭喜！内容编辑成功！',U("/qwadmin/activity/control/id/$activity_id"));exit;

        } else {
            $data['code'] = build_order_no();
            if($type == 1|| $type == 2){
                $data['dofaut'] = 0;
                $data['tips'] = 1;
                $manyModel->where("activity_form_id = '$tips'")->delete();

            }

            if($type == 3|| $type == 4){
                $data['dofaut'] = 1;
                $data['tips'] = build_order_no();
            }
            $id = $activity_formModel->data($data)->add();

//            $activity_date = $community_activity_personnelModel ->where("activity_id =$activity_id ")->select();
//
//            foreach ($activity_date as $p) {
//                $data3 = array();
//                $data3['id'] = build_order_no();
//                $data3['activity_id'] = $activity_id;
//                $data3['openid'] = $p['openid'];
//                $data3['title'] = $title;
//                $data3['code'] = $id;
//                $data3['value'] = '';
//                $data3['create_at'] = date('Y-m-d H:s:i', time());
//                $community_activity_informationModel->data($data3)->add();
//            }

            if ($id) {
                addlog('新增内容，ID：' . $id);
                if($type == 3|| $type == 4 ){
                    $this->success('恭喜！内容新增成功！',U("/qwadmin/activity/control_edit/id/$id"));exit;
                }
                $this->success('恭喜！内容新增成功！',U("/qwadmin/activity/control/id/$activity_id"));exit;
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function box($p = 1){
        $p = intval($p) > 0 ? $p : 1;

        $tips = isset($_REQUEST['tips']) ? $_REQUEST['tips'] : false;
        $activity_id = isset($_REQUEST['activity_id']) ? $_REQUEST['activity_id'] : false;

        $manyModel = D('many');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where="1 = 1 and activity_form_id = '$tips' and is_delete = 0";

        $count=$manyModel->where($where)->order("id desc")->count();

        $list=$manyModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();

//        print_r($id); exit;

        $page = new\Think\Page($count,$pagesize);
        $page=$page->show();

        $this->assign('tips',$tips);
        $this->assign('activity_id',$activity_id);
        $this->assign('many',$list);
        $this->assign('page',$page);

        $this->display();
    }

    public function box_edit($id = 0){
        $id = intval($id);
        // print_r($id); exit;
        $manyModel = D('many');

        $item = $manyModel->where("id='$id'")->find();


        if (!$item) {
            $this->error('参数错误！');
        }

        $this->assign('item',$item);
        $this->display('box_form');
    }

    public function box_add(){
        $activity_id = isset($_REQUEST['activity_id']) ? $_REQUEST['activity_id'] : false;
        $activity_form_id = isset($_REQUEST['tips']) ? $_REQUEST['tips'] : false;
        // print_r($activity_form_id); exit;
        $manyModel = D('many');
        $many = $manyModel->where('id')->select();


        $this->assign('activity_id',$activity_id);
        $this->assign('activity_form_id',$activity_form_id);
        $this->assign('many',$many);
        $this->display('box_form');
    }

    public function box_update($id = 0){
        $id = intval($id);

        $manyModel = D('many');
        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $activity_id = isset($_POST['activity_id']) ? $_POST['activity_id'] : false;
        $activity_form_id = isset($_POST['activity_form_id']) ? $_POST['activity_form_id'] : false;
        $time = date('Y-m-d H:s:i', time());

        $data = array();
        $data['name'] = $name;
        $data['time'] = $time;


        if ($id) {
            $manyModel->data($data)->where('id=' . $id)->save();
            $list =  $manyModel->where('id=' . $id)->find();

            $activity_form_id1 =$list['activity_form_id'];
            $activity_id1 =$list['activity_id'];

            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U("/qwadmin/activity/box/tips/$activity_form_id1/activity_id/$activity_id1"));
        } else {
            $data['activity_form_id'] = $activity_form_id;
            $data['activity_id'] = $activity_id;
            $id = $manyModel->data($data)->add();
            if ($id) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U("/qwadmin/activity/box/tips/$activity_form_id/activity_id/$activity_id"));
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function box_del(){
        $id = isset($_REQUEST['aids']) ? $_REQUEST['aids'] : false;

        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();

            $data['is_delete'] = '1';

            $result = M('many')->data($data)->where($map)->save();

            if ($result !== false) {
                addlog('删除内容，AID：' . $id);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    public function control_del(){
        $id = isset($_REQUEST['aids']) ? $_REQUEST['aids'] : false;
        $community_activity_informationModel = D('community_activity_information');
        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();

            $data['is_delete'] = '1';

            $result = M('activity_form')->data($data)->where($map)->save();

            $list = M('activity_form')->where("id = '$id'")->find();

            $key = $list['code'];

            $community_activity_informationModel->where("code = '$key'")->save($data);
            // print_r($list); exit;

            if ($result !== false) {
                addlog('删除内容，AID：' . $id);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    public function fz($id=0){
        $id = intval($id);

        $community_activityModel = D('community_activity');
        $currentcategory = $community_activityModel->where("id='$id'")->find();

        if (!$currentcategory) {
            $this->error('参数错误！');
        }

        $QR= $currentcategory['id'];
        $currentcategory['id'] = "https://lgcommunity.webetter100.com/home/Activity/index/id/$QR.html";

        $this->assign('currentcategory', $currentcategory);
        $this->display('ssc');
    }

    public function generate($id=0){
        $id = intval($id);

        $community_activityModel = D('community_activity');
        $currentcategory = $community_activityModel->where("id='$id'")->find();

        if (!$currentcategory) {
            $this->error('参数错误！');
        }

        $QR= $currentcategory['id'];
        $currentcategory['id'] = "home/app/scanning?id=$QR";

        $this->assign('currentcategory', $currentcategory);
        $this->display('qrcode');
    }

    public function cancel(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();

            $data['active_state'] = '2';

            $result =M('community_activity')->data($data)->where($map)->save();

            if ($result !== false) {
                addlog('删除内容，AID：' . $id);
                $this->success('恭喜，活动已终止报名！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    public function activity_venues($p = 1){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $community_sceneModel = D('community_scene');
        $integral_stateModel = D('integral_state');


        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where="1 = 1 and activity_id = '$id' and is_delete = 0";

        $count=$community_sceneModel->where($where)->order("id desc")->count();

        $tmp_list=$community_sceneModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();

        $list = array();

        foreach ($tmp_list as $p) {
            $p['integral_state'] = $integral_stateModel->where("id={$p['integral_state']}")->find();

            $list[] = $p;
        }

        //print_r($list); exit;

        $page = new\Think\Page($count,$pagesize);
        $page=$page->show();
        $this->assign('id',$id);
        $this->assign('community_scene',$list);
        $this->assign('page',$page);

        $this->display();


    }
    public function activity_venues_add(){
        $activity_id = isset($_REQUEST['activity_id']) ? $_REQUEST['activity_id'] : false;
        $community_sceneModel = D('community_scene');
        $integral_stateModel = D('integral_state');

        $community_scene = $community_sceneModel->where('id')->select();
        $integral_state = $integral_stateModel->select();

        $this->assign('activity_id',$activity_id);
        $this->assign('integral_state',$integral_state);
        $this->assign('community_scene', $community_scene);

        $this->display('activity_venues_form');

    }
    public function activity_venues_edit($id = 0){
        $id = intval($id);
        $activity_id = isset($_REQUEST['activity_id']) ? $_REQUEST['activity_id'] : false;
        $community_sceneModel = D('community_scene');
        $integral_stateModel = D('integral_state');

        $item = $community_sceneModel->where("id = '$id'")->find();
        $integral_state = $integral_stateModel->select();

        $this->assign('activity_id',$activity_id);
        $this->assign('integral_state',$integral_state);
        $this->assign('item', $item);
        $this->display('activity_venues_form');

    }

    public function activity_venues_update($id = 0){
        $id = intval($id);
        $community_activityModel = D('community_activity');
        $community_activity_personnelModel = D('community_activity_personnel');
        $activity_id = isset($_REQUEST['activity_id']) ? $_REQUEST['activity_id'] : false;

        $activity = $community_activityModel->where("id = $activity_id")->find();
        $data['total_number'] = $activity['limit_number'];

        $data['title'] = isset($_POST['title']) ? $_POST['title'] : false;
        $data['start_time'] = isset($_POST['start_time']) ? $_POST['start_time'] : false;
        $data['ending_time'] = isset($_POST['ending_time']) ? $_POST['ending_time'] : false;
        $data['integral'] = isset($_POST['integral']) ? $_POST['integral'] : false;
        $data['integral_state'] = isset($_POST['integral_state']) ? intval($_POST['integral_state']):0;
        $data['time'] = date('Y-m-d H:s:i', time());
        $data['activity_id'] = $activity_id;


        if ($id) {
            D('community_scene')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U("/qwadmin/activity/activity_venues/id/$activity_id"));
        } else {
            $data["id"] = build_order_no();

            $result = D('community_scene')->data($data)->add();

            $personnel = $community_activity_personnelModel ->where("activity_id = '$activity_id'")->select();

            foreach ($personnel as $p) {
                $list = array();
                $list["id"] =  build_order_no();
                $list["activity_id"] = $data["id"];
                $list["openid"] = $p['openid'];
                $list["name"] = $p['name'];

                $community_activity_personnelModel->data($list)->add();
            }

            if ($result) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U("/qwadmin/activity/activity_venues/id/$activity_id"));
            } else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function activity_venues_generate($id=0){
        $id = intval($id);

        $community_sceneModel = D('community_scene');
        $currentcategory = $community_sceneModel->where("id='$id'")->find();

        if (!$currentcategory) {
            $this->error('参数错误！');
        }

        $QR= $currentcategory['id'];
        $currentcategory['id'] = "home/app/scanning?id=$QR";

        $this->assign('currentcategory', $currentcategory);
        $this->display('qrcode');
    }

    public function activity_venues_query($p=1){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $p = intval($p) > 0 ? $p : 1;

        $pagesize =10;
        $offset = $pagesize * ($p - 1);

        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        if ($keyword) {
            if ($field == 'name') {
                $data = array();
                $data['name'] = array('like', '%'.$keyword.'%');
                $data['is_delete'] = 0;
                $data['activity_id']=$id;
                $data['state1'] = 1;
            }
        }else{
            $data = array();
            $data['activity_id']=$id;
            $data['is_delete']= 0 ;
            $data['state1'] = 1;
        }


        $count = D('community_activity_personnel')->where($data)->count();

        $list_1 = D('community_activity_personnel')->order('id')->limit($offset . ',' . $pagesize)->where($data)->select();
        $list = array();

        foreach ($list_1 as $p) {
            $openid=$p['openid'];

            $user = D('user')->where("openid='$openid'")->find();
            $p['name']=$user['name'];
            $p['id_number']=$user['id_number'];
            $p['phone']=$user['phone'];
            $p['state1'] = D('hd_state')->where("id={$p['state1']}")->find();

            $list[] = $p;
        }
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('consult', $list);
        $this->assign('page', $page);
        $this->display();
    }

    public function activity_venues_del(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        if ($id) {
            if (is_array($id)) {
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $data = array();

            $data['is_delete'] = '1';

            $result = M('community_scene')->data($data)->where($map)->save();

            if ($result !== false) {
                addlog('删除内容，AID：' . $id);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    public function xcx($id=0){
        $id = intval($id);

        $community_activityModel = D('community_activity');
        $currentcategory = $community_activityModel->where("id='$id'")->find();

        if (!$currentcategory) {
            $this->error('参数错误！');
        }

        $QR= $currentcategory['id'];
        $currentcategory['id'] = "pages/activity_content/activity_content?id=$QR";

        $this->assign('currentcategory', $currentcategory);
        $this->display('ssc');
    }


}