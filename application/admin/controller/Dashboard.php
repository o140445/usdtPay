<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\User;
use app\common\controller\Backend;
use app\common\model\Attachment;
use fast\Date;
use think\Db;

/**
 * 控制台
 *
 * @icon   fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {


        // 获取订单数据 总订单数，总金额，今日订单数，今日金额
        $totalOrderNum = Db::name('order')->where('status', 'in', '1')->count();
        $totalOrderAmount = Db::name('order')->where('status', 'in', '1')->sum('actual_amount');
        $todayOrderNum = Db::name('order')->where('status', 'in', '1')->whereTime('create_time', 'today')->count();
        $todayOrderAmount = Db::name('order')->where('status', 'in', '1')->whereTime('create_time', 'today')->sum('actual_amount');

        $this->view->assign([
            //$totalOrderNum
            'totalOrderNum' => $totalOrderNum,
            'totalAmount' => $totalOrderAmount,
            'orderNum' => $todayOrderNum,
            'amount' => $todayOrderAmount,

        ]);

        $start_date = date('Y-m-d', strtotime('-13 days'));
        $end_date = date('Y-m-d 23:59:59');

        // 获取每日总订单数和总金额和完成订单数和完成金额
        $orders = Db::name('order')
            ->field('count(id) as total_order_num, sum(actual_amount) as total_amount, count(if(status=1,1,null)) as order_num, sum(if(status=1,actual_amount,null)) as amount, date(FROM_UNIXTIME(create_time)) as date')
            ->where('create_time', 'between', [strtotime($start_date), strtotime($end_date)])
            ->group('date')
            ->select();

        foreach ($orders as $order) {
//            $orderList['total_order_num']['data'][$order['date']] = $order['total_order_num'];
//            $orderList['total_order_num']['name'] = '总订单数';
//            $orderList['total_order_num']['type'] = 0;
//
//
//            $orderList['order_num']['data'][$order['date']] = $order['order_num'];
//            $orderList['order_num']['name'] = '完成订单数';
//            $orderList['order_num']['type'] = 0;



            $orderList['total_amount']['data'][$order['date']] = $order['total_amount'];
            $orderList['total_amount']['name'] = '总金额';
            $orderList['total_amount']['type'] = 1;

            $orderList['amount']['data'][$order['date']] = $order['amount'];
            $orderList['amount']['name'] = '完成金额';
            $orderList['amount']['type'] = 1;
        }


//        $orderList = [];
        $date = [];
        while (strtotime($start_date) <= strtotime($end_date)) {
            $date[] = $start_date;
//            if (!isset($orderList['total_order_num']['data'][$start_date])) {
//                $orderList['total_order_num']['data'][$start_date] = 0;
//                $orderList['total_order_num']['name'] = '总订单数';
//                $orderList['total_order_num']['type'] = 0;
//            }
//
//
//            if (!isset($orderList['order_num']['data'][$start_date])) {
//                $orderList['order_num']['data'][$start_date] = 0;
//                $orderList['order_num']['name'] = '完成订单数';
//                $orderList['order_num']['type'] = 0;
//            }

            if (!isset($orderList['total_amount']['data'][$start_date])) {
                $orderList['total_amount']['data'][$start_date] = 0;
                $orderList['total_amount']['name'] = '总金额';
                $orderList['total_amount']['type'] = 1;
            }

            if (!isset($orderList['amount']['data'][$start_date])) {
                $orderList['amount']['data'][$start_date] = 0;
                $orderList['amount']['name'] = '完成金额';
                $orderList['amount']['type'] = 1;
            }

            $start_date = date('Y-m-d', strtotime($start_date . ' +1 day'));
        }

        foreach ($orderList as $key => $value) {
            ksort($value['data']);
            $orderList[$key]['data'] = array_values($value['data']);
        }

        $this->assignconfig('column', $date);
        $this->assignconfig('orderData', array_values($orderList));

        return $this->view->fetch();
    }

}
