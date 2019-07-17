/*
	 * 上传名字
	 */
	$('.upload').bind('change',function(){
		$('.jiben_filename').val($(this).val());
	});
	
	/*
	 * 附件按钮事件
	 */	
	$('.jiben_upload').mousedown(function(){
		$('.jiben_upload').addClass('clicksave');	
	});	
	$('.jiben_upload').mouseup(function(){
		$('.jiben_upload').removeClass('clicksave');
	});	
	//提交事件
	$('.jiben_submit').click(function(e){
		$(this).addClass('clicksave');
		if(confirm('管理员锁定后将无法修改,确定提交吗？')){
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
	$('#userinfo :text').not($('.jiben_readonly')).bind('click',function(){
		$('#jiben_warning').hide();	
		$(this).removeClass('blurclass');
		$(this).removeClass('warningclass');
		$(this).addClass('focusclass');	
	}); 

	//失去焦点input框时 延迟判断 除了序号，名字，部门，填写日期
		$('#userinfo :text').not($('.jiben_readonly')).bind('blur',function(){
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
		//找勾选的
		var $ckbox=$("#content .ckbox:checked");
		if($ckbox.length>0){
			for(var i=0;i<$ckbox.length;i++){
				var newtr=$($ckbox[i]).parent().parent().clone(true);
				newtr.removeClass('hide');		
				$($ckbox[i]).parent().parent().after(newtr);
			}
			reseatnum();
		}else{
			//没有勾选的时候则复制隐藏的插到最后
			var $newtr=$('#content tr:eq(2)').clone(true);
			$newtr.removeClass('hide');		
			$('#content').append($newtr);
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
		//找到被选择的checkbox的那个tr
		var $ckbox=$("#content .ckbox");
		for(var i=1;i<$ckbox.length;i++){
			if($ckbox[i].checked==true){
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
		$(".xuhao").each(function(i,item){
			if(i>=1){
				item.value=i;
			}
		});
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
	$('#content :text').bind('click',function(e){
		//关闭冒泡事件 不触发body那个
		e.stopPropagation();
		$('#jiben_warning').hide();	
		$(this).removeClass('blurclass');
		$(this).removeClass('warningclass');
		$inp=$(this);
	    var $nowtr=$inp.parent().parent().parent();
		$inp.addClass('focusclass');
		//下拉div对象
		var $hid=$(this).parent().next('div');
		//找到当前tr的行数
		//var line=$('#content tr').index($(this).parent().parent().parent());
		//找到除了当前下拉其他都关闭
		$('.con_model1,.con_time1,.con_status1').not($hid).css('display','none');
		//再次点击就隐藏div
		if($hid.css('display')=='block'){
			$hid.css('display','none');
		}else{
			//当前选中行的业务模块的值
			//var $nowtd_value=$nowtr.find('td:eq(2) input:eq(0)');
			//这里之前做的KPI指标不能修改
			/*如果这个值等于KPI指标并且不是业务模块下的那个input框则不显示隐藏的div
			if($nowtd_value.val()=="KPI指标" && $nowtd_value.attr('class')=="con_md"){
				$hid.css('display','none');
			}else{*/
				$hid.css('display','block');
			//}
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
		//点击li的时候
		$(this).click(function(){
				$inp.val(this.innerHTML);
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
	 * 自惩承诺如果选择第一个则不勾选第二个
	 */
	$('.con_checkbox input:eq(0)').toggle(function(){
		if(this.checked==true){
			$(this).next().prop('checked',false);
		}else{
			$(this).next().prop('checked',true);
		}
	});
	/*
	 * 是否重点如果选择第一个则不勾选第二个
	 */
	$('.con_sfzd input:eq(0)').click(function(){
		if(this.checked==true){
			$(this).next().prop('checked',false);
		}else{
			$(this).next().prop('checked',true);
		}
	});
	/*
	 * 全选反选
	*/
	 $('#con_select').click(function(){
	 	if($(this).prop('checked')==true){
	 		$('#content .ckbox').prop('checked',true);
	 	}else{
	 		$('#content .ckbox').prop('checked',false);
	 	}
	 }); 
	