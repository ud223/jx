<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/8
 * Time: 15:12
 */

namespace Org\Fit;

class UserOrder {
    public function OrderPaySucceed($out_trade_no, $transaction_id) {
        \Log::DEBUG('进入notify同步数据操作!');
        $orderModel = D('order');
        $userModel = D('user');

        $order_id = $out_trade_no;

        $order_condition = array(
            'order_id' => $order_id
        );

        $data = array(
            'state' => 30
        );
        $order = $orderModel->where($order_condition)->select();
//        print_r($order_condition); print_r($data); exit;

        $user_id = $order[0]['user_id'];
        $amount = $order[0]['amount'];
        $vip_amount = $order[0]['vip_amount'];

        $user_condition = array(
            'id' => $user_id
        );

        if ($order[0]['type'] == '1') {
            $result = $orderModel->where($order_condition)->save($data);

            if (false !== $result) {
                $result = $userModel->where($user_condition)->setDec('amount', $vip_amount);

                if (false !== $result) {
                    $return_text = "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";

                    return $return_text;
                }
                else {
                    $return_text = "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[预约失败: 用户金额数据失败]]></return_msg></xml>";

                    return $return_text;
                }
            }
            else {
                $return_text = "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[预约失败: 订单更新数据失败]]></return_msg></xml>";

                return $return_text;
            }
        }
        else {
            $amount = $order[0]['amount'];

            $result = $userModel->where($user_condition)->setInc('amount', $amount);

            if (false !== $result) {
                $return_text = "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";

                return $return_text;
            }
            else {
                $return_text = "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[充值失败: 更新数据失败]]></return_msg></xml>";

                return $return_text;
            }
        }
    }
} 