<?php
namespace Admin\Controller;
use Think\Controller;

class GoodsController extends Controller{
    //商品列表显示页
    public function index(){
        $type = M('type');
        //获取商品类别
        $types = $type -> field('id,name,pid') -> order('sort ASC') -> select();
        $typeList = getList($types);
        //过滤搜索条件
        if($_GET['filter']){
            $map = json_decode(urldecode($_GET['filter']),true);
        }else if($_POST){
            foreach($_POST as $k=>$v){
                if($v){
                    //按名称搜索
                    if($k == 'name'){
                        $map[$k] = array('like', "%{$v}%");
                    }
                    //按价格搜索
                    if($k == 'minPrice'){
                        $map['saleprice'] = array('egt', "{$v}");
                        if($_POST['maxPrice']){
                            $map['saleprice'] = array(array('egt', "{$v}"), array('elt', "{$_POST['maxPrice']}"));
                        }
                    }
                    if($k == 'maxPrice'){
                        if(!$_POST['minPrice']){
                            $map['saleprice'] = array('elt', "{$v}");
                        }
                    }
                    //按类别搜索
                    if($k == 'type_id'){
                        $typeids = getChildId($types, $v);
                        if(!empty($typeids)){
                            $typeids[] = $v;
                            $typeIdList = $typeids;
                        } else {
                            $typeIdList[] = $v;
                        }
                        $map['type_id'] = array('in', $typeIdList);
                    }
                    //按状态搜索
                    if($k == 'state' || $k == 'isbest' || $k == 'ishot'){
                        $map[$k] = array('eq', $v);
                    }
                }
            } 
        }else{
            $map = null;
        }
        //获取商品列表信息
        $goods = M("goods");
        $total = $goods -> where($map) -> count();
        $list = $goods -> where($map) -> page($_GET['p'],10) -> order('id DESC') -> select();
        $Page = new \Think\Page($total,10);
        $Page -> parameter = array('filter' => urlencode(json_encode($map)));//给分页类传入搜索条件，保持搜索
        $show = $Page -> show();

        
        $this -> assign('types',$typeList);
        $this -> assign('goods',$list);
        $this -> assign('page',$show);
        $this -> display();
    }
    
    //商品添加页
    public function add(){
        $attrs = M("attrvalue") -> select();//获取商品属性
        $types = M('type');
        $data = $types -> field('id,name,pid') -> select();//获取商品类别
        $typeList = getList($data);//类别排序

        $this -> assign('types',$typeList);
        $this -> assign('attrs',$attrs);
        $this -> display();
    }
    
    //商品详情页
    public function descr(){
        $goods = M('goods');
        $id = I('id');
        $data = $goods -> find($id);//查询一条商品信息
        $type = M('type') -> find($data['type_id']);
        $brand = M('brands') -> find($data['brand_id']);
        $data['typename'] = $type['name'];
        $data['brandname'] = $brand['name'];
        $this -> assign('good',$data);
        $this -> display();
    }
    
    //商品修改页
    public function edit(){
        $id = I('id');
        $goods = M('goods') -> find($id);
        $type = M('type');
        $types = $type -> field('id,pid,name') -> select();
        $typeList = getList($types);//得到商品分类排序列表
        $typeParentId = getParentId($types, $goods['type_id']).','.$goods['type_id'];//获取前辈类ID字符串
        $brands = M('brands') -> where("type_id in ({$typeParentId})") -> select();//查询当前分类下所有品牌

        $this -> assign('types',$typeList);
        $this -> assign('brands',$brands);
        $this -> assign('goods',$goods);
        $this -> display();
    }
    
