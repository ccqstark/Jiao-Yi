<?php
namespace app\api\validate;
use think\Validate;

class LoginByWhat extends Validate{

    protected $rule = [
        'useridentity' => 'email',
    ];

    protected $message = [
        'useridentity'     => '不是邮箱的格式',
    ];

}
