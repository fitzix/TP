<?php
namespace Home\Widget;
use Think\Controller;
class MyJDWidget extends Controller{
	public function index(){
		//全部订单，未完成订单，已完成订单
	    $user_id = session('uid');
		$orders = M('orders');
		$detail = M('detail');
		$order_data = $orders -> where("user_id={$user_id}") -> select();
		$order_count_all = $orders -> where("user_id={$user_id}") -> count();//===========得到全部订单数================
		$order_count_n = 0; //未完成的订单
		$order_count_y = 0; //已完成的订单
		
		//===定义一个变量存放评价与为评价的数量====
		$review_count = 0;//未评价
		
		//定义四个变量（存放售后相关数据）
		$after_count_all = 0; //所有售后商品
		$after_count_t = 0;   //退货商品
		$after_count_h = 0;   //换货商品
		$after_count_b = 0;   //保修商品
		
		foreach($order_data as $order_d){
			//获取到订单的id,查看订单是否已完成
			$order_id = $order_d['id'];
			$detail_data = $detail -> where("order_id={$order_id}") -> select();
			foreach($detail_data as $detail_d){
				//订单数量
				if($detail_d['state'] < 4){
					$order_count_n ++;
				}else if($detail_d['state']==4){
					$order_count_y ++;//未完成的订单加一
				}else if($detail_d['state']==5 || $detail_d['state']==6){
					$after_count_t ++; //退货加一
					$after_count_all ++ ;//所有售后加一
					$order_count_y ++;//未完成的订单加一
				}else if($detail_d['state']==7 || $detail_d['state']==8){
					$after_count_h ++; //换货加一
					$after_count_all ++ ;//所有售后加一
					$order_count_y ++;//未完成的订单加一
				}else if($detail_d['state']==9 || $detail_d['state']==10){
					$after_count_b ++; //保修加一
					$after_count_all ++ ;//所有售后加一
					$order_count_y ++;//未完成的订单加一
				}
				
			    //评价与未评价数量
				if($detail_d['isreview']==1){
					$review_count ++;
				}
			}
			
		}
		
		$order_count['all'] = $order_count_all;
		$order_count['n'] = $order_count_n;
		$order_count['y'] = $order_count_y;
		
		//全部售后，退货，换货，保修
		$after_count['all'] = $after_count_all;
		$after_count['t'] = $after_count_t;
		$after_count['h'] = $after_count_h;
		$after_count['b'] = $after_count_b;
		
		
		
		
		
		
		$this -> assign('after_count',$after_count);//待评价数量
		$this -> assign('review_count',$review_count);//待评价数量
		$this -> assign('order_count',$order_count);//=====订单数量=====
		$this -> display('Public:MyJD');
	}
}