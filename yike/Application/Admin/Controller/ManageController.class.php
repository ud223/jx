<?php
namespace Admin\Controller;
use Think\Controller;
class ManageController extends AdminController {
    private $app_id = '';
    private $app_secret = '';
    private $access_token = '';

    #region 系统操作action

    public function index(){
        $this->display();
    }


    public function site(){
        $this->display();
    }

    #endregion;

    #region 后台菜单操作action

    public function menu(){
        $menu = D('Menu');

        $pmenu_conditino = array('pid' => 0);

        $this->assign('data',array(
            'menu' => $menu->select(),
            'pmenu' => $menu->where($pmenu_conditino)->select()
        ));


        $this->display();
    }

    public function add_menu(){
        $menu = D('Menu');

        $pid = I('pid');
        $name = I('name');
        $url = I('url');
        $sort = I('sort');
        $enable = I('enable');

        $data = array(
            'pid' => $pid,
            'name' => $name,
            'url' => $url,
            'sort' => $sort,
            'enable' => $enable
        );

        //print_r($data);exit;
        $menu->add($data);

        echo fit_api(true, 200,'添加成功', '');
    }

    public function save_menu() {
        $menu = D('Menu');

        $id = I('id');
        $pid = I('pid');
        $name = I('name');
        $url = I('url');
        $sort = I('sort');
        $enable = I('enable');

        $condition = array('id' => $id);

        $data = array(
            'pid' => $pid,
            'name' => $name,
            'url' => $url,
            'sort' => $sort,
            'enable' => $enable
        );

        //print_r($data);exit;
        $menu->where($condition)->save($data);

        echo fit_api(true, 200,'保存成功', '');
    }

    public function del_menu() {
        $menu = D('Menu');

        $id = I('id');

        $data = array('id'=>$id);

        $menu->where($data)->delete();

        echo fit_api(true,200,'删除成功', '');
    }

    #endregion;

    #region 后台管理员操作action

    public function member(){
        $member = D('Member');
        //print_r($member->select());

        $this->assign('data',array(
            'member'=>$member->select()
        ));

        $this->display();
    }

    public function  member_type(){
        $member_type = D('MemberType');

        $m = $member_type->select();

        //print_r($m);exit;

        $this->assign('data',array(
            'member_type'=>$m
        ));

        $this->display();
    }

    #endregion;

    #region 微信信息处理action

    public function wxinfo() {
        $this->display();
    }

    public function save_wx() {
        $info = D('wxinfo');

        $id = I('id');
        $appid = I('appid');
        $appsecret = I('appsecret');
        $shopcode = I('shopcode');
        $shoppwd = I('shoppwd');

        $data = array(
            'appid' => $appid,
            'appsecret' => $appsecret,
            'shopcode' => $shopcode,
            'shoppwd' => $shoppwd
        );

        if (empty($id)) {
            $id = $info->add($data);

            echo fit_api(true, 200,'添加成功', $id);
        }
        else {
            $condition = array(
                'id' => $id
            );

            $info->where($condition)->save($data);

            echo fit_api(true, 200,'保存成功', '');
        }
    }

    #endregion;

    #region 颜色管理

