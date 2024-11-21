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
            'image' => $this->getImgUrl('tron:'.$channel->usdt_address.'?amount='.$params['actual_amount']),
        ];
    }


    protected function getImgUrl($usdt_address)
    {
        var_dump($usdt_address);die();
        // 获取 usdtAddress
        if (isset($usdt_address) && !empty($usdt_address)) {
            $url = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . $usdt_address;
            $img = file_get_contents($url);
            $img = base64_encode($img);
            return "data:image/png;base64," . $img;
        }
        return '';
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