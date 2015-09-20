<?php
namespace Admin\Controller;
use Think\Controller;

class TypeController extends Controller{
    public $listall = array();
    public function index(){
        $types = M("type");
        $type = $types -> select();
        foreach($type as $v){
            if($v['pid'] == 0){
                $this -> listall[] = $v;
                $this -> recursive($v['id']);
            }
        }
        dump($listall);exit;
        $this -> assign("types",$data);
        $this -> display();
    }
    
    public function add(){
        $types = M("type");
        $data = $types -> field("id,name") -> select();

        $this -> assign("types",$data);
        $this -> display();
    }
    
    public function insert(){
        $types = M("type");
        //判断是否有父类
        if($_POST['pid']){
            $id = $_POST['pid'];
            $p = $types -> find($id);
            $_POST['path'] = $p['path'].$id.",";//设置路径
            $_POST['layer'] = $p['layer'] + 1;//设置级别
        }
        //添加数据并判断解果
        if($types -> create()){
            if($types -> add()){
                $this -> success("添加成功！","{$_SERVER['HTTP_REFERER']}",'0');
            }else{
                $this -> error("添加失败");
            }
        }
    }
    
    protected function recursive($id){
        $types = M("type");
        $data = $types -> where("pid={$id}") -> select();
    }
}