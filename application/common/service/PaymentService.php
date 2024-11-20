<?php

namespace app\common\service;

use app\common\service\channels\ChannelInterface;
use app\common\service\channels\UsdtPayChannel;

class PaymentService
{
    protected ChannelInterface $channel;

    const PAY_CHANNEL = [
        'UsdtPay' => 'USDT支付',
    ];

    public function __construct(string $code)
    {
       switch ($code) {
           case 'UsdtPay':
                $this->channel = new UsdtPayChannel();
               break;
           default:
               throw new \Exception('未知支付渠道');
       }
    }

    public function pay($channel, $data)
    {
        return $this->channel->pay($channel, $data);
    }


    public function payNotify($channel, $data)
    {
        return $this->channel->payNotify($channel, $data);
    }

    public function getConfig()
    {
        return $this->channel->config();
    }

    public function response()
    {
        return $this->channel->response();
    }

}
