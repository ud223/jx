<?php
namespace Admin\Controller;
use Think\Controller;
class ManageController extends AdminController {
    private $app_id = 'wx8f573f8116d3d740';
    private $app_secret = 'f6c37757e5f11a12b4cdd80c2cda8b7e';
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

    #region 课程管理

    public function lesson_list() {
        $lesson = D('lesson');

        $d_lesson = $lesson->select();

        $this->assign('data', array(
            'lesson' => $d_lesson
        ));

        $this->display();
    }

    public function lesson_edit() {
        $lesson = D('lesson');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加课程'
            ));
        }
        else {
            $this->assign('title', array(
                'text' => '编辑课程'
            ));

            $condition = array('id' => $id);
          //  print_r($lesson->find($id)); exit;
            $this->assign('data', array(
                'lesson' => $lesson->find($id)//->where($condition)->select()
            ));
        }

        $this->display();
    }

    public function save_lesson() {
        $lesson = D('Lesson');

        $id = I('id');

        $name = I('name');
        $name_en = I('name_en');
        $local = I('local');
        $lat = I('lat');
        $lng = I('lng');
        $show_pic = I('show_pic');
        $old_show_pic = I('old_show_pic');
        $phone = I('phone');
        $remark = html_entity_decode(I('remark'));

        try {
            if ($show_pic) {
                $s = base64_decode(str_replace('data:image/jpeg;base64,', '', $show_pic));

                $pic_name = date('YmdHis') . '.jpg';

                $full_pic_name = SITE_DIR . UPLOAD_DIR . $pic_name;

                $file_count = file_put_contents($full_pic_name, $s);

                if (!$file_count) {
                    echo fit_api(true, 0, '文件保存失败!', '');

                    exit;
                }
            }
            else {
                $pic_name = $old_show_pic;
            }

            $data = array(
                'name' => $name,
                'name_en' => $name_en,
                'local' => $local,
                'lat' => $lat,
                'lng' => $lng,
                'show_pic' => $pic_name,
                'remark' => $remark,
                'phone' => $phone
            );

//            echo fit_api(true, 200,'保存成功', $data); exit;

            if (empty($id) || $id == '') {
                $id = $lesson->add($data);
            }
            else {
                $condition = array('id' => $id);

                $lesson->where($condition)->save($data);
            }

            echo fit_api(true, 200,'保存成功', $id);
        }
        catch (Exception $e) {
            echo fit_api(true, 0,'保存失败', '', '');
        }
    }

    public function del_lesson() {
        $lesson = D('Lesson');

        $id = I('id');

        $data = array('id' => $id);

        $lesson->where($data)->delete();

        echo fit_api(true,200,'删除成功', '');
    }

    #endregion

    #region vip类型管理

    public function vip_type_list() {
        $vip_type = D('vip_type');

        $d_vip_type = $vip_type->select();

        $this->assign('data', array(
            'vip_type' => $d_vip_type
        ));

        $this->display();
    }

    public function vip_type_edit() {
        $vip_type = D('vip_type');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加vip类型'
            ));
        }
        else {
            $this->assign('title', array(
                'text' => '编辑vip类型'
            ));

            $this->assign('data', array(
                'vip_type' => $vip_type->find($id)//->where($condition)->select()
            ));
        }

        $this->display();
    }

    public function save_vip_type() {
        $vip_type = D('vip_type');

        $id = I('id');

        $name = I('name');

        try {
            $data = array(
                'name' => $name
            );

            if (empty($id) || $id == '') {
                $id = $vip_type->add($data);
            }
            else {
                $condition = array('id' => $id);

                $vip_type->where($condition)->save($data);
            }

            echo fit_api(true, 200,'保存成功', $id);
        }
        catch (Exception $e) {
            echo fit_api(true, 0,'保存失败', '', '');
        }
    }

    public function del_vip_type() {
        $vip_type = D('vip_type');

        $id = I('id');

        $data = array('id' => $id);

        $vip_type->where($data)->delete();

        echo fit_api(true,200,'删除成功', '');
    }

    #endregion

    #region 课程配置

    public function lesson_config_list() {
        $db = M();

        $sql_text = 'select fit_lesson_config.id, fit_lesson_config.run_date, fit_lesson_config.end_date, fit_lesson_config.week_num, fit_lesson.name, fit_lesson_config.start_time, fit_lesson_config.end_time, fit_lesson_config.price, fit_lesson_config.vip_price_1, fit_lesson_config.max_count, fit_lesson_config.is_show from fit_lesson_config left join fit_lesson on fit_lesson_config.lesson_id = fit_lesson.id';

        $this->assign('data', array(
            'lesson_config' => $db->query($sql_text)
        ));

        $this->display();
    }

    public function lesson_config_edit() {
        $lesson_config = D('lesson_config');
        $lesson = D('lesson');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('data', array(
                'lesson' => $lesson->select()
            ));
        }
        else {
//            print_r($lesson_config->find($id)); exit;

            $this->assign('data', array(
                'lesson_config' => $lesson_config->find($id),
                'lesson' => $lesson->select()
            ));
        }

        $this->display();
    }

    public function save_lesson_config() {
        $lesson_config = D('lesson_config');

        $id = I('id');

        $lesson_id = I('lesson_id');
        $run_date = I('run_date');
        $end_date = I('end_date');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $price = I('price');
        $vip_price_1 = I('vip_price_1');
        $max_count = I('max_count');
        $week_num = I('week_num');
        $is_show = I('is_show');

        try {
            $data = array(
                'lesson_id' => $lesson_id,
                'run_date' => $run_date,
                'end_date' => $end_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'price' => $price,
                'vip_price_1' => $vip_price_1,
                'max_count' => $max_count,
                'week_num' => $week_num,
                'is_show' => $is_show
            );

            if (empty($id) || $id == '') {
//                $exist_condition = array('lesson_id' => $lesson_id);
//
//                $tmp_config = $lesson_config->where($exist_condition)->select();
//
//                if (!empty($tmp_config) || count($tmp_config) > 0) {
//                    echo fit_api(true, 0,'该课程配置已经存在', ''); exit;
//                }

                $id = $lesson_config->add($data);
            }
            else {
                $condition = array('id' => $id);

                $lesson_config->where($condition)->save($data);
            }

            echo fit_api(true, 200,'保存成功', $id);
        }
        catch (Exception $e) {
            echo fit_api(true, 0,'保存失败', '', '');
        }
    }

    public function del_lesson_config() {
        $lesson_config = D('lesson_config');

        $id = I('id');

        $data = array('id' => $id);

        $lesson_config->where($data)->delete();

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

    #region 教练管理

    public function coach_list() {
        $db = M();

        $sql_text = "select fit_user.id, fit_user.Name, fit_coach_config.price, fit_coach_config.title, fit_coach_config.label from fit_user left join fit_coach_config on fit_user.id = fit_coach_config.user_id where fit_user.is_coach = 1";

        $coach_list = $db->query($sql_text);

        $this->assign('data', $coach_list);

        $this->display();
    }

    public function  coach_edit() {
        $userModel = M('user');
        $coachModel = M('coach_config');

        $id = I('id');

        $user = $userModel->where('id = '. $id)->select();
        $user_config = $coachModel->where('user_id = '. $id)->select();

        $this->assign('data', array(
            'coach' => $user,
            'coach_config' => $user_config
        ));

        $this->display();
    }

    public function save_coach() {
        $userModel = D('user');
        $coachModel = D('coach_config');

        $id = I('id');

        $name = I('name');
        $label = I('label');
        $title = I('title');
        $price = I('price');
        $price_vip1 = I('price_vip1');
        $old_show_pic = I('old_show_pic');
        $show_pic = I('show_pic');
        $old_show_pic_big = I('old_show_pic_big');
        $show_pic_big = I('show_pic_big');

        if (!$id) {
            echo fit_api(true, 0, '用户ID不能为空!', '', '');
            exit;
        }

        try {
            if ($show_pic) {
                $s = base64_decode(str_replace('data:image/jpeg;base64,', '', $show_pic));

                $pic_name = date('YmdHis') . '.jpg';

                $full_pic_name = SITE_DIR . UPLOAD_DIR . $pic_name;

                $file_count = file_put_contents($full_pic_name, $s);

                if (!$file_count) {
                    echo fit_api(true, 0, '文件保存失败!', '');

                    exit;
                }


            }
            else {
                $pic_name = $old_show_pic;
            }

            if ($show_pic_big) {
                $s = base64_decode(str_replace('data:image/jpeg;base64,', '', $show_pic_big));

                $pic_name_big = date('YmdHis') . '_big.jpg';

                $full_pic_name = SITE_DIR . UPLOAD_DIR . $pic_name_big;

                $file_count = file_put_contents($full_pic_name, $s);

                if (!$file_count) {
                    echo fit_api(true, 0, '文件保存失败!', '');

                    exit;
                }
            }
            else {
                $pic_name_big = $old_show_pic_big;
            }

            $user_data = array(
                'Name' => $name
            );

            $coach_data = array(
                'title' => $title,
                'label' => $label,
                'price' => $price,
                'price_vip1' => $price_vip1,
                'show_pic' => $pic_name,
                'show_pic_big' => $pic_name_big,
                'user_id' => $id
            );

            $user_condition = array('id' => $id);

            $userModel->where($user_condition)->save($user_data);

            $coach_condition = array('user_id' => $id);

            $coach_config = $coachModel->where($coach_condition)->select();

            if (empty($coach_config) || count($coach_config) == 0) {
                $coachModel->add($coach_data);
            }
            else {
                $coachModel->where($coach_condition)->save($coach_data);
            }

            echo fit_api(true, 200, '保存成功', $id);
        } catch (Exception $e) {
            echo fit_api(true, 0, '保存失败', '', '');
        }
    }

    #endregion

    #region 私教时间安排

    public function pt_config() {
        $pt_configModel = M('pt_config');

        $pt_config = $pt_configModel->select();

        $this->assign('data', $pt_config);

        $this->display();
    }

    public function save_pt_config() {
        $pt_configModel = M('pt_config');

        $id = I('id');

        $time_1 = I('time_1');
        $time_2 = I('time_2');
        $time_3 = I('time_3');
        $time_4 = I('time_4');
        $time_5 = I('time_5');
        $time_6 = I('time_6');
        $time_7 = I('time_7');

        $pt_configModel->where('1')->delete();

        $config1["week_num"] = 1;
        $config1["time"] = $time_1;

        $pt_configModel->add($config1);

        $config2["week_num"] = 2;
        $config2["time"] = $time_2;

        $pt_configModel->add($config2);

        $config3["week_num"] = 3;
        $config3["time"] = $time_3;

        $pt_configModel->add($config3);

        $config4["week_num"] = 4;
        $config4["time"] = $time_4;

        $pt_configModel->add($config4);

        $config5["week_num"] = 5;
        $config5["time"] = $time_5;

        $pt_configModel->add($config5);

        $config6["week_num"] = 6;
        $config6["time"] = $time_6;

        $pt_configModel->add($config6);

        $config7["week_num"] = 7;
        $config7["time"] = $time_7;

        $pt_configModel->add($config7);

        echo fit_api(true, 200, '保存成功', $id);
    }

    #endregion

    #region 渠道管理

    public function channel_list() {
        $db = D('channel');

        $list = $db->select();

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function channel_edit() {
        $db = D('channel');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加渠道'
            ));
        }
        else {
            $this->assign('title', array(
                'text' => '编辑渠道'
            ));

            $item = $db->find($id);

            $this->assign('data', array(
                'item' => $item
            ));
        }

        $this->display();
    }

    public function channel_save() {
        $db = D('channel');

        $id = I('id');

        $name = I('name');

        $data = array(
            'name' => $name,
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

    public function channel_del() {
        $db = D('channel');

        $id = I('id');

        $data = array('id' => $id);

        $db->where($data)->delete();

        echo fit_api(true,200,'删除成功', '');
    }
    #endregion

    #region 套餐管理

    public function setmeal_list() {
        $db = D('setmeal');

        $list = $db->select();

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function setmeal_edit() {
        $db = D('setmeal');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加套餐'
            ));
        }
        else {
            $this->assign('title', array(
                'text' => '编辑套餐'
            ));

            $item = $db->find($id);

            $this->assign('data', array(
                'item' => $item
            ));
        }

        $this->display();
    }

    public function setmeal_save() {
        $db = D('setmeal');

        $id = I('id');

        $name = I('name');

        $data = array(
            'name' => $name,
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

    public function setmeal_del() {
        $db = D('setmeal');

        $id = I('id');

        $data = array('id' => $id);

        $db->where($data)->delete();

        echo fit_api(true,200,'删除成功', '');
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

    #endregion
}