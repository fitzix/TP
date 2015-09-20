<?php
namespace Admin\Controller;
use Think\Controller;
class LeaveController extends Controller{
	//留言列表页
	public function index(){
		$leave = M("leavemessage");
		//留言分页浏览
		$count = $leave -> count();
		$Page = new \Think\Page($count,3);
		$show = $Page -> show();
	    $list = $leave -> order("addtime") -> limit($Page->firstRow.','.$Page->listRows) -> select();
		$this -> assign('list',$list);
		$this -> assign('page',$show);
		$this -> display();
	}
	
	//查看每条留言内容页面
	public function check(){
		$leave = M('leavemessage');
		//修改留言为已读
	    $id = I('id');
		$leave->state = 2;
		$leave -> where("id={$id}") -> save();
		//将信息显示在修改页面
		$data = $leave -> find($id);
		$this -> assign('leave',$data);
		$this -> display();
	}
	
	public function reply(){
		//添加回复内容到回复留言表中
		$_POST['addtime'] = time();
		//===================================== 从session中获取回复人的id=================================
		$messagereply = M('messagereply');
		if($messagereply -> create()){
			if($messagereply -> add()){
				$this -> redirect('leave/index');
			}else{
			    $this -> redirect('leave/check');
			}
		}
	}
	
	//回复留言列表页
	public function messagereply(){
		
		$messagereply = M('messagereply');
		$count = $messagereply ->where('state=1') -> count();
		$Page = new \Think\Page($count,3);
		$show = $Page -> show();
		$list = $messagereply -> order('addtime') -> limit($Page->firstRow.','.$Page->listRows) -> select();
		$this -> assign('list',$list);
		$this -> assign('page',$show);
		$this -> display();
	}
	
	//查看每条回复详细内容
	public function checkreply(){
		$messagereply = M('messagereply');
		$id = I('id');
		$data = $messagereply -> find($id);
		$this -> assign('messagereply',$data);
		$this -> display();
	}
	
	
	
	
	
}