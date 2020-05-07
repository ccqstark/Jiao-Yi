<?php
namespace app\api\validate;
use think\Validate;

class UserInfo extends Validate{

    protected $rule = [
        'username'  => 'require|max:25',
        'email'     => 'email',
        'password'  => 'require|min:6|max:20',
    ];

    protected $message = [
        'username.require' => '用户名不能为空',
        'username.max'     => '用户名不能超过25个字符',
        'email'            => '邮箱格式错误',           
        'password.require' => '密码不能为空',
        'password.min'     => '密码长度不能小于6个字符',
        'password.max'     => '密码长度不能超过25个字符',
    ];
    

}

