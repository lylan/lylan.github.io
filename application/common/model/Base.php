<?php
namespace app\common\model;
use think\Model;
use think\Request;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 17:55
 */
class Base extends Model
{
    protected $type = array(
      'id' => 'integer'
    );

    /**
     * 修改/添加数据
     * @return false|int
     */
    public function change()
    {
        $data = Request::instance()->post();
        if (isset($data['id']) && $data['id']) {
            return $this->save($data, array('id' => $data['id']));
        } else {
            return $this->save($data);
        }
    }
}