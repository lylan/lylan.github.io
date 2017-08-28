<?php
namespace app\admin\model;
use app\common\model\Base;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 17:53
 */
class User extends Base
{

    protected $name = 'user'; //不包含前缀的数据库表名
    protected $autoWriteTimestamp = 'int'; //默认是int型，也可以修改为datetime 类型
    protected $createTime = 'create_time';  //默认的是 'create_at'
    protected $updateTime = 'update_time';  //默认的是  'update_at'， 如果某个字段不需要自动写入,则可以设置为 false, eg：$updateTime = false;
    protected $resultSetType = 'collection';


    /**
     * 设置密码
     * @param $value
     * @return bool|false|string
     */
    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    public function setLoginIpAttr()
    {
        return request()->ip();
    }


    /**
     * 定义表中不存在的字段
     * 可以直接通过 $user->status_text 获取到值
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = ['0' => '禁用', '1' => '启用'];
        return $status[$data['status']];
    }

    public function getGroupName($value, $data)
    {
        $g = AuthGroup::get($data['id'])->toArray();
        $groupId = $g->group_id;
        $group = Group::get($groupId)->toArray();

        return $group->title;
    }

    /**
     * 检查密码
     * @param $pwd
     * @param $hash
     * @return bool
     */
    public function checkPwd($pwd, $hash)
    {
        if ($pwd) {
            $result = password_verify($pwd, $hash);
            if ($result) {
                return true;
            } else {
                $this->error = '密码错误';
                return false;
            }
        } else {
            $this->error = '密码不能为空！';
            return false;
        }
    }

    /**
     * 用户登录
     * @param $username
     * @param $password
     * @param int $type
     * @return bool
     */
    public function login($username, $password, $type = 1)
    {
        $username = trim($username);
        $password = trim($password);
        if (empty($username) || empty($password)) {
            $this->error = '用户名或密码不能为空';
            return false;
        }

        $map = array();
        switch ($type) {
            case 1:
                $map['username'] = $username;
                break;
            case 2:
                $map['email'] = $username;
                break;
            case 3:
                $map['phone'] = $username;
                break;
            case 4:
                $map['id'] = intval($username);
                break;
            default:
                return 0;
        }

        $user = $this->where($map)->find();


        if (isset($user['status']) && $user['status']) {

            $result = $this->checkPwd($password, $user['password']);
            if ($result) {
                $this->autoLogin($user);
                return true;
            } else {
                return false;
            }

        } else {
            $this->error = '该用户不存在或被禁用';
            return false;
        }
    }


    /**
     * 自动登录
     * @param $user
     */
    public function autoLogin($user)
    {
        $data = array(
            'id' => $user['id'],
            'login_time' => time(),
        );

        $this->save($data, array('id' => $user['id']));

        $auth = array(
            'uid' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'nickname' => $user['nickname']
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
    }

    /**
     * 退出登录
     */
    public function logOut()
    {
        session('user_auth', null);
        session('user_auth_sign', null);
    }

}