<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/12
 * Time: 14:31
 */

namespace app\admin\model;


use app\common\model\Base;

class Posts extends Base
{
    protected $name = 'posts';
    protected $resultSetType = 'collection';

    public function setUidAttr()
    {
        return session('user_auth.uid');
    }
}