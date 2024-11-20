<?php

namespace app\common\service\channels;

class UsdtPayChannel implements ChannelInterface
{
    /**
     * config 配置
     */
    public function config()
    {
        return [
            [
                'name'=>'usdt地址',
                'key'=>'usdtAddress',
                'value'=>'',
                'type' => 'text',
            ]
        ];
    }

    /**
     * pay 支付
     */
    public function pay($channel, $params) : array
    {
//        $userId = $this->getExtraConfig($channel, 'userId');
        return  [
            'status' => 1,
            'pay_url' => $channel->usdt_address,
            'image' => $channel->image,
        ];
    }

    public function payNotify($channel, $params) : array
    {
        return [
            'status' => 1,
            'msg' => 'success',
        ];
    }

    public function response() : string
    {
        return 'success';
    }
}