<?php
namespace Home\Controller;
use Think\Controller;

class OrderController extends Controller{
	public function index(){
		$orders = M("orders");
		$id=session('uid');
		
		foreach($_POST as $k=>$v){
			if($v){
				$map[$k] = $v;//获取搜索条件
			}
		}
		unset($map['p']);
		$total = $orders -> where("user_id={$id}") -> count();
		$Page = new \Think\Page($total,5);//实例化分页类
		$data = $orders -> where("user_id={$id}") -> page($_GET['p'],5) ->order("addtime desc") -> select();
		$show = $Page -> show();//分页显示输出
		$this -> assign("orders",$data);
		$this -> assign("page",$show);
		
		$state = array(1 => "未完成",2 => "已完成");
		$this -> assign('state',$state);
		//=======猜你喜欢============
		//从session中获取到用户的id
		$histories = M("histories");
		$his_data = $histories -> where("user_id={$id}") -> order('addtime') -> limit("4") -> select();
		//遍历数据得到商品id，进而得到商品的详细信息
		$goods = M('goods');
		foreach($his_data as $g){
			$goods_id = $g['goods_id'];
			$goods_data[$goods_id] = $goods -> find($goods_id);
		}
		$res = $histories -> where("user_id={$id}") -> order('addtime') -> limit('4,4') -> select();
		foreach($res as $g){
			$goods_id = $g['goods_id'];
			$rel[$goods_id] = $goods -> find($goods_id);
		}
		//===================
		$this -> assign("rel",$rel);
		$this -> assign("goods_data",$goods_data);
		$this -> display();
	}
	
	public function checkorder(){
		$orders = M("orders");
		$id = $_GET['id'];
		$data = $orders -> where("id={$id}") ->find();
		$this -> assign("orders",$data);
		$state = array(1 => "未发货",2 => "已提醒发货",3=> "已发货",4 => "已完成",5 => "申请退货",6 => "退货完成",7 => "申请换货",8=> "换货完成",9=> "申请保修",10 => "保修完成");
		$this -> assign('state',$state);
		
		$detail = M("detail");
		$res = $detail -> where("order_id={$id}") -> select();
		foreach($res as $k => $v){
			$filter .= $v['goods_id'].',';
		}
		$goodsids = trim($filter, ',');
		$goods = M("goods");
		$tal = $goods -> where("id in ($goodsids)") -> select();
		$this -> assign("goods",$tal);
		$this -> assign("details",$res);
		$this -> display();
	}
	
	//===================删除订单============================
	public function delete_order(){
		$order_id = $_GET['id'];
		$detail = M('detail');
		$orders = M('orders');
		//=========首先删除订单中的详细信息=========
		if($detail -> where("order_id={$order_id}") -> delete()){
			if($orders -> where("id={$order_id}") -> delete()){
				$this -> success("订单删除成功！");
			}else{
				$this -> error("订单删除失败！");
			}
		}else{
			$this -> error("订单详情删除失败!");
		}
		
		
	}
	
	public function order_list(){
		//echo $_POST['case'];
		switch($_POST['case']){
			case 1:
				//全部订单
				$user_id = session('uid');
				$orders = M('orders');
				$detail = M('detail');
				$order_data = $orders -> where("user_id={$user_id}") -> select();
				//dump($order_data);
				foreach($order_data as &$order_d){
					$order_id = $order_d['id'];
					$detail_data = $detail -> where("order_id={$order_id}") -> select();
					foreach($detail_data as $detail_d){
						if($detail_d['state'] < 4){
							$order_d['state'] = 1;
						}
					}
				}
				$this -> ajaxReturn($order_data);
				
				break;
			
			
			
			case 2:
				break;
			
			
			
			case 3:
				break;
			
		}
		
	}
	
}