//公司及部门目标新增
$('#head1 div:eq(0)').mousedown(function(){
	$(this).addClass('clickhover');
	var $newtr=$('#target_table .two_tr').clone(true);
	$newtr.removeClass('two_tr');  
	$newtr.removeAttr('style'); //移除高度
	$('#target_table').append($newtr);	
});
$('#head1 div').mouseup(function(){
	$(this).removeClass('clickhover');
});
//公司及部门目标删除





//个人目标和评估新增
$('#head2 div:eq(0)').mousedown(function(){
	$(this).addClass('clickhover');
	var $newtr=$('#grade_table .two_tr').clone(true);
	$newtr.removeClass('two_tr');
	$('#grade_table').append($newtr);	
});
$('#head2 div').mouseup(function(){
	$(this).removeClass('clickhover');
});




 
/*
*  全选反选
*/
$('.ckbox_input').click(function(){
if($(this).prop('checked')==true){
	$(this).parent().parent().parent().find('.ckbox').prop('checked',true);
}else{
	$(this).parent().parent().parent().find('.ckbox').prop('checked',false);
}
}); 
/*
*  点td也触发勾选
*/
$('.ckbox_td').click(function(){
  $(this).children().trigger('click');
});
$('.ckbox,.ckbox_input').click(function(){
  $(this).trigger('click');
});


/*
 * 传入需要初始化的table的id数组
 * 1、根据这个table下th遍历出width复制给次table和第一个tr(固定首行)
 * 2、初始化把固定首行的高度赋值给第2行
 * 3、复制固定首行的td的width赋值给第2行的td
 */
function load_width(arr){
	for(var i=0;i<arr.length;i++)
	{
		var id=arr[i];
		//1、遍历th计算出总width
		$("#"+id+" th").each(function(i,item){
			var str=$(item).attr('style');         
			var num=parseFloat(str.replace("width:","")); //去掉width:后转数字
			$("#"+id+" .two_tr td")[i].style.width=num+"px"; //把第二个tr下面的td宽度也改了
			width+=parseFloat(num);				
		});
		//2、修改target_table和固定tr的width
		$("#"+id+"").css('width',width);
		$("#"+id+" .top_tr").css('width',width);
		width=0;
		//3、初始化table 1 2行高度,把固定行的高度赋值给第2行
		$("#"+id+" tr:eq(1)").height($("#"+id+" tr:eq(0)").height());
	}		
}

//滚动时第一行固定
$('.table_body').scroll(function(){
	if(pre_scrollTop!=$(this).scrollTop())
	{
		pre_scrollTop=$(this).scrollTop();
	}

	$(this).find('.top_tr').css('top',pre_scrollTop+"px");
});


/*******************验证***********************/

//是否存在方案表
$('#case_name').blur(function(){
	check_casename();
});