    public function color_list() {
        $db = D('color');

        $list = $db->select();

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function color_edit() {
        $db = D('color');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加颜色'
            ));
        }
        else {
            $this->assign('title', array(
                'text' => '编辑颜色'
            ));

            $item = $db->find($id);

            $this->assign('data', array(
                'item' => $item
            ));
        }

        $this->display();
    }

    public function color_save() {
        $db = D('color');

        $id = I('id');

        $color = I('color');
        $code = I('code');

        $data = array(
            'color' => $color,
            'code' => $code
        );

        $result = false;

        if (empty($id) || $id == '') {
            $result = $db->add($data);

            $id = $result;
        }
        else {
            $condition = array('id' => $id);

            $result = $db->where($condition)->save($data);
        }

        if ($result)
            echo fit_api(true, 200,'保存成功', $id);
        else
            echo fit_api(true, 0,'保存失败', $id);
    }

    public function color_del() {
        $db = D('color');

        $id = I('id');

        $condition = array('id' => $id);

        $db->where($condition)->delete();

        echo fit_api(true,200,'删除成功', '');
    }

    #endregion

    #region 商品管理

    public function item_list() {
        $itemModel = D('item');

        $list = $itemModel->select();

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function item_edit() {
        $itemModel = D('item');
        $colorModel = D('color');

        $id = I('id');

        $color = $colorModel->select();

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加商品'
            ));

            $this->assign('data', array(
                'color' => $color
            ));
        }
        else {
            $this->assign('title', array(
                'text' => '编辑商品'
            ));

            $this->assign('data', array(
                'item' => $itemModel->find($id),
                'color' => $color
            ));
        }

        $this->display();
    }

    public function save_item() {
        $itemModel = D('item');

        $item_id = I('item_id');

        $item_name = I('item_name');
        $sub_name = I('sub_name');
        $price = I('price');
        $color = I('color');
        $show_photo = I('show_photo');
        $content_photo = I('content_photo');

        $data = array(
            'item_name' => $item_name,
            'sub_name' => $sub_name,
            'price' => $price,
            'color' => $color,
            'show_photo' => $show_photo,
            'content_photo' => $content_photo
        );

//            echo fit_api(true, 200,'保存成功', $data); exit;

        if (!$item_id) {
//            echo fit_api(true, 0,'保存失败1', $data); exit;
            $item_id = $itemModel->add($data);
        }
        else {
//            echo fit_api(true, 0,'保存失败2', $data); exit;
//            $condition = array('id' => $item_id);

            $item_id = $itemModel->where('id = '. $item_id)->save($data);
        }

        if ($item_id == false) {
            echo fit_api(true, 0, '保存失败!', $item_id);
            exit;
        }
        else {
            if ($item_id >= 0) {
                echo fit_api(true, 200, '保存成功!', $item_id);
                exit;
            }
            else {
                echo fit_api(true, 0, '保存失败!', $item_id);
                exit;
            }
        }
    }

    public function del_item() {
        $itemModel = D('item');

        $id = I('id');

        $data = array('id' => $id);

        $itemModel->where($data)->delete();

        echo fit_api(true,200,'删除成功', '');
    }

    #endregion

    #region 订单管理

    public function order_list() {
        $db = M();

        $sql_text = 'select fit_order.order_id, fit_order.lesson_id, fit_lesson.name as lesson_name, fit_lesson.name_en as lesson_name_en, fit_order.run_date, fit_order.start_time, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.end_time, fit_lesson.local, fit_user.name, fit_user.tel, fit_order.state from fit_order left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.id left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id left join fit_user on fit_order.user_id = fit_user.id where fit_order.state > 10 order by order_id desc';

        $this->assign('data', array(
           'order' => $db->query($sql_text)
        ));

        $this->display();
    }

    public function order_detail() {
        $db = M();

        $id = I('id');

        $sql_text = "select fit_order.order_id, fit_order.lesson_id, fit_lesson.name as lesson_name, fit_lesson.name_en as lesson_name_en, fit_order.run_date, fit_order.start_time, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.end_time, fit_lesson.local, fit_user.name, fit_user.tel, fit_order.state from fit_order left join fit_lesson on fit_order.lesson_id = fit_lesson.id left join fit_lesson_config on fit_order.lesson_id = fit_lesson_config.lesson_id left join fit_user on fit_order.user_id = fit_user.id where fit_order.state > 10 and order_id = '". $id ."'";
        $sql_coach = "select * from fit_user where is_coach = 1";

//        print_r($db->query($sql_coach)); exit;

        $this->assign('data', array(
            'order' => $db->query($sql_text),
            'coach' => $db->query($sql_coach)
        ));

        $this->display();
    }

    public function setCoach() {
        $order = D('order');

        $order_id = I('order_id');
        $coach_id = I('coach_id');

        $condition = array('order_id' => $order_id);

        $data = array(
          'coach_id' => $coach_id
        );

        $order->where($condition)->save($data);

        echo fit_api(true,200,'保存成功', '');
    }

    #endregion

    #region 二维码管理

    public function qrcode_list() {
        $db = D('');

        $sql_text = "select fit_scene.id, fit_channel.name as channel_name, fit_setmeal.name as setmeal_name, is_use, fit_scene.create_date, fit_scene.ticket, fit_user.name as user_name from fit_scene left join fit_channel on fit_scene.channel_id = fit_channel.id left join fit_setmeal on fit_scene.setmeal_id = fit_setmeal.id left join fit_user on fit_scene.user_id = fit_user.id order by fit_scene.id desc";

        $list = $db->query($sql_text);

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function qrcode_edit() {
        $channelController = D('channel');
        $setmealController = D('setmeal');

        $channel_list = $channelController->select();
        $setmeal_list = $setmealController->select();

        $this->assign('data', array(
            'channel' => $channel_list,
            'setmeal' => $setmeal_list
        ));

        $this->display();
    }

    public function qrcode_create() {
        $db = D('scene');

        $data = array();

        $num = I('num');

        $data['channel_id'] = I('channel');
        $data['setmeal_id'] = I('setmeal');

        for ($i = 0; $i < $num; $i++) {
            $id = $db->add($data);

            if ($id) {
                $ticket = $this->create_qrcode($id);

                $data['ticket'] = $ticket['ticket'];
                $data['create_date'] = date('y-m-d h:i:s',time());

                $condition = array('id' => $id);

                $result = $db->where($condition)->save($data);

                if (!$result) {
                    //删除创建失败的二维码数据
                    $db->where($condition)->delete();

                    echo fit_api(true,200,'生成'.$i.'张二维码', ''); exit;
                }
            }
            else {
                echo fit_api(true,200,'生成'.$i.'张二维码', ''); exit;
            }
        }

        echo fit_api(true,200,'生成成功', '');
    }

    #endregion

    #region tools方法

    public function create_qrcode($scene_id) {
        $accessToken = $this->getAccessToken();//获取access_token
        $PostString = '{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '. $scene_id .'}}}';

        $PostUrl = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$accessToken;//POST的url

        $result = $this->dataPost($PostString, $PostUrl);//将菜单结构体POST给微信服务器

        $jsondecode = json_decode($result);

        $array = get_object_vars($jsondecode);

        return $array;
    }

    public function getAccessToken() {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->app_id."&secret=".$this->app_secret;
        $data = $this->getCurl($url);//通过自定义函数getCurl得到https的内容

        $resultArr = json_decode($data, true);//转为数组
        return $resultArr["access_token"];//获取access_token
    }

    public function getCurl($url) {//get https的内容
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不输出内容
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result =  curl_exec($ch);
        curl_close ($ch);
        return $result;
    }

    public function dataPost($post_string, $url) {//POST方式提交数据
        $context = array('http' => array('method' => "POST", 'header' => "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) \r\n Accept: */*", 'content' => $post_string));
        $stream_context = stream_context_create($context);
        $data = file_get_contents($url, FALSE, $stream_context);
        return $data;
    }

    public function upload() {
        $targetFolder = '/Public/uploads'; // Relative to the root

        $verifyToken = md5('unique_salt' . $_POST['timestamp']);

        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;

            // Validate the file type
            $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
            $fileParts = pathinfo($_FILES['Filedata']['name']);

            $new_file_name = uniqid().time().rand(10000000,99999999). '.' . $fileParts['extension'];

            $targetFile = rtrim($targetPath,'/') . '/' . $new_file_name;
            $web_file = '/Public/uploads/'. $new_file_name;

            if (in_array($fileParts['extension'], $fileTypes)) {
                move_uploaded_file($tempFile,$targetFile);

                $info = array(
                    'url' => $web_file,
                    'path' => $targetFile
                );

                echo fit_api(true,200,'上传成功!', $info);
            } else {
                echo fit_api(true,0,'上传失败, 错误文件类型!', '');
            }
        }
    }

    public function save_photo() {
        $photoModel = D('photo');

        $url = I('url');
        $path = I('path');
        $text = I('text');
        $type = I('type');

        $data = array(
            'url' => $url,
            'path' => $path,
            'text' => $text,
            'type' => $type
        );

        $id = $photoModel->add($data);

        if ($id) {
            echo fit_api(true,200,'保存成功!', $id);
        }
        else {
            unlink($path);

            echo fit_api(true,0,'保存失败!', '');
        }
    }

    public function link_photo() {
        $itemModel = D('item');
        $photoModel = D('photo');
        $db = M('');

        $item_id = I('item_id');
        $photo_id = I('photo_id');

        if (!$item_id) {
            $item_data = array(
                'item_name' => ''
            );

            $item_id = $itemModel->add($item_data);

            if (!$item_id) {
                echo fit_api(true, 0,'关联图片失败!', $item_id);

                exit;
            }
        }

        $photo_data = array(
            'other_id' => $item_id
        );

        $result = $photoModel->where('id = '. $photo_id)->data($photo_data)->save();

        if (false !== $result) {
            echo fit_api(true, 200, '关联图片成功!', $item_id);
        }
        else {
            echo fit_api(true, 0, '关联图片失败, 请与管理员联系!', $item_id);
        }
    }

    public function loadImageList() {
        $photoModel = D('photo');

        $other_id = I('item_id');
        $type = I('type');

        $photo_condition = array('other_id' => $other_id ,'type' => $type);

        $list = $photoModel->where($photo_condition)->select();

        echo fit_api(true, 200, '获取图片成功', $list);
    }

    public function del_photo() {
        $photoModel = D('photo');

        $id = I('photo_id');

        if (!$id) {
            echo fit_api(true, 0, '删除图片失败, 图片id不能为空!', '');
            exit;
        }

        $photo = $photoModel->find($id);

        fopen($photo['path'], 'a+');

        $result = unlink($photo['path']);

        if (!$result) {
            echo fit_api(true, 200, '删除图片失败', $photo['path']);

            exit;
        }

        $photoModel->where('id = '. $id)->delete();

        echo fit_api(true, 200, '删除图片成功', $id);
    }

    #endregion
}