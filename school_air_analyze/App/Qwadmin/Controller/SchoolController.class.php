<?php
/**
 *
 * 版权所有：素材火<qwadmin.sucaihuo.com>
 * 作    者：素材水<hanchuan@sucaihuo.com>
 * 日    期：2016-01-20
 * 版    本：1.0.0
 * 功能说明：用户控制器。
 *
 **/

namespace Qwadmin\Controller;

class SchoolController extends ComController {
    public function index() {
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $where = '';

        if ($order == 'asc') {
            $order = "create_at asc";
        } elseif (($order == 'desc')) {
            $order = "create_at desc";
        } else {
            $order = "id asc";
        }
        if ($keyword <> '') {
            if ($field == 'name') {
                $where = "name LIKE '%$keyword%'";
            }
            if ($field == 'city') {
                $where = "city_name LIKE '%$keyword%'";
            }
            if ($field == 'region') {
                $where = "reigon_name LIKE '%$keyword%'";
            }
        }

        $school_db = M('school');

        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $school_db->where($where)->count();

        $list = $school_db ->order($order)
            ->order($order)
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();
        //$school_db->getLastSql();
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        
        $this->assign('list', $list);
        $this->assign('page', $page);
        
        $this->display();
    }

    public function del() {
        $uids = isset($_REQUEST['uids']) ? $_REQUEST['uids'] : false;
        //uid为1的禁止删除
        if ($uids == 1 or !$uids) {
            $this->error('参数错误！');
        }

        if (is_array($uids)) {
            foreach ($uids as $k => $v) {
                if ($v == 1) {//uid为1的禁止删除
                    unset($uids[$k]);
                }
                $uids[$k] = intval($v);
            }
            if (!$uids) {
                $this->error('参数错误！');
                $uids = implode(',', $uids);
            }
        }
        
        $school_db = M('school');

        $map['uid'] = array('in', $uids);

        $data['status'] = 9;

        if ($school_db->where($map)->save($data) !== false) {
            addlog('删除学校UID：' . $uids);
            $this->success('恭喜，学校删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    
    public function add() {
        $this->display('form');
    }

    public function edit() {
        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : false;

        if ($uid) {
            $school_db = M('school');

            $item = $school_db->where("id=$uid")->find();

            if (!$item) {
                $this->error('该学校信息失效');
            }

        } else {
            $this->error('参数错误！');
        }

        $this->assign('item', $item);

        $this->display('form');
    }

    public function update($ajax = '') {
        if ($ajax == 'yes') {
            $uid = I('get.uid', 0, 'intval');
            $gid = I('get.gid', 0, 'intval');
            M('auth_group_access')->data(array('group_id' => $gid))->where("uid='$uid'")->save();
            die('1');
        }

        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : false;

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';

        $data['name'] = $name;
        $data['province_name'] = isset($_POST['province_name']) ? trim($_POST['province_name']) : '';
        $data['city_name'] = isset($_POST['city_name']) ? trim($_POST['city_name']) : '';
        $data['region_name'] = isset($_POST['region_code']) ? trim($_POST['region_name']) : '';
        $data['uid'] = isset($_POST['uid']) ? trim($_POST['uid']) : '';
        $data['pwd'] = isset($_POST['pwd']) ? trim($_POST['pwd']) : '';
        $data['status'] = isset($_POST['status']) ? intval($_POST['status']) : '1';

        $school_db = M('school');
        
        if (!$uid) {
            $data['create_at'] = time();

            if ($school_db->where("name = '$name'")->count()) {
                $this->error('已有相同名学校名称！');
            }

            $uid = $school_db->data($data)->add();

            if ($uid) {
                addlog('新增学校, UID：' . $data['name']);
            }
            else {
                addlog('新增学校失败, 学校名称：' . $uid);
            }
        } else {
            if ($school_db->where("name ='$name' and id != $uid")->count()) {
                $this->error('已有相同名学校名称, 且ID不一致！');
            }
           
            $result = $school_db->data($data)->where("uid=$uid")->save();

            if ($result !== false) {
                addlog('编辑会员信息，会员UID：' . $uid);
            }
            else {
                addlog('编辑会员信息失败，会员UID：' . $uid);
            }
        }

        $this->success('操作成功！');
    }
}
