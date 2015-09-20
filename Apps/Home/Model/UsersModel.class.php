<?php
namespace Home\Model;
use Think\Controller;

class UsersController extends Controller{
	protected $_validate = array(
		array('name','require','用户名不能为空'),
		array('password','password2','确认密码不正确！',0,'confirm'),
		array('password','6,20','密码长度不正确！',0,'length'),
		array('email','email','邮箱格式不正确！'),
		
	
	
	);	
	
}
