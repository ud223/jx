<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/28
 * Time: 21:39
 */

namespace Mall\Controller;

use Think\Controller;

class UserController
{
    public function login()
    {
        $userModel = M('user');
        $loginModel = M('login');

        $phone = I('phone');
        $password = I('password');

        if (!$phone || strlen($phone) != 11) {
            echo fit_api(true, 0, '请填写正确手机号码!', strlen($phone));
            exit;
        }

        if (!$password) {
            echo fit_api(true, 0, '请填写登录密码!', '');
            exit;
        }

        $user = $userModel->where("phone_number = '" . $phone . "'")->select();

        if (!$user || count($user) < 1) {
            echo fit_api(true, 0, '用户名或密码错误!', count($user));
            exit;
        }

        if (strtolower($user[0]['password']) != strtolower(md5($password))) {
            echo fit_api(true, 0, '用户名或密码错误!', strtolower($user[0]['password']) . ':' . strtolower(md5($password)));
            exit;
        }

        $access_token = md5(uniqid() . time() . rand(10000000, 99999999));

        $loginModel->key = $access_token;
        $loginModel->user_id = $user[0]['id'];
        $loginModel->login_time = date('Y-m-d H:i:s', time());
        $loginModel->expire_time = date("Y-m-d H:i:s", strtotime("8 hour"));
        $loginModel->is_expire = 2;

        $result = $loginModel->add();

        if (!$result) {
            echo fit_api(true, 0, '登录失败, 请返回登录页面重新登录!', $result);
            exit;
        }

        echo fit_api(true, 200, '登录成功!', $access_token);
    }

    public function register()
    {
        $userModel = M('user');
        $smsModel = M('sms');
        $loginModel = M('login');

        $phone = I('phone');
        $code = I('code');
        $password = I('password');

        if (!$phone || strlen($phone) != 11) {
            echo fit_api(true, 0, '请填写正确手机号码!', strlen($phone));
            exit;
        }

        if (!$code) {
            echo fit_api(true, 0, '请填写验证码!', '');
            exit;
        }

        if (!$password) {
            echo fit_api(true, 0, '请填写登录密码!', '');
            exit;
        }

        $sms = $smsModel->where("phone_number = '" . $phone . "' and msg_type = 0")->order('create_at desc')->limit(1)->select();

        if ($sms[0]['code'] != $code) {
            echo fit_api(true, 0, '验证码不正确!', '');
            exit;
        }

        $user = $userModel->where("phone_number = '" . $phone . "'")->select();

        if (count($user) > 0) {
            echo fit_api(true, 0, '当前电话号码已注册!', '');
            exit;
        }

        $userModel->phone_number = $phone;
        $userModel->name = $phone;
        $userModel->user_name = $phone;
        $userModel->password = md5($password);
        $userModel->create_at = date('Y-m-d H:i:s', time());

        $result = $userModel->add();

        if (!$result) {
            echo fit_api(true, 0, '注册失败!', $result);
            exit;
        }

        $access_token = md5(uniqid() . time() . rand(10000000, 99999999));

        $loginModel->key = $access_token;
        $loginModel->user_id = $result;
        $loginModel->login_time = date('Y-m-d H:i:s', time());
        $loginModel->expire_time = date("Y-m-d H:i:s", strtotime("8 hour"));
        $loginModel->is_expire = 2;

        $result = $loginModel->add();

        if (!$result) {
            echo fit_api(true, 0, '注册成功, 但登录失败, 请返回登录页面重新登录!', $result);
            exit;
        }

        echo fit_api(true, 200, '注册成功!', $access_token);
    }

