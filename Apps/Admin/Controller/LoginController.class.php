<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller{
	public function index(){
		$this -> display();
	}
	
	public function code(){
        $config = array(
            'imageW' => "0",
            'imageH' => "0",
            "fontSize" => "14",
            "length" => 4
        );
        $verify = new \Think\Verify($config);
        $verify -> useImgBg = false;
        $verify -> useNoise = false;
        $verify -> entry();
    }
	
	public function prologin(){
		$code = I("code");
        $verify = new \Think\Verify(); 
		$res = $verify->check($code);
		if(!$res){
			$this -> error("验证码错误！");
		}
		$admin = M("admin");
		$data = $admin -> where(array('name'=>"{$_POST['name']}")) -> find();
		if($data){
			if($data['password'] == md5($_POST['password'])){
				session('name',"{$data['name']}");
				session('id',"{$data['id']}");
				$this -> redirect('/Index/index');
			}else{
				$this -> error("密码错误，登录失败！");
			}
		}else{
			$this -> error("用户名错误，登录失败！");
		}
	}
	
	public function logout(){
		session('name',null);
        session('id',null);
		$this -> redirect('/Login/index');
	}
	
	
}