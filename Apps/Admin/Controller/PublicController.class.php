<?php
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller{
	//初始化
	public function _initialize(){
		$rule = CONTROLLER_NAME.'/'.ACTION_NAME;
		$auth = new \Think\Auth();
		$uid = $_SESSION['id'];
		if(!$auth -> check($rule,$uid)){
			$this -> error('你没有操作权限',U('Login/index'));
			//echo "你没有权限";
			//exit;
		}
	}
}
