<?php
namespace app\common\controller;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 17:08
 */
class Base extends \think\Controller
{

    protected $param;
    protected $url;
    protected $request;
    protected $module;
    protected $controller;
    protected $action;


    public function _initialize()
    {

        $this->requestInfo();
    }

    /**
     * request 信息
     */
    protected function requestInfo()
    {
        $this->param = $this->request->param();
        defined('MODULE_NAME') or define('MODULE_NAME', $this->request->module());
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $this->request->controller());
        defined('ACTION_NAME') or define('ACTION_NAME', $this->request->action());
        defined('IS_POST') or define('IS_POST', $this->request->isPost());
        defined('IS_GET') or define('IS_GET', $this->request->isGet());
        $this->url = strtolower($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
        $this->assign('request', $this->request);
        $this->assign('param', $this->param);
    }


    /**
     * @param $code
     * @param int $id
     * @param bool $reset  是否需要重置
     * @return bool
     */
    public function checkCaptcha($code, $id = 1, $reset = true)
    {
        if ($code) {
            $result = captcha_check($code, $id, array('reset' => $reset));
            if (!$result) {
                return $this->error("验证码错误！", "");
            }
        } else {
            return $this->error('验证码为空', "");
        }

    }

    /**
     * 获取单个参数的数组形式
     */
    protected function getArrayParam($param) {
        if (isset($this->param['id'])) {
            return array_unique((array) $this->param[$param]);
        } else {
            return array();
        }
    }

}