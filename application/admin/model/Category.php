<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/23
 * Time: 15:54
 */

namespace app\admin\model;


use app\common\model\Base;

class Category extends Base
{
    protected $name = 'category';
    protected $resultSetType = 'collection';

    public function setUidAttr()
    {
        return session('user_auth.uid');
    }
}