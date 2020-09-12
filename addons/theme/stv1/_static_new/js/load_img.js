var aImages = document.getElementById("loading").getElementsByTagName('img'); //获取id为SB的文档内所有的图片
loadImg(aImages);
window.onscroll = function() { //滚动条滚动触发
    loadImg(aImages);
};
//getBoundingClientRect 是图片懒加载的核心
function loadImg(arr) {
    for(var i = 0, len = arr.length; i < len; i++) {
        if(arr[i].getBoundingClientRect().top < document.documentElement.clientHeight && !arr[i].isLoad) {
            arr[i].isLoad = true; //图片显示标志位
            //arr[i].style.cssText = "opacity: 0;";
            (function(i) {
                setTimeout(function() {
                    if(arr[i].dataset) { //兼容不支持data的浏览器
                        aftLoadImg(arr[i], arr[i].dataset.imgurl);
                    } else {
                        aftLoadImg(arr[i], arr[i].getAttribute("data-imgurl"));
                    }
                    arr[i].style.cssText = "transition: 0.4s; opacity: 1;" //相当于fadein
                }, 500)
            })(i);
        }
    }
}

function aftLoadImg(obj, url) {
    var oImg = new Image();
    oImg.onload = function() {
        obj.src = oImg.src; //下载完成后将该图片赋给目标obj目标对象
    }
    oImg.src = url; //oImg对象先下载该图像
}