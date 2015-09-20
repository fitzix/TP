<?php
namespace Admin\Controller;
use Think\Controller;

class BrandsController extends Controller{
	public function index(){
        $types = M('type');
        $data = $types -> field('id,name,pid') -> select();//查询类别信息
        $typeList = getList($data);
        $typeId = array();//自定义数组，下标为类别ID 值为类别名称
        foreach($data as $v){
            $typeId[$v['id']] = $v['name'];
        }
        
		$brands = M("brands");
        //获取搜索条件
        if($_GET['type_id']){
            $map['type_id'] = $_GET['type_id'];//组装搜索条件
        }
        
		$total = $brands -> where($map) -> count();//查询数据总条数
		$Page = new \Think\Page($total, 10, $map);//实例化分页类
		$data = $brands -> where ($map) -> order('sort ASC') -> page($_GET['p'],10) -> select();
		$show = $Page -> show();//分页显示输出
		
        $type_id = $brands -> field('id,type_id') -> select();
        foreach($type_id as $v){
            $types = explode(',', $v['type_id']);
            for($i = 0; $i < count($types); $i++){
                $typename[$v['id']][] = $types[$i];
            }
        }
        $this -> assign('typename',$typename);
        $this -> assign("type",$typeId);
        $this -> assign("typeList",$typeList);
        $this -> assign("brands",$data);
		$this -> assign("page",$show);
		$this -> display();
	}
	
		//商品品牌的添加
	public function add(){
		$type = M("type");
		$data = $type -> where('pid = 0') -> order('sort ASC') -> select();
        //$typeList = getList($data);
		$this -> assign("types",$data);
		$this -> display();
	}
	
	public function insert(){
		$brands = M("brands");
		if($_FILES['logo']['name']){
			$upload = new \Think\Upload();
			$upload -> maxSize = 1024000000;
			$upload -> autoSub = false;
			$upload -> exts = array('jpg','gif','png','jpeg');
			$upload -> rootPath = "Public/";
			$upload -> savePath = '/Uploads/brands/';
			$info = $upload -> upload();
			if(!$info){
				$this -> error($upload->getError());
			}else{
				$path = "Public/".$info['logo']['savepath'];
				$name = $info['logo']['savename'];
				$file = $path.$name;
				$image = new \Think\Image();
				$image ->open($file);
				$image -> thumb(120, 60)->save($path."s_".$name);
				@unlink($file);
				$_POST['logo'] = $name;
			}
		}
        $_POST['type_id'] = implode(',', $_POST['type_id']);//得到类别ID字串
		if($brands -> create()){
			if($brands -> add()){
                $this -> success("添加成功");
            }else{
                $this -> error("添加失败");
            }
		}
	}
	
	public function edit(){
        $type = M("type");
		$typeList = $type -> where('pid = 0') -> order('sort ASC') -> select();//获取分类列表
		$id = I("id");
		$brands = M("brands");
		$data = $brands -> find($id);
        $type_id = explode(',', $data['type_id']);//获取品牌类别数组
        //组合数组，下标为类ID ，值为checked
        for($i = 0; $i < count($type_id); $i++){
            $state[$type_id[$i]] = 'checked';
        }
		$this -> assign("brand",$data);
		$this -> assign("types",$typeList);
        $this -> assign('state',$state);
		$this -> display();
	}
	
	public function update(){
		$type = M("type");
		$data = $type -> order('id') -> select();
		$this -> assign("types",$data);
		$brands = M("brands");
		//进行图片上传
		if($_FILES['logo']['name']){
			$upload = new \Think\Upload();
			$upload -> maxSize = 1024000000;
			$upload -> autoSub = false;
			$upload -> exts = array('jpg','gif','png','jpeg');
			$upload -> rootPath = "Public/";
			$upload -> savePath = '/Uploads/brands/';
			$info = $upload -> upload();
			if(!$info){
				$this -> error($upload->getError());
			}else{
				$path = "Public/".$info['logo']['savepath'];
				$name = $info['logo']['savename'];
				$file = $path.$name;
				//对图片进行缩放处理
				$image = new \Think\Image();
				$image ->open($file);
				$image -> thumb(120, 60)->save($path."s_".$name);
				@unlink($file);
				$_POST['logo'] = $name;
			}
		}
		
		$brands = M("brands");
        $_POST['type_id'] = implode(',', $_POST['type_id']);//得到类别ID字串
		//判断获取的数据来进行修改
		if($brands -> create()){
			if($brands -> save()){
				$this -> success("修改成功！",index);
			}else{
				$this -> error("修改失败！");
			}
		}
	}
	
	public function del(){
		$id = I("id");
		$brands = M("brands");
        $count = M('goods') -> where("brand_id = {$id}") -> count();
        if($count > 0){
            $this -> error('品牌下有商品，请先删除商品！');
        }
		//用id删除数据来进行删除
		if($brands -> delete($id)){
			$this -> success("删除成功！");
		}else{
			$this -> error("删除失败！");
		}
	}
}