    //商品添加方法
    public function insert(){
        $msg = '商品添加成功<br/>';//定义提示信息
        //定义主图上传参数
        $upload = new \Think\Upload();
        $upload -> maxSize = 1024000000;
        $upload -> autoSub = false;
        $upload -> exts = array('jpg','gif','png','jpeg');
        $upload -> rootPath = "Public/";
        $upload -> savePath = '/Uploads/goods/';//主图保存地址
        $info = $upload -> uploadOne($_FILES['pic']);//上传图片
        if(!$info){
            $this -> error($upload -> getError());//如果图片上传失败获取失败信息
        }
        //图片缩放
        $file = "Public/".$info['savepath'].$info['savename'];
        $image = new \Think\Image();
        $image ->open($file);
        $image -> thumb(350, 350)->save("Public/".$info['savepath']."s_".$info['savename']); 
        $image -> thumb(220, 282)->save("Public/".$info['savepath']."l_".$info['savename']); 
        $image -> thumb(50, 50)->save("Public/".$info['savepath']."a_".$info['savename']); 
        
        //添加商品表
        $goods = M('goods');
        //自定义组装添加数据,防止create()转义html标签
        $good['state'] = $_POST['state'];
        $good['isbest'] = $_POST['isbest'] ? $_POST['isbest'] : '1';
        $good['ishot'] = $_POST['ishot'] ? $_POST['ishot'] : '1';
        $good['psn'] = time();
        $good['name'] = $_POST['name'];
        $good['saleprice'] = $_POST['saleprice'];
        $good['type_id'] = $_POST['type_id'];
        $good['brand_id'] = $_POST['brand_id'];
        $good['costprice'] = $_POST['costprice'];
        $good['pic'] = $info['savename'];
        $good['descr'] = $_POST['editorValue'];
        $good['addtime'] = time();
        //添加到数据库并判断结果，错误返回错误信息
        $goods_id = $goods -> add($good);//添加成功返回商品ID
        if(!$goods_id){
            $this -> error('商品添加失败！');
        }
        
        //添加商品属性
        $postColor = $_POST['colors'];//获取填写的商品属性信息
        $postSize = $_POST['sizes'];
        $postPrice = $_POST['price'];
        $postNum = $_POST['num'];
        
        //循环把信息添加到数据库
        $attr = M('attr');
        $goodsAttr['goods_id'] = $goods_id;
        $attrmsg = "属性添加失败信息：";//定义失败提示信息
        $num = 0;//定义变量作为价格和库存下标，默认0
        for($i = 0; $i < count($postColor); $i++){
            for($k = 0; $k < count($postSize); $k++){
                $goodsAttr['attrvalue_id'] = $postColor[$i].','.$postSize[$k];
                $goodsAttr['price'] = $postPrice[$num];
                $goodsAttr['stock'] = $postNum[$num];
                if(!$attr -> add($goodsAttr)){
                    $amsg .= "颜色：{$postColor[$i]} 尺码：{$postSize[$k]} 价格：{$goodsAttr['price']} 库存：{$goodsAttr['stock']} <br />";
                }
                $num++;//每次循环下标+1；从0开始
            }
        }
        if(!$amsg){
            $attrmsg = null;//如果没有错误，赋空值
        }
        
        //添加商品图库
        if(!$_POST['imagesName']){
            $this -> success($msg);//判断是否有描述图
        }
        $goodspic = M('goodspics');
        $imgs['goods_id'] = $goods_id;//组合添加数据
        $imgsName = explode(',',$_POST['imagesName']);//拆分描述图地址为数组
        //循环遍历添加到数据库
        foreach($imgsName as $k => $v){
            $imgs['pic'] = $v;
            $insertId = $goodspic -> add($imgs);
            //判断是否添加成功，返回错误信息
            if(!$insertId){
                $msg .= '第'.($k + 1).'张图片上传失败<br/>';
            }
        }
        $msg = $msg.$attrmsg.$amsg;//组合提示信息
        //$this -> redirect('add', '', 3, $msg);//跳转到商品属性添加页
        $this -> success($msg);//跳转到商品属性添加页
    }
    
