<?php

namespace app\index\validate;

use think\Validate;

class PhoneValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
                 'phone'=>'require|length:11'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
                'phone.require'=>'手机号没有写哦!',
                'phone.length'=>'手机号长度错误!'
    ];
}