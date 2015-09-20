<?php
namespace Admin\Controller;
use Think\Controller;

class NoticesController extends Controller{
	//消息列表页
	public function index(){
		$notices = M('notices');
		$count = $notices -> count();
		$Page = new \Think\Page($count,3);
		$show = $Page -> show();
		$list = $notices -> order('addtime') -> limit($Page->firstRow.','.$Page->listRows) -> select();
		$this -> assign('list',$list);
		$this -> assign('page',$show);
		$this -> display();
	}
	
	//获取消息添加页面
	public function add(){
		$this -> display();
	}
	//添加数据到数据库
	public function insert(){
		$_POST['addtime'] = time();
		$_POST['user_id'] = 0;
		//dump($_POST);
		//exit;
		$notices = M('notices');
		if($notices -> create()){
			if($notices -> add()){
				$this -> redirect('notices/index');
			}else{
				$this -> redirect('notices/add');
			}
		}
	}
	
	//获取消息修改页面
	public function edit(){
		$id = I('id');
		$notices = M('notices');
		$data = $notices -> find($id);
		$this -> assign('notice',$data);
		$this -> display();
	}
	//将修改的信息添加到数据库
	public function update(){
		unset($_POST['addtime']);
		$_POST['addtime'] = time();
		$_POST['admin_id'] = 0;
		$notices = M('notices');
		if($notices -> create()){
			if($notices -> save()){
				$this -> redirect("notices/index");
			}else{
				$this -> redirect("notices/edit");
			}
		}
	}
	
	//消息删除
	public function del(){
		$id = I('id');
		$notices = M('notices');
		if($notices -> delete($id)){
			$this -> redirect('notices/index');
		}else{
			$this -> redirect('notices/index');
		}
	}
}