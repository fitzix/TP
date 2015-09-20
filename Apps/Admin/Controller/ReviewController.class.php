<?php
namespace Admin\Controller;
use Think\Controller;

class ReviewController extends Controller{
	public function index(){
        $goodsreviews = M("goodsreviews");
        foreach($_POST as $k=>$v){
            if($v){
                $map[$k] = $v;//获取搜索条件
            }
        }
        $total = $goodsreviews -> where($map) -> count();//查询数据总条数
        $Page = new \Think\Page($total,5);//实例化分页类
        $data = $goodsreviews -> where($map) -> order('id') -> page($_GET['p'],5) -> select();
        $show = $Page -> show();//分页显示输出
		$state = array(1 =>"显示",2 =>"不显示");
		$this -> assign("state",$state);
        $this -> assign("goodsreviews",$data);
        $this -> assign("page",$show);
        $this -> display();
	}
	
	public function reply(){
		$reviewsreply = M("reviewsreply");
		$goodsreviews = M("goodsreviews");
		$id = $_POST['reviews_id'];
		$data = $goodsreviews -> where("id={$id}") -> find();
		
		$_POST['addtime'] = time();
		$_POST['user_id'] = $data['user_id'];
		$_POST['ip'] = $_SERVER[REMOTE_ADDR];
		if($reviewsreply -> create()){
			if($reviewsreply -> add()){
                $this -> success("回复成功",index);
            }else{
                $this -> error("回复失败");
            }
		}
	}
	public function insert(){
		$this -> display();
	}
	
	public function del(){
		$id = I("id");
		$goodsreviews = M("goodsreviews");
		if($goodsreviews -> delete($id)){
			$this -> success("删除成功！");
		}else{
			$this -> error("删除失败！");
		}
	}
}