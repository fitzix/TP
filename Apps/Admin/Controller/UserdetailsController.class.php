<?php
namespace Admin\Controller;
use Think\Controller;

class UserdetailsController extends Controller{
	public function index(){
		$user_id = $_GET['id'];
		//exit;
		$userdetails = M('userdetails');
		$data = $userdetails -> find($user_id);
		//dump($data);
		$this -> assign('data',$data);
		$this -> display();
	}
}