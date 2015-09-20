/* 颜色多选 */
//颜色收起、展开
$('.moreattr').toggle(function(){
    $('.attrvalue').css({'height':'auto'});
    $(this).html('收起');
},function(){
    $('.attrvalue').css({'height':'35px'});
    $(this).html('更多');
})
//单选、多选
$('.morecheck').toggle(function(){
    $(this).html('单选');
    $('.attrvalue a').removeClass('check');
    $('.subfilter').unbind("click");//去除a标签单事件
    $('.attrvalue a').attr('filter','attrid[]');
    //点击选择颜色
    $('.subfilter').toggle(function(){
        $(this).addClass('check');
        $(this).find('input').attr('checked',true);
        $('.attrbox button').css({'display':'block'});
    },function(){
        $(this).removeClass('check');
        $(this).find('input').attr('checked',false);
    })
    
},function(){
    $('.attrvalue a').removeClass('check');
    $('.attrvalue a').attr('filter','attrid');
    $('.attrvalue a').bind("click", function(){
        selectFilter(Tag, $(this), $('.attrvalue a'));//绑定a标签单事件，触发的函数
    });
    $(this).html('+ 多选');
    $('.attrbox button').css({'display':'none'});
})
//提交多选条件
function subAttrs(){
    selectFilter(Tag, $(this), $(this));
}
//多选条件维持
var moreSearch = '{$_GET['attrid']|json_encode}'?{$_GET['attrid']|json_encode}:'';//获取颜色多选条件
//如果是对象,遍历颜色,如果颜色Tag值和接收到的值相同,添加样式
if(typeof(moreSearch) == 'object'){
    $('.attrvalue a').each(function(i,data){
        var index = parseInt($(data).attr('tag'));
        for(var i = 0; i < moreSearch.length; i++){
            var tag = parseInt(moreSearch[i]);
            if(index == tag){
                $(this).addClass('check');
            }
            //alert(tag);
        }
    });
}
/* 商品搜索 */
var Tag = '__CONTROLLER__/index?';//搜索跳转主链接
var keyword = '{$_GET['keyword']}';
if(keyword){
    Tag += 'keyword=' + keyword + '&';
}
//品牌选择
$('#goods-brands ul li a').click(function(){
    selectFilter(Tag, $(this), $('#goods-brands ul li a'));
})
//价格选择
$('#price ul li a').click(function(){
    selectFilter(Tag, $(this), $('#price ul li a'));
});
//颜色选择
$('.attrvalue a').click(function(){
    selectFilter(Tag, $(this), $('.attrvalue a'));
})

//尺码选择
$('#hotsale ul li a').click(function(){
    selectFilter(Tag, $(this), $('#hotsale ul li a'));
});
//选择函数
function selectFilter(Tag, obj, reclass){
    reclass.removeClass('check');
    obj.addClass('check');
    $('.check').each(function(i, data){
        if($(data).attr('tag')){
            Tag += $(data).attr('filter') + '=' + $(data).attr('tag') + '&';
        }
    })
    //window.location.href= Tag;
    $('#order').attr('action',Tag);//维持排序状态，并提交搜索条件
    $('#order').submit();
}
/* 商品搜索 */
//排序
$(".f-sear").click(function(){
    $(this).parent().siblings().children().removeClass('f-order');
    $(this).addClass('f-order');
    var sort = $(this).attr('sort');
    $('#order input[name=order]').val(sort);
    $('#order').attr('action',window.location.href);
    $('#order').submit();
});
//状态维持
var state = '{$_POST['order']}';
if(state == 'salecount_DESC'){
    state = 'salecount_ASC';
} else if (state == 'salecount_ASC'){
    state = 'salecount_DESC';
}
if(state == 'saleprice_DESC'){
    state = 'saleprice_ASC';
} else if (state == 'saleprice_ASC'){
    state = 'saleprice_DESC';
}
if(state == 'addtime_DESC'){
    state = 'addtime_ASC';
} else if (state == 'addtime_ASC'){
    state = 'addtime_DESC';
}
if(state == 'reviewcount_DESC'){
    state = 'reviewcount_ASC';
} else if (state == 'reviewcount_ASC'){
    state = 'reviewcount_DESC';
}
$(".f-sear").each(function(i,data){
    if($(data).attr('sort') == state){
        $(data).addClass('f-order');
    }
});
//上一页下一页维持排序状态
$('.num').click(function(){
    $('#order').attr('action', $(this).attr('href'));
    $('#order').submit();
    return false;
});
$('.prev').click(function(){
    $('#order').attr('action', $(this).attr('href'));
    $('#order').submit();
    return false;
});
$('.next').click(function(){
    $('#order').attr('action', $(this).attr('href'));
    $('#order').submit();
    return false;
});