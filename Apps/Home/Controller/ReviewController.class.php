<?php
namespace Home\Controller;
use Think\Controller;
class ReviewController extends Controller{
		//评价页面
	public function index(){
	
		//获取到订单id ，取出订单中的商品
		$order_id = $_GET['id'];
		$orders = M('orders');
		$detail = M('detail');
		$ord = $orders -> where("id={$order_id}") -> find();
		$data = $detail -> where("order_id={$order_id}") -> select();
		//dump($data);
		$this -> assign("order",$ord);
		$this -> assign("data",$data);
		$this -> display();
		
	}
	
	public function add_review(){
		//dump($_FILES);
		//EXIT;
		if(!empty($_FILES['goods_pic']['name'])){
			$upload = new \Think\Upload();
			$upload -> maxSize = 10240000;
			$upload -> autoSub = false;
			$upload -> exts = array('jpg','gif','jpeg','png');
			$upload -> rootPath = 'Public/';
			$upload -> savePath = '/Uploads/review/';
			$info = $upload -> upload();
			//dump($info);
			//exit;
			if(!$info) {
				// 上传错误提示错误信息        
				$this->error($upload->getError());    
			}else{
				// 上传成功        
				foreach($info as $file){ 
					$img ="Public/".$file['savepath'].$file['savename'];
				  
					$image = new \Think\Image();
					$image -> open($img);
					$img3 = "Public".$file['savepath'].'s_'.$file['savename'];
					$image -> thumb(150,150) -> save($img3); //在缩略图前加前缀 “s_” ！
					@unlink($img);
					$_POST['goods_pic'] = $file['savename'];
					
					$_POST['addtime'] = time();
					 $_POST['user_id'] = session("uid");
					//exit;
					$goodsreviews = M('goodsreviews');
					//
					if($goodsreviews -> create()){
						if($goodsreviews -> add()){
							 //评价成功后将订单详情表中的商品修改为 “已评价”
							 $detail = M('detail');
							 $d['id'] = $_POST['detail_id'];
							 $d['isreview'] = 2;
							 
							 if($detail -> create($d)){
								if($detail -> save()){
									 $this -> success("评价成功！");
								}else{
									$this -> error("评价失败1111");
								}
							 }else{
								 $this -> error("评价失败333");
							 }
								
							
						}else{
							$this -> error("评价失败222！");
						}
					}
								
				}
			}
		}else{
			//如果没有上传图，将信息添加到数据库
			$d['addtime'] = time();
			$d['user_id'] = session("uid");
			$d['message'] = $_POST['message'];
			$d['star'] = $_POST['star'];
			$d['goods_id'] = $_POST['goods_id'];
			$d['detail_id'] = $_POST['detail_id'];
			$d['goods_name'] = $_POST['goods_name'];
			//dump($d);
			//exit;
		    $goodsreviews = M('goodsreviews');
				if($goodsreviews -> create($d)){
					if($goodsreviews -> add()){
						 //评价成功后将订单详情表中的商品修改为 “已评价”
						 $detail = M('detail');
						 $d['id'] = $_POST['detail_id'];
						 $d['isreview'] = 2;
						 
						 if($detail -> create($d)){
							if($detail -> save()){
								 $this -> success("评价成功！");
							}else{
								$this -> error("评价失败1111");
							}
						 }else{
							 $this -> error("评价失败333");
						 }
							
						
					}else{
						$this -> error("评价失败222！");
					}
				}
		
		
		
		}
		
	    
	}
	
	
	//===============状态改变=====================
	public function order_state(){
	    //dump($_GET);
		//EXIT;
		
		
		$d['id'] = $_GET['id'];
		$detail = M('detail');
		switch($_GET['aa']){
			case 1:
				$d['state'] = 2;
				if($detail -> create($d)){
					if($detail -> save()){
						$this -> success("已提醒发货");
					}else{
						$this -> error("提醒失败");
					}
				}else{
					$this -> error("提醒失败");
				}
				break;
			case 2:
				break;
			case 3:
				//============确认收货===  state=4   ===========
				$d['state'] = 4;
				if($detail -> create($d)){
					if($detail -> save()){
						$this -> success("确认收货成功！");
					}else{
						$this -> error("11");
					}
				}else{
					$this -> error("22！");
				}
				break;
			case 4:
					$d['id'] = $_GET['order_detail_id'];
					//echo $_GET['sl'];
					if($_GET['sl']==1){
						//买家申请退货，将 state 修改为 5
						$d['state'] = 5;
						//dump($d);
						//exit;
						if($detail -> create($d)){
							if($detail -> save()){
								$this -> success("申请退货中！");
							}else{
								$this -> error("申请退货失败");
							}
						}else{
								$this -> error("申请退货失败");
						}
					}else if($_GET['sl']==2){
						//买家为申请换货 将 state 修改为 7
						$d['state'] = 7;
						if($detail -> create($d)){
							if($detail -> save()){
								$this -> success("申请换货中！");
							}else{
								$this -> error("申请换货失败");
							}
						}else{
							$this -> error("申请换货失败");
						}
					}else{
						//买家申请保修  将 state 修改为 9
						$d['state'] = 9;
						if($detail -> create($d)){
							if($detail -> save()){
								$this -> success("申请保修中！");
							}else{
								$this -> error("确认收货失败！");
							}
						}else{
							$this -> error("确认收货失败！");
						}
						
					}
				break;
			case 5:
				//=======取消退货申请===将  state 修改为 4  ========
					$d['state'] = 4 ;
					if($detail -> create($d)){
								if($detail -> save()){
									$this -> success("取消退货申请成功！！");
								}else{
									$this -> error("取消失败");
								}
							}else{
									$this -> error("取消失败");
							}
				break;
			case 6:
				break;
			case 7:
				break;
			case 8:
				break;
			case 9:
				break;
			case 10:
				break;
		}
	}
	
}