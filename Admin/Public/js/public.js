
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
 * 模态框关闭
 */
$(document).on("click",".close",function(){
	$('.modal').css('display','none');
	$(this).parents(".dynamicMoadl").remove();
	$(".modal-backdrop").remove();
});

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
 * 抖动 传入jquery对象
 */
function shake($obj){   
    if($obj.is(":animated")){ 
		$obj.stop();
	}  
    box_left = 0;   
    $obj.css({'left': box_left,'position':'relative'});   
    for(var i=1; 6>=i; i++){   
        $obj.animate({left:box_left-(12-2*i)},10);   
        $obj.animate({left:box_left+2*(12-2*i)},10);   
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





$.fn.extend({
    checkform: function(){
    	var res=true;
    	$(this).find(":input").each(function(i,ele){
        	//1、验证空
        	if($(ele).attr("empty-no")=="")
        	{
        		$v=$(ele).val();
        		if($.trim($v)==""){
        			alert($(ele).attr("empty-msg"));
        			res=false;
        			return false;
        		}		
        	}
        });
    	return res;
    }
});