    public function setPassword()
    {
        $userModel = D('user');
        $smsModel = M('sms');
        $loginModel = M('login');

        $phone = I('phone');
        $code = I('code');
        $password = I('password');

        if (!$phone || strlen($phone) != 11) {
            echo fit_api(true, 0, '请填写正确手机号码!', strlen($phone));
            exit;
        }

        if (!$code) {
            echo fit_api(true, 0, '请填写验证码!', '');
            exit;
        }

        if (!$password) {
            echo fit_api(true, 0, '请填写登录密码!', '');
            exit;
        }

        $sms = $smsModel->where("phone_number = '" . $phone . "' and msg_type = 0")->order('create_at desc')->limit(1)->select();

        if ($sms[0]['code'] != $code) {
            echo fit_api(true, 0, '验证码不正确!', '');
            exit;
        }

        $user = $userModel->where("phone_number = '" . $phone . "'")->select();

        if (!$user || count($user) == 0) {
            echo fit_api(true, 0, '未找到该号码注册用户!', '');
            exit;
        }

//        $userModel->phone_number = $phone;
//        $userModel->name = $phone;
//        $userModel->user_name = $phone;
//        $userModel->password = md5($password);
//        $userModel->create_at = date('Y-m-d H:i:s', time());

        $condition = array(
            'id' => $user->id
        );

        $data = array(
            'password' => md5($password)
        );

        $result = $userModel->where("phone_number = '" . $phone . "'")->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '修改密码成功!', $data);
            exit;
        }

        echo fit_api(true, 0, '修改密码失败!', $result);
    }

    public function change_pwd()
    {
        $userModel = M('user');
        $loginModel = M('login');

        $access_token = I('user_id');
        $old_password = I('old_password');
        $password = I('password');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        $user = $userModel->find($user_id);

        if (!$user) {
            echo fit_api(true, 102, '用户信息错误,请重新的登录!', $login);
            exit;
        }

        if (md5($old_password) != $user['password']) {
            echo fit_api(true, 103, '密码不正确!', $user_id . '|' . md5($old_password) . ':' . $user['password']);
            exit;
        }

        $data = array(
            'password' => md5($password)
        );

        $user_condition = array(
            'id' => $user['id']
        );

        $result = $userModel->where($user_condition)->data($data)->save();

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '密码修改成功!', $result);
        } else {
            echo fit_api(true, 0, '密码修改失败!', $result);
        }
    }

    public function get_address()
    {
        $loginModel = M('login');
        $addressModel = M('address');

        $access_token = I('user_id');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        $address_condition = array(
            'user_id' => $user_id,
            'is_delete' => 0
        );

        $address = $addressModel->where($address_condition)->select();

        echo fit_api(true, 200, '地址获取成功!', $address);
    }

    public function add_address()
    {
        $loginModel = M('login');
        $addressModel = M('address');

        $access_token = I('user_id');
        $address_id = I('address_id');
        $name = I('name');
        $phone = I('phone');
        $province = I('province');
        $city = I('city');
        $address = I('address');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        $data = array(
            user_id => $user_id,
            name => $name,
            phone => $phone,
            province => $province,
            city => $city,
            address => $address,
            is_default => 0
        );

//        $addressModel->user_id = $user_id;
//        $addressModel->name = $name;
//        $addressModel->phone = $phone;
//        $addressModel->provinde = $province;
//        $addressModel->city = $city;
//        $addressModel->address = $address;

        if (!$address_id) {
//            echo fit_api(true, 0, '登录信息错误,请重新的登录!1', $data); exit;
            $result = $addressModel->add($data);

            if ($result) {
                echo fit_api(true, 200, '保存地址成功!', $data);
                exit;
            } else {
                echo fit_api(true, 101, '保存地址失败!', $data);
                exit;
            }
        } else {
//            echo fit_api(true, 0, '登录信息错误,请重新的登录!2', $address_id); exit;
            $address_condition = array(
                'id' => $address_id
            );

            $result = $addressModel->where($address_condition)->save($data);

            if (false !== $result) {
                echo fit_api(true, 200, '保存地址成功!', $data);
                exit;
            } else {
                echo fit_api(true, 101, '保存地址失败!', $data);
                exit;
            }
        }


//        $address_condition = array(
//            'user_id' => $user_id
//        );
//
//        $address = $addressModel->where($address_condition)->select();

//        echo fit_api(true, 200, '保存地址成功!', $address);
    }

    public function save_address()
    {
        $loginModel = M('login');
        $addressModel = M('address');
        $addressModel1 = D('address');

        $access_token = I('user_id');
        $address_id = I('address_id');
        $name = I('name');
        $phone = I('phone');
        $address = I('address');
        $is_default = I('is_default');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        $addressModel->user_id = $user_id;
        $addressModel->name = $name;
        $addressModel->phone = $phone;
        $addressModel->address = $address;
        $addressModel->is_default = $is_default;

        $data = array(
            'is_default' => 0
        );

        if ($is_default) {
            $result = $addressModel1->where('is_default = 1')->save($data);
        }

        if (!$address_id) {
            $address_id = $addressModel->add();

            if (!$address_id) {
                echo fit_api(true, 101, '保存地址失败!', $login);
                exit;
            }
        } else {
            $address_condition = array(
                'id' => $address_id
            );

            $result = $addressModel->where($address_condition)->save();

            if (false == $result) {
                echo fit_api(true, 101, '保存地址失败!', $login);
                exit;
            }
        }

        echo fit_api(true, 200, '保存地址成功!', $address_id);
    }

    public function del_address()
    {
        $loginModel = M('login');
        $addressModel = M('address');

        $access_token = I('user_id');
        $address_id = I('address_id');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        $address_condition = array(
            'id' => $address_id
        );

        $data = array(
            'is_delete' => 1
        );

        $result = $addressModel->where($address_condition)->save($data);

        if (!$result) {
            echo fit_api(true, 0, '删除地址失败!', $login);
            exit;
        }

        $address_condition = array(
            'user_id' => $user_id,
            'is_delete' => 0
        );

        $address = $addressModel->where($address_condition)->select();

        echo fit_api(true, 200, '删除地址成功!', $address);
    }

    public function get_userinfo()
    {
        $loginModel = M('login');
        $userModel = M('user');

        $access_token = I('user_id');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        $user_condition = array(
            'id' => $user_id
        );

        $user_data = $userModel->where($user_condition)->select();

        echo fit_api(true, 200, '用户获取成功!', $user_data);
    }

    public function save_userinfo()
    {
        $loginModel = M('login');
        $userModel = M('user');

        $access_token = I('user_id');
        $nick_name = I('nick_name');
        $img = I('img');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        $userModel->nick_name = $nick_name;
        $userModel->head_pic = $img;

        $condition = array(
            'id' => $user_id
        );

        $result = $userModel->where($condition)->save();

        echo fit_api(true, 200, '保存成功!', $result);
    }

    public function set_default()
    {
        $loginModel = M('login');
        $addressModel = M('address');

        $access_token = I('user_id');
        $address_id = I('address_id');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        $address_condition = array(
            'id' => $address_id
        );

        $data = array(
            'is_default' => 0
        );

        $result = $addressModel->where('1=1')->save($data);

        $data = array(
            'is_default' => 1
        );

        $result = $addressModel->where($address_condition)->save($data);

        if (!$result) {
            echo fit_api(true, 0, '设置默认地址失败!', $login);
            exit;
        }

        $address_condition = array(
            'user_id' => $user_id
        );

        $address = $addressModel->where($address_condition)->select();

        echo fit_api(true, 200, '设置默认地址成功!', $address);
    }

    public function add_order()
    {
        $orderModel = M('order');
        $orderDetailModel = M('order_detail');
        $loginModel = M('login');
        $addressModel = M('address');
        $db = M();

        $access_token = I('user_id');
        $item_id = I('item_id');
        $num = I('num');
        $color = I('color');
        $price = I('price');

//        echo fit_api(true, 101, '当前商品已下架!', '');
//        exit;

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $sql_text = 'select sum(fit_order_detail.num) as num from fit_order_detail left join fit_order on fit_order_detail.order_id = fit_order.id where item_id = ' . $item_id . ' and (fit_order.state > 10 and fit_order.state < 80)';//

        $sales = $db->query($sql_text);

        $sale_count = $sales[0]['num'];

        switch ($item_id) {
            case '45': {
                if ((int)$sale_count >= 18) {
                    echo fit_api(true, 101, '已没有库存!', '');
                    exit;
                }

                break;
            }
            case '46': {
                if ((int)$sale_count >= 2) {
                    echo fit_api(true, 101, '已没有库存!', '');
                    exit;
                }

                break;
            }
            case '47': {
                if ((int)$sale_count >= 4) {
                    echo fit_api(true, 101, '已没有库存!', '');
                    exit;
                }

                break;
            }
            case '48': {
                if ((int)$sale_count >= 10) {
                    echo fit_api(true, 101, '已没有库存!', '');
                    exit;
                }

                break;
            }
            case '50': {
                if ((int)$sale_count >= 4) {
                    echo fit_api(true, 101, '已没有库存!', '');
                    exit;
                }

                break;
            }
            default: {
                echo fit_api(true, 101, '不是销售商品!', '');
                exit;
            }
        }

        $user_id = $login[0]['user_id'];

//        $address_condition = array(
//            'user_id' => $user_id,
//            'is_default' => 1
//        );
//
//        $address = $addressModel->where($address_condition)->select();
//
//        if (!$address || count($address) < 1) {
//            echo fit_api(true, 0, '请先设置默认地址!', $login); exit;
//        }

//        $order_id = md5(uniqid().time().rand(10000000,99999999));
        $order_id = date("YmdHis") . rand(10000000, 99999999);

        $orderModel->id = $order_id;
        $orderModel->user_id = $user_id;
        $orderModel->address_id = '';//$address[0]['id'];
        $orderModel->create_at = date('Y-m-d H:i:s', time());
        $orderModel->amount = ((float)$price) * ((float)$num);
        $orderModel->pay_amount = ((float)$price) * ((float)$num);
        $orderModel->state = 10;

        $result = $orderModel->add();

        if (!$result) {
            echo fit_api(true, 0, '创建订单失败!', $result);
            exit;
        }

        $orderDetailModel->id = md5(uniqid() . time() . rand(10000000, 99999999));
        $orderDetailModel->order_id = $order_id;
        $orderDetailModel->item_id = $item_id;
        $orderDetailModel->color = $color;
        $orderDetailModel->price = $price;
        $orderDetailModel->num = $num;
        $orderDetailModel->amount = ((float)$price) * ((float)$num);

        $result = $orderDetailModel->add();

        if (!$result) {
            echo fit_api(true, 0, '创建订单失败!', $result);
        } else {
            echo fit_api(true, 200, '创建订单成功!', $order_id);
        }
    }

    public function getOrderInfo()
    {
        $orderModel = D('order');
        $orderDetailModel = M('order_detail');
        $db = M();

        $order_id = I('trade_no');

        $order = $orderModel->find($order_id);

        $details = $orderDetailModel->where("order_id='" . $order_id . "'")->select();

        foreach ($details as $d) {
            $sql_text = 'select sum(fit_order_detail.num) as num from fit_order_detail left join fit_order on fit_order_detail.order_id = fit_order.id where item_id = ' . $d['item_id'] . " and ((fit_order.state > 10 and fit_order.state < 80) or (fit_order.state = 10 and fit_order.create_at > '" . date("Y-m-d H:i:s", strtotime("-10 minute")) . "' and user_id != " . $order['user_id'] . " and fit_order.id not in (select tmp.id from fit_order as tmp where tmp.create_at < '" . $order['create_at'] . "')))";

//            echo fit_api(true, 101, '没有足够库存!', $sql_text); exit;

            $sales = $db->query($sql_text);

            $sale_count = (int)$sales[0]['num'] + (int)$d['num'];

            $item_id = $d['item_id'];

            switch ($item_id) {
                case '45': {
                    if ((int)$sale_count > 18) {
                        $order['sale_out'] = 1;
                    }

                    break;
                }
                case '46': {
                    if ((int)$sale_count > 2) {
                        $order['sale_out'] = 1;
                    }

                    break;
                }
                case '47': {
                    if ((int)$sale_count > 4) {
                        $order['sale_out'] = 1;
                    }

                    break;
                }
                case '48': {
                    if ((int)$sale_count > 10) {
                        $order['sale_out'] = 1;
                    }

                    break;
                }
                case '50': {
                    if ((int)$sale_count > 4) {
                        $order['sale_out'] = 1;
                    }

                    break;
                }
                default: {
                    echo fit_api(true, 101, '不是销售商品!', '');
                    exit;
                }
            }
        }

        echo fit_api(true, 200, '获取订单成功!', $order);
    }

    public function get_order()
    {
        $orderModel = M('order');
        $orderDetailModel = M('order_detail');
        $loginModel = M('login');
        $addressModel = M('address');
        $itemModel = M('item');
        $photoModel = D('photo');

        $db = M();

        $access_token = I('user_id');
        $state = I('state');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        if ($state == '0') {
            $order_condition = array(
                'user_id' => $user_id
            );
        } else {
            $order_condition = array(
                'user_id' => $user_id,
                'state' => $state
            );
        }

        $orders = $orderModel->where($order_condition)->order('create_at desc')->select();

        $list = array();

        foreach ($orders as $o) {
            $address_condition = array(
                'id' => $o['address_id']
            );

            $address = $addressModel->where($address_condition)->find();

//            echo fit_api(true, 0, '获取成功!', $addressModel->getLastSql());

            $o['address'] = $address;

            $list[] = $o;
        }

        $list2 = array();

        foreach ($list as $o) {
            $detail_condition = array(
                'order_id' => $o['id']
            );

            $details = $orderDetailModel->where($detail_condition)->select();

            $details_list = array();

            foreach ($details as $d) {
                $product = $itemModel->find($d['item_id']);

                $show_photo = $photoModel->where('id in (' . $product['show_photo'] . ')')->find();

                $product['show_photo'] = $show_photo;

                $d['product'] = $product;

                $details_list[] = $d;
            }

            $o['details'] = $details_list;

            $list2[] = $o;
        }

        echo fit_api(true, 200, '获取成功!', $list2);
    }

    public function add_shopping_cart()
    {
        $shoppingCarModel = M('shopping_car');
        $loginModel = M('login');
        $db = M();

//        echo fit_api(true, 101, '当前商品已下架!', '');
//        exit;

        $access_token = I('user_id');
        $item_id = I('item_id');
        $num = I('num');
        $color = I('color');
        $price = I('price');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $sql_text = 'select sum(fit_order_detail.num) as num from fit_order_detail left join fit_order on fit_order_detail.order_id = fit_order.id where item_id = ' . $item_id . ' and (fit_order.state > 10 and fit_order.state < 80)';//

        $sales = $db->query($sql_text);

        $sale_count = $sales[0]['num'];

        switch ($item_id) {
            case '45': {
                if ((int)$sale_count >= 18) {
                    echo fit_api(true, 101, '已没有库存!', '');
                    exit;
                }

                break;
            }
            case '46': {
                if ((int)$sale_count >= 2) {
                    echo fit_api(true, 101, '已没有库存!', '');
                    exit;
                }

                break;
            }
            case '47': {
                if ((int)$sale_count >= 4) {
                    echo fit_api(true, 101, '已没有库存!', '');
                    exit;
                }

                break;
            }
            case '48': {
                if ((int)$sale_count >= 10) {
                    echo fit_api(true, 101, '已没有库存!', '');
                    exit;
                }

                break;
            }
            case '50': {
                if ((int)$sale_count >= 4) {
                    echo fit_api(true, 101, '已没有库存!', '');
                    exit;
                }

                break;
            }
            default: {
                echo fit_api(true, 101, '不是销售商品!', '');
                exit;
            }
        }

        $user_id = $login[0]['user_id'];

        $shoppingCarModel->id = md5(uniqid() . time() . rand(10000000, 99999999));
        $shoppingCarModel->user_id = $user_id;
        $shoppingCarModel->item_id = $item_id;
        $shoppingCarModel->color = $color;
        $shoppingCarModel->price = $price;
        $shoppingCarModel->num = $num;
        $shoppingCarModel->create_at = date('Y-m-d H:i:s', time());
        $shoppingCarModel->amount = ((float)$price) * ((float)$num);

        $result = $shoppingCarModel->add();

        if (!$result) {
            echo fit_api(true, 0, '添加失败!', $result);
        } else {
            echo fit_api(true, 200, '添加成功!' . $sale_count, $result);
        }
    }

    public function get_shopping_car()
    {
        $shoppingCarModel = M('shopping_car');
        $loginModel = M('login');
        $itemModel = M('item');
        $photoModel = D('photo');

        $access_token = I('user_id');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        $car_condition = array(
            'user_id' => $user_id
        );

        $cars = $shoppingCarModel->where($car_condition)->order('create_at desc')->select();
//        echo fit_api(true, 101, '登录信息错误,请重新的登录!', $cars); exit;
        $list = array();

        foreach ($cars as $c) {
            $detail_condition = array(
                'id' => $c['id']
            );

            $product = $itemModel->find($c['item_id']);

            $show_photo = $photoModel->where('id in (' . $product['show_photo'] . ')')->find();
//
            $product['show_photo'] = $show_photo;

            $c['product'] = $product;

            $list[] = $c;
        }

        echo fit_api(true, 200, '获取成功!', $list);
    }

    public function del_shopping_car()
    {
        $shoppingCarModel = M('shopping_car');

        $car_id = I('car_id');

        $shoppingCarModel->delete($car_id);

        echo fit_api(true, 200, '获取成功!', '');
    }

    public function submit_shopping_car()
    {
        $shoppingCarModel = M('shopping_car');
        $loginModel = M('login');
        $orderModel = M('order');
        $addressModel = M('address');
        $orderDetailModel = M('order_detail');

        $access_token = I('user_id');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];

        $car_condition = array(
            'user_id' => $user_id
        );

        $cars = $shoppingCarModel->where($car_condition)->order('create_at desc')->select();

        $address_condition = array(
            'user_id' => $user_id,
            'is_default' => 1
        );

        $address = $addressModel->where($address_condition)->select();

        if (!$address || count($address) < 1) {
            $address = 0;
//            echo fit_api(true, 0, '请先设置默认地址!', $login); exit;
        }

        $order_id = date("YmdHis") . rand(10000000, 99999999);

        $count = 0;

        foreach ($cars as $c) {
            $count = $count + $c['num'];
        }

        $orderModel->id = $order_id;
        $orderModel->user_id = $user_id;
        $orderModel->address_id = $address[0]['id'];
        $orderModel->create_at = date('Y-m-d H:i:s', time());
        $orderModel->amount = ((float)$cars[0]['price']) * ((float)$count);
        $orderModel->pay_amount = ((float)$cars[0]['price']) * ((float)$count);
        $orderModel->state = 10;

        $result = $orderModel->add();

        if (!$result) {
            echo fit_api(true, 0, '创建订单失败!', $result);
            exit;
        }

        foreach ($cars as $c) {
            $orderDetailModel->id = md5(uniqid() . time() . rand(10000000, 99999999));
            $orderDetailModel->order_id = $order_id;
            $orderDetailModel->item_id = $c['item_id'];
            $orderDetailModel->color = $c['color'];
            $orderDetailModel->price = $c['price'];
            $orderDetailModel->num = $count;
            $orderDetailModel->amount = ((float)$c['price']) * ((float)$count);

            $result = $orderDetailModel->add();
        }

        if (!$result) {
            echo fit_api(true, 0, '创建订单失败!', $result);
        } else {
            $car_condition = array(
                'user_id' => $user_id
            );

            $shoppingCarModel->where($car_condition)->delete();

            echo fit_api(true, 200, '创建订单成功!', $order_id);
        }
    }

    public function close_order()
    {
        $orderModel = D('order');
        $loginModel = D('login');

        $user_id = I('user_id');
        $order_id = I('order_id');

        if (!$user_id) {
            echo fit_api(true, 0, '请重新登录!', '');
            exit;
        }

        if (!$order_id) {
            echo fit_api(true, 0, '请刷新页面后 重新选择!', '');
            exit;
        }

        $order = $orderModel->where("id='" . $order_id . "'")->find();

        if (!$order) {
            echo fit_api(true, 0, '订单不存在!', '');
            exit;
        }

        $login_condition = array(
            'key' => $user_id
        );

        $login = $loginModel->where($login_condition)->find();
//        echo fit_api(true, 0, '请重新登录!', $login); exit;
        if (!$login) {
            echo fit_api(true, 0, '请重新登录!', $login);
            exit;
        }

        if ($order['user_id'] != $login['user_id']) {
            echo fit_api(true, 0, '登录账号错误 请重新登录!', $order['user_id'] . ':' . $user_id);
            exit;
        }

        $data = array(
            'state' => 80
        );

        $result = $orderModel->where("id='" . $order_id . "'")->save($data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '关闭成功!', '');
        } else {
            echo fit_api(true, 0, '关闭失败!', '');
        }
    }

    public function change_car_num()
    {
        $shoppingCarModel = M('shopping_car');

        $car_id = I('car_id');
        $num = I('num');

        $car = $shoppingCarModel->find($car_id);

        $car_condition = array(
            'id' => $car_id
        );

        $data = array(
            'num' => $num,
            'amount' => (float)$car['price'] * (float)$num
        );

        $shoppingCarModel->where($car_condition)->save($data);

        echo fit_api(true, 200, '修改成功!', '');
    }

    public function pay()
    {
        $orderModel = M('order');
        $orderDetailModel = M('order_detail');
        $addressModel = M('address');
        $db = M();

        $order_id = I('order_id');
        $address_id = I('address_id');
        $pay_amount = I('pay_amount');
        $freight = I('freight');

        $order_condition = array(
            'id' => $order_id
        );

        $order = $orderModel->where($order_condition)->find();

//        echo fit_api(true, 101, '当前商品已下架!', '');
//        exit;

        $details = $orderDetailModel->where("order_id='" . $order_id . "'")->select();

        foreach ($details as $d) {
            $sql_text = 'select sum(fit_order_detail.num) as num from fit_order_detail left join fit_order on fit_order_detail.order_id = fit_order.id where item_id = ' . $d['item_id'] . ' and (fit_order.state > 10 and fit_order.state < 80)';

            $sales = $db->query($sql_text);

            $sale_count = (int)$sales[0]['num'] + (int)$d['num'];

            $item_id = $d['item_id'];

            switch ($item_id) {
                case '45': {
                    if ((int)$sale_count > 18) {
                        echo fit_api(true, 101, '没有足够库存!', '');
                        exit;
                    }

                    break;
                }
                case '46': {
                    if ((int)$sale_count > 2) {
                        echo fit_api(true, 101, '没有足够库存!', '');
                        exit;
                    }

                    break;
                }
                case '47': {
                    if ((int)$sale_count > 4) {
                        echo fit_api(true, 101, '没有足够库存!', '');
                        exit;
                    }

                    break;
                }
                case '48': {
                    if ((int)$sale_count > 10) {
                        echo fit_api(true, 101, '没有足够库存!', '');
                        exit;
                    }

                    break;
                }
                case '50': {
                    if ((int)$sale_count > 4) {
                        echo fit_api(true, 101, '没有足够库存!', '');
                        exit;
                    }

                    break;
                }
                default: {
                    echo fit_api(true, 101, '不是销售商品!', '');
                    exit;
                }
            }
        }

        $address = $addressModel->find($address_id);

        $data = array(
            'address_id' => $address_id,
            'vip_amount' => $pay_amount,
            'freight' => $freight,
            'name' => $address['name'],
            'phone' => $address['phone'],
            'address' => $address['province'] . ' ' . $address['city'] . ' ' . $address['address']
        );
//        echo fit_api(true, 101, '当前还未开放购买!', $data); exit;
        $orderModel->where($order_condition)->save($data);

        echo fit_api(true, 200, '支付成功!', '');
    }

    public function take_over()
    {
        $orderModel = M('order');
        $loginModel = M('login');

        $order_id = I('order_id');
        $access_token = I('user_id');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $order_condition = array(
            'id' => $order_id
        );

        $order_data = array(
            'state' => 40
        );

        $result = $orderModel->where($order_condition)->save($order_data);

        if (false !== $result || 0 !== $result) {
            echo fit_api(true, 200, '收货成功!', $order_data);
        } else {
            echo fit_api(true, 0, '收货失败!', $order_data);
        }
    }

    public function comment()
    {
        $commentModel = M('comment');
        $orderModel = M('order');
        $loginModel = M('login');

        $order_id = I('order_id');
        $order_score = I('product_score');
        $logistics_score = I('logistics_score');
        $comment_text = I('comment_text');

        $access_token = I('user_id');

        $login_condition = array(
            'key' => $access_token
        );

        $login = $loginModel->where($login_condition)->select();

        if (!$login || count($login) < 1) {
            echo fit_api(true, 101, '登录信息错误,请重新的登录!', $login);
            exit;
        }

        $user_id = $login[0]['user_id'];
        $score = ($order_score + $logistics_score) / 2;

        $data = array(
            'id' => date("YmdHis") . rand(10000000, 99999999),
            'order_id' => $order_id,
            'user_id' => $user_id,
            'comment' => $comment_text,
            'order_score' => $order_score,
            'logistics_score' => $logistics_score,
            'score' => $score
        );
//        echo fit_api(true, 101, '登录信息错误,请重新的登录!', $data); exit;
        $comment_id = $commentModel->add($data);

        if ($comment_id) {
            $order_condition = array(
                'id' => $order_id
            );

            $order_data = array(
                'state' => 50
            );

            $result = $orderModel->where($order_condition)->save($order_data);

            if (false !== $result || 0 !== $result) {
                echo fit_api(true, 200, '评论成功!', $data);
                exit;
            }

            echo fit_api(true, 0, '评论失败!', '');
            exit;
        } else {
            echo fit_api(true, 0, '评论失败!', '');
            exit;
        }
    }

    public function company_query()
    {
        $db = M();

        $req_owner = I('req_owner');
        $req_owner_card_id = I('req_owner_card_id');

        $str_sql = "select fit_traders.*, (select store_name from fit_store where company_id = fit_traders.id limit 1) as store_name, (select is_show from fit_store where company_id = fit_traders.id limit 1) as state from fit_traders where owner = '" . $req_owner . "' and owner_card_id = '" . $req_owner_card_id . "'";
//        echo fit_api(true, 0, '获取失败!', $str_sql);exit;
        $items = $db->query($str_sql);

        if ($items && count($items) > 0) {
            echo fit_api(true, 200, '获取成功!', $items[0]);
            exit;
        }

        echo fit_api(true, 0, '获取失败!', '');
        exit;
    }

    public function company_submit()
    {
        $companyModel = D('traders');
        $storeModel = D('store');
        $pointModel = D('point');

        $company = I('company');
        $stores = I('stores');

        $company_data = array(
            'company_name' => $company['company_name'],
            'owner' => $company['owner'],
            'owner_card_id' => $company['owner_card_id'],
            'province' => $company['province'],
            'city' => $company['city'],
            'region' => $company['region'],
            'registered_capital' => $company['registered_capital'],
            'brand' => $company['brand'],
            'operating_number' => $company['operating_number'],
            'server_number' => $company['server_number'],
            'store_number' => $company['store_number'],
            'join_store_number' => $company['join_store_number'],
            'contact_name' => $company['contact_name'],
            'contact_phone' => $company['contact_phone'],
            'fax' => $company['fax'],
            'qq' => $company['qq'],
            'wechat' => $company['wechat'],
            'email' => $company['email'],
            'remark' => $company['remark'],
            'business_fund' => $company['business_fund'],
            'operating_region' => $company['operating_region']
        );
        //判断是否有相同公司名称和法人的数据 如果已存在 就提取老的公司数据, 否则就新增公司信息
        $tmp_company = $companyModel->where("company_name='" . $company['company_name'] . "' and owner='" . $company['owner'] . "' and owner_card_id='" . $company['owner_card_id'] . "'")->select();

        if ($tmp_company && count($tmp_company) > 0) {
            $company_item = $tmp_company[0];

            $result = $companyModel->where('id = ' . $company_item['company_id'])->save($company_data);

            if (false !== $result || 0 !== $result) {
                $company_id = $company_item['id'];

                $result = true;
            } else {
                $result = false;
            }
        } else {
            $company_id = $companyModel->add($company_data);

            if ($company_id) {
                $result = true;
            } else {
                $result = false;
            }
        }


//        $company_id = '';
//
//        if ($company['company_id']) {
//            $result = $companyModel->where('id = '. $company['company_id'])->save($company_data);
//
//            if(false !== $result || 0 !== $result){
//                $result = true;
//            }else{
//                $result = false;
//            }
//        }
//        else {
//            $company_id = $companyModel->add($company_data);
//
//            if ($company_id) {
//                $result = true;
//            }
//            else {
//                $result = false;
//            }
//        }

        if (!$result) {
            echo fit_api(true, 0, '公司信息保存失败!', '');
            exit;
        }
//        echo fit_api(true, 0, $company_id, '');exit;
        $result = $storeModel->where(array('company_id' => $company_id))->delete();

        if ($result == false && $result != 0) {
            echo fit_api(true, 0, '初始化服务网点失败!', '');
            exit;
        }

        foreach ($stores as $s) {
            $data = array(
                'join_type' => $s['join_type'],
                'industry_experience' => $s['industry_experience'],
                'store_name' => $s['store_name'],
                'store_area' => $s['store_area'],
                'is_backup_part' => $s['is_backup_part'],
                'is_stand' => $s['is_stand'],
                'stand_number' => $s['stand_number'],
                'province' => $s['province'],
                'city' => $s['city'],
                'region' => $s['region'],
                'operating_time_start' => $s['operating_time_start'],
                'operating_time_end' => $s['operating_time_end'],
                'address' => $s['address'],
                'operating_region' => $s['operating_region'],
                'gps' => $s['gps'],
                'business_fund' => $s['business_fund'],

                'photo_door' => $this->save_img($s['photo_door']),
                'photo_indoor' => $this->save_img($s['photo_indoor']),
                'photo_overall_view' => $this->save_img($s['photo_overall_view']),
                'photo_license' => $this->save_img($s['photo_license']),

                'contact_name' => $s['contact_name'],
                'contact_phone' => $s['contact_phone'],
                'company_id' => $company_id,
                'create_at' => date('Y-m-d H:i:s', time())
            );

            $reuslt = $storeModel->add($data);

            if (!$reuslt) {
                echo fit_api(true, 0, '保存服务网点失败!', '');
                exit;
            }

            $point_data = array(
                'join_type' => $s['join_type'],
                'point_text' => $s['store_name'],
                'long' => explode(',', $s['gps'])[0],
                'lat' => explode(',', $s['gps'])[1],
                'province' => $s['province'],
                'city' => $s['city'],
                'region' => $s['region'],
                'address' => $s['address'],
                'name' => $s['contact_name'],
                'phone' => $s['contact_phone'],
                'store_id' => $reuslt
            );

            $pointModel->add($point_data);
        }

        echo fit_api(true, 200, '您的资料我们已收到，客服人员将尽快与您取得联系!', $stores);
    }

    public function get_cartinfo()
    {
        $cartModel = D('car');

        $type = I('type');
        $val = I('val');

        if ($type == '1') {
            $car = $cartModel->where("car_sn='" . $val . "'")->find();
        } else if ($type = '2') {
            $car = $cartModel->where("car_frame_no='" . $val . "'")->find();
        } else {
            $car = $cartModel->where("car_engine_no='" . $val . "'")->find();
        }

        if ($car) {
            echo fit_api(true, 200, '查询成功!', $car);
        } else {
            echo fit_api(true, 0, '没有找到符合条件的机车!', '');
        }
    }

    public function repair_log()
    {
        $repair_logModel = D('repair_log');

        $log = I('log');

        $data = array(
            'id' => date("YmdHis") . rand(10000000, 99999999),
            'car_id' => $log['car_id'],
            'point_name' => $log['point_name'],
            'repair' => $log['repair'],
            'phone' => $log['phone'],
            'create_at' => date('Y-m-d H:i:s', time()),
        );

        $result = $repair_logModel->add($data);
        if ($result) {
            echo fit_api(true, 200, '备案成功!', '');
        } else {
            echo fit_api(true, 0, '备案成功, 请刷新后重试!', '');
        }
    }

    public function lose_log()
    {
        $lose_logModel = D('lose_log');

        $log = I('log');

        $data = array(
            'id' => date("YmdHis") . rand(10000000, 99999999),
            'car_id' => $log['car_id'],
            'repair_name' => $log['repair_name'],
            'repair_phone' => $log['repair_phone'],
            'point_name' => $log['point_name'],
            'opt_name' => $log['opt_name'],
            'opt_phone' => $log['opt_phone'],
            'create_at' => date('Y-m-d H:i:s', time()),
        );

        $result = $lose_logModel->add($data);

        if ($result) {
            echo fit_api(true, 200, '备案成功!', '');
        } else {
            echo fit_api(true, 0, '备案成功, 请刷新后重试!', '');
        }
    }

    public function save_img($image)
    {
        //如果上传的文件没有图片base64加密前缀 就是原始图片路径 直接返回
        if (strpos($image, "data:image/jpeg;") < 0) {
            return $image;
        }

        $s = base64_decode(str_replace('data:image/jpeg;base64,', '', $image));

        $pic_name = date('YmdHis') . rand(1000000, 9999999) . '.jpg';

        $full_pic_name = SITE_DIR . UPLOAD_DIR . $pic_name;
//        echo fit_api(true, 0, '保存服务网点失败!'.$full_pic_name, '');exit;
        $file_count = file_put_contents($full_pic_name, $s);

        if (!$file_count) {
            return false;
        } else {
            return $pic_name;
        }
    }

    public function refund_order()
    {
        $orderModel = M('order');

        $user_id = I('user_id');
        $order_id = I('order_id');
        $alipay_id = I('alipay_id');

        $order = $orderModel->find($order_id);

        if ($order) {
            if ($order['state'] != '20') {
                echo fit_api(true, 0, '订单状态错误!', '');
                exit;
            }

            $data = array(
                state => 100,
                alipay_id => $alipay_id
            );

            $result = $orderModel->where("id='" . $order_id . "'")->save($data);

            if (false !== $result) {
                echo fit_api(true, 200, '申请成功!', '');
            } else {
                echo fit_api(true, 0, '申请失败!', '');
            }
        } else {
            echo fit_api(true, 0, '未找到对应订单!', '');
        }
    }
}