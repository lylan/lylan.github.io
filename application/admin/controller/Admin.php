<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 15:26
 */

namespace app\admin\controller;

use app\common\controller\Base;
use think\Config;
use think\Db;

class Admin extends Base
{

    public function _initialize()
    {
        parent::_initialize();


        if (!is_login() && !in_array($this->url, array('admin/index/login', 'admin/index/logout', 'admin/index/verify'))) {
            $this->redirect('/admin/index/login');
        }

        if (!in_array($this->url, array('admin/index/login', 'admin/index/logout', 'admin/index/verify'))) {

            define('IS_ROOT', is_administrator());

            //检查权限
            if (!IS_ROOT) {
                $access = $this->accessControl();
                if (false === $access) {
                    $this->error('403:禁止访问');
                } elseif (null === $access) {
                    //检测访问权限
                    if (!$this->checkRule($this->url, array('in', '1,2'))) {
                        $this->error('未授权访问!');
                    }
                }

            }
            $this->setMenu();
        }


    }


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *  返回 **false**, 不允许任何人访问(超管除外)
     *  返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *  返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     */
    final function accessControl()
    {
        $allow = Config::get('ALLOW_VISIT');
        $deny  = Config::get('DENY_VISIT');
        $check = strtolower($this->request->controller() . '/' . $this->request->action());
        if (!empty($deny) && in_array_case($check, $deny)) {
            return false; //非超管禁止访问deny中的方法
        }
        if (!empty($allow) && in_array_case($check, $allow)) {
            return true;
        }
        return null; //需要检测节点权限
    }

    /**
     * 权限检测
     * @param $rule
     * @param int $type
     * @param string $mode
     * @return bool
     */
    final function checkRule($rule, $type = 1, $mode = 'url')
    {
        static $Auth = null;
        if (!$Auth) {
            $Auth = new \Auth();
        }
        if (!$Auth->check($rule, session('user_auth.uid'), $type, $mode)) {
            return false;
        }
        return true;
    }

    protected function setMenu()
    {
        $controller = $this->url;
        $menu = array();

        $order = 'pid asc,sort desc';
        $where['type'] = 'admin';
        $where['hide'] = 0;
        if (!\config('DEVELOP_MODE')) {
            //是否开启开发者模式
            $where['is_dev'] = 0;
        }

        $row = Db::name('menu')->field('id, title, url, icon, group, pid, sort, "" as style')->where($where)->order($order)->select();

        foreach ($row as $value) {
            //此处用来做权限判断
            if (!IS_ROOT && !$this->checkRule($value['url'], 2, null)) {
                unset($value);
                continue; //继续循环
            }
            if ($controller == $value['url']) {
                $value['style'] = "active";
                if ($value['pid'] != 0) {
                    $menu[$value['pid']]['style'] = 'active';
                }
            }
            $menu[$value['id']] = $value;
        }

        $list = list_to_tree($menu);

        $this->assign('__menu__', $list);

    }


    /**
     * 设置页面标题
     * @param $title
     */
    protected function setMeta($title = '')
    {
        $this->assign('meta_title', $title);
    }


    /**
     * 删除
     * @param string $id  主键值
     * @param string $mod  数据模型类
     * @param bool $del_pk 是否级联删除 父数据
     * @param string $pk 父类的 字段名
     *
     */
    public function del($id = '', $mod = '', $del_pk = false, $pk = 'pid')
    {

        if (empty($mod) || empty($id)) {
            $this->error('删除失败！');
        }
        $model = model($mod);
        // 获取主键名
        $key = $model->getPk();
        $id = $this->getArrayParam('id');
        $map[$key] = array('IN', $id);
        $result = $model->where($map)->delete();

        if ($del_pk) {
            $where[$pk] = array('IN', $id);
            $model->where($where)->delete();
        }
        if ($result) {
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }

    }

}