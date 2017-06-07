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
    $group = \app\admin\model\GroupAccess::get($uid);

    return $group && ($group->group_id == 1);

}
