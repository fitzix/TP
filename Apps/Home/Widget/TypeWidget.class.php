<?php
namespace Home\Widget;
use Think\Controller;

class TypeWidget extends Controller{
    public function index(){
        $types = M('type');
        $type = $types -> order('sort ASC') -> where('state != 2') -> select();//获取商品显示中的全部类别
        $typeList = getLayer($type);//获取排序类别数组
        $typeHosts = $types -> order('sort ASC') -> where('state = 3') -> select();//得到推荐分类
        //自定义(三维)数组存放推荐分类,一维下标为父类ID，二维下标为id和name，三维下标为数字索引
        foreach($typeHosts as $v){
            $typeHost[$v['pid']]['id'][] = $v['id'];
            $typeHost[$v['pid']]['name'][] = $v['name'];
        }
        $typeShow = $types -> order('sort ASC') -> where('state = 4 AND pid != 0') -> select();//得到首页展示小分类
        $typeIndex = $types -> order('sort ASC') -> where('state = 4 AND pid = 0') -> select();//得到首页展示小分类
        $brands = M('brands') -> field('id,logo,type_id') -> where('state = 1') -> select();
        //遍历得到二维数组$brandList，下标1：ID，值为品牌ID号；下标2：logo，值为品牌logo图
        foreach($type as $v){
            foreach($brands as $res){
                $index = strpos($res['type_id'], "{$v['id']}");
                if($index !== false){
                    $brandList[$v['id']]['id'][] = $res['id'];
                    $brandList[$v['id']]['logo'][] = $res['logo'];
                }
            }
        }
        
        $this -> assign('typeHost', $typeHost);
        $this -> assign('typeShow', $typeShow);
        $this -> assign('typeIndex', $typeIndex);
        $this -> assign('brands', $brandList);
        $this -> assign('types', $typeList);
        $this -> display('Public:type');
    }
}