<?php

namespace app\common\service;

use app\admin\model\Order;
use app\admin\model\PayConfig;
use app\admin\model\Server;

class OrderService
{
    /**
     * create order
     */
    public function createOrder($data)
    {
        // check server
        $server = Server::get($data['server_id']);
        if (!$server) {
            throw new \Exception('server not found');
        }

        // check amount
        if ($data['amount'] <= 0) {
            throw new \Exception('amount error');
        }

        // check account
        if ($data['account'] != $data['account_confirm']) {
            throw new \Exception('两次输入的账号不一致');
        }
        //  `expire_time` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
        $pay_config = PayConfig::get($data['pay_id']);
        if (!$pay_config || $pay_config->status != 1) {
            throw new \Exception('支付通道未开启');
        }



        $order = new Order();
        // save order
        $order->order_no = $this->getOrderNo();
        $order->server_id = $data['server_id'];
        $order->user_name = $data['amount'];
        $order->contact = $data['contact'];
        $order->pay_id = $data['pay_id'];
        $order->amount = $data['amount'];
        $order->amount_nonce = $this->getAmountNonce($data['amount']);
        $order->actual_amount = $data['amount'] + $order->amount_nonce;
        $order->status = 0;
        $order->pay_time = 0;
        $order->expire_time = time() + 1800; // 30分钟过期
        $rse = $order->save();
        if (!$rse) {
            throw new \Exception('订单创建失败');
        }
        return $order;

//       return $this->requestChannel($order);
    }


    /**
     * 获取支付信息
     */
    public function getPayInfo($order_no)
    {
        $order = Order::get(['order_no' => $order_no, 'status' => 0]);
        if (!$order) {
            throw new \Exception('订单不存在');
        }

        $pay_config = PayConfig::get($order->pay_id);
        if (!$pay_config || $pay_config->status != 1) {
            throw new \Exception('支付通道未开启');
        }

        $paymentService = new PaymentService($pay_config->code);
        $res = $paymentService->pay($pay_config, $order);

        // 三位小数
        $res['amount'] = number_format($order->actual_amount, 3);
        $res['expire_time'] = date('d/m/y H:i', $order->expire_time);
        return $res;
    }

    /**
     * 获取随机数
     */
    public function getAmountNonce($amount)
    {
        // 0.001-0.999
        $order = Order::where('amount', $amount)
            ->where('create_time', '>', strtotime('-30 minute')) // 40分钟内的订单
            ->order('create_time', 'desc')->select();

       // 优先设置为小的随机数
        $min = 0.001;
        foreach ($order as $item) {
            if ($item->amount_nonce == $min) {
                $min = $min + 0.001;
            }
        }

        if ($min >= 1) {
            $min = 0.001;
        }

        return $min;
    }

    /**
     * 获取订单号
     */
    public function getOrderNo()
    {
        $no =  date('YmdHis') . get_random_string(6);
        $order = Order::where('order_no', $no)->find();
        if ($order) {
            return $this->getOrderNo();
        }
        return $no;
    }
}