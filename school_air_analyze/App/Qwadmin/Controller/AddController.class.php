<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12/012
 * Time: 16:46
 */

namespace Qwadmin\Controller;


class AddController extends ComController
{
    public function informationAdd(){
        $informationModel = D('information');

        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $position = isset($_POST['position']) ? $_POST['position'] : false;
        $company = isset($_POST['company']) ? $_POST['company'] : false;
        $telephone = isset($_POST['telephone']) ? $_POST['telephone'] : false;
        $mailbox = isset($_POST['mailbox']) ? $_POST['mailbox'] : false;

        if(!$name){
            echo ajax_api(true,'100','姓名不能为空',false);exit;
        }
        if(!$position){
            echo ajax_api(true,'100','职位不能为空',false);exit;
        }
        if(!$company){
            echo ajax_api(true,'100','公司不能为空',false);exit;
        }
        if(!$telephone){
            echo ajax_api(true,'100','电话不能为空',false);exit;
        }
        if(!$mailbox){
            echo ajax_api(true,'100','电子邮件不能为空',false);exit;
        }

        $data = array();
        $data['name'] = $name;
        $data['position'] = $position;
        $data['company'] = $company;
        $data['telephone'] = $telephone;
        $data['mailbox'] = $mailbox;

        $result = $informationModel->add($data);

        if ($result !== false) {
            echo ajax_api(true,200,'添加数据成功');
        } else {
            echo ajax_api(true,200,'添加数据失败','');
        }
    }

    public function company_informationAdd(){
        $company_informationModel = D('company_information');

        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $address = isset($_POST['address']) ? $_POST['address'] : false;
        $industry = isset($_POST['industry']) ? $_POST['industry'] : false;
        $scope_of_operation = isset($_POST['scope_of_operation']) ? $_POST['scope_of_operation'] : false;

        if(!$name){
            echo ajax_api(true,'100','公司姓名不能为空',false);exit;
        }
        if(!$address){
            echo ajax_api(true,'100','公司地址不能为空',false);exit;
        }
        if(!$industry){
            echo ajax_api(true,'100','公司类型不能为空',false);exit;
        }
        if(!$scope_of_operation){
            echo ajax_api(true,'100','经营范围不能为空',false);exit;
        }

        $data = array();
        $data['name'] = $name;
        $data['address'] = $address;
        $data['industry'] = $industry;
        $data['scope_of_operation'] = $scope_of_operation;

        $result = $company_informationModel->add($data);

        if ($result !== false) {
            echo ajax_api(true,200,'添加数据成功');
        } else {
            echo ajax_api(true,200,'添加数据失败','');
        }
    }

    public function type_of_companyAdd(){
        $type_of_companyModel = D('type_of_company');
        $type = isset($_POST['type']) ? $_POST['type'] : false;

        if(!$type){
            echo ajax_api(true,'100','类型不能为空',false);exit;
        }

        $data = array();

        $data['type'] = $type;

        $result = $type_of_companyModel->add($data);

        if ($result !== false) {
            echo ajax_api(true,200,'添加数据成功');
        } else {
            echo ajax_api(true,200,'添加数据失败','');
        }
    }

    public function informationDel(){
        $informationModel = D('information');

        $id = isset($_POST['id']) ? $_POST['id'] : false;

        if(!$id){
            echo ajax_api(true,'100','ID不能为空',false);exit;
        }

        $data = array();

        $data['is_delete'] = '1';

        $result =$informationModel->data($data)->where('id=' . $id)->save();

        if ($result !== false) {
            echo ajax_api(true,200,'添加数据成功');
        } else {
            echo ajax_api(true,200,'添加数据失败','');
        }
    }

    public function company_informationDel(){
        $company_informationModel = D('company_information');

        $id = isset($_POST['id']) ? $_POST['id'] : false;

        if(!$id){
            echo ajax_api(true,'100','ID不能为空',false);exit;
        }

        $data = array();

        $data['is_delete'] = '1';

        $result =$company_informationModel->data($data)->where('id=' . $id)->save();

        if ($result !== false) {
            echo ajax_api(true,200,'添加数据成功');
        } else {
            echo ajax_api(true,200,'添加数据失败','');
        }
    }

