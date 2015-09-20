<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * @parpam 商品属性管理控制器
 */
class AttrvalueController extends Controller{
    public function index(){
        //遍历循环搜索条件
        foreach($_GET as $k => $v){
            if($v){
                $map[$k] = $v;
            }
        }
        unset($map['p']);
        $attrvalue = M("attrvalue");
        $count = $attrvalue -> where($map) -> count();//查询数据总条数
        $data = $attrvalue -> where($map) -> order("sort ASC") -> page($_GET['p'], 10) -> select();//获取属性数据
        $Page = new \Think\Page($count, 10, $map);//给分页类传入搜索条件，维持搜索
        $show = $Page -> show();//分页显示输出
        /* //获取商品类型
        $types = M("type") -> field('id,name,pid') -> select();
        $types = getList($types);
        foreach($types as $v){
            $type[$v['id']] = $v['name'];//定义数组存放类型，键为ID，值为类名
        } */
        $this -> assign('map',$map);
        $this -> assign('page',$show);
        //$this -> assign('types',$types);
        //$this -> assign('type',$type);
        $this -> assign('attrvalues',$data);
        $this -> display();
    }
    
    public function add(){
        $types = M("type");
        $data = $types -> where('pid = 0') -> order("sort ASC") -> select();//获取类别信息
        $this -> assign('types',$data);
        $this -> display();
    }
    
    public function edit(){
        $attrvalue = M("attrvalue");
        $types = M("type");
        $id = I('id');//获取要要修改的属性ID值
        $data = $attrvalue -> find($id);//查询要修改的属性信息
        $typeList = $types -> order("sort ASC") -> select();//查询类别信息
        
        $this -> assign('types',$typeList);
        $this -> assign('attrvalue',$data);
        $this -> display();
    }
    
    public function insert(){
        $attrvalue = M("attrvalue");
        if(!$_POST['type_id']){
            unset($_POST['type_id']);
        }
        //判断并组装要添加的数据
        if($attrvalue -> create()){
            //获取要添加的数据信息
            if($_POST['type_id']){
                $typeid = $_POST['type_id'];
            }
            $name = $_POST['attrname'];
            $value = $_POST['attrvalue'];
            $msg = $this -> filter($typeid,$name,$value);//调用过滤函数，判断信息是否已存在
            if($msg){
                $this -> error($msg);//若信息已存在，返回错误信息
            }
            
            //添加并判断是否添加成功
            if($attrvalue -> add()){
                $this -> success("添加成功");
            }else{
                $this -> error("添加失败");
            }
        }
    }
    
    public function update(){
        $attrvalue = M('attrvalue');
        
        //判断并组装要修改的数据
        if($attrvalue -> create()){
            $id = $_POST['id'];//获取要添加的数据信息
            $typeid = $_POST['type_id'];
            $name = $_POST['attrname'];
            $value = $_POST['attrvalue'];
            $msg = $this -> filter($typeid,$name,$value);//调用过滤函数，判断信息是否已存在
            if($msg){
                $attr = $attrvalue -> find($id);//查询状态并判断
                if($attr['state'] == $_POST['state']){
                    $this -> error($msg);//若状态也相同，返回错误信息
                }
            }

            //修改并判断是否修改成功
            if($attrvalue -> save()){
                $this -> success("修改成功");
            }else{
                $this -> error("修改失败");
            }
        }else{
            $this -> error("没有数据");
        }
    }
    
    public function del(){
        $id = I('id');
        $attrvalue = M('attrvalue');
        $attr = M('attr') -> field('attrvalue_id,goods_id') -> select();
        foreach($attr as $v){
            $index = strpos($v['attrvalue_id'], $id);
            if($index !== false){
                $this -> error("有ID为{$v['goods_id']}商品正在使用此属性,请先修改商品属性！");
            }
        }
        if($attrvalue -> delete($id)){
            $this -> success('删除成功！');
        }else{
            $this -> error('删除失败！');
        }
        
    }
    
    /**
     * @param   过滤信息，判断是否存在，
     */
    private function filter($id,$name,$value){
        $attrvalue = M("attrvalue");
        //从数据库匹配要添加的数据信息
        $data = $attrvalue -> field('attrname,attrvalue') -> select();
        //若匹配到数据，过滤信息
        if(!$data){
            return;
        }else{
            //若信息以存在，退出程序，提示信息已存在
            foreach($data as $v){
                if($v['attrname'] == $name && $v['attrvalue'] == $value){
                    return ("信息已存在");
                }
            }
        }
    }
}