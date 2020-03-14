<?php
/**
 * Created by PhpStorm.
 * User: employee_1
 * Date: 2018/10/31
 * Time: 17:21
 */

namespace Qwadmin\Controller;


class CooperateController extends ComController{
    public function index($p = 1)
    {
        $p = intval($p) > 0 ? $p : 1;
        $community_cooperate_loginModel = D('community_cooperate_login');
        $community_cooperate_login_typeModel = D('community_cooperate_login_type');

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $where = '1 = 1  and  is_delete = 0';

        $count = $community_cooperate_loginModel->where($where)->order("id desc")->count();

        $tmp_list = $community_cooperate_loginModel->where($where)->order("id desc")->limit($offset . ',' . $pagesize)->select();

        $list = array();

        foreach ($tmp_list as $p) {
            $p['type'] = $community_cooperate_login_typeModel->where("id={$p['type']}")->find();
            $list[] = $p;
        }
        $page = new\Think\Page($count, $pagesize);
        $page = $page->show();

        $this->assign('community_cooperate_login', $list);
        $this->assign('page', $page);

        $this->display();
    }

    public function add() {
        $community_cooperate_type = D('community_cooperate_type')->select();
        $community_cooperate_login = D('community_cooperate_login')->where('id')->select();
        $community_cooperate_login_type = D('community_cooperate_login_type')->select();

        $this->assign('community_cooperate_type',$community_cooperate_type);
        $this->assign('community_cooperate_login_type',$community_cooperate_login_type);
        $this->assign('community_cooperate_login', $community_cooperate_login);

        $this->display('form');
    }

    public function edit($id = 0){
        $id = intval($id);
        $community_cooperate_loginModel = D('community_cooperate_login');
        $community_cooperate_login_type = D('community_cooperate_login_type')->select();
        $community_cooperate_type = D('community_cooperate_type')->select();

        $item = $community_cooperate_loginModel->where("id='$id'")->find();

        if (!$item) {
            $this->error('参数错误！');
        }

        $this->assign('community_cooperate_type',$community_cooperate_type);
        $this->assign('community_cooperate_login_type',$community_cooperate_login_type);
        $this->assign('item', $item);
        $this->display('form');
    }
    public function update($id = 0){
        $id = intval($id);

        $data['name'] = isset($_POST['name']) ? $_POST['name'] : false;
        $data['usermane'] = isset($_POST['usermane']) ? $_POST['usermane'] :false;
        $data['password'] = isset($_POST['password']) ? $_POST['password'] : false;
        $data['type'] = isset($_POST['type']) ? intval($_POST['type']):0;
        $data['phone'] = isset($_POST['phone']) ? intval($_POST['phone']):0;
        $data['manage'] = isset($_POST['manage']) ? intval($_POST['manage']):0;

        $manage = $data['manage'];

        if($manage == 0){
            $this->success('抱歉！请选择分类！');exit;
        }

        if ($id) {
            D('community_cooperate_login')->data($data)->where('id=' . $id)->save();
            addlog('编辑内容，ID：' . $id);
            $this->success('恭喜！内容编辑成功！',U('/qwadmin/cooperate/index'));
        } else {
            $id = D('community_cooperate_login')->data($data)->add();
            if ($id) {
                addlog('新增内容，ID：' . $id);
                $this->success('恭喜！内容新增成功！',U('/qwadmin/cooperate/index'));
            } else {
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

            $result = M('community_cooperate_login')->where($map)->data($data)->save();

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

}