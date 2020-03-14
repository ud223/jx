<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12/012
 * Time: 11:53
 */

namespace Qwadmin\Controller;


class CompanyinformationController extends ComController
{
    public function index( $p = 1){
        $p = intval($p) > 0 ? $p : 1;
        $company_informationModel=D('company_information');
        $type_of_companyModel=D('type_of_company');

        $pagesize = 5;
        $offset = $pagesize * ($p - 1);
        $where = '1 = 1 ';
        $count = $company_informationModel->where($where)->count();
        $reco_list = $company_informationModel->order('id')->limit($offset . ',' . $pagesize)->select();

        $list = array();
        foreach ($reco_list as $p) {
            $p['type_of_company'] = $type_of_companyModel->where("id={$p['industry']}")->find();
            $list[] = $p;
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display();
    }
    public function del(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        if ($id) {
            if (is_array($id)){
                $aids = implode(',', $id);
                $map['id'] = array('in', $id);
            } else {
                $map = 'id=' . $id;
            }

            $result=M('company_information')->where($map)->delete();

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
}