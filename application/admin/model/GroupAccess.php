<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5
 * Time: 14:40
 */

namespace app\admin\model;

use app\common\model\Base;

class GroupAccess extends Base
{
    protected $name = 'auth_group_access'; //不包含前缀的数据库表名
    protected $pk = 'uid';

}