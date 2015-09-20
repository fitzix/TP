<?php
namespace Home\Controller;
use Think\Controller;
use Think\Area;
class DetailsController extends Controller{
	public function index(){
		//==========地址三级连动=================
		$city = Area::city();
		$this -> assign("city",$city);
		//=============根据商品 id 获取商品评价======
			$goods = M('goods');
			//作为测试  假设商品id=1
			$goods_id = I('id');//
			$goodsreviews = M('goodsreviews');
			
			//===========将浏览次浏览记录添加到浏览历史开始=================
			$histories = M('histories');
			$his['goods_id'] = $goods_id;
			$his['user_id'] = session('uid');
			$his['addtime'] = time();
			//dump($his);
			$data = $histories -> where("goods_id={$his['goods_id']}") -> find();
			if(!$data){
				if($histories -> create($his)){
					$histories -> add();
				}
			}
			
			//===========将浏览次浏览记录添加到浏览历史结束=================
			
			$users = M('users');
			$attr = M('attr');
			$attrvalue = M('attrvalue');
			//==========获取商品的详细信息============
			$goods_info = $goods -> find($goods_id);
			//根据商品的id获取属性id，进而获取属性值
			$attr_ids = $attr ->where("goods_id={$goods_id}") -> select();
			$attrvalue_id = "" ; //定义一个变量，用户存放属性值id
			$stock = 0; //定义一个变量，用户存放总库存
			foreach($attr_ids as $attr_id){
				$attrvalue_id .= $attr_id['attrvalue_id'].",";
				$stock +=  $attr_id['stock'];
			}
			$attrvalue_id = rtrim($attrvalue_id,",");
			
			//获取商品分类级别栏
            $type_id = $goods_info['type_id'];//所属类别ID
            $typeData = getData('type');
			$typeIds = getParentId($typeData, $type_id);
            $typeIds = explode(',', $typeIds);//组合数组
            foreach($typeData as $v){
                $typeInfo[$v['id']] = $v['name'];//遍历得到一维数组，下标为类ID，值为类名称
            }
			//获取颜色
			$c['attrname'] = array('EQ',"颜色");
			$c['id'] = array("IN",$attrvalue_id);
			$at_value['color'] = $attrvalue -> where($c) -> select();
			//获取尺寸
			$s['attrname'] = array('EQ',"尺码");
			$s['id'] = array("IN",$attrvalue_id);
			$at_value['size'] = $attrvalue -> where($s) -> select();
			
			//dump($at_value);
			//exit;
			
			
			
			//=================获取全部评价数，好，中，差========
				$al['goods_id'] = array('EQ',$goods_id);
				$al['state'] = array('EQ',"1");
			$count['all'] = $goodsreviews ->where($al) -> count();
				$wl['goods_id'] = array('EQ',$goods_id);
				$wl['star'] = array('EQ',"5");
			$count['w'] = $goodsreviews ->where($wl) -> count();
				$ml['goods_id'] = array('EQ',$goods_id);
				$ml['star'] = array('IN',"3,4");
			$count['m'] = $goodsreviews ->where($ml) -> count();
				$bl['goods_id'] = array('EQ',$goods_id);
				$bl['star'] = array('IN',"1,2");
			$count['b'] = $goodsreviews ->where($bl) -> count();
			
			//====好，中差评率===
			$rate['w'] =ceil($count['w'] / $count['all'] * 100) ;
			$rate['m'] =floor($count['m'] / $count['all'] * 100) ;
			$rate['b'] =floor($count['b'] / $count['all'] * 100) ;
			
			
		//=======================获取商品全部评价============================================
			$page_count = $goodsreviews -> where($al) -> count();
			$Page = new \Think\Page($page_count,10);
			$all_show = $Page -> show();
			$all_data = $goodsreviews -> where($al) ->limit($Page->firstRow.','.$Page->listRows) -> select();
		//	dump($all_data);
		//	exit;	
			//获取到评论人的 id ,在根据id 获取到====（会员名）===（会员等级）=====
			$reviewsreply = M('reviewsreply');
			foreach($all_data as $all){
				//将评论信息存储到以评论id为名字的数组中，下标为'review' ,
				$a[$all['id']]['review'] = $all;
				//获取评论人的id,进而获取详细信息，并将获取的信息储存到 $a[$all['id']]['user']
				 $user_id = $all['user_id'];
			
				$a[$all['id']]['user'] = $users ->where("id={$user_id}") -> find();
				//评论id = $all['id'] ,利用其查询出对应的回复 
				$a[$all['id']]['reviewsreply'] = $reviewsreply -> where("reviews_id={$all['id']}") -> order('addtime desc') ->limit("5") -> select();
				$a[$all['id']]['count'] = $reviewsreply -> where("reviews_id={$all['id']}") -> order('addtime desc') -> count();
			
			}
			
		//=======================获取商品好评============================================
		
			$all_data = $goodsreviews -> where($wl) -> select();
			//获取到评论人的 id ,在根据id 获取到====（会员名）===（会员等级）=====
			$reviewsreply = M('reviewsreply');
			foreach($all_data as $all){
				//将评论信息存储到以评论id为名字的数组中，下标为'review' ,
				$w[$all['id']]['review'] = $all;
				//获取评论人的id,进而获取详细信息，并将获取的信息储存到 $a[$all['id']]['user']
				$user_id = $all['user_id'];
				$w[$all['id']]['user'] = $users -> find($user_id);
				//评论id = $all['id'] ,利用其查询出对应的回复 
				$w[$all['id']]['reviewsreply'] = $reviewsreply -> where("reviews_id={$all['id']}") -> order('addtime desc') ->limit("5") -> select();
				$w[$all['id']]['count'] = $reviewsreply -> where("reviews_id={$all['id']}") -> order('addtime desc') -> count();
			}		
			
		//=======================获取商品中评============================================
			
			$all_data = $goodsreviews -> where($ml) -> select();
			//获取到评论人的 id ,在根据id 获取到====（会员名）===（会员等级）=====
			$reviewsreply = M('reviewsreply');
			foreach($all_data as $all){
				//将评论信息存储到以评论id为名字的数组中，下标为'review' ,
				$m[$all['id']]['review'] = $all;
				//获取评论人的id,进而获取详细信息，并将获取的信息储存到 $a[$all['id']]['user']
				$user_id = $all['user_id'];
				$m[$all['id']]['user'] = $users -> find($user_id);
				//评论id = $all['id'] ,利用其查询出对应的回复 
				$m[$all['id']]['reviewsreply'] = $reviewsreply -> where("reviews_id={$all['id']}") -> order('addtime desc') ->limit("5") -> select();
				$m[$all['id']]['count'] = $reviewsreply -> where("reviews_id={$all['id']}") -> order('addtime desc') -> count();
			}			
		//=======================获取商品差评============================================
		
			$all_data = $goodsreviews -> where($bl) -> select();
			//获取到评论人的 id ,在根据id 获取到====（会员名）===（会员等级）=====
			$reviewsreply = M('reviewsreply');
			foreach($all_data as $all){
				//将评论信息存储到以评论id为名字的数组中，下标为'review' ,
				$b[$all['id']]['review'] = $all;
				//获取评论人的id,进而获取详细信息，并将获取的信息储存到 $a[$all['id']]['user']
				$user_id = $all['user_id'];
				$b[$all['id']]['user'] = $users -> find($user_id);
				//评论id = $all['id'] ,利用其查询出对应的回复 
				$b[$all['id']]['reviewsreply'] = $reviewsreply -> where("reviews_id={$all['id']}") -> order('addtime desc') ->limit("5") -> select();
				$b[$all['id']]['count'] = $reviewsreply -> where("reviews_id={$all['id']}") -> order('addtime desc') -> count();
			}

            $best_data = $goods -> where("ishot=2 AND state = 2") -> limit('10') -> order('addtime') -> select();//热销商品           

            $this -> assign('typeIds',$typeIds);//所属类前辈类ID数组(一维)
            $this -> assign('typeInfo',$typeInfo);//类信息数组(一维)
            $this -> assign('type_id',$type_id);//所属类ID
            $this -> assign('best_goods',$best_data);
            $this -> assign('histories',$hot_data);
			$this -> assign("count",$count); //评论数
			$this -> assign("rate",$rate); //好评率
			$this -> assign("goods_info",$goods_info); //商品信息
			$this -> assign("a",$a);   //$a 全部评级
			$this -> assign("w",$w);   //$w 好评
			$this -> assign("m",$m);   //$m 中评
			$this -> assign("b",$b);   //$b 差评
			$this -> assign("all_page",$all_show);  //评论分页
			$this -> assign("at_color",$at_value['color']);  //商颜色属性值
			$this -> assign("at_size",$at_value['size']);  //商品尺寸属性值
			//$this -> assign("stock",$stock);  //商品库存量
			
		$this -> display();
	}
	
