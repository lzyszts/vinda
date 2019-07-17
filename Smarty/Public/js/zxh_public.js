/*
 * F5刷新当前框架页
 */
$(document).keydown(function(e) {
	e = window.event || e;
	if (e.keyCode == 116) 
	{	
		e.preventDefault();
		location.reload();
	}
});

/*
 * 点击时iframe时关闭父窗口的下拉
 */
$('html').click(function(){
	$('.dropdown', parent.document).removeClass('open');
});


/*
 * 保持弹出框在中间方法
 */
function keep_body(){
	var body_height=$('.modal').height(); //模态框
	var clock_height=$('.modal-dialog').height();//窗口声明
	var new_margin_top=(body_height-clock_height)/2;
	$('.modal-dialog').css('margin-top',new_margin_top);
}
/*
 * 过滤 转义
 */
function htmlspecialchars_decode(str){           
  str = str.replace(/&amp;/g, '&'); 
  str = str.replace(/&lt;/g, '<');
  str = str.replace(/&gt;/g, '>');
  str = str.replace(/&quot;/g, "''");  
  str = str.replace(/&#039;/g, "'");  
  return str;  
}
function htmlspecialchars(str){            
  str = str.replace(/&/g, '&amp;');  
  str = str.replace(/</g, '&lt;');  
  str = str.replace(/>/g, '&gt;');  
  str = str.replace(/"/g, '&quot;');  
  str = str.replace(/'/g, '&#039;');  
  return str;  
}  