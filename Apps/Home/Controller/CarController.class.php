<?php
namespace Home\Controller;
use Think\Controller;
use Think\Area;
class CarController extends Controller{
	public function index(){
		
		$this -> display();
	}
	
	public function edit_num(){
		//echo $_POST['car_id'];
		$data['num'] = $_POST['num'];
		$data['id'] = $_POST['car_id'];
		$car = M('car');
		if($car -> create($data)){
			if($car -> save()){
				echo 1;
			}else{
				echo 2;
			}
		}else{
			echo 3;
		}
		
	}
	
	//======根据传递过来的id删除购物车中的商品=====
	public function del_car_goods(){
		$id = $_GET['id'];
		$car = M('car');
		if($car -> delete($id)){
			$this -> success('删除成功！');
		}else{
			$this -> error("删除失败！");
		}
		
		
	}
	
	
	//================订单页========================
	public function order(){
		$user_id = session('uid');
		//根据user_id 查询出地址
		$useraddress = M('useraddress');
		//主地址
		$main_add = $useraddress -> where("user_id={$user_id} AND ismain=2") -> find(); 
		$adds = $useraddress -> where("user_id={$user_id} AND ismain=1") -> select(); 
		
		//========将session中的值分配到订单页==========
		$order = session('order');
		$this -> assign('orders',$order);
		//dump($order);
		//exit;
        
		//==========地址三级连动=================
		$city = Area::city();
		$this -> assign("city",$city);
		
		$this -> assign('main_add',$main_add);
		$this -> assign('adds',$adds);
		$this -> display();
	}
	
	
//==================添加订单到session===========================
	
	public function add_goods_session(){
		//$a = session('order');
		//echo dump($a);
		//exit;
		if(session('?order')){
			$order = session('order');  //
		
		}
		
		 $car_id = $_POST['car_id'];
		 $order[$car_id] =  $_POST;
		 session('order',$order);//将商品信息储存到session中，
		// echo dump($_POST);
		// exit;
		$a = session('order');
		if($a[$car_id]){
			echo "添加成功";
		}else{
			echo "添加失败";
		}
		
		
		
	}
	
	//==================删除取消的session============================
	public function del_goods_session(){
        $car_id = $_POST['car_id']; 
		$order = session('order');
		//echo dump($order[$car_id]);
		//exit;
		unset($order[$car_id]); //删除目标数据
		if(!empty($order)){
			session('order',$order);//将删除数据后的数据添加到session
			$order = session('order');
			if(!$order[$car_id]){
				echo "取消成功";
			}else{
				echo "取消失败";
			}
		}else{
			session('order',null);
			echo "取消成功2！";
		}
		
	}
	
	//=======================确认订单==============================
	public function add_order(){
		 $address_id = $_POST['address_id']; //获取到地址id
		//根据$address_id获取联系信息
		$useraddress = M('useraddress');
		$link_info = $useraddress -> find($address_id);
		$user_id = session('uid');
		$order['user_id'] = $user_id;
		$order['osn'] = time().$user_id;
		$order['linkman'] = $link_info['linkman'];
		$order['address'] = $link_info['address'];
		$order['code'] = $link_info['code'];
		$order['phone'] = $link_info['phone'];
		$order['addtime'] = time();
		//$order['state'] = 1;
		$money = 0;
		$ords = session('order');
		//dump($ords);
		//exit;
		$detail = M('detail');
		$car = M('car');
		foreach($ords as $ord){
			$money += $ord['goods_price'] * $ord['goods_num'];
		}
		$order['money'] = $money;
		$order['integral'] = ceil($money);
		$order['rankcredits'] = ceil($money);
		$orders = M('orders');
		if($orders -> create($order)){
			$lastInsertId = $orders -> add(); //获取到最后一条数据的 id
			//dump($order);exit;
			//$lastInsertId = $orders -> add($order); //获取到最后一条数据的 id
			if($lastInsertId){
				//============将积分添加到用户表中================
				$d['id'] = $order['user_id'];
				$d['integral'] = $order['integral'];
				$d['rankcredits'] = $order['rankcredits'];
				$users = M("users");
				if($users -> create($d)){
				    $users -> save();
				}
			  
				
				//遍历session中的商品，添加到订单表中
				foreach($ords as $ord){
				    $order_details['order_id'] = $lastInsertId;
					$order_details['goods_id'] = $ord['goods_id'];
					//exit;
					$order_details['goods_name'] = $ord['goods_name'];
					$order_details['showimg'] = $ord['goods_pic'];
					$order_details['num'] = $ord['goods_num'];
					$order_details['price'] = $ord['goods_price'];
					//dump($ord);
					//exit;
					if($detail -> create($order_details)){
						if($detail -> add()){
						//	echo $order_details['car_id'];
						//	exit;
							
							//添加成功，将购物车中的商品删除
						    $car -> delete($ord['car_id']);
						}
					}
				}
				//添加成功后，清楚session 中的商品信息
				session('order',null);
				echo 1;
			}else{
				session('order',null);
				echo 2;
			}
		}else{
			session('order',null);
			echo 3;
				
		}
	} 
	
	
	//======================新增收货人地址==================================
	public function add_address(){
		//echo dump($_POST);
	   // exit;
		$_POST['address'] = $_POST['province'].'#'.$_POST['city'].'#'.$_POST['county'].'#'.$_POST['address2'];
		$_POST['addtime'] = time();
		$_POST['user_id'] = session('uid');
		$useraddress = M('useraddress');
		//dump($_POST);
		//exit;
		if($useraddress -> create()){
			if($useraddress -> add()){
				echo 1;
			}else{
				echo 2;
			}
		}else{
			echo 3;
		}
	}
	
	//=========================地址设置==========================================
	
	public function edit_address(){
		$useraddress = M('useraddress');
	    $data['id'] = $_GET['address_id'];//地址id
		$data['ismain'] = 2;//地址id
		$d['ismain'] = 1;
		//=============首先把原有的默认地址的 ismain 设为 1 ==============
		$useraddress -> where("ismain=2") -> save($d);
		if($useraddress -> save($data)){
			$main_add = $useraddress ->where("ismain=2") -> find();//获取默认地址
			$adds = $useraddress ->where("ismain=1") -> select();//获取默认地址
		    $this -> success("修改成功！");
		}else{
			$this -> error("修改失败！");
		}
	}
	
	//==========删除地址================
	public function del_address(){
		$id = $_GET['address_id'];
		$useraddress = M('useraddress');
		if($useraddress -> delete($id)){
			$this -> success("删除成功！");
		}else{
			$this -> error("删除失败！");
		}
	}
	
	
	
	
	
	
	
}