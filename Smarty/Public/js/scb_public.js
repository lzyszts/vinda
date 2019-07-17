/*
 * 保持弹出框在中间方法
 */
function keep_body(){
	$('.modal').css('display', 'block');  
	var body_height=$('.modal').height(); //模态框
	var clock_height=$('.modal-dialog').height();//窗口声明
	var new_margin_top=(body_height-clock_height)/2;
	$('.modal-dialog').css('margin-top',new_margin_top);
}


/*
*	反过滤
*/

function htmlspecialchars_decode(str){           
	  str = str.replace(/&amp;/g, '&'); 
	  str = str.replace(/&lt;/g, '<');
	  str = str.replace(/&gt;/g, '>');
	  str = str.replace(/&quot;/g, "''");  
	  str = str.replace(/&#039;/g, "'");  
	  return str;  
}

/*
 * 抖动
 */
function shake(o){   
    var $panel = $(o);   
    box_left = 0;   
    $panel.css({'left': box_left,'position':'relative'});   
    for(var i=1; 4>=i; i++){   
        $panel.animate({left:box_left-(40-10*i)},50);   
        $panel.animate({left:box_left+2*(40-10*i)},50);   
    }   
}  

/*传入#id或者.class
 * 如果为空则增加红色警告框同时焦点
 */
function checkEmpty(name){
	if($.trim($(name).val())==""){
		$(name).parent().addClass("has-error");
		$(name).focus();
		return false;
	}else{
		return true;
	}
}

/*
 * 验证Easyui的input框是否为空,
 * 根据name属性找元素
 */
function easyUiEmpty(name){
	var value=$("input[name='"+name+"']").val();
	if(value=="" || typeof(value)=="undefined"){
		return false;
	}else{
		return true;
	}
}


/*
 * 创建模态框  
 * 动态生成带dynamicMoadl类名
 * className 整体类名
 * title 标题
 */
function creatModal(className,title,width){
	var width=width?width:"";
	var str="<div class='modal fade dynamicMoadl "+className+"' data-backdrop='static' >";
	str+="<div class='modal-dialog sm-lg' style='width:"+width+"'>";
	str+="<div class='modal-content'>";
	str+="<div class='modal-header'>";
	str+="<button class='close' data-dismiss='modal'><span>&times;</span></button>";
	str+="<h4 class='modal-title'>"+title+"</h4></div>";
	//中
	str+="<div class='modal-body'></div>";
	//下
	str+="<div class='modal-footer'></div></div></div></div>";
	return str;
}
/*
 * 创建input
 * id  
 * className   ("className1 className2")
 */
function creatInput(type,className,id,value,name){
	var id=id?id:"";
	var name=name?name:"";
	var value=value?value:"";	
	var btn="<input class='"+className+"'";
	btn+=" type='"+type+"'";
	if(id!="")
	{
		btn+=" id='"+id+"'";
	}
	if(value!="")
	{
		btn+=" value='"+value+"'";
	}
	if(name!="")
	{
		btn+=" name='"+name+"'";
	}
	btn+=">";
	return btn;
}


/*
 * 模态框关闭
 */
$(document).on("click",".close",function(){
	$(this).parents(".dynamicMoadl").remove();
	$(".modal-backdrop").remove();
});














