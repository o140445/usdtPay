<?php

namespace app\admin\validate;

use think\Validate;

class PayConfig extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        "name" => "require",
        "code" => "require",
        "usdt_address" => "require",
    ];
    /**
     * 提示消息
     */
    protected $message = [
        "name.require" => "名称不能为空",
        "code.require" => "编码不能为空",
        "usdt_address.require" => "USDT地址不能为空",
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['name', 'code', 'usdt_address'],
        'edit' => ['name', 'code', 'usdt_address'],
    ];
    
}
