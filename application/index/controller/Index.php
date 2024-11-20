<?php

namespace app\index\controller;

use app\admin\model\Notice;
use app\admin\model\PayConfig;
use app\admin\model\Server;
use app\common\controller\Frontend;
use app\common\service\OrderService;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = 'default';

    public function _initialize()
    {
        parent::_initialize();

        // 获取公告
        $home_notice = Notice::where('position', 1)->where('status', 1)->order('id', 'desc')->limit(1)->find();
        $amount_notice = Notice::where('position', 2)->where('status', 1)->order('id', 'desc')->limit(1)->find();

        //支付
        $pay_config = PayConfig::where('status', 1)->where('status', 1)->order('id', 'desc')->limit(1)->find();

        $this->assign('home_notice', $home_notice);
        $this->assign('amount_notice', $amount_notice);
        $this->assign('pay_config', $pay_config);
    }

    public function index()
    {

        // 服务器列表
        $server_list = Server::all(['status' => 1]);
        $this->assign('server_list', $server_list);
        $this->assign('title', "支付");
        return $this->view->fetch();
    }

    public function pay()
    {
        // 判断是否post请求
        if (!$this->request->isPost()) {
           $order_id = $this->request->get('order_id');
           if (empty($order_id)) {
               $this->error('参数错误');
           }
            try {
                $orderService = new OrderService();
                $data = $orderService->getPayInfo($order_id);
            }catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            $this->assign('title', "支付");
            $this->assign('data', $data);
            return $this->view->fetch();
        }


        $data = $this->request->post();
        $data = $data['row'];
        // 参数检查
        if (empty($data['server_id']) ||
            empty($data['amount']) ||
            empty($data['account']) ||
            empty($data['account_confirm']) ||
            empty($data['pay_id']))
        {
            $this->error('参数错误');
        }


        try {
            $orderService = new OrderService();
            $order = $orderService->createOrder($data);
        }catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success('success','/index/index/pay?order_id='.$order['order_no']);
    }

}
