<?php
/**
 * Created by PhpStorm.
 * User: lylan
 * Date: 2017/5/31
 * Time: 16:23
 */

/**
 * 数据签名认证
 * @param $data
 * @return string
 */
function data_auth_sign($data)
{
    if (!is_array($data)) {
        $data = (array) $data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query 字符串
    $sign = sha1($code);

    return $sign;

}

/**
 * 检查是否登录
 * @return bool
 */
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return false;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : false;
    }
}

/**
 * 检查是否是管理员
 * @param null $uid
 * @return bool
 */
function is_administrator($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;
    $group = \app\admin\model\AuthGroup::get($uid);

    return $group && ($group->group_id == 1);

}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0) {
    if (!($uid && is_numeric($uid))) {
        //获取当前登录用户名
        return session('user_auth.username');
    }
    $name = db('user')->where(array('id' => $uid))->value('username');
    return $name;
}
