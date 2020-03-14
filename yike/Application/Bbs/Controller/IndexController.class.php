<?php

namespace Bbs\Controller;

use Think\Controller;

class IndexController extends Controller
{
    //首页
    public function Index()
    {
        $access = I('access', '');
        $bbs_post = D('bbs_post');
        $this->assign('access', $access);

        $m = M('bbs_post');
        $where = "parent_post_id=0 and status = 1";
        $count = $m->where($where)->count();

        $p = getpage($count, 20);
        //$list = $m->query("select * from fit_bbs_post");
        $list = $m->query("select a.*,b.name section_name,(select count(Id) from fit_bbs_post where parent_post_id = a.Id) num from fit_bbs_post a left join fit_bbs_section b on a.section_id = b.Id where a.parent_post_id=0 and a.status = 1 order by replay_at desc limit " . $p->firstRow . ',' . $p->listRows);

        $this->assign('data', $list); // 赋值数据集
        $this->assign('page', $p->show()); // 赋值分页输出

        //读取版块信息
        $section = M('bbs_section');
        $section_data = $section->query("select * from fit_bbs_section order by hot desc,sort");

        for ($i = 0; $i < count($section_data); $i++) {
            if ($section_data[$i]["hot"] == 1) {
                $section_data[$i]["title"] = '<a class="sindex" href="javascript:void(0);" val="' . $section_data[$i]["Id"] . '"><span style="color:#ff0000;">' . $section_data[$i]["name"] . '</span></a>';
            } else {
                $section_data[$i]["title"] = '<a class="sindex" href="javascript:void(0);" val="' . $section_data[$i]["Id"] . '">' . $section_data[$i]["name"] . '</a>';
            }
        }
        $this->assign('section', $section_data);

        //论坛公告
        $bbs_notice = D('bbs_notice');

        $this->assign('notice', $bbs_notice->query("select * from fit_bbs_notice order by creat_at desc limit 1 "));

        //论坛轮播图
        $queue = D('bbs_queue');
        $this->assign('queue', array('img' => $queue->query("select * from fit_bbs_queue where enable = 1 order by sort ")));

        $this->display();
    }

    //版块首页
    public function sIndex()
    {
        $access = I('access', '');
        $section = I('id', '');
        $bbs_post = D('bbs_post');
        $this->assign('access', $access);

        if ($section) {
            $section_where = ' and section_id = ' . $section;

            $section_model = M('bbs_section');
            $condition = array("Id" => $section);
            $section_data = $section_model->where($condition)->select();
            $this->assign('title', $section_data[0]["name"]); // 赋值分页输出
        }
        $m = M('bbs_post');
        $where = "parent_post_id=0 and status = 1 " . $section_where;
        $count = $m->where($where)->count();

        $p = getpage($count, 20);

        $list = $m->query("select a.*,b.name section_name,(select count(Id) from fit_bbs_post where parent_post_id = a.Id) num from fit_bbs_post a left join fit_bbs_section b on a.section_id = b.Id where a.parent_post_id=0 and a.status = 1 " . $section_where . " order by istop desc, replay_at desc limit " . $p->firstRow . ',' . $p->listRows);

        $this->assign('data', $list); // 赋值数据集
        $this->assign('page', $p->show()); // 赋值分页输出

        //读取版块信息
        $section_model = M('bbs_section');
        $section_data = $section_model->query("select * from fit_bbs_section order by sort");

        for ($i = 0; $i < count($section_data); $i++) {
            if ($section_data[$i]["hot"] == 1) {
                $section_data[$i]["title"] = '<a class="sindex" href="javascript:void(0);" val="' . $section_data[$i]["Id"] . '"><span style="color:#ff0000;">' . $section_data[$i]["name"] . '</span></a>';
            } else {
                $section_data[$i]["title"] = '<a class="sindex" href="javascript:void(0);" val="' . $section_data[$i]["Id"] . '">' . $section_data[$i]["name"] . '</a>';
            }
        }
        $this->assign('section', $section_data);

        //读取版块版主信息
        $bbs_section_admin = M('bbs_section_admin');
        $section_admin_data = $bbs_section_admin->query("select b.name from fit_bbs_section_admin a left join fit_user b on a.user_id = b.id where a.section_id = " . $section);

        $this->assign('section_admin', $section_admin_data);
        $this->assign('empty', '<p class="intro">无</p>');

        //加载版主删除功能
        $login = M('Login');
        $condition = array('key' => $access);
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            $bbs_section_admin_data = $bbs_section_admin->query("select * from fit_bbs_section_admin where section_id=" . $section . " and user_id = " . $userid);

            if (count($bbs_section_admin_data) > 0) {
                $this->assign('delete', 1);
            } else
                $this->assign('delete', 0);
        }

