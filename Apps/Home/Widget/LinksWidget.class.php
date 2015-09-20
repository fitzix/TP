<?php
namespace Home\Widget;
use Think\Controller;
class LinksWidget extends Controller{
	public function index(){
		$links = M("links");
		$data = $links -> where("state=2") -> order("addtime") -> limit(15) -> select();
		$this -> assign("links",$data);
		$this -> display('Public:links');
	}
}