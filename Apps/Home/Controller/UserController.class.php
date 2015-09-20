<?php
namespace Home\Controller;
use Think\Controller;
use Think\Area;

class UserController extends Controller{
    //用户个人中心主页(基本信息页)
    public function index(){
		$users = M("users");
		$id = session('uid');
		$data = $users -> where("id={$id}") -> find();
		$this -> assign("users",$data);
        $this  -> display();
    }
	
	public function update(){
		$users = M("users");
		$_POST['id'] = session('uid');
		$_POST['password'] = md5($_POST['password']);
		if($_POST['password']){
			if($_POST['password'] == md5($_POST['repassword'])){
				//dump($_POST);exit;
				if($users -> create()){
					if($users -> save()){
						$this -> success("修改成功！");
					}else{
						$this -> error("修改失败！");
					}
				}
			}else{
				$this -> error("确认密码不相同！");
			}
		}
	}
	public function address(){
		//地址列表显示
		$useraddress = M("useraddress");
		$id = session('uid');
		$data = $useraddress -> where("user_id={$id}") -> select();
		$this -> assign("useraddress",$data);
		foreach($data as $k => &$v){
			$v[$v['id']] = str_replace('#','',$v['address']);//完整地址
			$v['last'] = explode('#',$v['address'])[3];
		}
		$this -> assign("address",$data);
		//三级连动地址
		$city = Area::city();
		$this -> assign("city",$city);
		$this -> display();
	}
	
	public function safety(){
		$userdetails = M("userdetails");
		$id = session("uid");
		$res = array('1'=>"没有验证邮箱",'2' => "已验证邮箱");
		$data = $userdetails -> where("user_id={$id}") -> find();
		$this -> assign("userdetails",$data);
		$this -> assign("res",$res);
		$this -> display();
	}
    
    public function addAddress(){
		//dump($_POST);
		$useraddress = M("useraddress");
		$_POST['user_id'] = session('uid');
		$_POST['address'] = $_POST['province'].'#'.$_POST['city'].'#'.$_POST['county'].'#'.$_POST['address2'];
        $_POST['addtime'] = time();
		if($useraddress -> create()){
			if($useraddress -> add()){
				echo 1;
			}else{
				echo 2;
			}
		}
    }
	public function del(){
		$useraddress = M("useraddress");
		$id = $_GET['id'];
		if($useraddress -> delete($id)){
			$this -> success("删除成功！");
		}else{
			$this -> error("删除失败！");
		}
	}
	
}