        $this->display();
    }

    //我的贴子
    public function my_post()
    {
        $access = I('access', '');
        $bbs_post = D('bbs_post');

        $login = M('Login');
        $condition['key'] = $access;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            $where = "parent_post_id=0 and status = 1 and user_id = " . $userid;

            $count = $bbs_post->where($where)->count();

            $p = getpage($count, 20);

            //$list = $bbs_post->query("select *,(select count(Id) from fit_bbs_post where parent_post_id = a.Id) num from fit_bbs_post a where parent_post_id=0 and user_id = " . $userid . " order by replay_at desc limit " . ($currentPage - 1) * $pageSize . ',' . $pageSize);
            $list = $bbs_post->query("select a.*,b.name section_name,(select count(Id) from fit_bbs_post where parent_post_id = a.Id) num from fit_bbs_post a left join fit_bbs_section b on a.section_id = b.Id where a.parent_post_id=0 and a.status = 1 and a.user_id = " . $userid . " order by replay_at desc limit " . $p->firstRow . ',' . $p->listRows);
            $this->assign('data', $list); // 赋值数据集
            $this->assign('page', $p->show()); // 赋值分页输出
        }

        $section = M('bbs_section');
        $section_data = $section->query("select * from fit_bbs_section order by sort");

        for ($i = 0; $i < count($section_data); $i++) {
            if ($section_data[$i]["hot"] == 1) {
                $section_data[$i]["title"] = '<a class="sindex" href="javascript:void(0);" val="' . $section_data[$i]["Id"] . '"><span style="color:#ff0000;">' . $section_data[$i]["name"] . '</span></a>';
            } else {
                $section_data[$i]["title"] = '<a class="sindex" href="javascript:void(0);" val="' . $section_data[$i]["Id"] . '">' . $section_data[$i]["name"] . '</a>';
            }
        }
        $this->assign('section', $section_data);

        //论坛公告
        $bbs_notice = D('bbs_notice');

        $this->assign('notice', $bbs_notice->query("select * from fit_bbs_notice order by creat_at desc limit 1 "));

        $this->display();
    }

    //跳转新增贴子页面
    public function post_add()
    {
        $access = I('access', '');
        $section = M('bbs_section');
        $section_data = $section->query("select * from fit_bbs_section order by sort");
        $this->assign('data', $section_data);

        //读取版块信息
        for ($i = 0; $i < count($section_data); $i++) {
            if ($section_data[$i]["hot"] == 1) {
                $section_data[$i]["title"] = '<a class="sindex" href="javascript:void(0);" val="' . $section_data[$i]["Id"] . '"><span style="color:#ff0000;">' . $section_data[$i]["name"] . '</span></a>';
            } else {
                $section_data[$i]["title"] = '<a class="sindex" href="javascript:void(0);" val="' . $section_data[$i]["Id"] . '">' . $section_data[$i]["name"] . '</a>';
            }
        }
        $this->assign('section', $section_data);

        $this->display();
    }

    //新增贴子
    public function post_save()
    {

        $bbs_post = D('bbs_post');

        $access = I('access');
        $content = html_entity_decode(I('content'));
        $title = I('title');
        $section = I('section');

        $bbs_post->title = $title;
        $bbs_post->creat_at = date('Y-m-d H:i:s', time());
        $bbs_post->replay_at = date('Y-m-d H:i:s', time());
        $bbs_post->content = $content;
        $bbs_post->section_id = $section;

        $login = M('Login');
        $condition['key'] = $access;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            $user = M('user');

            $condition = array(
                "id" => $userid
            );

            $user_data = $user->where($condition)->select();

            $user_name = $user_data[0]["nick_name"];

            $bbs_post->user_id = $userid;
            if ($user_name)
                $bbs_post->author = $user_name;
            else
                $bbs_post->author = '易客用户';

            $result = $bbs_post->add();

            if (false !== $result || 0 !== $result) {
                echo fit_api(true, 200, '提交成功!', '');
            } else {
                echo fit_api(true, 0, '提交失败!', '');
            }
            //获取用户信息
        } else {
            echo fit_api(true, 0, 'access错误!', '');
        }
    }

    //跟贴
    public function post_replay_save()
    {
        $bbs_post = D('bbs_post');

        $pid = I('pid');
        $access = I('access');
        $content = html_entity_decode(I('content'));

        $bbs_post->parent_post_id = $pid;
        $bbs_post->creat_at = date('Y-m-d H:i:s', time());
        $bbs_post->replay_at = date('Y-m-d H:i:s', time());
        $bbs_post->content = $content;

        $login = M('Login');
        $condition['key'] = $access;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            $user = M('user');

            $condition = array(
                "id" => $userid
            );

            $user_data = $user->where($condition)->select();

            $user_name = $user_data[0]["nick_name"];

            $bbs_post->user_id = $userid;

            if ($user_name)
                $bbs_post->author = $user_name;
            else
                $bbs_post->author = '易客用户';

            $result = $bbs_post->add();

            if (false !== $result || 0 !== $result) {
                echo fit_api(true, 200, '提交成功!', '');
            } else {
                echo fit_api(true, 0, '提交失败!', '');
            }
            //获取用户信息
        } else {
            echo fit_api(true, 0, 'access错误!', '');
        }
    }

    //回复贴子
    public function post_replay_add()
    {
        $bbs_post_replay = D('bbs_post_replay');

        $pid = I('pid');
        $access = I('access');
        $content = html_entity_decode(I('content'));

        $bbs_post_replay->post_id = $pid;
        $bbs_post_replay->creat_at = date('Y-m-d H:i:s', time());
        $bbs_post_replay->content = $content;

        $login = M('Login');
        $condition['key'] = $access;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            $user = M('user');

            $condition = array(
                "id" => $userid
            );

            $user_data = $user->where($condition)->select();

            $user_name = $user_data[0]["name"];

            $bbs_post_replay->user_id = $userid;
            $bbs_post_replay->author = $user_name;

            $result = $bbs_post_replay->add();

            if (false !== $result || 0 !== $result) {
                echo fit_api(true, 200, '提交成功!', '');
            } else {
                echo fit_api(true, 0, '提交失败!', '');
            }
            //获取用户信息
        } else {
            echo fit_api(true, 0, 'access错误!', '');
        }
    }

    //查看贴子
    public function post_info()
    {
        $access = I('access', '');
        $pid = I('pid');
        $bbs_post = D('bbs_post');

        $where = " status = 1 and (id=" . $pid . " or parent_post_id=" . $pid . ')';
        $count = $bbs_post->where($where)->count();

        $p = getpage($count, 20);

        $post_data = $bbs_post->query("select a.*,b.head_pic from fit_bbs_post a left join fit_user b on a.user_id = b.id where a.status = 1 and (a.id=" . $pid . " or a.parent_post_id=" . $pid . ") order by a.creat_at limit " . $p->firstRow . ',' . $p->listRows);

        if ($post_data) {
            $this->assign('title', $post_data[0]["title"]);
            $this->assign('section', $post_data[0]["section_id"]);
            $this->assign('pid', $pid);
            $this->assign('num', $count - 1);

            for ($i = 0; $i < count($post_data); $i++) {
                if ($i == 0) {
                    $post_data[$i]["floor"] = '楼主';
                } else {
                    $post_data[$i]["floor"] = $i . '楼';
                }

                //取出回复信息
                $bbs_post_replay = D('bbs_post_replay');
                $bbs_post_replay_data = $bbs_post_replay->query("select * from fit_bbs_post_replay where post_id = " . $post_data[$i]["Id"]);
                $post_data[$i]["replay_data"] = $bbs_post_replay_data;
            }
            $this->assign('data', $post_data);
            $this->assign('page', $p->show()); // 赋值分页输出

            //加载版主删除功能
            $login = M('Login');
            $condition = array('key' => $access);
            $data = $login->where($condition)->select();

            if ($data) {
                $userid = $data[0]["user_id"];

                $bbs_section_admin = M('bbs_section_admin');
                $bbs_section_admin_data = $bbs_section_admin->query("select * from fit_bbs_section_admin where section_id = (select section_id from fit_bbs_post where Id=" . $pid . ") and user_id = " . $userid);

                if (count($bbs_section_admin_data) > 0) {
                    $this->assign('delete', 1);
                } else
                    $this->assign('delete', 0);
            }
        }

        $this->display();
    }

    //删除贴子
    public function del_post()
    {
        $post = D('bbs_post');
        $id = I('id');

        //$condition = array("parent_post_id" => $id);
        //$post->status = 0;
        //$post->where($condition)->save();

        //$condition = array("Id" => $id);
        // $post->status = 0;
        // $post->where($condition)->save();

        echo fit_api(true, 200, '删除成功', $id);
    }

    //置顶贴子
    public function top_post()
    {
        $post = D('bbs_post');
        $id = I('id');

        $condition = array("Id" => $id);
        $post->istop = 1;
        $post->where($condition)->save();

        echo fit_api(true, 200, '置顶成功', $id);
    }

    public function del_post_replay()
    {
        $post = D('bbs_post_replay');
        $id = I('id');

        if ($id) {
            $condition = array("parent_post_id" => $id);
            $post->status = 0;
            $post->where($condition)->save();

            $condition = array("Id" => $id);
            $post->status = 0;
            $post->where($condition)->save();

        }
        echo fit_api(true, 200, '删除成功', '');
    }


}