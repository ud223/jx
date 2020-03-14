<?php
/**
 * Created by PhpStorm.
 * User: ud223
 * Date: 2018/5/1
 * Time: 18:25
 */

namespace Qwadmin\Controller;

class PointController extends ComController
{
    public function index($p = 1) {
        $p = intval($p) > 0 ? $p : 1;

        $point = M('point');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $sid = isset($_GET['sid']) ? $_GET['sid'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        $where = '1 = 1 and is_delete = 0 ';

        if ($sid) {
            $where .= "and (country_id={$sid} or id={$sid})";
        }

        if ($keyword) {
            $where .= "and (name_en like '%{$keyword}%' or name_cn like '%{$keyword}%'')  ";
        }

        $orderby = "name_en asc";

        //获取国家列表
        $country_list = M('point')->where("not_for_country=1 and is_delete=0")->order($orderby)->select();

        $this->assign('country_list', $country_list);

        $count = $point->where($where)->count();
        $list = $point->where($where)->order($orderby)->limit($offset . ',' . $pagesize)->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();

        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('list_uri', '/qwadmin/point/index');

        $this->display();


    }

    public function add() {
        $country_list = M('point')->field('id, name_cn')->where("not_for_country=1 and is_delete=0")->order('name_cn asc')->select();

        $this->assign('country_list', $country_list);//国家列表

        $this->assign('list_uri', '/qwadmin/point/index');

        $this->display('form');
    }

    public function edit($aid) {
        $aid = intval($aid);

        $where = "is_delete = 0";

        $point = M('point')->where($where .' and id=' . $aid)->find();

        if ($point) {
            if ($point["not_for_country"] == 0) {
                $country_list = M('point')->field('id, name_cn')->where("not_for_country=1 and is_delete=0")->order('name_cn asc')->select();

                $this->assign('country_list', $country_list);//国家列表
            }

            if ($point["not_for_province"] == 0) {
                $province_list = M('point')->field('id, name_cn')->where("not_for_province=1 and is_delete=0 and country_id=". $point["country_id"])->order('name_cn asc')->select();

                $this->assign('province_list', $province_list);//国家列表
            }

            $this->assign('item', $point);
        } else {
            $this->error('参数错误！');
        }

        $this->assign('list_uri', '/qwadmin/point/index');

        $this->display('form');
    }

    public function update($aid = 0) {
        $aid = intval($aid);

        $data = array();

        $data['lng'] = isset($_POST['lng']) ? $_POST['lng'] : false;
        $data['lat'] = isset($_POST['lat']) ? $_POST['lat'] : false;
        $data['not_for_country'] = isset($_POST['not_for_country']) ? 1 : 0;
        $data['not_for_province'] = isset($_POST['not_for_province']) ? 1 : 0;
        $data['country_id'] = isset($_POST['country_id']) ? intval($_POST['country_id']) : 0;
        $data['province_id'] = isset($_POST['province_id']) ? intval($_POST['province_id']) : 0;
        $data['name_en'] = isset($_POST['name_en']) ? $_POST['name_en'] : false;
        $data['name_cn'] = isset($_POST['name_cn']) ? $_POST['name_cn'] : false;
        $data['name_jp'] = isset($_POST['name_jp']) ? $_POST['name_jp'] : '';
        $data['name_ger'] = isset($_POST['name_ger']) ? $_POST['name_ger'] : '';
        $data['create_at'] = date('Y-m-d H:s:i', time());

//        print_r($data); exit;

        if (!$data['lng'] or !$data['lat']) {
            $this->error('警告！地点经纬度为必填项');
        }

        if ($data['not_for_country'] == 0) {
            if (!$data['country_id']) {
                $this->error('警告！非国家地点所属国家为必选项');
            }
        }
        else {
            $data['country_id'] = 0;
            $data['province_id'] = 0;
        }

        if ($data['not_for_country'] == 0 && $data['not_for_province'] == 0) {
            if (!$data['province_id']) {
                $this->error('警告！非国家地点所属国家为必选项');
            }
        }
        else {
            $data['province_id'] = 0;
        }

        if (!$data['name_en']) {
            $this->error('警告！地点英文名称为必填项');
        }

        if (!$data['name_cn']) {
            $this->error('警告！地点中文名称为必填项');
        }
//        print_r($data); exit;
        //生成完整地点名称
        if ($data['not_for_country'] === 1) {
            $data['full_name_en'] = $data['name_en'];
        }
        else {
            $country = M('point')->where("id={$data['country_id']}")->find();

            if (!$country) {
                $this->error('警告！错误数据结构');
            }

            if ($data['not_for_province'] === 1) {
                $data['full_name_en'] = $country['name_en'] . '/' . $data['name_en'];
            }
            else {
                $province = M('point')->where("id={$data['province_id']}")->find();

                if (!$province) {
                    $this->error('警告！错误数据结构2');
                }

                $data['full_name_en'] = $country['name_en']. '/'. $province['name_en'] . '/' .$data['name_en'];
            }
        }

        if ($aid) {
            $result = M('point')->data($data)->where('id=' . $aid)->save();

            if ($result !== false) {
                addlog('编辑地点，AID：' . $aid);
                $this->success('恭喜！地点编辑成功！');
            }
            else {
                $this->error('抱歉，未知错误！');
            }
        }
        else {
            $is_point = M('point')->where("name_en='{$data['name_en']}' and is_delete=0")->find();

            if ($is_point) {
                $this->error('警告！已经有相同英文名称地点存在');
            }

            $aid = M('point')->data($data)->add();

            if ($aid) {
                addlog('新增地点，AID：' . $aid);
                $this->success('恭喜！地点新增成功！', '/qwadmin/point/edit/aid/'. $aid .'.html');
            }
            else {
                $this->error('抱歉，未知错误！');
            }
        }
    }

    public function del()
    {
        $aids = isset($_REQUEST['aids']) ? $_REQUEST['aids'] : false;

        if ($aids) {
            if (is_array($aids)) {
                $aids = implode(',', $aids);
                $map['id'] = array('in', $aids);
            } else {
                $map = 'id=' . $aids;
            }

            $data['is_delete'] = 1;

            $result = M('point')->where($map)->data($data)->save();

            if ($result !== false) {
                addlog('删除内容，AID：' . $aids);
                $this->success('恭喜，内容删除成功！');
            } else {
                $this->error('抱歉，未知错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    public function getProvince() {
        $country_id = $_GET['country_id'];

        if ($country_id) {
            $province_list = M('point')->field('id, name_cn')->where("not_for_country=0 and not_for_province=1 and is_delete=0 and country_id = ". $country_id)->order('name_cn asc')->select();

            echo ajax_api(true, 200, '操作成功', $province_list); exit;
        }
        else {
            echo ajax_api(true, 0, '参数错误', '');
        }
    }
    //1234545
}
