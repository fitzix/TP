<?php
namespace Admin\Controller;
use Think\Controller;

class LinkController extends PublicController{
    public function index(){
        $links = M("links");
        foreach($_POST as $k=>$v){
            if($v){
                $map[$k] = $v;//获取搜索条件
            }
        }
        $total = $links -> where($map) -> count();//查询数据总条数
        $Page = new \Think\Page($total,10);//实例化分页类
        $data = $links -> where($map) -> order('id') -> page($_GET['p'],10) -> select();
        $show = $Page -> show();//分页显示输出
        $this -> assign("links",$data);
        $this -> assign("page",$show);
		$state = array(1 => "新添加",2 => "显示中",3=> "禁用");
		$this -> assign('state',$state);
        $this -> display();
    }
    public function add(){
        $this -> display();
    }
    
    public function insert(){
		$links = M("links");
		$_POST['addtime'] = time();
		if($_FILES['pic']['name']){
			$upload = new \Think\Upload();
			$upload -> maxSize = 1024000000;
			$upload -> autoSub = false;
			$upload -> exts = array('jpg','gif','png','jpeg');
			$upload -> rootPath = "Public/";
			$upload -> savePath = '/Uploads/links/';
			$info = $upload -> upload();
			if(!$info){
				$this -> error($upload->getError());
			}else{
				$path = "Public/".$info['pic']['savepath'];
				$name = $info['pic']['savename'];
				$file = $path.$name;
				$image = new \Think\Image();
				$image ->open($file);
				$image -> thumb(100, 100)->save($path."s_".$name);
				unlink($file);
				$_POST['linklogo'] = $name;
			}
		}
		
		if($links -> create()){
			if($links -> add()){
                $this -> success("添加成功");
            }else{
                $this -> error("添加失败");
            }
		}
    }
    
	public function edit(){
		$id = I("id");
		$links = M("links");
		$data= $links -> find($id); 
		$this -> assign("links",$data);
		$this -> display();
	}
	
    public function update(){
		if($_FILES['pic']['name']){
			$upload = new \Think\Upload();
			$upload -> maxSize = 1024000000;
			$upload -> autoSub = false;
			$upload -> exts = array('jpg','gif','png','jpeg');
			$upload -> rootPath = "Public/";
			$upload -> savePath = '/Uploads/links/';
			$info = $upload -> upload();
			if(!$info){
				$this -> error($upload->getError());
			}else{
				$path = "Public/".$info['pic']['savepath'];
				$name = $info['pic']['savename'];
				$file = $path.$name;
				$image = new \Think\Image();
				$image ->open($file);
				$image -> thumb(100, 100)->save($path."s_".$name);
				unlink($file);
				$_POST['linklogo'] = $name;
			}
		}
		
		$links = M("links");
		if($links -> create()){
			if($links -> save()){
				$this -> success("修改成功！","index");
			}else{
				$this -> error("修改失败！");
			}
		}
		
    }
    
    public function del(){
		$id = I("id");
		$links = M("links");
		if($links -> delete($id)){
			$this -> success("删除成功！");
		}else{
			$this -> error("删除失败！");
		}
    }
}