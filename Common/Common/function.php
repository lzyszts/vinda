<?php

/* 1、
 * 有选择性的过滤XSS --》 说明：性能非常低-》尽量少用
 */
function removeXSS($data)
{
	require_once './public/HtmlPurifier/HTMLPurifier.auto.php';
	$_clean_xss_config = HTMLPurifier_Config::createDefault();
	$_clean_xss_config->set('Core.Encoding', 'UTF-8');
	// 设置保留的标签
	$_clean_xss_config->set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]');
	$_clean_xss_config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
	$_clean_xss_config->set('HTML.TargetBlank', TRUE);
	$_clean_xss_obj = new HTMLPurifier($_clean_xss_config);
	// 执行过滤
	return $_clean_xss_obj->purify($data);
}


/* 2、
 * 用一个表的数据生成select下拉
 * 传入表名,select的name值,表中搜索的两个值一个vlaue一个名字,默认被选中的值
 *
 */
function createSelect($tableName, $selectName, $valueFieldName, $textFieldName, $selectedValue = '')
{
    $model = D($tableName);
    $data = $model->field("$valueFieldName,$textFieldName")->select();
    $select = "<select name='$selectName'><option value=''>所有</option>";
    foreach ($data as $k => $v)
    {
        $value = $v[$valueFieldName];
        $text = $v[$textFieldName];
        if($selectedValue && $selectedValue==$value)
            $selected = 'selected="selected"';
            else
               $selected = '';
                $select .= '<option '.$selected.' value="'.$value.'">'.$text.'</option>';
    }
    $select .= '</select>';
    echo $select;
}

/*
 * 用一个表生成bootstrap版本select
 */
function createBootstrapSelect($tableName, $selectName, $valueFieldName, $textFieldName, $selectedValue = '')
{
    $model = D($tableName);
    $data = $model->field("$valueFieldName,$textFieldName")->select();
    $select = "<select name='$selectName' class='form-control'><option value=''>所有</option>";
    foreach ($data as $k => $v)
    {
        $value = $v[$valueFieldName];
        $text = $v[$textFieldName];
        if($selectedValue && $selectedValue==$value)
            $selected = 'selected="selected"';
            else
                $selected = '';
                $select .= "<option ".$selected." value='$value'>".$text."</option>";
    }
    $select .= '</select>';
    echo $select;
}

/*
 * 用一个表生成layui版本select
 */
function createLayuiSelect($tableName, $selectName, $valueFieldName, $textFieldName, $selectedValue = '')
{
    $model = D($tableName);
    $data = $model->field("$valueFieldName,$textFieldName")->select();
    $select = "<select name='$selectName' lay-filter='$selectName' >";
    foreach ($data as $k => $v)
    {
        $value = $v[$valueFieldName];
        $text = $v[$textFieldName];
        if($selectedValue && $selectedValue==$value)
            $selected = 'selected="selected"';
            else
                $selected = '';
                $select .= "<option ".$selected." value='$value'>".$text."</option>";
    }
    $select .= '</select>';
    echo $select;
}




/* 3、生成模态框
 *
 * 

function createModal($id,$width="420",$height="300",$close=true,$title="提示",$url,$input){
    //模态框声明
    $str="<div class='modal fade' id='{$id}' data-backdrop='static'>";
    //窗口声明
    $str.="<div class='modal-dialog sm-lg' style='width:{$width}px;height:{$height}px;margin:0 auto;'>";
    //内容声明
    $str.="<div class='modal-content'>";
    //上
    $str.="<div class='modal-header'>";
    //关闭按钮
    if($close){
        $str.="<button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>";
    }
    //头部标题
    $str.="<h4 class='modal-title'>{$title}</h4></div> ";
    //中
    $str.="<form action='{$url}' method='post' enctype='multipart/form-data'>";
    $str.="<div class='modal-body'>";
    做到这里了
    $str.="</div>";
    
    str+="<img src='/Admin/Public/images/loading.gif' width='100' height='100'>";
    str+="<span id='loading_msg'>"+msg+"</span>";
    str+="</div></div></div></div>";
    $('body').append(str);

}
 */



/*
 * 接收数组形式
 * array(6) {
 ["range"] => array(2) {
 [0] => string(9) "部门KPI"
 [1] => string(9) "部门KPI"
 }
 ["target"] => array(2) {
 [0] => string(0) ""
 [1] => string(27) "客户投诉次数不高于"
 }
 ["chanllenge"] => array(2) {
 [0] => string(0) ""
 [1] => string(1) "5"
 }
 ["base"] => array(2) {
 [0] => string(0) ""
 [1] => string(3) "20%"
 }
 ["xiaxian"] => array(2) {
 [0] => string(0) ""
 [1] => string(1) "3"
 }
 ["case_id"] => array(5) {
    [0] => string(2) "21"
    [1] => string(2) "21"
    [2] => string(2) "21"
    [3] => string(2) "21"
    [4] => string(2) "21"
  }
 }*/

/*
 * 循环插入二维数组元素
 * 数据数组，表名,以什么字段算长度,第几行开始
 */
function add_array($arr,$table_name,$field,$start_line=0){
    $model=D($table_name);
    $len=count($arr[$field]);
    for($i=0;$i<$len;$i++)
    {
        if($i>=$start_line)
        {
            //循环插入
            foreach ($arr as $k=>$v)
            {
                $data[$k]=$v[$i];
            }
            $model->add($data);
        }
    }
}




/*
 *  array_column 函数
 */

function i_array_column($input, $columnKey, $indexKey=null){
    if(!function_exists('array_column')){
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false;
        $indexKeyIsNull            = (is_null($indexKey))?true :false;
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false;
        $result                         = array();
        foreach((array)$input as $key=>$row){
            if($columnKeyIsNumber){
                $tmp= array_slice($row, $columnKey, 1);
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null;
            }else{
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null;
            }
            if(!$indexKeyIsNull){
                if($indexKeyIsNumber){
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && !empty($key))?current($key):null;
                    $key = is_null($key)?0:$key;
                }else{
                    $key = isset($row[$indexKey])?$row[$indexKey]:0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}


/**********时间*************/
/*
 * 生成年下拉框
 * 默认id=year,name=year
 */

function createYear($id="year",$name="year"){
    $arr_year=array('2017','2018','2019','2020','2021','2022','2023');
    $now_year=isset($_GET[$name])?$_GET[$name]:date('Y');
    $select = "<select id='$id' name='$name' class='form-control'>";
    foreach ($arr_year as $v)
    {
        if($now_year==$v)
        {
            $selected = 'selected="selected"';
        }else{
            $selected = '';
        }
        $select .= '<option '.$selected.' value="'.$v.'">'.$v.'</option>';
    }
    $select .= '</select>';
    echo $select;
}



/*
 * 生成月下拉框
 */
function createMonth($id="month",$name="month"){
    $arr_month=array(
        '1月'=>'1',
        '2月'=>'2',
        '3月'=>'3',
        '4月'=>'4',
        '5月'=>'5',
        '6月'=>'6',
        '7月'=>'7',
        '8月'=>'8',
        '9月'=>'9',
        '10月'=>'10',
        '11月'=>'11',
        '12月'=>'12',
    );
    $now_month=isset($_GET[$name])?$_GET[$name]:date('n');
    $select = "<select id='$id' name='$name' class='form-control'>";
    foreach ($arr_month as $k=>$v)
    {
        if($now_month==$v)
        {
            $selected = 'selected="selected"';
        }else{
            $selected = '';
        }
        $select .= '<option '.$selected.' value="'.$v.'">'.$k.'</option>';
    }
    $select .= '</select>';
    echo $select;
}








