<?php
namespace Admin\Controller;
use Think\Controller;

class OrdersController extends Controller{
	public function index(){
		$orders = M("orders");
        foreach($_GET as $k=>$v){
            if($v){
                $map[$k] = $v;//获取搜索条件
            }
        }
		unset($map['p']);
        $total = $orders -> where($map) -> count();//查询数据总条数
        $Page = new \Think\Page($total,1,$map);//实例化分页类
        $data = $orders -> where($map) -> order('addtime desc') -> page($_GET['p'],1) -> select();
        $show = $Page -> show();//分页显示输出
        
		//=========根据订单详情判断订单状态==============
		$detail = M('detail');
		foreach($data as &$d){
			$order_id = $d['id'];
			$map['order_id'] = array('EQ',$order_id);
			$detail_data  = $detail -> where($map) -> select();
			//dump($detail_data);
			//exit;
			foreach($detail_data as $a){
				if($a['state'] < 4){
					$d['state'] = 1;
				}
			}
		}
		//dump($data);
		//exit;
		$this -> assign("orders",$data);
        $this -> assign("page",$show);
		
		
		$paymode = array(1 => "在线付款",2 => "货到付款",3 => "线下付款");
		$this -> assign('paymode',$paymode);
        $this -> display();
	}
	//===================订单修改========================
	public function edit(){
		$id = I("id");
		$orders = M("orders");
		$data = $orders -> where("id={$id}") -> find();
		$this -> assign("orders",$data);
		$addr = str_replace("#","",$data['address']);
		$this -> assign("addr",$addr);
		$this -> display();
	}
	
	public function update(){
		$orders = M("orders");
		//dump($_POST);exit;
		if($orders -> create()){
			if($orders -> save()){
				$this -> success("修改成功！",index);
			}else{
				$this -> error("修改失败！");
			}
		}
	}
	
	//=================查看订单详情==========================
	public function order_details(){
		//获取订单id
		$id = $_GET['id'];
		$detail = M('detail');
		$data = $detail -> where("order_id={$id}") -> select();
		//dump($data);
		$goods = M('goods');
		foreach($data as &$d){
		    $goods_id = $d['goods_id'];
			$goods_data = $goods -> where("id={$goods_id}") -> find();
			//dump($goods_data);
			//exit;
			$d['psn'] = $goods_data['psn'];
		}
		
		$this -> assign("data",$data);
		$this -> display();
	}
	
	//===================改变状态订单商品====================================
	public function order_state(){
		$detail = M('detail');
		$d['id'] = $_GET['id'];
		//echo $_GET['aa'];
		switch($_GET['aa']){
			case 1:
				//点击发货,将状态修改为 3
				$d['state'] = 3;
				if($detail -> create($d)){
					if($detail -> save()){
						$this -> success("已发货！");
					}else{
						$this -> error("发货失败！");
					}
				}else{
					$this -> error("发货失败！");
				}
				break;
			case 2:
				//点击发货,将状态修改为 3
					$d['state'] = 3;
					if($detail -> create($d)){
						if($detail -> save()){
							$this -> success("已发货！");
						}else{
							$this -> error("发货失败！");
						}
					}else{
						$this -> error("发货失败！");
					}
				break;
			case 3:
				break;
			case 4:
				break;
			case 5:
				//同意退货,将状态修改为 6
					$d['state'] = 6;
					if($detail -> create($d)){
						if($detail -> save()){
							$this -> success("已退货！");
						}else{
							$this -> error("退货失败！");
						}
					}else{
						$this -> error("退货失败！");
					}
				break;
			case 6:
				break;
			case 7:
			//同意换货，将状态修改为 8			  
			  $d['state'] = 8;
					if($detail -> create($d)){
						if($detail -> save()){
							$this -> success("已换货！");
						}else{
							$this -> error("换货失败！");
						}
					}else{
						$this -> error("换货失败！");
					}
				break;
			case 8:
				break;
			case 9:
				//同意保修，将状态修改为 10			  
			  $d['state'] = 10;
					if($detail -> create($d)){
						if($detail -> save()){
							$this -> success("已同意保修！");
						}else{
							$this -> error("保修失败！");
						}
					}else{
						$this -> error("保修失败！");
					}
				break;
			case 10:
				break;
		}
	}
	
	//=====================获取评价=============================
	public function get_review(){
	    $goods_id = $_POST['goods_id'];
	    $order_id = $_POST['order_id'];
		$orders = M('orders');
		$map['id'] = array('EQ',$order_id);
		$ord = $orders ->where($map) ->  find();
	//	dump($ord);
	//	exit;
		$user_id = $ord['user_id'];
		$map2['user_id'] = array('EQ',$user_id);
		$map2['goods_id'] = array('EQ',$goods_id);
		
		
		
		$goodsreviews = M('goodsreviews');
		$data = $goodsreviews ->where($map2) -> find();
		//dump($data);
		//exit;
		echo json_encode($data);

		//dump($data);
	}
	
	//===================删除订单================================
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
	
}