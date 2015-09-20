<?php
/**
 * @param 商品类别管理控制器
 */
namespace Admin\Controller;
use Think\Controller;

class TypeController extends Controller{
    //商品类别列表显示页控制器
    public function index(){
        $types = M("type");
        $data = $types -> order("sort ASC") -> select();
        $arr = getList($data, '　　');//获取无限分类排序列表结果集
        $typeList = getLayer($data);
        $this -> assign("typeList",$typeList);
        $this -> assign("types",$arr);
        $this -> display();
    }
    
    //商品类别添加显示页控制器
    public function add(){
        $types = M("type");
        $data = $types -> field("id,name") -> select();//查询类别ID、NAME值
        $type = array();//自定义数组，键为分类id , 值为分类名
        foreach($data as $v){
            $type[$v['id']] = $v['name'];//给数组赋值
        }
        $type[0] = '顶级分类';//定义顶级分类id为0
        
        $this -> assign("types",$type);
        $this -> display();
    }
    
    //商品类别添加方法控制器
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
                $this -> error("添加失败！");
            }
        }
    }
    
    //商品类别修改显示页控制器
    public function edit(){
        $id = I('id');
        $data = M('type') -> find($id);
        $this -> assign('type', $data);
        $this -> display();
    }
    
    //商品类别修改方法控制器
    public function update(){
        $types = M("type");
        //组合修改数据，判断修改结果
        if($types -> create()){
            
            if($types -> save()){
                $this -> success('修改成功');
            }else{
                $this -> error($types -> getError());
            }
            
        } else {
            $this -> error('修改失败！');
        }
    }
    
    //商品类别删除方法控制器
    public function del(){
        $types = M("type");
        $goods = M('goods');
        $id = I('id');//获取要删除的ID；
        $typesId = $types -> field('id') -> where("path like '%,{$id},%'") -> select();//查询子类ID

        //判断是否有子类，有则返回错误信息
        if($typesId){
            $this -> error('有子类无法删除！');
        }
        $strId = '';//定义字符串存放ID，
        foreach($typesId as $v){
            $strId .= $v['id'].",";//遍历子类ID数组，用 , 号组合子类ID字符串
        }
        $strId .= $id;//把要删除的类ID压入字串中

        $goodsNum = $goods -> where("type_id in ({$strId})") -> count();//查询类别下商品数量
        
        if($goodsNum){
            $this -> error('本类或子类中有商品，无法删除！');
        }
        
        if($types -> delete($id)){
            $this -> success('删除成功！');
        } else {
            $this -> error('删除失败！');
        }
    }
    
    //商品类别排序方法控制器
    public function getSort(){
        $types = M("type");
        //遍历排序数据
        foreach($_POST as $id => $sort){
            if($sort){
                $types -> where(array('id' => $id)) -> setField('sort',$sort);//修改排序字段
            }
        }
        
        $this -> redirect(index);//跳转到分类列表显示控制器
    }
 
}