    public function type_of_companyDel(){
        $type_of_companyModel = D('type_of_company');

        $id = isset($_POST['id']) ? $_POST['id'] : false;

        if(!$id){
            echo ajax_api(true,'100','ID不能为空',false);exit;
        }

        $data = array();

        $data['is_delete'] = '1';

        $result =$type_of_companyModel->data($data)->where('id=' . $id)->save();

        if ($result !== false) {
            echo ajax_api(true,200,'添加数据成功');
        } else {
            echo ajax_api(true,200,'添加数据失败','');
        }
    }

    public function informationUpdate(){
        $informationModel = D('information');

        $id = isset($_POST['id']) ? $_POST['id'] : false;
        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $position = isset($_POST['position']) ? $_POST['position'] : false;
        $company = isset($_POST['company']) ? $_POST['company'] : false;
        $telephone = isset($_POST['telephone']) ? $_POST['telephone'] : false;
        $mailbox = isset($_POST['mailbox']) ? $_POST['mailbox'] : false;

        if(!$name){
            echo ajax_api(true,'100','姓名不能为空',false);exit;
        }
        if(!$position){
            echo ajax_api(true,'100','职位不能为空',false);exit;
        }
        if(!$company){
            echo ajax_api(true,'100','公司不能为空',false);exit;
        }
        if(!$telephone){
            echo ajax_api(true,'100','电话不能为空',false);exit;
        }
        if(!$mailbox){
            echo ajax_api(true,'100','电子邮件不能为空',false);exit;
        }

        $data = array();
        $data['name'] = $name;
        $data['position'] = $position;
        $data['company'] = $company;
        $data['telephone'] = $telephone;
        $data['mailbox'] = $mailbox;

        $result =$informationModel->data($data)->where('id=' . $id)->save();

        if ($result !== false) {
            echo ajax_api(true,200,'修改数据成功');
        } else {
            echo ajax_api(true,200,'修改数据失败','');
        }
    }

    public function company_informationUpdate(){
        $company_informationModel = D('company_information');

        $id = isset($_POST['id']) ? $_POST['id'] : false;
        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $address = isset($_POST['address']) ? $_POST['address'] : false;
        $industry = isset($_POST['industry']) ? $_POST['industry'] : false;
        $scope_of_operation = isset($_POST['scope_of_operation']) ? $_POST['scope_of_operation'] : false;

        if(!$name){
            echo ajax_api(true,'100','公司姓名不能为空',false);exit;
        }
        if(!$address){
            echo ajax_api(true,'100','公司地址不能为空',false);exit;
        }
        if(!$industry){
            echo ajax_api(true,'100','公司类型不能为空',false);exit;
        }
        if(!$scope_of_operation){
            echo ajax_api(true,'100','经营范围不能为空',false);exit;
        }

        $data = array();
        $data['name'] = $name;
        $data['address'] = $address;
        $data['industry'] = $industry;
        $data['scope_of_operation'] = $scope_of_operation;

        $result =$company_informationModel->data($data)->where('id=' . $id)->save();

        if ($result !== false) {
            echo ajax_api(true,200,'修改数据成功');
        } else {
            echo ajax_api(true,200,'修改数据失败','');
        }
    }
    public function informationQuery(){
        $informationModel = D('information');
        $company_informationModel=D('company_information');

        $name = isset($_POST['name']) ? $_POST['name'] : false;
        $position = isset($_POST['position']) ? $_POST['position'] : false;
        $company = isset($_POST['company']) ? $_POST['company'] : false;
        $telephone = isset($_POST['telephone']) ? $_POST['telephone'] : false;
        $mailbox = isset($_POST['mailbox']) ? $_POST['mailbox'] : false;

        $data = array();

        if($name){
            $data['name'] = $name;
        }

        if($position){
            $data['position'] = $position;
        }
        if($company){
            $data['company'] = $company;
        }
        if($telephone){
            $data['telephone'] = $telephone;
        }
        if($mailbox){
            $data['mailbox'] = $mailbox; $data['mailbox'] = $mailbox;
        }

        $reco_list = $informationModel->where('is_delete=0')->where($data)->select();

        $list = array();

        foreach ($reco_list as $p) {
            $p['company_information'] = $company_informationModel->where("id={$p['company']}")->find();

            $list[] = $p;
        }

        echo ajax_api(true,200,'查询数据成功', $list);
    }
}