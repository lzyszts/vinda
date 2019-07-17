$(function(){
	$('#content_area').layout({
		fit : true,
	});
});
//左侧选项
$("#list li").click(function(){
	var type=$(this).attr("data-type");
	//1、找有没有正在编辑的
	if($(".question div").length>0)
	{
		$('#center').scrollTop($('#center')[0].scrollHeight); //滚动最下
		//$('#center').animate({scrollTop:$('.question').prop("offsetTop")}, 400);
		shake($(".question"));
	}
	else if($(".question_edit div").length>0)
	{
		$.messager.alert('警告框','请先完成正在编辑内容!','info');
	}
	else{
		$('#center').scrollTop($('#center')[0].scrollHeight);
		//如果没有则复制过来
		var newType=$("#type div[data-type="+type+"]").clone(true).show();
		$(".question").prepend(newType);
		$("#list li").removeClass("active");
		$(this).addClass("active");
	}
});

/**杂项**/

/*
 * x按钮
 */
$(".base_close").click(function(){
	$(this).parents('.drag_group').fadeOut(function(){$(this).remove();});
});


/*
 * 新建
 */
$(document).on("click", ".new_group", function() {
	$newElement=$(this).parent().find(".drag_group").eq(0).clone(true).css("display","block");
	$(this).before($newElement);
});

/*
 * 模态框关闭
 */
$(document).on("click",".close",function(){
	$(this).parents(".dynamicMoadl").remove();
	$(".modal-backdrop").remove();
});

/*
 * 取消
 */
$(".btn-cancel").click(function(){
	var remove_area=$(this).parents(".question_edit");
	remove_area.empty();
	remove_area=$(this).parents(".question");
	remove_area.empty();
	$("#list li").removeClass("active");
});

/**
 *  点击图片选择img-radio
 */
$(".img-group-out").click(function(){
	$(this).prev().find("input").prop('checked',true);
});

/**验证**/

$("input").change(function(){
	$(this).parent().removeClass("has-error");
});

function check(){
	var state=true;
	//新建或编辑的题目的类型
	var questionType=$(".question").find("[data-type]").attr("data-type");
	var qusetionEditType=$(".question_edit").find("[data-type]").attr("data-type");
	//1、验证标题
	if(!checkEmpty(".wj_title")){
		state=false;
	}
	//2、验证选项有name属性并且不隐藏的
	$('.question input[name],.question_edit input[name]').not(":hidden").each(function(i,item){
		if($.trim($(item).val())==""){
			//验证上传图片是否为空
			if($(item).attr('type')=='file')
			{
				$.messager.alert('警告框','请上传图片!','info');	
			}
			//其他直接验证input
			$(item).parent().addClass("has-error");
			$(item).focus();
			state=false;
			return false;
		}
	});
	//3、验证必须有选项
	//不是问答题的时候
	if(questionType!="textarea" && qusetionEditType!="textarea")
	{
		var op=$('.question .drag_group,.question_edit .drag_group').not(":hidden");		
		if(op.length==0)
		{
			state=false;
			$.messager.alert('警告框','必须有选项！','info');	
		}
	}
	return state;
}

//问卷标题验证
$(".wj_title").blur(function(){
	checkEmpty(".wj_title");
});



/**编辑组**/
$(".op_area").hover(function(){
	$(this).addClass("gray");
	var $tool=$(this).find(".tool");
	//如果在动先停止再滑出来，否者直接滑出来
	if($tool.is(":animated")){ 
		$tool.stop();
		$tool.animate({margin:'0'},350);
	}else{
		$tool.animate({margin:'0'},350);
	}
},function(){
	$(this).removeClass("gray");
	var $tool=$(this).find(".tool");
	if($tool.is(":animated")){ 
		$tool.stop();
		$tool.animate({margin:'0 -58px'},350);
	}else{
		$tool.animate({margin:'0 -58px'},350);
	}
});

/*
 * 编辑组编辑
 */

