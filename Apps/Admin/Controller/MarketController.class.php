<?php
namespace Admin\Controller;
use Think\Controller;

class MarketController extends Controller{
	
	public function index(){
		$market = M("market");
		$data = $market -> order('id') -> select();
		$this -> assign("markets",$data);
		$this -> assign("page",$show);
		$this -> display();
	}
	
	public function edit(){
		$id = I("id");
		$market = M("market");
		$data = $market -> find($id);
		$this -> assign("market",$data);
		$this -> display();
	}
	
	public function update(){
		$market = M("market");
		if($market -> create()){
			if($market -> save()){
				$this -> success("修改成功！","index");
			}else{
				$this -> error("修改失败！");
			}
		}
	}
}