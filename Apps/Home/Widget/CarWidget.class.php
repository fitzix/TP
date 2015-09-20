<?php
namespace Home\Widget;
use Think\Controller;
class CarWidget extends Controller{
	public function index(){
		//return $_SESSION('uid');
		//return $v = session('username');
	    //从session中获取用户的 id ,根据用户id,获取到购物车中的商品
		 $uid = session('uid');
		 $car = M('car');
		 $data = $car ->where("user_id={$uid}") -> select();
		//return dump($data);
		
	    //遍历后获取到每个商品的id ,在根据商品id，从商品表中获取商品的详细信息
     	$goods = M('goods');
		foreach($data as $g){
			 $id = $g['id'];
			 $goods_id = $g['goods_id'];
			 $d[$id]['car_id'] = $g['id'];
			 $d[$id]['num'] = $g['num'];
			 $d[$id]['color'] = $g['num'];
			 $d[$id]['size'] = $g['num'];
			 $d[$id]['goods'] = $goods -> find($goods_id);
		}
		// return dump($d[$goods_id]);
		$this -> assign("goods",$d);
		$this -> display("Public:car");
    }
	
	
	//==============================
		public function my_car(){
		
	    //从session中获取用户的 id ,根据用户id,获取到购物车中的商品
		 $uid = session('uid');
		 $car = M('car');
		 $attrvalue = M(attrvalue);
		 $data = $car ->where("user_id={$uid}") -> select();
		
		
	    //遍历后获取到每个商品的id ,在根据商品id，从商品表中获取商品的详细信息
     	$goods = M('goods');
		foreach($data as $g){
			 $id = $g['id'];
			 $goods_id = $g['goods_id'];
			 $d[$id]['car_id'] = $g['id'];
			 $d[$id]['num'] = $g['num'];
		     $d[$id]['color'] = $g['color'];
			 
			 $color_data = $attrvalue -> find($g['color']);
			 $d[$id]['color'] = $color_data['attrvalue'];
			 
			 $size_data = $attrvalue -> find($g['size']);
			 $d[$id]['size'] = $size_data['attrvalue'];
			 
			 //$d[$id]['size'] = $g['size'];
			 $d[$id]['goods'] = $goods -> find($goods_id);
		}
	    //return dump($d);
		$this -> assign("goods",$d);
		$this -> display("Public:my_car");
    }
	
	
	
	
	//==============购物车商品数量===================
	public function car_num(){
		$uid = session('uid');
	    $car = M('car');
	    $data = $car ->where("user_id={$uid}") -> select();
	   //遍历后获取到每个商品的id ,在根据商品id，从商品表中获取商品的详细信息
     	$goods = M('goods');
		$num = 0;
		foreach($data as $g){
			$num += $g['num'];
		
		}
		 return $num;
	
		
	}
	//==============购物车商品总价格===================
	public function car_total(){
		$uid = session('uid');
		$car = M('car');
		$data = $car -> where("user_id={$uid}") -> select();
		$goods = M('goods');
		$total_price = 0;
		foreach($data as $g){
			//商品id
			$goods_id = $g['goods_id'];
			//商品数量
		    $num['goods_id'] = $g['num'];
		    $p['goods_id'] = $goods ->where("id={$goods_id}") -> find();
			//return dump($p['goods_id']);
			$price['goods_id'] = $p['goods_id']['saleprice'] * $num['goods_id'];
			$total_price += $price['goods_id'];
		}
		return $total_price ;
	}
	
}
