<?php
namespace Admin\Controller;

use Think\Controller;
use Think\Page;

class ManageController extends AdminController
{
    private $app_id = '';
    private $app_secret = '';
    private $access_token = '';

    #region 系统操作action

    public function index()
    {
        $this->display();
    }


    public function site()
    {
        $this->display();
    }

    #endregion;

    #region 后台菜单操作action

    public function menu()
    {
        $menu = D('Menu');

        $pmenu_conditino = array('pid' => 0);

        $this->assign('data', array(
            'menu' => $menu->select(),
            'pmenu' => $menu->where($pmenu_conditino)->select()
        ));


        $this->display();
    }

    public function menu_edit()
    {
        $menuModel = D('menu');

        $id = I('id');

        if ($id) {
            $menu = $menuModel->find($id);
        }

        $pmenu_conditino = array('pid' => 0);

        $this->assign('data', array(
            'item' => $menu,
            'pmenu' => $menuModel->where($pmenu_conditino)->select()
        ));

        $this->display();
    }
//
//    public function add_menu(){
//        $menu = D('Menu');
//
//        $pid = I('pid');
//        $name = I('name');
//        $url = I('url');
//        $sort = I('sort');
//        $enable = I('enable');
//
//        $data = array(
//            'pid' => $pid,
//            'name' => $name,
//            'url' => $url,
//            'sort' => $sort,
//            'enable' => $enable
//        );
//
//        //print_r($data);exit;
//        $menu->add($data);
//
//        echo fit_api(true, 200,'添加成功', '');
//    }

    public function save_menu() {
        $menuModel = D('Menu');

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

        if (!$id) {
            $result = $menuModel->add($data);

            $id = $result;
        } else {
            $condition = array('id' => $id);

            $result = $menuModel->where($condition)->save($data);

            if (false !== $result || 0 !== $result) {
                $result = true;
            } else {
                $result = false;
            }
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', $id);
        else
            echo fit_api(true, 0, '保存失败', $id);
    }

    public function del_menu()
    {
        $menu = D('Menu');

        $id = I('id');

        $data = array('id' => $id);

        $menu->where($data)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    #endregion;

    #region 后台管理员操作action

    public function member()
    {
        $member = D('Member');
        //print_r($member->select());

        $this->assign('data', array(
            'member' => $member->select()
        ));

        $this->display();
    }

    public function member_type()
    {
        $member_type = D('MemberType');

        $m = $member_type->select();

        //print_r($m);exit;

        $this->assign('data', array(
            'member_type' => $m
        ));

        $this->display();
    }

    #endregion;

    #region 微信信息处理action

    public function wxinfo()
    {
        $this->display();
    }

    public function save_wx()
    {
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

            echo fit_api(true, 200, '添加成功', $id);
        } else {
            $condition = array(
                'id' => $id
            );

            $info->where($condition)->save($data);

            echo fit_api(true, 200, '保存成功', '');
        }
    }

    #endregion;

    #region 颜色管理

    public function color_list()
    {
        $db = D('color');

        $list = $db->select();

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function color_edit()
    {
        $db = D('color');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加颜色'
            ));
        } else {
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

    public function color_save()
    {
        $colorModel = M('color');

        $id = I('id');

        $color = I('color');
        $code = I('code');

        $data = array(
            'color' => $color,
            'code' => $code
        );

        $result = false;

        if (!$id) {
            $result = $colorModel->add($data);

            $id = $result;
        } else {
            $condition = array('id' => $id);

            $result = $colorModel->where($condition)->save($data);

            if (false !== $result || 0 !== $result) {
                $result = true;
            } else {
                $result = false;
            }
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', $id);
        else
            echo fit_api(true, 0, '保存失败', $id);
    }

    public function color_del()
    {
        $db = D('color');

        $id = I('id');

        $condition = array('id' => $id);

        $db->where($condition)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    #endregion

    #region 快递公司管理

    public function exp_list()
    {
        $db = D('exp');

        $list = $db->select();

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function exp_edit()
    {
        $db = D('exp');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加快递公司'
            ));
        } else {
            $this->assign('title', array(
                'text' => '编辑快递公司'
            ));

            $item = $db->find($id);

            $this->assign('data', array(
                'item' => $item
            ));
        }

        $this->display();
    }

    public function exp_save()
    {
        $db = D('color');

        $id = I('id');

        $text = I('text');
        $code = I('code');

        $data = array(
            'text' => $text,
            'code' => $code
        );

        $result = false;

        if (!$id) {
            $result = $db->add($data);

            $id = $result;
        } else {
            $condition = array('id' => $id);

            $result = $db->where($condition)->save($data);

            if (false !== $result || 0 !== $result) {
                $result = true;
            } else {
                $result = false;
            }
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', $id);
        else
            echo fit_api(true, 0, '保存失败', $id);
    }

    public function exp_del()
    {
        $db = D('exp');

        $id = I('id');

        $condition = array('id' => $id);

        $db->where($condition)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    #endregion

    #region 服务网点管理

    public function store_auditing() {
        $db = M();

        $sql_text = 'select fit_store.*, (select company_name from fit_traders where id = fit_store.company_id) as company_name from fit_store where is_show = 0 order by fit_store.create_at desc';

        $list = $db->query($sql_text);

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function store_show()
    {
        $storeModel = D('store');
        $pointModel = D('point');

        $id = I('id');

        $data = array(
            'is_show' => 1
        );

        $condition = array('id' => $id);

        $storeModel->where($condition)->save($data);

        $data_1 = array(
            'is_use' => 1
        );

        $result = $pointModel->where('store_id=' . $id)->save($data_1);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '保存成功!', $id);
        } else {
            echo fit_api(true, 0, '保存失败!', '');
        }
    }

    public function feedback_list()
    {
        $feedback = D('feedback');

        $key = I('key');
        $status = I('status');

        $condition = ' 1 = 1 ';
        if ($key) {
            $condition = $condition . " and title like '%" . $key . "%'";
        }

        if ($status) {
            $condition = $condition . " and status = " . $status;
        }

        $list = $feedback->where($condition)->select();


        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function feedback_edit()
    {
        $feedback = D('feedback');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '参数错误'
            ));
        } else {
            $item = $feedback->find($id);

            if ($item) {
                if ($item['status'] == 0) {
                    $item['statustext'] = '未处理';
                } else {
                    $item['statustext'] = '已处理';
                }
            }

            $this->assign('data', array(
                'item' => $item
            ));
        }

//        print_r($item); exit;

        $this->display();
    }

