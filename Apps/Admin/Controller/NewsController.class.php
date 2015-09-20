<?php
namespace Admin\Controller;
use Think\Controller;

class NewsController extends Controller{
	
	public function index(){
		//新闻分页
		$news = M("news");
		$count = $news -> count(); //总条数
		$Page = new \Think\Page($count,3); //实例化分页类，并传递参数
		$show = $Page -> show();
		$list = $news -> order("addtime,sort") -> limit($Page->firstRow.','.$Page->listRows)->select();
		$state = array();//定义状态数组
        $state[1] = '新添加';
        $state[2] = '发布中';
        $state[3] = '已过期';
        $type = array();//定义类型数组
        $type[1] = '公告';
        $type[2] = '特惠';
        $this -> assign('type', $type);
        $this -> assign('state', $state);
        $this -> assign('list',$list);
		$this -> assign('page',$show);
		$this -> display();
	}
	
	//获取添加新闻页面
	public function add(){
		$this -> display();
	}
	
	//添加新闻到数据库
	public function insert(){
		$news = M("news");
		$_POST['addtime'] = time();
	    if($news -> create()){
			if($news -> add()){
				$this -> redirect("news/index");
			}else{
				$this -> redirect("news/add");
			}
		}
	}
 	
	//获取修改新闻页面
	public function edit(){
		$id = I("id");
		$news = M("news");
		$data = $news -> find($id);
		$this -> assign("news",$data);
		$this -> display();
		
	}
	
	//修改新闻
	public function update(){
		$_POST['addtime'] = time();
		//dump($_POST);
		$news = M("news");
		if($news -> create()){
			if($news -> save()){
				$this -> redirect("news/index");
			}else{
				$this -> error("修改失败！");
			}
		}
	}
	
	public function del(){
		$news = M("news");
		echo $id = I("id");
		
		if($news -> delete($id)){
			$this -> redirect("news/index");
		}else{
			$this -> redirect("news/index");
		}
		
	}
}
