<?php
/**
 *  查询表数据
 *@param $table string 表名
 *@param $where string、array 条件
 *@param $table string 字段
 *@return array 返回结果集
 */

function getData($table, $where = '', $order = ''){
    $tables = M("{$table}");
    $data = $tables -> where($where) -> order("{$order}") -> select();
    return $data;
}

/**
 *  无限分类列表
 *@param $types array 分类结果集
 *@param $html string 子级分类填充字符串
 *@param $pid int 父类id
 *@param $num int 填充字符串个数
 *@return array 返回排序后结果集
 */
function getList($types, $html = '----', $pid = 0, $num = 0){
    $arr = array();
    foreach($types as $v){
        if($v['pid'] == $pid){
            //$v['num'] = $num + 1;//可做自定义级别使用
            $v['html'] = str_repeat($html, $num);//填充字符串个数
            $arr[] = $v;
            $arr = array_merge($arr, getList($types, $html, $v['id'], $num + 1));//递归把子类压入父类数组后
        }
    }
    return $arr;
}

/**
 *  无限分类级别排序    子类作为父类的数组值的多维数组
 *@param $types array 分类结果集
 *@param $name string 自定义子类下标名称
 *@param $pid int 父类id
 *@return array 返回级别排序后结果集(多维数组)
 */
function getLayer($types, $name = 'child', $pid = 0){
    $arr = array();
    foreach($types as $v){
        if($v['pid'] == $pid){
            $v[$name] = getLayer($types, $name, $v['id']);//递归 把子类作为数组值压入数组中
            $arr[$v['id']] = $v;
        }
    }
    return $arr;
}

/**
 *  获取子类ID    
 *@param $types array 分类结果集
 *@param $pid int 父类id
 *@return array 返回子类ID数组(一维数组)
 */
function getChildId($types, $pid = 0){
    $arr = array();
    foreach($types as $v){
        if($v['pid'] == $pid){
            $arr[] = $v['id'];
            $arr = array_merge($arr, getChildId($types, $v['id']));
        }
    }
    return $arr;
}

/**
 *  获取前辈ID (字串)
 *@param $types array 分类结果集
 *@param $id int 本类id
 *@return string 返回前辈类ID字符串
 */
function getParentId($types, $id){
    $str = '';
    foreach($types as $v){
        if($v['id'] == $id){
            if($v['pid'] != 0){
                $str .= $v['pid'].",";
                $str .= getParentId($types, $v['pid']);
            }
        }
    }
    $str = trim($str, ',');
    return $str;
}


/**
 *  获取前辈ID   (ID号)
 *@param $types array 分类结果集
 *@param $id int 本类ID
 *@param $pid int 要获取分类的父ID
 *@return int 返回类ID
 */
function getTopId($types, $id, $pid = 0){
    $str = '';
    foreach($types as $v){
        if($v['pid'] != $pid){
            if($v['id'] == $id){
                $str = $v['pid'];
                $st = getTopId($types, $v['pid']);
                if($st){
                    $str = $st;
                }
            }
        }
    }
    return $str;
}
