var swiper = new Swiper('.swiper-container', {
  spaceBetween: 30,
  centeredSlides: true,
  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
  },
  pagination: {
    el: '.swiper-pagination',
    clickable: true,
  },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  loop: true,
});
// 直播预告
$(".slideTxtBox").slide({
   // effect:"left",
   // autoPlay:true,
   // pnLoop:true,
   interTime:5000,
   delayTime:500,
   easing:"easeInQuint",
    autoPage:true,
    effect:"leftLoop",
    autoPlay:true,
    trigger:"click"
});
// $(".live_list").slide({
//  mainCell:".bd ul",
//  autoPlay:true,
//  effect:"topMarquee",
//  vis:5,
//  interTime:50,
//  pnLoop:false,
//  mouseOverStop:false
// });
$(".live_list li").hover(function(){
  var index_img= $(this).index();
  $(".left_box li").eq(index_img).addClass("on").siblings().removeClass("on");
})