$(".edit").click(function(){
	var question_id=$(this).parents(".op_area").attr("data-id");	
	var op_area=$(this).parents(".op_area"); //问题区域
	var type=op_area.attr("data-type");
	var is_bx=op_area.attr("is-bx");
	var question_edit=op_area.next();
	//1、验证是否存在
	if(question_edit.find("div").length>0)
	{
		console.log(question_edit.find("div"));
		$('#center').animate({scrollTop:question_edit.prop("offsetTop")}, 500);
	}
	else if($(".question div").length>0)
	{
		//有新建的跳到底部
		$('#center').scrollTop($('#center')[0].scrollHeight);
		shake($(".question"));
	}
	else if($(".question_edit div").length>0)
	{
		$.messager.alert('警告框','请先完成正在编辑内容!','info',function(){
			$('#center').animate({scrollTop:$(".question_edit div").prop("offsetTop")}, 500);
		});
	}
	else
	{
		/*没正在编辑的则复制过来同时增加类型下拉*/
		//1、增加类型选项
		var str=creatType(type,question_id);
		question_edit.prepend(str);
		//根据type选择此题类型
		question_edit.find("option").each(function(i,item){
			if($(item).val()==type)
			{
				$(item).attr("selected","selected");
			}
		});
		//2、复制插入编辑区(.edit_area)
		var newType=$("#type div[data-type="+type+"]").clone(true).show();
		question_edit.append(newType);	
		newType.wrap("<div class='edit_area'></div>");		
		
		//3、是否必选(1必选0不选)
		var is_bx_input=question_edit.find(".is_bx input");
		if(is_bx=="1")
		{
			is_bx_input.prop('checked',true);
		}else{
			is_bx_input.prop('checked',false);
		}
		//5、根据题目类型遍历插入旧的内容
		creatEditArea(type,op_area,question_edit);
		//6、滚动到此处
		$('#center').animate({scrollTop:question_edit.prop("offsetTop")}, 500);
	}
});
/*
 * 根据原题生成编辑区内容
 * 需要类型type，旧的选项区op_area，新的编辑区question_edit
 * 删除类型描述，插入题目，插入选项
 */
function  creatEditArea(type,op_area,question_edit){
	var drag_group=question_edit.find(".drag_group"); //编辑的选项区
	var new_group=question_edit.find(".new_group"); //编辑的新增行
	var label=op_area.find(".op_group").find("label");	//旧的label区
	//1、删除类型描述
	question_edit.find(".question_type").remove();
	//2、插入题目
	var title=op_area.find(".title span").html();
	question_edit.find(".question_title").val(title);

	//遍历老的选项区插入到编辑区域
	if(type=="radio" || type=="checkbox")
	{
		for(var i=1;i<label.length;i++)
		{			
			var text=$.trim($(label[i]).text());
			if(typeof(drag_group[i])=="undefined")
			{
				//如果没有则新建一行再插入数据
				new_group.click();
				drag_group=question_edit.find(".drag_group");//更新drag_group
				$(drag_group[i]).find("input").val(text);
			}else{
				$(drag_group[i]).find("input").val(text);
			}
		}
	}
	//编辑图片单选处理
	if(type=="img-radio")
	{
		var img=op_area.find(".op_group").find("img");//待修改图片对象
		for(var i=1;i<label.length;i++)
		{			
			var text=$.trim($(label[i]).text());
			//先先增加有多少行
			if(typeof(drag_group[i])=="undefined")
			{
				//如果没有则新建一行再插入数据
				new_group.click();
				drag_group=question_edit.find(".drag_group");//更新drag_group
				$(drag_group[i]).find("input[type='text']").val(text);
				
			}else{
				$(drag_group[i]).find("input[type='text']").val(text);
			}
			//再插入图片		
			//需要修改的图片src和id
			var img_src=$(img[i-1]).attr("src"); //绝对路径
			var img_id=$(img[i-1]).attr("img_id");
		    var now_img_group_out=$(drag_group[i]).find(".img-group-out").empty();
		    var img_str="<img src='"+img_src+"' class='img-group-out-center-img'>";
			var input="<input type='hidden' name='img_dx_id[]' value='"+img_id+"'>";
			//如果是老图则img_dx_src中也加入老图id
			input+="<input type='hidden' name='img_dx_src[]' value='"+img_id+"'>";	
			now_img_group_out.append(img_str);
			now_img_group_out.append(input);
		}
	}
	
	if(type=="textarea")
	{	
		var line=op_area.find("textarea").attr("rows");
		var wt=question_edit.find(":input[name='wt'] option");
		wt.each(function(i,item){
			{
				if($(item).val()==line)
				{
					$(item).attr("selected","selected");
				}
			}
		});
	}	
}
/*
 * 编辑组的更改类型
 */
