<?php

namespace app\admin\controller\pay;

use app\common\controller\Backend;
use app\common\service\PaymentService;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 支付配置
 *
 * @icon fa fa-circle-o
 */
class Config extends Backend
{

    /**
     * Config模型对象
     * @var \app\admin\model\PayConfig
     */
    protected $model = null;
    protected $modelValidate = true;


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\PayConfig;
        // 获取支付渠道
        $channels = PaymentService::PAY_CHANNEL;
        $channels[0] = "请选择支付渠道";

        $this->view->assign('channels', $channels);
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    public function add()
    {
        if (false === $this->request->isPost()) {

            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }

            // 生成签名
            $params['sign'] = $this->getSign();
            $params['image'] =  $this->getImgUrl($params['usdt_address']);

            $result = $this->model->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
    }

    public function config()
    {
        $code = $this->request->get('code');
        if (empty($code)) {
            $this->success('', null, []);
        }

        $paymentService = new PaymentService($code);
        $config = $paymentService->getConfig();

        $this->success('', null, $config);
    }

    protected  function getSign()
    {

        $string = get_random_string(4);

        // 检查是否已经存在
        $count = $this->model->where('sign', $string)->count();
        if ($count > 0) {
            return $this->getSign();
        }

        return $string;

    }

    protected function getImgUrl($usdt_address)
    {
        // 获取 usdtAddress
        if (isset($usdt_address) && !empty($usdt_address)) {
            $url = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . $usdt_address;
            $img = file_get_contents($url);
            $img = base64_encode($img);
            return "data:image/png;base64," . $img;
        }
        return '';
    }

    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }

            // 如果 usdt_address 发生变化，重新生成二维码
            if ($row->usdt_address != $params['usdt_address']) {
                $params['image'] =  $this->getImgUrl($params['usdt_address']);
            }

            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

}
