<?php
/**
 *
 *
 **/

namespace Qwadmin\Controller;

class ZuopinController extends ComController
{

    //作品子类作品图片
    public function index()
    {

        $list = M('zuopin')->where(array('type'=>0))->order('o asc')->select();
        $this->assign('list', $list);
        $this->display();
    }

    //首页大图
    public function catepic(){
        $list = M('zuopin')->where(array('type'=>1))->order('o asc')->select();
        $this->assign('list', $list);
        $this->display();
    }
    public function cateeditor($id = null){
        $id = intval($id);
        $link = M('zuopin')->where('id=' . $id)->find();
        $this->assign('link', $link);
        $this->display();
    }


    //新增作品
    public function add()
    {

        $this->display('form');
    }

    //新增或修改作品图像
    public function edit($id = null)
    {

        $id = intval($id);
        $link = M('zuopin')->where('id=' . $id)->find();
        $this->assign('link', $link);
        $this->display('form');
    }

    //删除图像
    public function del()
    {

        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if ($ids) {
            if (is_array($ids)) {
                $ids = implode(',', $ids);
                $map['id'] = array('in', $ids);
            } else {
                $map = 'id=' . $ids;
            }
            if (M('zuopin')->where($map)->delete()) {
                addlog('删除作品图片，ID：' . $ids);
                $this->success('恭喜，删除成功！');
            } else {
                $this->error('参数错误！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

    //保存作品图片
    public function update($id = 0)
    {
        $id = intval($id);
        $type=M("zuopin")->where(array('id'=>$id))->getField("type");

        $data['title'] = I('post.title', '', 'strip_tags');
        if (!$data['title']) {
            $this->error('请填写标题！');
        }
        $data['url'] = I('post.url', '', 'strip_tags');
        $data['o'] = I('post.o', '', 'strip_tags');
        $pic = I('post.logo', '', 'strip_tags');
        if ($pic <> '') {
            $data['logo'] = $pic;
        }
        if ($id) {
            M('zuopin')->data($data)->where('id=' . $id)->save();
         
        } else {
            M('zuopin')->data($data)->add();
           
        }

        if($type){
            $this->success('恭喜，操作成功！',U('catepic'));
        }else{
            $this->success('恭喜，操作成功！',U('index'));
        }
        
    }
}