    public function feedback_save()
    {
        $feedbackModel = D('feedback');


        $feedback = I('feedback');
        $id = $feedback['id'];

        $data = array(
            input => $feedback['input'],
            remark => $feedback['remark'],
            result_at => date('Y-m-d H:i:s', time()),
            status => 1,
            result => $feedback['result']
        );

        $result = $feedbackModel->where('Id=' . $id)->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '保存成功!', $id);
        } else {
            echo fit_api(true, 0, '保存失败!', '');
        }

    }

    public function feedback_del()
    {
        $feedback = D('feedback');

        $id = I('id');

        $condition = array('Id' => $id);
        $feedback->where($condition)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    public function point_list()
    {
        $pointModel = D('point');

        $key = I('key');

        if ($key == '') {
            $list = $pointModel->select();
        } else {
            $list = $pointModel->where("point_text like '%" . $key . "%' or name like '%" . $key . "%' or phone like '%" . $key . "%'")->select();
        }

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function point_edit()
    {
        $pointModel = D('point');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加服务网点'
            ));
        } else {
            $this->assign('title', array(
                'text' => '编辑服务网点'
            ));

            $item = $pointModel->find($id);

            $this->assign('data', array(
                'item' => $item
            ));
        }

        $this->display();
    }

    public function point_save()
    {
        $pointModel = D('point');

        $id = I('id');

        $point_text = I('point_text');
        $province = I('province');
        $city = I('city');
        $region = I('region');
        $address = I('address');
        $name = I('name');
        $phone = I('phone');
        $long = I('long');
        $lat = I('lat');

        $data = array(
            'join_type' => '经销商/服务商',
            'point_text' => $point_text,
            'province' => $province,
            'city' => $city,
            'region' => $region,
            'address' => $address,
            'name' => $name,
            'phone' => $phone,
            'long' => $long,
            'lat' => $lat,
            'is_use' => 1
        );

        $result = false;

        if (empty($id) || $id == '') {
            $result = $pointModel->add($data);

            $id = $result;

            if ($id)
                echo fit_api(true, 200, '保存成功', $id);
            else
                echo fit_api(true, 0, '保存失败', $id);
        } else {
            $condition = array('id' => $id);

            $result = $pointModel->where($condition)->save($data);

            if (false !== $result || 0 !== $result) {
                echo fit_api(true, 200, '保存成功!', $id);
            } else {
                echo fit_api(true, 0, '保存失败!', '');
            }
        }
    }

    public function point_del()
    {
        $pointModel = D('point');

        $id = I('id');

        $condition = array('id' => $id);

        $pointModel->where($condition)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    #endregion

    #region 素材管理

    public function source_list()
    {
        $db = M();

        $sql_text = 'select fit_source.*, fit_source_class.text as source_class_text from fit_source left join fit_source_class on fit_source.source_class = fit_source_class.id order by fit_source.id';

        $this->assign('data', array(
            'list' => $db->query($sql_text)
        ));

        $this->display();
    }

    public function source_edit()
    {
        $sourceModel = D('source');
        $sourceClassModel = D('source_class');
        $photoModel = D('photo');

        $id = I('id');

        if ($id) {
            $source = $sourceModel->find($id);

            if ($source) {
                $photo = $photoModel->find($source['photo_id']);

                $source['photo'] = $photo;
            }
        }

        $source_class = $sourceClassModel->select();

        $this->assign('data', array(
            'item' => $source,
            'source_class' => $source_class
        ));

//        print_r($source); exit;

        $this->display();
    }

    public function source_save()
    {
        $sourceModel = D('source');

        $id = I('id');
        $source_class = I('source_class');
        $photo_id = I('photo_id');
        $text = I('text');
        $content = html_entity_decode(I('content'));
        $sort = I('sort');
        $description = I('description');

        $data = array(
            'text' => $text,
            'source_class' => $source_class,
            'photo_id' => $photo_id,
            'content' => $content,
            'sort' => $sort,
            'description' => $description,
            'create_at' => date('Y-m-d H:m:s', time())
        );

        if (!$id) {
            $result = $sourceModel->add($data);

            $id = $result;
        } else {
            $condition = array('id' => $id);

            $result = $sourceModel->where($condition)->save($data);

            if (false !== $result || 0 !== $result) {
                $result = true;
            } else {
                $result = false;
            }
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', $id);
        else
            echo fit_api(true, 0, '保存失败', $id);
    }

    public function source_del()
    {
        $sourceModel = D('source');

        $id = I('id');

        $data = array('id' => $id);

        $sourceModel->where($data)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    #endregion

    #region 素材类型管理

    public function source_class_list()
    {
        $db = D('source_class');

        $list = $db->select();

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function source_class_edit()
    {
        $db = D('source_class');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加素材类型'
            ));
        } else {
            $this->assign('title', array(
                'text' => '编辑素材类型'
            ));

            $item = $db->find($id);

            $this->assign('data', array(
                'item' => $item
            ));
        }

        $this->display();
    }

    public function source_class_save()
    {
        $db = D('source_class');

        $id = I('id');

        $text = I('text');

        $data = array(
            'text' => $text
        );

        $result = false;

        if (!$id) {
            $result = $db->add($data);

            $id = $result;
        } else {
            $condition = array('id' => $id);

            $result = $db->where($condition)->save($data);

            if (false !== $result || 0 !== $result) {
                $result = true;
            } else {
                $result = false;
            }
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', $id);
        else
            echo fit_api(true, 0, '保存失败', $id);
    }

    public function source_class_delete()
    {
        $db = D('source_class');

        $id = I('id');

        $condition = array('id' => $id);

        $db->where($condition)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    #endregion

    #region 机车备案管理

    public function cart_import()
    {
        $this->display();
    }

    public function import_carts()
    {
        $cartModel = M('car');
        $colorModel = M('color');
        $cartClassModel = M('cartclass');
        $db = M();

        $file_path = I('file_name');

        $carts = $this->InputExpressExcel($file_path);

        if (!is_array($carts)) {
            echo fit_api(true, 0, '导入失败, 没有获取到数据!', '');
            exit;
        }

        if (count($carts) == 0) {
            echo fit_api(true, 0, '导入失败, 没有获取到数据!', '');
            exit;
        }

        foreach ($carts['data'] as $c) {

            $cart = $cartModel->where("car_sn='" . $c['car_sn'] . "'")->find();

            $car_class = $cartClassModel->where("name='" . $c['car_class'] . "'")->find();

            if (!$car_class) {
                echo fit_api(true, 0, '车型:' . $c['car_class'] . '没有备案!', '');
                exit;
            }

            $c['car_class_id'] = $car_class['id'];

            $color = $colorModel->where("color='" . $c['color'] . "'")->find();

            if (!$color) {
//                echo fit_api(true, 0, '颜色:'. $c['color'] .'没有备案!', ''); exit;
                $c['color_id'] = 0;
            } else {
                $c['color_id'] = $color['id'];
            }
//            echo fit_api(true, 0, '导入时遇到错误, 请重新导入!', $car_class); exit;

            $data = array(
                'car_sn' => $c['car_sn'],
                'car_name' => $c['car_class'],
                'car_frame_no' => $c['car_frame_no'],
                'car_engine_no' => $c['car_engine_no'],
                'battery_number' => $c['battery_number'],
                'car_class' => $c['car_class_id'],
                'color' => $c['color_id']
//                'create_at' => date('Y-m-d H:i:s', time())
            );

            if ($cart) {
                $c['id'] = $cart['id'];

                $condition = array(
                    id => $c['id']
                );

                $result = $cartModel->where($condition)->save($data);

//                echo fit_api(true, 0, '导入时遇到错误, 请重新导入2!', $cartModel); exit;

                if (false !== $result || 0 !== $result) {

                } else {
                    echo fit_api(true, 0, '导入时遇到错误, 请重新导入!', '');
                    exit;
                }
            } else {
//                echo fit_api(true, 0, '导入时遇到错误, 请重新导入!', $data); exit;
                $num = $db->query("select count(id) as num from fit_car");//生成id
                $num = $num[0]["num"] + 1;

                while (strlen($num) < 6) {
                    $num = '0' . $num;
                }

                $data['id'] = uniqid() . time() . rand(10000000, 99999999) . $num;

                $result = $cartModel->add($data);

                if (!$result) {
                    echo fit_api(true, 0, '导入时遇到错误, 请重新导入!', '');
                    exit;
                }
            }
        }

        echo fit_api(true, 200, '导入成功', $carts);
    }

    public function cart_list()
    {
        $cartModel = D('car');
        $cartClassModel = D('cartclass');

        $carts = $cartModel->select();

        $list = array();

        foreach ($carts as $c) {
            $class = $cartClassModel->where("id=" . $c['car_class'])->find();

            $c['class_name'] = $class['name'];

            $list[] = $c;
        }

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function cart_add()
    {
        $cartClassModel = D('cartclass');

        $this->assign('data', array(
            'class' => $cartClassModel->select()
        ));

        $this->display();
    }

    public function cart_edit()
    {
        $cartModel = D('car');
        $cartClassModel = D('cartclass');

        $id = I('id');

        if ($id) {
            $cart = $cartModel->find($id);
//            print_r($cartClassModel->select()); exit;
            $this->assign('data', array(
                'item' => $cart,
                'class' => $cartClassModel->select()
            ));

            $this->display();
        } else {
            header('Location: /manage/cart_list');
        }
    }

    public function cart_save()
    {
        $cartModel = D('car');
        $db = M();

        $cart = I('cart');

        $tmp_cart = $cartModel->where("car_sn='" . $cart['car_sn'] . "'")->find();

//        if ($cart) {
//            echo fit_api(true, 0, '已存在相同的车辆SN码车辆信息!', ''); exit;
//        }

//        $carts = $cartModel->where("car_frame_no='". $cart['car_frame_no'] ."'")->select();
//
//        if ($carts && count($carts) > 0) {
//            echo fit_api(true, 0, '已存在相同的车架号车辆信息!', ''); exit;
//        }
//
//        $carts = $cartModel->where("car_engine_no='". $cart['car_engine_no'] ."'")->select();
//
//        if ($carts && count($carts) > 0) {
//            echo fit_api(true, 0, '已存在相同的电机车辆信息!', ''); exit;
//        }

        if ($tmp_cart) {
            $data = array(
                'car_sn' => $cart['car_sn'],
                'car_frame_no' => $cart['car_frame_no'],
                'car_engine_no' => $cart['car_engine_no'],
                'car_name' => $cart['car_name'],
                'car_class' => $cart['car_class']
//                'create_at' => date('Y-m-d H:i:s', time())
            );

            $result = $cartModel->where("id='" . $tmp_cart['id'] . "'")->save($data);

            if (false !== $result || 0 !== $result) {
                echo fit_api(true, 200, '保存成功!', $tmp_cart['id']);
            } else {
                echo fit_api(true, 0, '保存失败!', '');
            }
        } else {
            if ($cart['id']) {
                $data = array(
                    'car_sn' => $cart['car_sn'],
                    'car_frame_no' => $cart['car_frame_no'],
                    'car_engine_no' => $cart['car_engine_no'],
                    'car_name' => $cart['car_name'],
                    'car_class' => $cart['car_class']
//                    'create_at' => date('Y-m-d H:i:s', time())
                );

                $result = $cartModel->where("id='" . $cart['id'] . "'")->save($data);

                if (false !== $result || 0 !== $result) {
                    echo fit_api(true, 200, '保存成功!', $cart['id']);
                } else {
                    echo fit_api(true, 0, '保存失败!', '');
                }
            } else {
                $num = $db->query("select count(id) as num from fit_car");//生成id
                $num = $num[0]["num"] + 1;
                while (strlen($num) < 6) {
                    $num = '0' . $num;
                }

                $id = uniqid() . time() . rand(10000000, 99999999) . $num;

                $data = array(
                    'id' => $id,
                    'car_sn' => $cart['car_sn'],
                    'car_frame_no' => $cart['car_frame_no'],
                    'car_engine_no' => $cart['car_engine_no'],
                    'car_name' => $cart['car_name'],
                    'car_class' => $cart['car_class'],
                    'create_at' => date('Y-m-d H:i:s', time())
                );

                $result = $cartModel->add($data);

                if ($result) {
                    echo fit_api(true, 200, '保存成功!', $id);
                } else {
                    echo fit_api(true, 0, '保存失败!', '');
                }
            }
        }
    }

    public function lose_log()
    {
        $lose_logModel = D('lose_log');
        $user_carModel = D('user_car');
        $userModel = D('user');

        $car_id = I('id');

        $logs = $lose_logModel->where("car_id='" . $car_id . "'")->order('create_at desc')->select();

        $list = array();

        foreach ($logs as $l) {
//            $cart = $cartModel->find($l['car_id']);

            $user_car = $user_carModel->where("car_id='" . $l['car_id'] . "'")->find();

            $user = $userModel->find($user_car['user_id']);

            $l['user'] = $user;

            $list[] = $l;
        }

//        print_r($list); exit;

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function lose_send()
    {
        $lose_logModel = D('lose_log');

        $log_id = I('id');

        $data = array(
            'is_notify' => 1
        );

        $result = $lose_logModel->where("id='" . $log_id . "'")->save($data);

        if ($result) {
            echo fit_api(true, 200, '保存成功!', '');
        } else {
            echo fit_api(true, 0, '保存失败!', '');
        }
    }

    public function repair_log()
    {
        $repair_logModel = D('repair_log');

        $car_id = I('id');

        $logs = $repair_logModel->where("car_id='" . $car_id . "'")->order('create_at desc')->select();

        $this->assign('data', array(
            'list' => $logs
        ));

        $this->display();
    }

    #endregion

    #region 机车车型管理

    public function cartclass_list()
    {
        $db = D('cartclass');

        $list = $db->select();

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function cartclass_edit()
    {
        $db = D('cartclass');

        $id = I('id');

        if (empty($id) || $id == 0) {
            $this->assign('title', array(
                'text' => '添加车型'
            ));
        } else {
            $this->assign('title', array(
                'text' => '编辑车型'
            ));

            $item = $db->find($id);

            $this->assign('data', array(
                'item' => $item
            ));
        }

        $this->display();
    }

    public function cartclass_save()
    {
        $db = D('cartclass');

        $cart = I('cart');

        $id = $cart['id'];
//        $simple_name = I('simple_name');
//        $photo = I('photo');


        $data = array(
            'name' => $cart['name']
        );

//        echo fit_api(true, 0,'保存失败', $data);

        $result = false;

        if (empty($id) || $id == '') {
            $result = $db->add($data);

            $id = $result;
        } else {
            $condition = array('id' => $id);

            $result = $db->where($condition)->save($data);
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', $id);
        else
            echo fit_api(true, 0, '保存失败', $id);
    }

    public function cartclass_del()
    {
        $db = D('color');

        $id = I('id');

        $condition = array('id' => $id);

        $db->where($condition)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    #endregion

    #region 商品管理

    public function item_list()
    {
        $itemModel = D('item');

        $list = $itemModel->select();

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function item_edit()
    {
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
        } else {
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

    public function save_item()
    {
        $itemModel = D('item');

        $item_id = I('item_id');

        $item_name = I('item_name');
        $sub_name = I('sub_name');
        $price = I('price');
        $color = I('color');
        $type = I('type');
        $show_photo = I('show_photo');
        $content_photo = I('content_photo');

        $data = array(
            'item_name' => $item_name,
            'sub_name' => $sub_name,
            'price' => $price,
            'color' => $color,
            'type' => $type,
            'show_photo' => $show_photo,
            'content_photo' => $content_photo
        );

//            echo fit_api(true, 200,'保存成功', $data); exit;

        if (!$item_id) {
//            echo fit_api(true, 0,'保存失败1', $data); exit;
            $item_id = $itemModel->add($data);

            if ($item_id) {
                echo fit_api(true, 200, '保存成功!', $item_id);
                exit;
            } else {
                echo fit_api(true, 0, '保存失败!', '');
                exit;
            }
        } else {
//            echo fit_api(true, 0,'保存失败2', $data); exit;
//            $condition = array('id' => $item_id);

            $result = $itemModel->where('id = ' . $item_id)->save($data);

            if ($result !== false) {
                echo fit_api(true, 200, '保存成功!', $item_id);
            } else {
                echo fit_api(true, 0, '保存失败!', $item_id);
                exit;
            }
        }

//        if ($result !== false) {
//            echo fit_api(true, 0, '保存失败!', $item_id);
//            exit;
//        }
//        else {
//            if ($result >= 0) {
//                echo fit_api(true, 200, '保存成功!', $item_id);
//                exit;
//            }
//            else {
//                echo fit_api(true, 0, '保存失败!', $item_id);
//                exit;
//            }
//        }
    }

    public function del_item()
    {
        $itemModel = D('item');

        $id = I('id');

        $data = array('id' => $id);

        $itemModel->where($data)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    #endregion

    #region 订单管理

    public function order_list()
    {
        $db = M();

        $sql_text = 'select fit_order.*, fit_address.name, fit_address.phone, fit_address.province, fit_address.city, fit_address.address from fit_order left join fit_address on fit_order.address_id = fit_address.id where fit_order.state > 10 and fit_order.state < 60 order by fit_order.create_at desc';

        $orders = $db->query($sql_text);

//        print_r($orders); exit;

        $this->assign('data', array(
            'order' => $orders
        ));

        $this->display();
    }

    public function order_detail()
    {
        $db = M();
        $expModel = D('exp');

        $id = I('id');

        $sql_text = "select fit_order.*, fit_address.name, fit_address.phone, fit_address.address from fit_order left join fit_address on fit_order.address_id = fit_address.id where fit_order.state > 10 and fit_order.id = '" . $id . "'";

        $orders = $db->query($sql_text);

        $order = '';

        if ($orders && count($orders) > 0) {
            $order = $orders[0];

            switch ($order['state']) {
                case '20': {
                    $order['state_text'] = '待发货';
                    break;
                }
                case '30': {
                    $order['state_text'] = '已收货';
                    break;
                }
                case '40': {
                    $order['state_text'] = '待评价';
                    break;
                }
                case '50': {
                    $order['state_text'] = '已评价';
                    break;
                }
                case '80': {
                    $order['state_text'] = '已关闭';
                    break;
                }
                case '90': {
                    $order['state_text'] = '已退货';
                    break;
                }
            }
        }

        $exps = $expModel->select();


        $this->assign('data', array(
            'order' => $order,
            'exp' => $exps
        ));

        $this->display();
    }

    public function order_send()
    {
        $orderModel = D('order');

        $order_id = I('order_id');

        $exp_id = I('exp_id');
        $exp_code = I('exp_code');
        $exp_text = I('exp_text');

        $data = array(
            'exp_id' => $exp_id,
            'exp_code' => $exp_code,
            'exp_text' => $exp_text,
            'state' => 30
        );

//            echo fit_api(true, 200,'保存成功', $data); exit;

        if (!$order_id) {
            echo fit_api(true, 0, '订单号不能为空!', '');
            exit;
        } else {
//            echo fit_api(true, 0,'保存失败2', $data); exit;
//            $condition = array('id' => $item_id);

            $result = $orderModel->where("id = '" . $order_id . "'")->save($data);
        }

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '保存成功!', '');
        } else {
            echo fit_api(true, 0, '保存失败!', '');
        }
    }

    public function setCoach()
    {
        $order = D('order');

        $order_id = I('order_id');
        $coach_id = I('coach_id');

        $condition = array('order_id' => $order_id);

        $data = array(
            'coach_id' => $coach_id
        );

        $order->where($condition)->save($data);

        echo fit_api(true, 200, '保存成功', '');
    }

    public function refund_list()
    {
        $orderModel = M('order');
        $addressModel = M('address');

        $tmp_list = $orderModel->where('state = 100')->order('create_at desc')->select();

        $list = array();

        foreach ($tmp_list as $o) {
            $address = $addressModel->where('id=' . $o['address_id'])->find();

            $o['address'] = $address;

            $list[] = $o;
        }

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function order_refund()
    {
        $orderModel = M('order');

        $order_id = I('order_id');

        $data = array(
            state => 101
        );

        $result = $orderModel->where("id='" . $order_id . "'")->save($data);

        if (false !== $result) {
            echo fit_api(true, 200, '操作成功!' . $order_id, '');
        } else {
            echo fit_api(true, 0, '操作失败!', '');
        }
    }

    #endregion

    #region 机车信息管理

    public function pc_bike_edit()
    {
        $bikeInfoModel = D('bike_info');

        $info = $bikeInfoModel->find(1);

        $this->assign('data', array(
            'item' => $info
        ));

        $this->display();
    }

    public function mobile_bike_edit()
    {
        $bikeInfoModel = D('bike_info');

        $info = $bikeInfoModel->find(2);

        $this->assign('data', array(
            'item' => $info
        ));

        $this->display();
    }

    public function add_photo()
    {
        $bikeInfoModel = D('bike_info');

        $id = I('id');
        $photo_ids = I('photo_ids');

        $data = array(
            'photo_ids' => $photo_ids
        );

        $result = $bikeInfoModel->where('id = ' . $id)->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '上传图片成功!', '');
        } else {
            echo fit_api(true, 0, '上传图片失败!', '');
        }
    }

    public function save_pc_bike_info()
    {
        $bikeInfoModel = D('bike_info');

        $photo_ids = I('photo_ids');
        $models = I('models');

        $data = array(
            'photo_ids' => $photo_ids,
            'models' => $models
        );

        $result = $bikeInfoModel->where('id = 1')->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '上传图片成功!', '');
        } else {
            echo fit_api(true, 0, '上传图片失败!', '');
        }
    }

    public function save_mobile_bike_info()
    {
        $bikeInfoModel = D('bike_info');

        $photo_ids = I('photo_ids');
        $models = I('models');

        $data = array(
            'photo_ids' => $photo_ids,
            'models' => $models
        );

        $result = $bikeInfoModel->where('id = 2')->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '上传图片成功!', '');
        } else {
            echo fit_api(true, 0, '上传图片失败!', '');
        }
    }

    #endregion

    #region 信息管理

    public function about_edit()
    {
        $contentModel = D('content');

        $about = $contentModel->find(1);

        $this->assign('data', array(
            'content' => $about
        ));

        $this->display();
    }

    public function about_save()
    {
        $contentModel = D('content');

        $content = html_entity_decode(I('content'));

        $data = array(
            'content' => $content
        );

        $result = $contentModel->where('id=1')->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '保存成功!', '');
        } else {
            echo fit_api(true, 0, '保存失败!', '');
        }
    }

    public function buy_question_edit()
    {
        $contentModel = D('content');

        $about = $contentModel->find(6);

        $this->assign('data', array(
            'content' => $about
        ));

        $this->display();
    }

    public function buy_question_save()
    {
        $contentModel = D('content');

        $content = html_entity_decode(I('content'));

        $data = array(
            'content' => $content
        );

        $result = $contentModel->where('id=6')->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '保存成功!', '');
        } else {
            echo fit_api(true, 0, '保存失败!', '');
        }
    }

    public function question_edit()
    {
        $contentModel = D('content');

        $question = $contentModel->find(2);

        $this->assign('data', array(
            'content' => $question
        ));

        $this->display();
    }

    public function question_save()
    {
        $contentModel = D('content');

        $content = html_entity_decode(I('content'));

        $data = array(
            'content' => $content
        );

        $result = $contentModel->where('id=2')->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '保存成功!', '');
        } else {
            echo fit_api(true, 0, '保存失败!', '');
        }
    }

    public function test_self_edit()
    {
        $contentModel = D('content');

        $test_self = $contentModel->find(3);

        $this->assign('data', array(
            'content' => $test_self
        ));

        $this->display();
    }

    public function test_self_save()
    {
        $contentModel = D('content');

        $content = html_entity_decode(I('content'));

        $data = array(
            'content' => $content
        );

        $result = $contentModel->where('id=3')->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '保存成功!', '');
        } else {
            echo fit_api(true, 0, '保存失败!', '');
        }
    }

    public function protocol_edit()
    {
        $contentModel = D('content');

        $protocol = $contentModel->find(4);

        $this->assign('data', array(
            'content' => $protocol
        ));

        $this->display();
    }

    public function protocol_save()
    {
        $contentModel = D('content');

        $content = html_entity_decode(I('content'));

        $data = array(
            'content' => $content
        );

        $result = $contentModel->where('id=4')->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '保存成功!', '');
        } else {
            echo fit_api(true, 0, '保存失败!', '');
        }
    }

    #endregion

    #region 文件管理

    public function file_list()
    {
        $fileModel = D('car_file');
        $file_typeModel = D('file_type');

        $tmp_files = $fileModel->order('create_at desc')->select();

        $files = array();

        foreach ($tmp_files as $f) {
            $file_type = $file_typeModel->find($f['type']);

            $f['file_type'] = $file_type;

            $files[] = $f;
        }

        $this->assign('data', array(
            'list' => $files
        ));

        $this->display();
    }

    public function file_edit()
    {
        $fileModel = D('car_file');
        $file_typeModel = D('file_type');

        $id = I('id');

        $file_type = $file_typeModel->select();

        if ($id) {
            $file = $fileModel->find($id);
        }

        $this->assign('data', array(
            'item' => $file,
            'file_type' => $file_type
        ));

        $this->display();
    }

    public function file_save()
    {
        $fileModel = D('car_file');

        $file = I('file');
        $id = $file['id'];

        $data = array(
            'file_name' => $file['file_name'],
            'file_app_url' => $file['path'],
            'file_car_url' => $file['path'],
            'file_app_length' => $file['file_app_length'],
            'file_car_length' => $file['file_app_length'],
            'is_builtIn' => 1,
            'type' => $file['type'],
            'file_app_md5' => $file['file_app_md5'],
            'create_at' => date('Y:m:d H:i:s', time()),
        );

        if ($id) {
            $result = $fileModel->where('fileId=' . $id)->save($data);

            if (false !== $result || 0 !== $result) {
                echo fit_api(true, 200, '保存成功!', $id);
                exit;
            } else {
                echo fit_api(true, 0, '保存失败!', '');
                exit;
            }
        } else {
            $id = $fileModel->add($data);

            if ($id) {
                echo fit_api(true, 200, '保存成功!', $id);
                exit;
            } else {
                echo fit_api(true, 0, '保存失败!', '');
                exit;
            }
        }
    }

    #endregion

    #region 版本管理

    public function version_save()
    {
        $versionModel = D('version');

        $version = I('version');

        $id = $version['id'];

        $version['new_version_url'] = str_replace('/Public/uploads/', '', $version['new_version_url']);

        $data = array(
            'version_code' => $version['version_code'],
            'newVersionName' => $version['newVersionName'],
            'version_size' => $version['version_size'],
            'new_version_url' => '/Public/uploads/' . $version['new_version_url'],
            'file_md5' => $version['file_md5'],
            'is_must_update' => $version['is_must_update'],
            'type_id' => $version['type_id'],
            'car_class' => $version['car_class'],
            'create_at' => date('Y:m:d H:i:s', time()),
        );

//        echo fit_api(true, 0, '保存失败!', $data);

        if ($id) {
            $result = $versionModel->where('id=' . $id)->save($data);

            if (false !== $result || 0 !== $result) {
                echo fit_api(true, 200, '保存成功!', $id);
            } else {
                echo fit_api(true, 0, '保存失败!', '');
            }
        } else {
            $id = $versionModel->add($data);

            if ($id) {
                echo fit_api(true, 200, '保存成功!', $id);
            } else {
                echo fit_api(true, 0, '保存失败!', '');
            }
        }
    }

    public function version_list()
    {
        $versionModel = D('version');
        $cartClassModel = M('cartclass');
        $appTypeModel = M('app_type');

        $tmp_list = $versionModel->order('id desc')->select();

        $list = array();

        foreach ($tmp_list as $v) {
            $app = $cartClassModel->find($v['car_class']);
            $type = $appTypeModel->find($v['type_id']);

            $v['app'] = $app;
            $v['type'] = $type;

            $list[] = $v;
        }

//        print_r($list); exit;

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function version_edit()
    {
        $versionModel = D('version');
        $appTypeModel = M('app_type');
        $cartClassModel = M('cartclass');

        $id = I('id');

        if ($id) {
            $item = $versionModel->find($id);

            $this->assign('data', array(
                'item' => $item,
                'type' => $appTypeModel->select(),
                'class' => $cartClassModel->select()
            ));
        } else {
            $this->assign('data', array(
                'type' => $appTypeModel->select(),
                'class' => $cartClassModel->select()
            ));
        }

        $this->display();
    }

    public function version_appad()
    {
        $versionModel = D('version');
        $cartClassModel = M('cartclass');

        $item = $versionModel->find(1);

        $this->assign('data', array(
            'item' => $item,
            'class' => $cartClassModel->select()
        ));

        $this->display();
    }

    public function version_appios()
    {
        $versionModel = D('version');
        $cartClassModel = M('cartclass');

        $item = $versionModel->find(2);

        $this->assign('data', array(
            'item' => $item,
            'class' => $cartClassModel->select()
        ));

        $this->display();
    }

    public function version_arm()
    {
        $versionModel = D('version');
        $cartClassModel = M('cartclass');

        $item = $versionModel->find(3);

        $this->assign('data', array(
            'item' => $item,
            'class' => $cartClassModel->select()
        ));

        $this->display();
    }

    public function version_mcu()
    {
        $versionModel = D('version');
        $cartClassModel = M('cartclass');

        $item = $versionModel->find(4);

        $this->assign('data', array(
            'item' => $item,
            'class' => $cartClassModel->select()
        ));

        $this->display();
    }

    public function version_controller()
    {
        $versionModel = D('version');
        $cartClassModel = M('cartclass');

        $item = $versionModel->find(5);

        $this->assign('data', array(
            'item' => $item,
            'class' => $cartClassModel->select()
        ));

        $this->display();
    }

    public function version_bms()
    {
        $versionModel = D('version');
        $cartClassModel = M('cartclass');

        $item = $versionModel->find(6);

        $this->assign('data', array(
            'item' => $item,
            'class' => $cartClassModel->select()
        ));

        $this->display();
    }

    #endregion

    #region 二维码管理

    public function qrcode_list()
    {
        $db = D('');

        $sql_text = "select fit_scene.id, fit_channel.name as channel_name, fit_setmeal.name as setmeal_name, is_use, fit_scene.create_date, fit_scene.ticket, fit_user.name as user_name from fit_scene left join fit_channel on fit_scene.channel_id = fit_channel.id left join fit_setmeal on fit_scene.setmeal_id = fit_setmeal.id left join fit_user on fit_scene.user_id = fit_user.id order by fit_scene.id desc";

        $list = $db->query($sql_text);

        $this->assign('data', array(
            'list' => $list
        ));

        $this->display();
    }

    public function qrcode_edit()
    {
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

    public function qrcode_create()
    {
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
                $data['create_date'] = date('y-m-d h:i:s', time());

                $condition = array('id' => $id);

                $result = $db->where($condition)->save($data);

                if (!$result) {
                    //删除创建失败的二维码数据
                    $db->where($condition)->delete();

                    echo fit_api(true, 200, '生成' . $i . '张二维码', '');
                    exit;
                }
            } else {
                echo fit_api(true, 200, '生成' . $i . '张二维码', '');
                exit;
            }
        }

        echo fit_api(true, 200, '生成成功', '');
    }

    #endregion

    #region tools方法

    public function create_qrcode($scene_id)
    {
        $accessToken = $this->getAccessToken();//获取access_token
        $PostString = '{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": ' . $scene_id . '}}}';

        $PostUrl = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $accessToken;//POST的url

        $result = $this->dataPost($PostString, $PostUrl);//将菜单结构体POST给微信服务器

        $jsondecode = json_decode($result);

        $array = get_object_vars($jsondecode);

        return $array;
    }

    public function getAccessToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->app_id . "&secret=" . $this->app_secret;
        $data = $this->getCurl($url);//通过自定义函数getCurl得到https的内容

        $resultArr = json_decode($data, true);//转为数组
        return $resultArr["access_token"];//获取access_token
    }

    public function getCurl($url)
    {//get https的内容
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不输出内容
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function dataPost($post_string, $url)
    {//POST方式提交数据
        $context = array('http' => array('method' => "POST", 'header' => "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) \r\n Accept: */*", 'content' => $post_string));
        $stream_context = stream_context_create($context);
        $data = file_get_contents($url, FALSE, $stream_context);
        return $data;
    }

    public function upload()
    {
        $targetFolder = '/Public/uploads'; // Relative to the root

        $verifyToken = md5('unique_salt' . $_POST['timestamp']);

        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;

            // Validate the file type
            $fileTypes = array('jpg', 'jpeg', 'gif', 'png', 'mp4'); // File extensions
            $fileParts = pathinfo($_FILES['Filedata']['name']);

            $new_file_name = uniqid() . time() . rand(10000000, 99999999) . '.' . $fileParts['extension'];

            $targetFile = rtrim($targetPath, '/') . '/' . $new_file_name;
            $web_file = '/Public/uploads/' . $new_file_name;

            if (in_array($fileParts['extension'], $fileTypes)) {
                move_uploaded_file($tempFile, $targetFile);

                $info = array(
                    'url' => $web_file,
                    'path' => $targetFile
                );

                echo fit_api(true, 200, '上传成功!', $info);
            } else {
                echo fit_api(true, 0, '上传失败, 错误文件类型!', '');
            }
        }
    }

    public function save_photo()
    {
        $photoModel = D('photo');

        $url = I('url');
        $path = I('path');
        $text = I('text');
        $type = I('type');

        $pic_class = 1;

        $info2 = explode(".", $path);
        $extension = end($info2);

        if ($extension == 'mp4') {
            $pic_class = 2;
        } else if ($extension == 'swf') {
            $pic_class = 3;
        } else {
            $pic_class = 1;
        }


        $data = array(
            'url' => $url,
            'path' => $path,
            'text' => $text,
            'type' => $type,
            'pic_class' => $pic_class
        );

        $id = $photoModel->add($data);

        if ($id) {
            echo fit_api(true, 200, '保存成功!', $id);
        } else {
            unlink($path);

            echo fit_api(true, 0, '保存失败!', '');
        }
    }

    public function link_photo()
    {
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
                echo fit_api(true, 0, '关联图片失败!', $item_id);

                exit;
            }
        }

        $photo_data = array(
            'other_id' => $item_id
        );

        $result = $photoModel->where('id = ' . $photo_id)->data($photo_data)->save();

        if (false !== $result) {
            echo fit_api(true, 200, '关联图片成功!', $item_id);
        } else {
            echo fit_api(true, 0, '关联图片失败, 请与管理员联系!', $item_id);
        }
    }

    public function loadImageList()
    {
        $photoModel = D('photo');

        $other_id = I('item_id');
        $type = I('type');

        $photo_condition = array('other_id' => $other_id, 'type' => $type);

        $list = $photoModel->where($photo_condition)->select();

        echo fit_api(true, 200, '获取图片成功', $list);
    }

    public function loadInfoImageList()
    {
        $bikeInfoModel = D('bike_info');
        $photoModel = D('photo');

        $id = I('id');

        $info = $bikeInfoModel->find($id);

        $photo_ids = explode(',', $info['photo_ids']);

        $str_condition = "";

        foreach ($photo_ids as $p) {
            if ($str_condition) {
                $str_condition = $str_condition . ',';
            }

            $str_condition = $str_condition . "'" . $p . "'";
        }

        $photo_list = $photoModel->where("id in (" . $str_condition . ")")->select();

        $str_models = explode(',', $info['models']);

        for ($index = 0; $index < count($photo_list); $index++) {
            $photo_list[$index]['text'] = $str_models[$index];
        }

        echo fit_api(true, 200, '获取图片成功', $photo_list);
    }

    public function del_photo()
    {
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

        $photoModel->where('id = ' . $id)->delete();

        echo fit_api(true, 200, '删除图片成功', $id);
    }

    #endregion

    #region 运营商管理
    public function operator_list()
    {
        $operator = D('operator');

        $this->assign('data', array(
            'operator' => $operator->select()
        ));

        $this->display();
    }

    public function operator_edit()
    {
        $operator = D('operator');

        $id = I('id');

        if ($id) {
            $this->assign('title', '编辑');
            $operator_data = $operator->find($id);
            $this->assign('pwdinfo', '(不修改时不须填写)');
        } else {
            $this->assign('title', '新增');
            $this->assign('pwdinfo', '');
        }

        $this->assign('data', array(
            'item' => $operator_data
        ));

        $this->display();
    }

    public function operator_save()
    {
        $operator = D('operator');

        $id = I('id');
        $name = I('name');
        $user_name = I('user_name');
        $password = I('password');
        $enable = I('enable');

        $data = array();
        if (!$id) {
            $data = array(
                'name' => $name,
                'user_name' => $user_name,
                'password' => md5($password),
                'enable' => $enable
            );

            $result = $operator->add($data);

            $id = $result;
        } else {
            if ($password) {
                $data = array(
                    'name' => $name,
                    'user_name' => $user_name,
                    'password' => md5($password),
                    'enable' => $enable
                );
            } else {
                $data = array(
                    'name' => $name,
                    'user_name' => $user_name,
                    'enable' => $enable
                );
            }

            $condition = array('id' => $id);

            $result = $operator->where($condition)->save($data);

            if (false !== $result || 0 !== $result) {
                $result = true;
            } else {
                $result = false;
            }
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', $id);
        else
            echo fit_api(true, 0, '保存失败', $id);
    }

    public function operator_del()
    {
        $operator = D('operator');

        $id = I('id');

        $data = array('id' => $id);

        $operator->where($data)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    #endregion

    #region 版块管理
    public function bbs_section()
    {
        $section = D('bbs_section');

        $this->assign('data', array(
            'section' => $section->select()
        ));

        $this->display();
    }

    public function bbs_section_edit()
    {
        $section = D('bbs_section');

        $id = I('id');

        if ($id) {
            $section_data = $section->find($id);
        }

        $this->assign('data', array(
            'item' => $section_data
        ));

        $this->display();
    }

    public function save_bbs_section()
    {
        $bbs_section = D('bbs_section');

        $id = I('id');
        $name = I('name');
        $hot = I('hot');
        $sort = I('sort');
        $enable = I('enable');

        $data = array(
            'name' => $name,
            'hot' => $hot,
            'sort' => $sort,
            'enable' => $enable
        );

        if (!$id) {
            $result = $bbs_section->add($data);

            $id = $result;
        } else {
            $condition = array('Id' => $id);

            $result = $bbs_section->where($condition)->save($data);

            if (false !== $result || 0 !== $result) {
                $result = true;
            } else {
                $result = false;
            }
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', $id);
        else
            echo fit_api(true, 0, '保存失败', $id);
    }

    public function del_bbs_section()
    {
        $section = D('bbs_section');

        $id = I('id');

        $data = array('Id' => $id);

        $section->where($data)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    #endregion

    #region 版主管理
    public function bbs_section_admin()
    {
        $section_admin = D('bbs_section_admin');

        $this->assign('data', array(
            'section' => $section_admin->query("select a.Id,b.nick_name,c.name section_name from fit_bbs_section_admin a left join fit_user b on a.user_id = b.id left join fit_bbs_section c on a.section_id = c.Id ")
        ));

        $this->display();
    }

    public function bbs_section_admin_add()
    {
        $user = D('user');
        $section = D('bbs_section');

        $this->assign('data', array(
            'user' => $user->query("select * from fit_user where name <> '' order by name "),
            'section' => $section->select()
        ));

        $this->display();
    }

    public function search_bbs_section_admin()
    {
        $user_name = I('user_name');
        $section = I('section');

        $userModel = M('user');

        $user_data = $userModel->query("select *,(select count(*) from fit_bbs_section_admin where section_id=" . $section . " and user_id = a.id) admin from fit_user a where nick_name <> '' and nick_name like '%" . $user_name . "%'  order by name ");

        $html = '';
        for ($i = 0; $i < count($user_data); $i++) {
            $html .= "<tr>";
            $html .= "<td>" . $user_data[$i]["nick_name"] . "</td>";
            $html .= "<td>" . $user_data[$i]["user_name"] . "</td>";
            $html .= "<td>" . $user_data[$i]["phone_number"] . "</td>";
            if ($user_data[$i]["admin"] == 1)
                $html .= "<td><input type='checkbox' checked name='ck_user' val='" . $user_data[$i]["id"] . "' /></a></td>";
            else
                $html .= "<td><input type='checkbox' name='ck_user' val='" . $user_data[$i]["id"] . "' /></a></td>";
            $html .= "</tr>";
        }
        echo fit_api(true, 200, '成功', $html);
    }

    public function save_bbs_section_admin()
    {
        $user = I('user');
        $section = I('section');
        $array = explode(',', $user);

        $bbs_section_admin = D('bbs_section_admin');
        for ($i = 0; $i < count($array); $i++) {
            if ($array[$i]) {
                $condition = array(
                    'section_id' => $section,
                    'user_id' => $array[$i]
                );
                $data = $bbs_section_admin->where($condition)->select();
                if (!$data)
                    $result = $bbs_section_admin->add($condition);
            }
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', "");
        else
            echo fit_api(true, 0, '保存失败', "");
    }

    public function del_bbs_section_admin()
    {
        $section_admin = D('bbs_section_admin');

        $id = I('id');

        $data = array('Id' => $id);

        $section_admin->where($data)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }

    public function bbs_notice()
    {
        $bbs_notice = D('bbs_notice');

        $this->assign('data', $bbs_notice->query("select * from fit_bbs_notice order by creat_at desc limit 1 "));

        $this->display();
    }

    public function bbs_notice_add()
    {
        $bbs_notice = D('bbs_notice');
        $content = I('content');

        $data = array(
            'content' => $content,
            'creat_at' => date('Y-m-d H:i:s', time())
        );

        $result = $bbs_notice->add($data);

        if ($result)
            echo fit_api(true, 200, '保存成功', "");
        else
            echo fit_api(true, 0, '保存失败', "");
    }

    #endregion

    #regeion 轮播图管理

    public function pushtype_list()
    {
        $pushtype = D('pushtype');

        $this->assign('data', $pushtype->query("select * from fit_pushtype order by id"));

        $this->display();
    }

    public function pushtype_save()
    {
        $pushtype = D('pushtype');

        $name = I('name');
        $enable = I('enable');
        $id = I('id');

        $data = array(
            'pushtype_name' => $name,
            'enable' => $enable
        );

        if ($id) {
            $result = $pushtype->where('id=' . $id)->save($data);

            if (false !== $result || 0 !== $result) {
                echo fit_api(true, 200, '保存成功!', $id);
            } else {
                echo fit_api(true, 0, '保存失败!', '');
            }
        } else {
            $id = $pushtype->add($data);

            if ($id) {
                echo fit_api(true, 200, '保存成功!', $id);
            } else {
                echo fit_api(true, 0, '保存失败!', '');
            }
        }
    }

    public function pushtype_edit()
    {
        $pushtype = D('pushtype');

        $id = I('id');

        if ($id) {
            $pushtype_data = $pushtype->find($id);
        }

        $this->assign('data', array(
            'item' => $pushtype_data
        ));

        $this->display();
    }

    function pushtype_del()
    {
        $pushtype = D('pushtype');

        $id = I('id');

        $data = array('id' => $id);

        $pushtype->where($data)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }
    #endregion

    #regeion 轮播图管理

    public function bbs_queue_list()
    {
        $queue = D('bbs_queue');

        $this->assign('data', $queue->query("select * from fit_bbs_queue order by sort"));

        $this->display();
    }

    public function bbs_queue_save()
    {
        $queue = D('bbs_queue');

        $version = I('version');
        $id = $version['id'];

        $version['img_url'] = str_replace('/Public/uploads/', '', $version['img_url']);

        $data = array(
            'title' => $version['title'],
            'sort' => $version['sort_field'],
            'img_url' => '/Public/uploads/' . $version['img_url'],
            'enable' => $version['enable_field'],
            'create_at' => date('Y:m:d H:i:s', time())
        );

//        echo fit_api(true, 0, '保存失败!', $data);

        if ($id) {
            $result = $queue->where('id=' . $id)->save($data);

            if (false !== $result || 0 !== $result) {
                echo fit_api(true, 200, '保存成功!', $id);
            } else {
                echo fit_api(true, 0, '保存失败!', '');
            }
        } else {
            $id = $queue->add($data);

            if ($id) {
                echo fit_api(true, 200, '保存成功!', $id);
            } else {
                echo fit_api(true, 0, '保存失败!', '');
            }
        }
    }

    public function bbs_queue_edit()
    {
        $queue = D('bbs_queue');

        $id = I('id');

        if ($id) {
            $queue_data = $queue->find($id);
        }

        $this->assign('data', array(
            'item' => $queue_data
        ));

        $this->display();
    }

    function bbs_queue_del()
    {
        $queue = D('bbs_queue');

        $id = I('id');

        $data = array('Id' => $id);

        $queue->where($data)->delete();

        echo fit_api(true, 200, '删除成功', '');
    }
    #endregion
#reigon excel 处理

    /**
     * todo:导入excel文件
     * @param $_file execl文件
     *
     * @return Excel文件内容信息，二维数组
     */
    function InputExpressExcel($_file)
    {
        $result = array();

        if (!file_exists($_file)) {
//            return ReturnError("文件不存在");
            $result['code'] = 0;
            $result['msg'] = '文件不存在';

            return $result;
        }

        if (end(explode('.', $_file)) != "xls" && end(explode('.', $_file)) != "xlsx") {
//            return ReturnError("");
            $result['code'] = 0;
            $result['msg'] = '只接受xls和xlsx格式的Excel文件';

            return $result;
        }

//        $FileCfg = GetExcelFileCfg();

//        if($_file["size"] > $FileCfg["size"]){
////            return ReturnError("文件大小不能超过{$FileCfg["size_desc"]}");
//            $result['code'] = 0;
//            $result['msg'] = '只接受xls和xlsx格式的Excel文件';
//
//            return $result;
//        }

        import("Org.PHPExcel.PHPExcel");

        try {
            if (end(explode('.', $_file)) == "xlsx") {
                $objExcel = new \PHPExcel_Reader_Excel2007();
            } else {
                $objExcel = new \PHPExcel_Reader_Excel5();
            }

            $objExcel->setReadDataOnly(true);

            //读取Excel文件
            $phpExcel = $objExcel->load($_file);

//            $box_list = array();

            $carts = array();

            for ($index = 0; $index < 1; $index++) {
                //设置当前的sheet
                $phpExcel->setActiveSheetIndex($index);

                //获取当前活动sheet
                $objWorksheet = $phpExcel->getActiveSheet();

                //获取行数
                $highestRow = $objWorksheet->getHighestRow();

                //获取列数
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

                //判断excel第一行第一列是否是模版的第一行第一列
                if (trim($objWorksheet->getCellByColumnAndRow(0, 1)->getValue()) != 'SN') {
                    $result['code'] = 0;
                    $result['msg'] = '不是规范的机车备案导入文件!' . trim($objWorksheet->getCellByColumnAndRow(0, 1)->getValue());

                    return $result;
                }

                //循环输出数据
//                $data = array();


                //第一行为header从第二行开始读取
                for ($row = 2; $row <= $highestRow; ++$row) {
                    //判断第一行第一列是否为空 为空则跳过
                    if (trim($objWorksheet->getCellByColumnAndRow(0, $row)->getValue()) == '') {
                        break;
                    }

                    $tmp_data = array();

                    $tmp_data['car_sn'] = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $tmp_data['car_frame_no'] = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $tmp_data['car_engine_no'] = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $tmp_data['battery_number'] = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $tmp_data['car_class'] = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $tmp_data['color'] = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();

                    $carts[] = $tmp_data;
                }
            }

            $result['code'] = 200;
            $result['data'] = $carts;

            return $result;//ReturnCorrect('', $data);
        } catch (Exception $e) {
//            return ReturnError("解析文件时，出现错误！");
            $result['code'] = 0;
            $result['msg'] = '解析文件时，出现错误！';

            return $result;
        }
    }

    #endregion

    public function usernotes()
    {
        $contentModel = D('content');

        $about = $contentModel->find(5);

        $this->assign('data', array(
            'content' => $about
        ));

        $this->display();
    }

    public function usernotes_edit()
    {
        $contentModel = D('content');

        $about = $contentModel->find(5);

        $this->assign('data', array(
            'content' => $about
        ));

        $this->display();
    }

    public function usernotes_save()
    {
        $content = html_entity_decode(I('content'));

        $contentModel = D('content');

        $contentModel->content = $content;
        $result = $contentModel->where(" id = 5")->save();

        if (false !== $result || 0 !== $result) {
            $result = true;
        } else {
            $result = false;
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', $content);
        else
            echo fit_api(true, 0, '保存失败', $content);
    }

    public function car_list()
    {
        $car_sn = I('car_sn');
        $carModel = D('car');

        if ($car_sn) {
            $condition = array("car_sn" => $car_sn);
            $count = $carModel->where($condition)->count();
        } else
            $count = $carModel->count();

        $p = getpage($count, 20);

        if ($car_sn)
            $list = $carModel->query("select * from fit_car where car_sn ='" . $car_sn . "' order by id limit " . $p->firstRow . ',' . $p->listRows);
        else
            $list = $carModel->query("select * from fit_car order by id limit " . $p->firstRow . ',' . $p->listRows);

        $this->assign('data', array(
            'list' => $list
        ));

        $this->assign('page', $p->show()); // 赋值分页输出

        $this->display();
    }

    public function car_del()
    {
        $id = I('id');

        $carModel = D('car');

        $condition = array("id" => $id);

        $carModel->freeze = 1;
        $carModel->locked = 0;

        $car_data = $carModel->where($condition)->select();

        if($car_data){
            $iccid = $car_data[0]["iccid"];
            $car_sn = $car_data[0]["car_sn"];
            //发送远程解锁
            IccidSendSms($iccid, 'CAR_UNLOCK:' . $iccid . '&' . $car_sn . '$');
        }

        $result = $carModel->where($condition)->save();

        if (false !== $result || 0 !== $result) {
            $result = true;
        } else {
            $result = false;
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', '');
        else
            echo fit_api(true, 0, '保存失败', '');
    }

    public function car_edit()
    {
        $id = I('id');

        $carModel = D('car');

        $condition = array("id" => $id);

        $carModel->freeze = 0;
        $carModel->locked = 1;

        $car_data = $carModel->where($condition)->select();

        if($car_data){
            $iccid = $car_data[0]["iccid"];
            $car_sn = $car_data[0]["car_sn"];
            //发送远程锁车
            IccidSendSms($iccid, 'CAR_LOCK:' . $iccid . '&' . $car_sn . '$');
        }

        //IccidSendSms($iccid, 'CAR_UNLOCK:' . $iccid . '&' . $car_sn . '$');

        $result = $carModel->where($condition)->save();

        if (false !== $result || 0 !== $result) {
            $result = true;
        } else {
            $result = false;
        }

        if ($result)
            echo fit_api(true, 200, '保存成功', '');
        else
            echo fit_api(true, 0, '保存失败', '');
    }

    public function run_history_list()
    {
        $car_sn = I('car_sn');
        $run_historyModel = D('run_history');
        $carModel = D('car');

        if ($car_sn) {
            $condition = array("car_sn" => $car_sn);
            $car_data = $carModel->where($condition)->select();

            if ($car_data) {
                $condition = array("car_id" => $car_data[0]['id']);
                $count = $run_historyModel->where($condition)->count();
            } else
                $count = 0;

            $p = getpage($count, 20);
            $list = $carModel->query("select * from fit_run_history where car_id ='" . $car_data[0]['id'] . "' order by id limit " . $p->firstRow . ',' . $p->listRows);
        } else {
            $count = $run_historyModel->count();
            $p = getpage($count, 20);

            $list = $carModel->query("select * from fit_run_history order by id limit " . $p->firstRow . ',' . $p->listRows);
        }

        $this->assign('data', array(
            'list' => $list
        ));

        $this->assign('page', $p->show()); // 赋值分页输出

        $this->display();
    }

    public function run_hhistory_del()
    {
        $id = I('id');

        $run_historyModel = D('run_history');

        $condition = array("id" => $id);

        $result = $run_historyModel->where($condition)->delete();


        if (false !== $result || 0 !== $result) {
            $result = true;
        } else {
            $result = false;
        }

        if ($result)
            echo fit_api(true, 200, '删除成功', '');
        else
            echo fit_api(true, 0, '删除失败', '');
    }
}