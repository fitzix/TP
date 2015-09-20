//mumu www.mumusj.com QQ:527576254


/* 缓冲运动方法
 *  Obj 运动的对象,可以传js获取的element对象或者jquery获取的Object对象
 *  Target 目标位置，数字
 *  Type 运动类型，BUFFER为缓冲运动(默认)，SPRING为弹性+摩擦运动
 *  调用 Object.move(Obj,Target[,'Type']);
 */
(function($){
    $.fn.move = function(Obj,Target){
        Type = arguments[2]?arguments[2]:'BUFFER';//设置Type默认值为BUFFER
        var iSpeed = 0;//自定义速度
        var ele = '';
        var left = 0;//累加速度变量
        
        if(Obj.style){
            ele = Obj;//如果是js获取的对象
        } else {
            ele = Obj.get(0); //如果是juqery获取的对象
        }
        //Type = if(Type)?Type:"BUFFER";alert(Type);
        switch(Type){
            case 'BUFFER':
                clearInterval(Obj.time);
                Obj.time = setInterval(function(){
                    var l = ele.offsetLeft;//获取对象定位左距离
                    var iSpeed = (Target - l)/8;//初始化速度
                    iSpeed=iSpeed>0?Math.ceil(iSpeed):Math.floor(iSpeed);//速度取整
                    if(l == Target){
                        clearInterval(Obj.time);//判断对象位置，到目标点结束运动
                        ele.style.left = Target+"px";
                    } else {
                        ele.style.left = l+iSpeed+'px';//设置对象位置
                    }
                },30)
                break;
            case 'SPRING':
                Obj.time = setInterval(function(){
                    var l = ele.offsetLeft;//获取对象定位左距离
                    iSpeed += (Target - l)/5;//速度运算
                    iSpeed*=0.7;
                    left+=iSpeed;
                    if(Math.abs(iSpeed)<1 && Math.abs(left-Target)<1){
                        clearInterval(Obj.time);//如果速度足够小，距离目标点足够近，停止运动
                        ele.style.left = Target+"px";
                    } else {
                        ele.style.left = l+iSpeed+"px";//设置对象位置
                    }
                },30)
                break;
        }
        return $(this);
    }
})(jQuery);