$(document).on("change",".question_edit .change_type", function() {
	var question_edit=$(this).parents(".question_edit"); //整体
	var edit_area=question_edit.find(".edit_area"); //编辑区
	var op_area=question_edit.prev();	//旧问题区
	var question_id=op_area.find("ul").attr("data-id");		
	var type=$(this).val();
	//1、清空
	edit_area.empty();	
	//2、插入选择的
	var newType=$("#type div[data-type="+type+"]").clone(true).show();
	edit_area.append(newType);
	creatEditArea(type,op_area,question_edit);	
});

/*
 * 生成选项类型下拉字符串
 * 单选,多选,问答一组
 * 图片单选一组
 */
function creatType(old_type,question_id){
	var str="<br><div class='type-group'><div class='form-group'><label class='col-sm-2 col-xs-2 control-label'>类型</label>";
	str+="<div class='col-sm-8 col-xs-8'>";
	str+="<select class='form-control change_type' name='type' style='width:200px'>";
	if(old_type=="radio" ||old_type=="checkbox" ||old_type=="textarea" )
	{
		str+="<option value='radio'>单选题</option>";
		str+="<option value='checkbox'>多选题</option>";
		str+="<option value='textarea'>问答题</option>";
	}
	if(old_type=="img-radio")
	{
		str+="<option value='img-radio'>图片单选题</option>";
	}
	str+="</select></div></div>";
	str+="<input type='hidden' data-id='"+question_id+"' value='"+question_id+"' name='question_id'>";
	return str;
}


/**排序**/

/*
 * 生成排序
 */
$(".sort").click(function(){
	//1、跟换的序号
	var op_area=$(this).parents(".op_area"); //需更换的area
	var old_order="";		//需跟换的序号
	$(".op_area").each(function(i,item){
		if(op_area.is($(item)))
		{
			old_order=i+1; 
		}
	});
	//2、生成模态框
	$("body").append(creatModal("sort_modal","排序列表","420px"));
	$(".sort_modal").modal("show");
	keep_body();
	//3、生成排序下拉
	var orderCount=$(".op_group .title").length;

	var str="<label>修改为：</label>";
	str+="<select class='form-control' old-order='"+old_order+"'>";
	for(var i=1;i<=orderCount;i++)
	{
		if(i!=old_order){
			str+="<option value='"+i+"'>第"+i+"题</option>";
		}
	}
	str+="</select>";
	$(".sort_modal .modal-body").append(str);
	//3、生成确定
	var btn="<input class='btn btn-primary' value='确定' type='submit'>";
	$(".sort_modal .modal-footer").append(btn);
	
});
/*
 * 排序确认
 */
$(document).on("click",".sort_modal .btn",function(){
	var select=$(this).parents(".modal-content").find("select");
	var new_order=select.val();		//新题号
	var old_order=select.attr("old-order"); //旧题号
	//找新旧op_area交换
	var allArea=$(".op .op_area");
	var old_area=$(allArea[old_order-1]); //旧题目整体
	var old_edit=old_area.next();	 //旧的编辑区
	//插入选定题号之后
	if(new_order=="1")		
	{
		$(".op").prepend(old_area);
		old_edit.insertAfter(old_area);	
		$(".sort_modal .close").click();
		reorder();
	}else{
		if(old_order=="1")
		{
			var new_area=$(allArea[new_order-1]);  //第1题变为其他题目的情况
		}else{
			var new_area=$(allArea[new_order-2]);  //变成3题则找下标1(也就是2题)后面
		}
		var new_edit=new_area.next();
		old_area.insertAfter(new_edit);
		old_edit.insertAfter(old_area);	
		$(".sort_modal .close").click();
		reorder();
	}
});