    //商品基本信息修改方法
    public function update(){
        $id = I('id');
        $goods = M('goods');//实例化商品表
        $descr = $goods -> field('descr,pic') -> find($id);//查询商品描述、主图
        //判断是否有修改主图
        if(!$_FILES['pic']['name']){
            $good['pic'] = $descr['pic'];
        } else {
            //定义主图上传参数
            $upload = new \Think\Upload();
            $upload -> maxSize = 1024000000;
            $upload -> autoSub = false;
            $upload -> exts = array('jpg','gif','png','jpeg');
            $upload -> rootPath = "Public/";
            $upload -> savePath = '/Uploads/goods/';//主图保存地址
            $info = $upload -> uploadOne($_FILES['pic']);//上传图片
            if(!$info){
                $this -> error($upload -> getError());//如果图片上传失败获取失败信息
            }
            //图片缩放
            $file = "Public/".$info['savepath'].$info['savename'];
            $image = new \Think\Image();
            $image ->open($file);
            $image -> thumb(350, 350)->save("Public/".$info['savepath']."s_".$info['savename']); 
            $image -> thumb(220, 282)->save("Public/".$info['savepath']."l_".$info['savename']); 
            $image -> thumb(50, 50)->save("Public/".$info['savepath']."a_".$info['savename']); 
            @unlink("Public/Uploads/goods/{$descr['pic']}");
            @unlink("Public/Uploads/goods/s_{$descr['pic']}");
            @unlink("Public/Uploads/goods/l_{$descr['pic']}");
            @unlink("Public/Uploads/goods/a_{$descr['pic']}");
            $good['pic'] = $info['savename'];
        
        }
        
        //组合商品信息
        $good['id'] = $_POST['id'];
        $good['name'] = $_POST['name'];
        $good['saleprice'] = $_POST['saleprice'];
        $good['type_id'] = $_POST['type_id'];
        $good['brand_id'] = $_POST['brand_id'];
        $good['costprice'] = $_POST['costprice'];
        $good['descr'] = $_POST['editorValue'];
        
        //修改数据库信息并判断执行结果
        if(!$goods -> save($good)){
            $this -> error('修改失败！');
        }

        //判断商品描述是否改变
        if($good['descr'] == $descr['descr']){
            $this -> success('修改成功！');
            exit;
        }
        //添加商品图库
        $msg = '商品修改成功<br/>';//定义提示信息
        //判断是否有描述图
        if(!$_POST['imagesName']){
            $this -> success($msg);
            exit;
        }
        $goodspic = M('goodspics');
        $data = $goodspic -> where("goods_id = {$id}") -> select();
        //遍历得到商品图库
        foreach($data as $v){
            $pics[] = $v['pic'];
        }
        $imgs['goods_id'] = $id;//组合添加数据
        $imgsName = explode(',',$_POST['imagesName']);//拆分描述图名称为数组
        //循环遍历添加到数据库
        foreach($imgsName as $k => $v){
            if(!in_array($v ,$pics)){
                $imgs['pic'] = $v;
                $insertId = $goodspic -> add($imgs);
                //判断是否添加成功，返回错误信息
                if(!$insertId){
                    $msg .= '第'.($k + 1).'张图片上传失败<br/>';
                }
            }
        }
        $this -> success($msg);
    }
    
    //商品删除方法
    public function del(){
        $id = i('id');
        $goods = M('goods');
        if($goods -> delete($id)){
            $this -> success('删除成功！');
        } else {
            $this -> error('删除失败！');
        }
    }

    //商品SKU属性修改方法
    public function editAttr(){
        $id = I('id');
        $attrs = M("attrvalue") -> select();//获取商品属性
        $attr = M('attr');
        $data = $attr -> where("goods_id = {$id}") -> select();
        //遍历得到商品属性ID字符串
        foreach($data as $v){
            $goodsAttr = explode(',', $v['attrvalue_id']);//组合得到商品属性ID数组
            $v['color'] = $goodsAttr[0];
            $v['size'] = $goodsAttr[1];
            $goods[] = $v;
        }
        
        //循环得到自定义数组，下标为商品属性ID ，值为属性值
        foreach($attrs as $v){
            $attrList[$v['id']] = $v['attrvalue'];
        }
        
        $this -> assign('goods', $goods);
        $this -> assign('attrs',$attrList);
        $this -> assign('attrAll',$attrs);
        $this -> display();
    }
    
    //添加商品属性
    public function insertAttr(){
        unset($_POST['id']);//删除接收的ID值
        unset($_POST['stock']);//删除接收的stock值
        //添加商品属性
        //dump($_POST);exit;
        $postColor = $_POST['colors'];//获取填写的商品属性信息
        $postSize = $_POST['sizes'];
        $postPrice = $_POST['price'];
        $postNum = $_POST['num'];
        
        //循环把信息添加到数据库
        $attr = M('attr');
        $goodsAttr['goods_id'] = $_POST['goods_id'];
        $msg = '添加成功<br/>';//定义提示信息
        $attrmsg = "属性添加失败信息：";//定义失败提示信息
        $num = 0;//定义变量作为价格和库存下标，默认0
        for($i = 0; $i < count($postColor); $i++){
            for($k = 0; $k < count($postSize); $k++){
                $goodsAttr['attrvalue_id'] = $postColor[$i].','.$postSize[$k];
                $goodsAttr['price'] = $postPrice[$num];
                $goodsAttr['stock'] = $postNum[$num];
                if(!$attr -> add($goodsAttr)){
                    $amsg .= "颜色：{$postColor[$i]} 尺码：{$postSize[$k]} 价格：{$goodsAttr['price']} 库存：{$goodsAttr['stock']} <br />";
                }
                $num++;//每次循环下标+1；从0开始
            }
        }
        $this -> success($msg);
    }

