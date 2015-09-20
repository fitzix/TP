<?php
namespace Admin\Controller;
use Think\Controller;

class PostsController extends Controller{
	
	//展示图列表页
	public function index(){
		$posts = M('posts');
		$count = $posts -> count();
		$Page = new \Think\Page($count,3);
		$show = $Page -> show();
		$list = $posts -> order('addtime') -> limit($Page->firstRow.','.$Page->listRows) -> select();
		$this -> assign('posts',$list);
		$this -> assign('page',$show);
		$this -> display();
	}
	
	//展示图修改页面
	public function edit(){
		$id = I('id');
		//exit;
		$posts = M('posts');
		$data = $posts -> find($id);
		//dump($data);
		//exit;
		$this -> assign('post',$data);
		$this -> display();
	}
	
	//将修改信息添加到数据库
	public function update(){
		//echo $a = $_GET['type'];
		//echo $id = $_GET['id'];
		$posts = M('posts');
		$data['id'] = $_GET['id'];
		$data['state'] = $_GET['type'];
			if($posts -> create($data)){
				if($posts -> save()){
					$this -> success('修改成功！');
					exit;
					//echo "1";
				}else{
					$this -> success('修改失败！');
					exit;
					//echo "2";
				}
			}
		
	}
	
	//获取添加页面
	public function add(){
		$this -> display();
	}
	
	//将信息添加到数据库
	public function insert(){
		$upload = new \Think\Upload();
		$upload -> autoSub = false;
		$upload -> maxSize = 3145782;
		$upload -> exts = array('jpg','gif','png','jpeg');
		$upload -> rootPath = 'Public';
		$upload -> savePath = '/Uploads/posts/';
		$info = $upload -> upload();
		if(!$info){
			$this -> error($upload->getError());
		}else{
			//文件上传成功则生成缩略图，并把图片，标题等信息加入到数据库
			foreach($info as $file){ 
			  	$img ="Public".$file['savepath'].$file['savename'];
                $image = new \Think\Image();
				$image -> open($img);
				$img2 = "Public".$file['savepath'].'s_'.$file['savename'];
				$img3 = "Public".$file['savepath'].'a_'.$file['savename'];
			    $image -> thumb(730,454) -> save($img2); //在缩略图前加前缀 “s_” ！
			    $image -> thumb(150,150) -> save($img3); //在缩略图前加前缀 “s_” ！
				@unlink($img);
                $_POST['pic'] = $file['savename'];
				$_POST['addtime'] = time();
				$_POST['user_id'] = 0;
				$posts = M('posts');
				if($posts -> create()){
					if($posts -> add()){
						$this -> redirect('posts/add'); 
					}else{
						$this -> redirect('posts/add'); 
					}
				}
              				
			}
		}
	}
	
	//展示图删除操作
	public function del(){
		$id = I('id');
		$posts = M('posts');
        $data = $posts -> find($id);
		if($posts -> delete($id)){
            @unlink("Public/Uploads/posts/s_{$data['pic']}");
            @unlink("Public/Uploads/posts/a_{$data['pic']}");
			$this -> redirect('posts/index');
		}else{
			$this -> error('删除失败！');
		}
	}
	
	
	
	
	
	
	
	
}