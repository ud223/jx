<?php
/**
 * Created by PhpStorm.
 * User: ud223
 * Date: 2018/4/27
 * Time: 22:29
 */

namespace Qwadmin\Controller;
use Vendor\Tree;

class LogoController extends ComController
{
    public function setting() {
        $id = 1;
        $link = M('logo')->where('id=' . $id)->find();
        $this->assign('link', $link);

        $this->display();
    }

    //保存作品图片
    public function update()
    {
        $id = 1;

        $data['img'] = I('post.img', '', 'strip_tags');

        if ($id) {
            M('logo')->data($data)->where('id=' . $id)->save();

        }

        $this->success('恭喜，操作成功！',U('setting'));
    }
}