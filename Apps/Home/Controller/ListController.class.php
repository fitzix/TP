<?php
namespace Home\Controller;
use Think\Controller;

class ListController extends Controller{
	public function index(){
		//=======活动开始============
			$news = M('news');
			$news_data = $news ->where("type=2") ->order("addtime") -> limit(5) -> select();
		//===================
		
		//=======浏览历史开始============
			//从session中获取到用户的id
			$uid = session('uid');
			$histories = M("histories");
			$his_data = $histories -> where("user_id={$uid}") -> order('addtime DESC') -> limit(15) -> select();
			//遍历数据得到商品id，进而得到商品的详细信息
            $goods = M('goods');
			foreach($his_data as $g){
				$goods_id = $g['goods_id'];
				$goods_data[$goods_id] = $goods -> find($goods_id);
			}
		//=========浏览历史结束==========

		//=======热卖商品开始============
			$hot_data = $goods -> where("ishot=2") -> order('addtime') -> limit('3') -> select();
		//===================
		
		//======推荐商品开始=============
			$best_data = $goods -> where("isbest=2") -> order('addtime') -> limit('10') -> select();
		//======获取商品类别=============
            $type = M('type') -> order('sort ASC') -> where('state != 2') -> select();//获取商品显示中的全部类别
            foreach($type as $v){
                $typeList[$v['id']] = $v['name'];//自定义数组$typeList,下标为类别ID，值为类名名称
            }
         //======商品搜索=============
            
            
            //获取搜索条件
            foreach($_GET as $k => $v){
                if($v){
                    //商品名称搜索
                    if($k == 'keyword'){
                        $map['name'] = array('like', "%{$v}%");//组合商品名称搜索条件
                    }
                    //按商品类别搜索
                    if($k == 'typeid'){
                        $v = implode(',', getChildId($type, $v)).','.$v;//获取子类及本类ID字符串
                        $map['type_id'] = array('in', $v);//组合类别搜索条件
                    }
                    //按商品品牌搜索
                    if($k == 'brandid'){
                        $map['brand_id'] = $v;//组合品牌搜索条件
                    }
                    //按价格
                    if($k == 'price'){
                        $priceFilter = explode('_', $v);
                        $map['saleprice'] = array('gt', $priceFilter[0]);
                        if($priceFilter[1]){
                            $map['saleprice'] = array(array('gt', $priceFilter[0]) ,array('elt', $priceFilter[1]));
                        }
                    }
                    //按属性(颜色、尺码)
                    
                    if($k == 'attrid'){
                        //$_GET['attrid'] = 
                        //如果存在
                        if(is_array($_GET['attrid'])){
                            //如果尺码存在，组合到属性数组
                            if($_GET['attrsizeid']){
                                $_GET['attrid'][] = $_GET['attrsizeid'];
                            }
                            $radio = 'OR';//设置属性搜索条件为合集
                            //遍历属性数组并组合为一维数组，
                            for($i = 0; $i < count($_GET['attrid']); $i++){
                                $searchAttr[] = '%'.$_GET['attrid'][$i].'%';
                            }
                        } else {
                            $radio = 'AND';//设置属性条件是交集还是合集，默认交集
                            if($_GET['attrsizeid']){
                                $searchAttr[] = '%'.$_GET['attrsizeid'].'%';
                            }
                            $searchAttr[] = '%'.$_GET['attrid'].'%';
                        }
                    }
                    //判断尺码是否存在
                    if($k == 'attrsizeid'){
                        if(!$_GET['attrid']){
                            $searchAttr[] = '%'.$_GET['attrsizeid'].'%';
                        }
                    }
                    $attrMap['attrvalue_id'] = array('like', $searchAttr, $radio);//组合搜索条件
                    $goodsId = M('attr') ->field('goods_id') -> where($attrMap) -> select();//查询商品ID字符串
                }
            }
            //如果有颜色尺码或属性存在,组合搜索条件
            if($_GET['attrid'] || $_GET['attrsizeid']){
                if($goodsId){
                    foreach($goodsId as $v){
                        $goodsids[] = $v['goods_id'];
                    }
                    $map['id'] = array('in',$goodsids);//组合颜色、尺码属性搜索条件
                } else {
                    $map['id'] = array('in','');//如果颜色或尺寸搜索条件，没有获取到商品，条件赋空
                }
            }
            
            $map['state'] = '2';//组合商品状态搜索条件
            if($_POST['order']){
                $orderFilter = explode('_', $_POST['order']);
                $order = "{$orderFilter[0]} {$orderFilter[1]}";
            } else {
                $order = 'id ASC';
            }
            
            $count = $goods -> where($map) -> order('id ASC') -> count();
            $Page = new \Think\Page($count, 4, $_GET);
            $goodsList = $goods -> where($map) -> order("{$order}") -> page($_GET['p'], 4) -> select();//获取商品列表
            
            $show = $Page -> show();
        //======获取商品品牌=============
            $brands = M('brands');
            if($_GET['typeid']){
                $typeid = implode(',', getChildId($type, $v).','.$v);//获取商品父类及本类字串
                $brandList = $brands -> where("state != 2 AND type_id in ({$_GET['typeid']})") -> select();
            }
            
        //======获取商品属性=============
            $attrs = getData('attrvalue', 'state = 1', 'id ASC');
        //======获取搜索分类属性=============
            $type_id = $_GET['typeid'];
            $navType = getParentId($type, $type_id);//获取前辈类ID
            if($navType){
                $navTypeList = explode(',', $navType);
            }
            $navTypeList[] = $type_id;

		$this -> assign("goodsList",$goodsList);  //======商品=======
		$this -> assign("count",$count);  //======商品总数=======
		$this -> assign("typeList",$typeList);  //======类别=======
		$this -> assign("navTypeList",$navTypeList);  //======类别导航=======
		$this -> assign("attrs",$attrs);  //======属性=======
		$this -> assign("brandList",$brandList);  //======品牌=======
		$this -> assign("page",$show);  //======分页=======
		$this -> assign("news",$news_data);  //======活动=======
		$this -> assign("histories",$goods_data); //=====浏览历史=====
		$this -> assign('hot_goods',$hot_data); //=====热卖商品======
		$this -> assign('best_goods',$best_data); //=====热卖商品======
		$this -> display();
	}
	
	
	
	
	
	
	
	
	//==============删除购物车商品====================
	public function del(){
		$car_id = I('car_id');
		$car = M("car");
		
		if($car -> where("id={$car_id}") -> delete()){
			$this -> success("删除成功！");
		}else{
			$this -> error("删除失败！");
		}
		
	}
    
    //搜索商品
    public function search(){
        dump(I());
        
    }
}