	//================评论回复添加到数据库======================
	public function review(){
		$data['message'] = $_POST['message'];
		$data['user_id'] = session('uid');
		$data['reviews_id'] = $_POST['review_id'];
		$data['addtime'] = time();
		$reviewsreply = M('reviewsreply');
		if($reviewsreply -> create($data)){
			if($reviewsreply -> add()){
				$this -> success("回复成功！");
			}else{
				$this -> error("回复失败！");
			}
		}else{
			$this -> error("回复失败！");
		}
	}
	
    
	//====================添加商品到购物车======================================
      public function add_car(){
		
		 $car = M('car');
		 $_POST['user_id'] = session('uid');
         if(!$_POST['user_id']){
            $this -> error(1);
         }
		 $data['user_id'] = array('EQ',$_POST['user_id']);
		 $data['goods_id'] = array('EQ',$_POST['goods_id']);
		 $data['color'] = array('EQ',$_POST['color']);
		 $data['size'] = array('EQ',$_POST['size']);
		 $d = $car -> where($data) -> find();
         //dump($_POST);exit;
		 if($d){
			//如果存在，则数量相加
			$num['id'] = $d['id'];
			$num['num'] = $_POST['num'] + $d['num'];
			if($car -> create($num)){
				if($car -> save()){
					$this -> success("添加成功！");
				}else{
					$this -> error("添加失败！");
				}
			}else{
				$this -> error("添加失败！");
			}
		 
		}else{
			//如果不存在，则加入购物车
			if($car -> create($_POST)){
					if($car -> add()){
						$this -> success("添加成功！");
					}else{
						$this -> error("添加失败");
					}
				}else{
					$this -> error("添加失败！");
				}
		}
	} 
	  
	  
    //====================商品库存量查询========================================
	public function check_stock(){
	    $attrvalue_id = rtrim($_POST['attrvalue_id'],",");
		//exit;
	    $goods_id = $_POST['goods_id'];
		
		$attr = M('attr');
		$data = $attr -> where("goods_id={$goods_id}") -> select();
		$stock = 0;
		
		if(strlen($attrvalue_id) > 1){
			$map['goods_id'] = array('EQ',$goods_id);
			$map['attrvalue_id'] = array('EQ',$attrvalue_id);
			$s =  $attr -> where($map) -> find();
			$stock = $s['stock'];
		}else{
			foreach($data as $a){
				$arr = explode(",",$a['attrvalue_id']); 
				if(in_array($attrvalue_id,$arr)){
					$stock += $a['stock']; 
				}
			}
		}
		echo $stock;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}