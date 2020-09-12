$(function(){
    // var preHandler = function(e){e.preventDefault();}


    /*导航面板的操作*/
    $(".modular ").on("click",function(){
        $(".shadow-panle").show();
        $(".window-panle").css({"marginLeft":"0","box-shadow":"5px 0 10px rgba(58,69,88,0.3)"});
        $("body").css("overflow-y", "hidden");
        $(".overall_situation_box_alls").css({"marginLeft":"70%","position":"fixed"});
        // document.addEventListener('touchmove', preHandler, false);
    })

    $(".shadow-panle ").on("click",function(){

        $(".shadow-panle").hide();
        $(".window-panle").css({"marginLeft":"-70%","box-shadow":"0 0 0 rgba(58,69,88,0.3)"})
        $("body").css("overflow-y", "auto");
        $(".overall_situation_box_alls").css({"marginLeft":"0"});
        // $(".overall_situation_box_alls").delay(300).css({"position":"static"});
        setTimeout(function(){$(".overall_situation_box_alls").css({"position":"static"})},100 );
        // document.removeEventListener('touchmove', preHandler, false);
    })

    /*回到顶部*/
    $(window).scroll(function(){
        var wins = $(document).scrollTop();
        if(wins>1000){
            $(".footer span").show();
        }
        else{
            $(".footer span").hide();
        }
    });

    $(".footer span").on("click",function(){
        $("html,body").animate({scrollTop:"0px"},200)
    })

    $(".users").click(function(){
      if ($(".users span").css("display") == "none") {
          $('.users span').show();
      }else {
          $('.users span').hide();
      }
    })
    //导航
    $(".nav_list ul li").click(function(){
        $(this).addClass("on").siblings("li").removeClass("on");
    })
})