    //修改商品属性
    public function updateAttr(){
        $attr = M('attr');
        if($attr -> create()){
            if($attr -> save()){
                $this -> ajaxReturn('修改成功');
            } else {
                $this -> ajaxReturn('修改失败');
            }
        } else {
            $this -> ajaxReturn('修改失败');
        }
    }
    
    //删除商品属性
    public function delAttr(){
        $id = I('id');
        $attr = M('attr');
        if($attr -> delete($id)){
            $this -> ajaxReturn('删除成功');
        } else {
            $this -> ajaxReturn('删除失败');
        }
    }
    
    public function editState(){
        $data['id'] = $_POST['id'];//ID存储
        unset($_POST['id']);//删除ID键值对
        $goods = M('goods');
        //遍历得到要修改的状态
        foreach($_POST as $k => $v){
            $data[$k] = $v;
            //更改状态
            if($data[$k] == 1){
                $data[$k] = 2;
            } else {
                $data[$k] = 1;
            }
        }
        //修改数据库信息
        if($goods -> save($data)){
            $this -> ajaxReturn(1);
        } else {
            $this -> ajaxReturn(0);
        }
    }
    
    //商品图库控制器
    public function pics(){
        $id = I('id');
        $data = getData('goodspics',"goods_id = {$id}");
        $this -> assign('pics', $data);
        $this -> display();
    }
    
    //ajax 请求，返回品牌数据
    public function getBrands(){
        $id = I('id');
        $types = M('type') -> field('id,pid') -> select();
        $typeId = getTopId($types, $id);//获取顶级分类
        if(!$typeId){
            $typeId = $id;//如果没有父类，去本类
        }
        $brands = M("brands");
        $data = $brands -> field('id,type_id,name') -> select();
        //遍历得到含有类别$typeId的品牌
        foreach($data as $v){
            $index = strpos($v['type_id'], "{$typeId}");//返回查找的类ID在品牌type_id 字串中出现位置
            //判断品牌中是否有查找的类，有则组合进数组，(二维)
            if($index !== false){
                $brandList['id'][] = $v['id'];
                $brandList['name'][] = $v['name'];
            }
        }
        $this -> ajaxReturn($brandList);//ajax返回所需品牌数据
    }
    
    //ajax 请求，返回商品属性值数据
    public function getAttrs(){
        $types = M("type");
        $data = $types -> field('id,pid') -> select();
        $id = I('id');//获取接受的类ID
        $typesId = getParentId($data, $id);//获取父类ID
        $typesId = rtrim($typesId, ',').",".$id;//安全过滤父类ID，组合本类ID，得到类ID字符串
        
        $map['attrname'] = I("attrname");//获取查询商品属性条件
        $map['type_id'] = array('in', $typesId);
        $attrvalue = M("attrvalue");
        $data = $attrvalue -> where($map) -> field('id,attrvalue') -> select();

        $this -> ajaxReturn($data);//ajax返回数据
    }
     
 
    //编辑器方法
    public function ueditor(){
    	$data = new \Org\Util\Ueditor();
		echo $data->output();
    }
    
    /* //商品描述图片上传方法
    public function upload(){
        $upload = new \Think\Upload();
        $upload -> maxSize = 2000000;
        $upload -> autoSub = false;
        $upload -> exts = array('jpeg','jpg','png','gif','bmp');
        $upload -> rootPath = "Public/";
        $upload -> savePath = "/uploads/";
        $info = $upload -> uploadOne($_FILES['upfile']);
        //判断上传结果，返回相应信息
        if(!$info){
            echo json_encode(array("state" => $upload -> getError()));
        }else{
            echo json_encode(array(
                "originalName" => $info[name],
                "name" => $info['savename'],
                "url" => $info['savepath'].$info['savename'],
                "size" => $info['size'],
                "type" => $info['type'],
                "state" => 'SUCCESS'
            ));
        }
        
        //$goods = 
        /* return array(
            "originalName" => $this->oriName ,//原始文件名
            "name" => $this->fileName ,//新文件名
            "url" => $this->fullName ,//完整文件名,即从当前配置目录开始的URL
            "size" => $this->fileSize ,//文件大小
            "type" => $this->fileType ,//文件类型
            "state" => $this->stateInfo//上传状态信息
        ); 
    }
    */

}