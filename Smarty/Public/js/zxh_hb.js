/*
 * 上传文件显示名字
 */
$('.upload').bind('change',function(){
	$('.jiben_filename').val($(this).val());
});


//提交事件
$('.jiben_submit').click(function(e){
	$(this).addClass('clicksave');
	if(confirm('提交后将无法修改,确定提交吗？')){
		$(this).removeClass('clicksave');
		return check();
	}else{
		$(this).removeClass('clicksave');
		return false;
	}
});

/*
 * 提交验证为空函数
 */
function check(){
	//基本信息的input框
	var $inp=$('#userinfo input');
	for(var i=0;i<$inp.length;i++){
		if($inp[i].value==""){
			$($inp[i]).addClass('warningclass');
			$('#jiben_warning').show();
			return false;
		}
	}
}

/*
 * 鼠标点击基本信息input框时
 */
$('#userinfo :text').not($('.readonly')).bind('click',function(){
	//隐藏警告，清除焦点class和警告class
	$('#jiben_warning').hide();	
	$(this).removeClass('blurclass');
	$(this).removeClass('warningclass');
	$(this).addClass('focusclass');	
}); 

//失去焦点input框时 延迟判断 除了序号，名字，部门，填写日期
	$('#userinfo :text').not($('.readonly')).bind('blur',function(){
		$(this).removeClass('focusclass');
		$(this).addClass('blurclass');
		var now_inp=$(this);
		window.setTimeout(function(){
			if(now_inp.val()==""){	
			now_inp.addClass('warningclass');
			$('#jiben_warning').show();
			}
		},250);
	});


/*
 * 新增div的点击抬起事件
 */
$('#content .head div:eq(0)').mousedown(function(){
	$(this).addClass('clickhover');
	//找勾选的的
	var $ckbox=$("#content .ckbox:checked");
	//如果有勾选的则复制一个插入到下一行
	if($ckbox.length>0){
		for(var i=0;i<$ckbox.length;i++){
			var newtr=$($ckbox[i]).parent().parent().clone(true);
			$($ckbox[i]).parent().parent().after(newtr);
		}
	}else{
		//没有勾选的时候则复制隐藏的插到最后
		var $newtr=$('#content tr:eq(2)').clone(true);
		$newtr.removeClass('hide');		
		$('#content').append($newtr);
		//调用排序
		reseatnum();
	}
});
$('#content .head div:eq(0)').mouseup(function(){
	$(this).removeClass('clickhover');
});

/*
 * 删除div的点击抬起事件
 */
$('#content .head div:eq(1)').mousedown(function(){
	$(this).addClass('clickhover');
});
$('#content .head div:eq(1)').mouseup(function(){
	$(this).removeClass('clickhover');
	if(confirm('确认删除？')){
		//找被选择的checkbox的tr
		var $ckbox=$("#content .ckbox");
		for(var i=1;i<$ckbox.length;i++){
			if($ckbox[i].checked==true){
				$($ckbox[i]).parent().parent().remove();
			}
		}	
		reseatnum();
	}
});	
/*
 * 上移按钮
 */
$('#content .head div:eq(2)').mousedown(function(){
	$(this).addClass('clickhover');
	//找被选择的checkbox的那个tr
	var $ckbox=$("#content .ckbox");
	for(var i=1;i<$ckbox.length;i++){
		if($ckbox[i].checked==true){
			//不能替换到hide那个tr
			if(i-1>0){
				var now_tr=$($ckbox[i]).parent().parent();
				var up_tr=$($ckbox[i-1]).parent().parent();
				now_tr.insertBefore(up_tr);
			}
		}
	}	
	reseatnum();
});
$('#content .head div:eq(2)').mouseup(function(){
	$(this).removeClass('clickhover');
});

/*
 * tr排序
 */

function reseatnum(){
	//统计tr数量
	var tr_lines=$('#content tr').length;
	//第3个tr开始找到下面的第2个td下面的input 修改value为i-2
	for(var i=0;i<tr_lines;i++){
		if(i>=2){
			$('#content tr:eq('+i+')').find('td:eq(1) input').attr('value',i-2);
		}
	}
}	
/*
 * 点击content里面下拉框以外的地方自动隐藏下拉框
 */
var $inp=null;
$('html').not($('.con_model1,.con_time1,.con_status1')).click(function(){
	$('.con_model1,.con_time1,.con_status1').hide();
	if($inp!=null){
		$inp.removeClass('focusclass');
	}
});
/*
 * 下拉框
 */		
$('#content :text').not('.readonly').bind('click',function(e){
	//关闭冒泡事件 不触发body那个
	e.stopPropagation();
	//隐藏警告，清除焦点class和警告class
	$('#jiben_warning').hide();	
	$(this).removeClass('blurclass');
	$(this).removeClass('warningclass');
	$inp=$(this);
	$inp.addClass('focusclass');
	//下拉div对象
	var $hid=$(this).parent().next('div');
	//除了当前下拉其他都关闭
	$('.con_model1,.con_time1,.con_status1').not($hid).css('display','none');
	//再次点击就隐藏div
	if($hid.css('display')=='block'){
		$hid.css('display','none');
	}else{
		$hid.css('display','block');
		/******下拉框是向上还是向下*****/
		var window_height=$(window).height();
		var xiala_height=$hid.height();
		var offset_top=$inp.offset().top;
		var offset_buttom=window_height-offset_top-30;
		if(offset_buttom<xiala_height){
			$hid.css('top',-xiala_height);
		}else{
			$hid.css('top','30px');
		}
	}	
	
	//当前选项背景变蓝色其他变白
	$hid.find('li').hover(function(){
	$(this).css('background','#dfeaf5').siblings().css('background','#fff');
	$(this).click(function(){
			//选中的赋值给input
			$inp.val(this.innerHTML);
			//隐藏下拉div
			$hid.css('display','none');
			$inp.removeClass('focusclass');
			$(this).removeClass('warningclass');
		});	
	});	
});	
/*
 * content中失去焦点
 */	

$('#content :text').bind('blur',function(){
	$(this).removeClass('focusclass');
});
/*
 * 全选
 */	
 $('#con_select').toggle(function(){
	$('#content .ckbox').attr('checked',true);
 },function(){
	$('#content .ckbox').attr('checked',false);
 });


/*
 * 是否重点如果选择第一个则不勾选第二个
 */
$('.con_sfzd input:eq(0)').click(function(){
	if(this.checked==true){
		$(this).next().attr('checked',false);
	}else{
		$(this).next().attr('checked',true);
	}
});
	
	