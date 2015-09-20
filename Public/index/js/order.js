//搜索input 默认值的显示与清空
$("#put").focus(function(){
	if($(this).val()=='商品名称、商品编号、订单编号'){
		$(this).val('');
	}
})
$("#put").blur(function(){
	if($(this).val()==''){
		$(this).val('商品名称、商品编号、订单编号');
	}
})
//搜索
$("#btn").click(function(){
	var val = $('#put').val();
	if(val != '商品名称、商品编号、订单编号'){
        $('.tab tbody').hide();
		$(".tab tr:contains("+val+")").parent().show();
	}else{
		$(".tab tbody").show();
	}
})
//猜你喜欢
$('.tab-item').hover(function(){
	$('.nt').css({'display':'none'});
	$('.tab-item').removeClass('tab-selected');
	$(this).addClass('tab-selected');
	$('.nt').eq( $(this).index() ).css({'display':'block'});
},function(){
	$('.hide').mouseover(function(){
		$(this).addClass('tab-selected');
	});
})