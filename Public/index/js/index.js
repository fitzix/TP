/**
* mumu  
*/
$(function() {
    /* 新闻动画 */
    $('#news').css({'left':'1394px'});
    $('#news').move($('#news'),960,'SPRING'); 
    /* #shortcut */
    //城市列表
    $('.send-target').hover(function(){
        eleShow($('.send-list'), $('.send-target'));
    },function(){
        $('.send-list').mouseover(function(){
            eleShow($('.send-list'), $('.send-target'));
        });
        eleHide($('.send-list'), $('.send-target'));
    });
    $('.send-list').hover(function(){
        eleShow($('.send-list'), $('.send-target'));
    },function(){
        eleHide($('.send-list'), $('.send-target'));
    });
    //选择运送城市
    $('.sendcity a').click(function(){
        $('.send-target span:first').html($(this).html());
    });
    //我的智联
    $('.short-user').hover(function(){
        eleShow($('.shortuser'), $('.short-user'));
    },function(){
        $('.shortuser').mouseover(function(){
            eleShow($('.shortuser'), $('.short-user'));
        });
        eleHide($('.shortuser'), $('.short-user'));
    });
    $('.shortuser').hover(function(){
        eleShow($('.shortuser'), $('.short-user'));
    },function(){
        eleHide($('.shortuser'), $('.short-user'));
    });

    /*# search */
    //我的购物车
    $('.sc-icons').hover(function(){
        eleShow($('.sc-history'), $('.sc-icons'));
    },function(){
        $('.sc-history').mouseover(function(){
            eleShow($('.sc-history'), $('.sc-icons'));
        });
        eleHide($('.sc-history'), $('.sc-icons'));
        
    });
    $('.sc-history').hover(function(){
        eleShow($('.sc-history'), $('.sc-icons'));
        $('.sc-icons').css({'border':'1px solid #ddd','box-shadow':'0 0 5px rgba(0, 0, 0, 0.2)'});
    },function(){
        eleHide($('.sc-history'), $('.sc-icons'));
        $('.sc-icons').css({'border':'1px solid #ddd','box-shadow':''});
    });

    /* #nav */
    $('.dd-list').mouseover(function(){
        $('.dd-content').eq( $(this).index() ).stop(true, true).show();
        $(this).addClass('dd-active');
    })
    $('.dd-list').mouseout(function(){
        $('.dd-content').mouseover(function(){
            $(this).stop(true, true).show();
            $(this).siblings('.dd-list').addClass('dd-active');
        });
        $('.dd-content').eq( $(this).index() ).stop(true, true).hide();
        $(this).removeClass('dd-active');
    })
    $('.dd-content').mouseover(function(){
        $(this).stop(true, true).show();
        $(this).siblings('.dd-list').addClass('dd-active');
    });
    $('.dd-content').mouseout(function(){
        $(this).stop(true, true).hide();
        $('.dd-list').removeClass('dd-active');
    });

    /* #focus */
    pnBar('slider', 'img');
    carousel();

    /* #todays */
    pnBar('tc', 'ul');//上一页，下一页
    moveT();

    /* 猜你喜欢 */
    var w = $('.gc .gc-list li').width() * 6;
    $('.gt-more').click(function(){
        //$('.gc-list').css('left':'-100px');
    });
    
    /* tab切换 */
    $('.tab-item:first').addClass('tab-selected');
    var tw = $('.tab-item:first').children('a').width() + 34 + 'px';
    $('.tab-item:first').css('width',tw);
    $('.tab-item:last').children('span').css('background','#fff');
    $('.fc .main').eq(0).css({'display':'block'});
    $('.tab-item').hover(function(){
        $('.main').css({'display':'none'});
        $('.tab-item').removeClass('tab-selected');
        $(this).addClass('tab-selected');
        var Tw1 = $(this).children('a').width() + 34 + 'px';
        $(this).css('width', Tw1);
        $('.main').eq( $(this).index() ).css({'display':'block'});
    },function(){
        $('.tab-item').mouseover(function(){
           $(this).addClass('tab-selected');
           var Tw2 = $(this).children('a').width() + 34 + 'px';
           $(this).css('width', Tw2);
        });
    });

    /* 今日抄底 */
    $(".tsc li").eq(0).addClass('fore1');
    $(".tsc li:first img").css('width','160');
    $('.p-img').hover(function(){
        $(this).animate({'left':'-10px'},400);
    },function(){
        $(this).animate({'left':'0px'},400);
    });
    /**
    **********************函数列表*************************************
    **/
    //显示模块
    function eleShow(showObj, addObj){
        showObj.show();
        addObj.addClass('active');
    }

    //隐藏模块
    function eleHide(hideObj, remObj){
        hideObj.hide();
        remObj.removeClass('active');
    }

    //轮播
    function carousel(){
        $('.picslist').eq(0).addClass('pics-acitve');
        var maxLength = $('.picslist').length;
        var index = 0;
        //自动循环图片
        var time = setInterval(function(){
            changeCs(index);
            index++;
            if(index == maxLength){
                index = 0;
            }
        },3000);
        //点击数字跳到对应图片
        $('.slider-extra ul li').click(function(){
            index = $(this).index();
            clearInterval('time');
            changeCs(index);
        })
        //点击上一张图
        $('.slider .slider-prev').click(function(){
            clearInterval('time');
            index = index - 1;
            if(index == -1) {
                index = maxLength - 1;
            }
            changeCs(index);
        })
        //点击下一张图
        $('.slider .slider-next').click(function(){
            clearInterval('time');
            index = index + 1;
            if(index == maxLength) {
                index = 0;
            }
            changeCs(index);
        })
    }

    //轮播图激活样式改变
    function changeCs(index){
        $('.picslist').removeClass('pics-acitve');
        $('.slider-extra ul li').removeClass('slider-selected');
        $('.picslist').eq(index).addClass('pics-acitve');
        $('.slider-extra ul li').eq( index ).addClass('slider-selected');
    }

    //上一页，下一页bar
    function pnBar(str1, str2){
        $('.' + str1 + ' ' + str2).hover(function(){
            $('.' + str1 + ' .slider-page').css({'display':'block'});
        },function(){
            $('.slider-page').mouseover(function(){
                $(this).css({'display':'block'});
            });
            $('.' + str1 + ' .slider-page').css({'display':'none'});
        })
        $('.slider-page').mouseout(function(){
            $(this).css({'display':'none'});
        })
    }
    
    //今日特别推荐轮播
    function moveT(){
        var w = ($('.tc li').width() + 17) * $('.tc li').length;
        $('.tc ul').css({'width': w + 'px'});
        //下一个
        $('.tc .slider-prev').click(function(){
            $('.tc ul').css({'left':'-250px'});
            $('.tc li').eq(0).appendTo($('.tc ul'));
            $('.tc ul').css({'left':'0px'});
        })
        //上一个
        $('.tc .slider-next').click(function(){
            $('.tc li').last().prependTo($('.tc ul'));
            $('.tc ul').css({'left':'250px'});
            $('.tc ul').css({'left':'0px'});
        })
    }
});