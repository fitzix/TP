<?php
namespace Admin\Controller;
use Think\Controller;

class BrandsController extends Controller{
	public function index(){
		$brands = M("brands");
		foreach($_POST as $k => $v){
			if($v){
				$map[$k] = $v;
			}
		}
		$total = $brands -> where($map) -> count();
		$Page = new \Think\Page($total,10);
		$data = $brands -> where ($map) -> order('id') -> page($_GET['p'],10) -> select();
		$show = $Page -> show();
		$this -> assign("brands",$data);
		$this -> assign("page",$show);
		$this -> display();
	}
}