<?php

namespace app\common\service\channels;

interface ChannelInterface
{
    // config
    public function config();

    // pay
    public function pay($channel, $params) : array;

    // payNotify
    public function payNotify($channel, $params) : array;

    // 返回
    public function response() : string;
}