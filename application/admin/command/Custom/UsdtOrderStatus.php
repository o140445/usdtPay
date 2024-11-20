<?php

namespace app\admin\command\Custom;

use app\admin\model\Message;
use app\admin\model\Order;
use fast\Http;
use think\console\Command;
use think\Log;

class UsdtOrderStatus extends Command
{
    protected  $chainData = [];

    protected function configure()
    {
        // 指令配置
        $this->setName('usdt_order_status')->setDescription('USDT订单状态检测');
    }

    protected function execute($input, $output)
    {
        $this->output->info('开始检测订单状态');
        // 查询未支付的订单
        $orders = Order::where('status', 0)->with(['payConfig'])->select();

        // 获取链上订单状态
        foreach ($orders as $order) {
            $usdtAddress = $order->payConfig->usdt_address;
            $data = $this->getChainOrderStatus($usdtAddress, $order);
            if ($data) {
                $this->output->info('订单号：' . $order->order_no . ' | 链上订单状态：支付成功');
                continue;
            }

            // 检查是否过期
            if (time() > $order->expire_time) {
                $order->status = 2;
                $order->save();
                Log::log('订单号：' . $order->order_no . ' | 链上订单状态：支付超时');
                $this->output->info('订单号：' . $order->order_no . ' | 链上订单状态：支付超时');
            }
        }


        $this->output->info('检测完成');
    }

    /**
     * 获取链上订单状态
     */
    public function getChainOrderStatus($usdtAddress, $order)
    {
        if (!$this->chainData) {
            $uri = 'https://api.trongrid.io/v1/accounts/' . $usdtAddress . '/transactions/trc20';
            $data = Http::getJson($uri, [
                'limit' => 30,
            ]);
            Log::info($data);
            if (!isset($data['data'])) {
                throw new \Exception('查询异常');
            }

            $this->chainData = $data['data'];
        }

        foreach ($this->chainData as $k => $item) {
            $tokenInfo = $item['token_info'];
            $amount = $item['value'] ?? 0;

            if ( ! $amount ) {
                continue;
            }
            //除以10的 N 次方获得真是USDT金额
            $payAmount = $amount / pow(10, $tokenInfo['decimals']);
            $timestamp = $item['block_timestamp'] / 1000;

            // 对比金额  $order['actual_amount'] 设置为三位小数
            if($payAmount != number_format($order['actual_amount'], 3)){
                continue;
            }

            // 对比时间 30分钟内
            if($timestamp > $order['expire_time'] || $order['expire_time']- $timestamp > 1800){
                continue;
            }

            $order->status = 1;
            $order->pay_time = $timestamp;
            $order->pay_no = $item['transaction_id'];
            $order->save();
            Log::log('订单号：' . $order->order_no . ' | 链上订单状态：支付成功' . ' data: ' . json_encode($item));
            // 加入消息通知
            $this->addMessage($order);

            return true;
        }

        return false;
    }

    /**
     * 添加消息通知
     */
    public function addMessage($order)
    {
        // 添加消息通知
        $message = [
            'title' => '订单支付成功',
            'content' => '您的订单已支付成功',
            'type' => 1,
        ];
        Message::create($message);
    }
}