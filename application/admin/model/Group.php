<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/6
 * Time: 16:43
 */

namespace app\admin\model;

use app\common\model\Base;

class Group extends Base
{
    protected $name = 'auth_group'; //不包含前缀的数据库表名
    protected $resultSetType = 'collection';

}