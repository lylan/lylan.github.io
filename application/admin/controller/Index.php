<?php
namespace app\admin\controller;

use app\admin\model\User;

class Index extends Admin
{

    public function index()
    {
        return view();
    }

    public function login($username='', $password='', $verify='')
    {
        if (IS_POST) {
            if (!$username || !$password) {
                return $this->error('用户名或者密码不能为空！', '');
            }

            //验证码验证
            if ($verify) {
                $this->checkCaptcha($verify);
            }

            $user = new User();
            $uid  = $user->login($username, $password);
            if ($uid) {
                return $this->success('登录成功！', url('admin/index/index'));
            } else {
                return $this->error($user->getError(), '');
            }
        }else {
            return view();
        }
    }

    public function logout() {
        $user = new User();
        $user->logout();
        $this->redirect('admin/index/login');
    }


}