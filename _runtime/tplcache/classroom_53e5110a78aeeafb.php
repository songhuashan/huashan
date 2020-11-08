<?php if (!defined('THINK_PATH')) exit();?>
<style media="screen">
  .bgc {
    background: none!important;
    font-size: 10px!important;
    margin-right: -15px;
  }
  .course-class{
    display: none;
    background: #3e3e3e;
  }
  .course-class a{
    background: #3e3e3e;
    width: 100%!important;
    height: auto;
  }

</style>
<!DOCTYPE HTML><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo ($seo['_title']); ?></title>
    <meta content="<?php echo ($seo['_keywords']); ?>" name="keywords">
	<meta content="<?php echo ($seo['_description']); ?>" name="description">
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0 minimal-ui"/>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link href="__THEMENEW__/css/public2.0.css" rel="stylesheet">
	<link href="__THEMENEW__/css/public_header_footer_media.css" rel="stylesheet">
	<link href="__THEMEW3G__/css/style.css" rel="stylesheet">
	<link href="__THEMEW3G__/css/style_add.css" rel="stylesheet">
	<script src="__THEMEW3G__/js/jquery.js"></script>
	<script src="__THEME__/js/index_new/public_header_footer_media.js"></script>
	<script src="__THEMEW3G__/js/TouchSlide.1.0.js"></script>
	<script src="__THEMEW3G__/js/jquery.SuperSlide.2.1.js"></script>
	<script src="__THEME__/js/cyjs/title_common.js"></script>
	<script src="__THEME__/js/cyjs/cyjs_common.js"></script>
	<script src="__THEME__/js/cyjs/offlights.js"></script>
	<script>
		//全局变量
		var SITE_URL  = '<?php echo isset($site["site_url"]) ? $site["site_url"] : SITE_URL; ?>';
		var UPLOAD_URL= '<?php echo UPLOAD_URL; ?>';
		var THEME_URL = '__THEME__';
		var APPNAME   = '<?php echo APP_NAME; ?>';
		var MID		  = parseInt('<?php echo $mid; ?>');
		var UID		  = '<?php echo $uid; ?>';
		var initNums  =  '<?php echo $initNums; ?>';
		var SYS_VERSION = '<?php echo $site["sys_version"]; ?>';
		var _ROOT_    = '__ROOT__';
		// Js语言变量
		var LANG = new Array();
		//注册登录模板
		var REG_LOGIN="<?php echo U('public/Passport/regLogin');?>";
		//邮箱验证地址
		var CLICK_EMIL="<?php echo U('public/Passport/clickEmail');?>";
		//异步注册地址
		var REG_ADDRESS="<?php echo U('public/Passport/ajaxReg');?>";
		//异步登录
		var LOGIN_ADDRESS="<?php echo U('public/Passport/ajaxLogin');?>";
		//退出登录
		var LOGINOUT_ADDRESS="<?php echo U('public/Passport/logout');?>";
	</script>
</head>
<body>

	<!--preloader-->
	<div id="preloader">
		<div id="status">
			<p class="center-text">
				加载内容...
				<em>加载取决于您的连接速度!</em>
			</p>
		</div>
	</div>
	<!--header-->

<!--<iframe src="__THEME__/_header_w3g.html" style="width:100%;z-index:1000000"></iframe>-->
<link rel="stylesheet" href="__THEME__/../_static_new/icon/iconfont.css">
<!-- <link rel="stylesheet" href="__THEME__/../_static_new/icon/iconfont.js"> -->
<script>
    if(navigator.userAgent.match(/(iPhone|Android|ios)/i)) {
        $(window).load(function () {
            $('.modular,.logos,.window-box').show();
            $('.users').attr('style','display:block');
        });
    }
    $(window).ready(function(){
        $(".dic-box:last").css("border-bottom","none");
    });
    /**
     * 退出成功
     */
    function logout(){
        $.ajax({
            type: "POST",
            async:false,
            url:LOGINOUT_ADDRESS,
            dataType:"json",
            success:function(data){
                alert("退出成功！");
                location.reload();
            }
        });
    }
</script>
<style type="text/css">
    header{
        width:100%;
    }
    .modular
    {
        width: 25px;
        height: 25px;
        margin: 15px 0 0 0;
        background: url(__THEMEW3G__/icon/icon.png);
        background-position: 163px 124px;
    }
    .fill-in{
        width: 25px;
        height: 25px;
        margin: 15px 15px 0 2%;
        background: url(__THEMEW3G__/icon/icon.png);
        background-position: 114px 124px;
    }
    .clear{clear: both;}
</style>
<?php if($_SESSION['mid'] == ''): ?><script>
    var CLICK_VERIFY = "<?php echo U('public/Passport/clickVerify');?>";
    var CLICK_UNAME = "<?php echo U('public/Passport/clickUname');?>";
    var CLICK_PHONE = "<?php echo U('public/Passport/clickPhone');?>";
    var CLICK_PHONEVER = "<?php echo U('public/Passport/clickPhoneVer');?>";
    var SETUSERFACE = "<?php echo U('public/Passport/setUserFace');?>";
    var GET_PHONEVERIFY = "<?php echo U('public/Passport/getVerify');?>";

    //更换验证码
    function changeverify() {
        var date = new Date();
        var ttime = date.getTime();
        var url = "__ROOT__/public/captcha.php";
        $('#verifyimg').attr('src', url + '?' + ttime);

    }
    ;

    /**
     * 取消注册
     */
    function removeReg() {
        $("#transparent").css("display", "none");
    }
</script>
<div id="transparent" style="display: none;">

    <div id="loging-worap-regsiter"><a class="loging-Cancel" href="javascript:;" onclick="removeReg()">×</a>
        <!--登录-->
        <div id="loging-worap">
            <div class="title">登录</div>
            <div class="loging" style="padding:0 20px;margin:0;width:auto;border:0">
                <ul>
                    <li class="her-login">
                        <div class="input-box">
                            <i class="icon-zhanghao"></i>
                            <input name="log_username" id="log_username" class="regsiter-width" maxlength="30" type="text"
                                   placeholder="请输入手机号">
                            <em>×</em>
                        </div>
                    </li>
                    <li class="her-login">
                        <div class="input-box">
                            <i class="icon-mima"></i>
                        <input name="log_pwd" id="log_pwd" class="regsiter-width" maxlength="30" type="password"
                               placeholder="请输入登录密码">
                            <em>×</em>
                        </div>
                    </li>
                    <li class="her-login">
                        <input name="" id="logSub" onclick="logSub()" class="loging-xy-submit" type="submit"
                               value="登录"/>
                        
                    </li>
                    
                </ul>
                <div class="loging-xy">
                    <div class="loging-bottom">
                        <?php if(($face_login)  ==  "1"): ?><div class="login-ft" style="width: auto;">
                            <a href="<?php echo U('public/Passport/faceLogin');?>" style="background: url('__THEME__/icon/face_scanning.png') no-repeat;"></a>
                        </div><?php endif; ?>
                        <?php if(Addons::requireHooks('login_input_footer') && Addons::hook('login_input_footer')): ?>
                        <div class="login-ft" style="">
                            <?php echo Addons::hook('login_input_footer');?>
                        </div>
                        <?php endif; ?>
                        <div class="loging-xy-bottom">
                            <?php if($this_mhm_id != ''): ?><a class="goto_res" href="<?php echo U('public/Passport/reg',array('this_mhm_id'=> $this_mhm_id));?>">去注册</a>
                                <?php else: ?>
                                <a class="goto_res" href="<?php echo U('public/Passport/reg');?>">注册账号</a><?php endif; ?>
                            <a href="<?php echo U('home/Repwd/index');?>" sty>忘记密码？</a></div>
                        <style>
                            #transparent a{color: #00BED4;}
                            .icon-qzone {
                                background-position: -461px -661px;
                                background-image:url(__THEME__/icon/icon.png);
                            }
                            .icon-weixin {
                                background-position: -511px -661px;
                                background-image:url(__THEME__/icon/icon.png);
                            }
                            .icon-sina {
                                background-position: -561px -661px;
                                background-image:url(__THEME__/icon/icon.png);
                            }
                        </style>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loging-back"></div>
</div>

<script>
    $(document).keydown(function (event) {
        if ($("#transparent").css("display") == "block") {
            if (event.keyCode == 13) {
               logSub();

            }
        }

    });

    $('.loging li .input-box em').on("click",function(){
        $(this).siblings(".regsiter-width").val("").focus();
    });

    $(".loging li .input-box .regsiter-width").blur(function(){
        $(this).parent(".loging li .input-box").css({"box-shadow":"0px 0px 0 0 rgba(255,255,255,1)","border": "1px solid #eeeeee"})
    });
    $(".loging li .input-box .regsiter-width").focus(function(){
        $(this).parent(".loging li .input-box").css({"box-shadow":"0px 0px 1px 1px rgba(0,190,212,1)","border": "1px solid transparent"})
    });
</script><?php endif; ?>
<div class="win hide"></div>
<header>
 <a href="/" class="a_box"><img src="<?php echo (($site["logo_head_w3g"])?($site["logo_head_w3g"]):$site['logo_head']); ?>" alt="logo"></a>
 <!-- <i class="icon icon-search"></i> -->
 <div class="users">
     <?php if($_SESSION['mid']): ?><img src="<?php echo getUserFace($user['uid'],'b');?>" width="30" height="30">
         <!--登录后显示-->
         <span>
             <i class="icon icon-shang1"></i>
             <!-- <a href="<?php echo U('classroom/UserShow/index',array('uid'=>$_SESSION['mid']));?>">我的主页</a> -->
             <a href="<?php echo U('classroom/User/index');?>">个人中心</a>
             <a href="javascript:;" onclick="logout()">退出</a>
         </span>
         <?php else: ?>
         <img src="__THEME__/image/noavatar/big.jpg">
         <!--登录后影藏-->
         <span>
             <i class="icon icon-shang1"></i>
             <a href="<?php echo U('public/Passport/login_g');?>">登录</a>
             <?php if($this_mhm_id != ''): ?><a href="<?php echo U('public/Passport/reg',array('this_mhm_id'=> $this_mhm_id));?>">注册</a>
                 <?php else: ?>
                 <a href="<?php echo U('public/Passport/reg');?>">注册</a><?php endif; ?>
         </span><?php endif; ?>
 </div>
 <a href="<?php echo U('classroom/Index/search');?>" class="sousuo_a"><i class="icon icon-search"></i></a>
</header>
<div class="clear"></div>
<script type="text/javascript">
    if(navigator.userAgent.match(/(iPhone|Android|ios)/i)) {
        $(window).load(function () {
            $('.modular,.logos,.window-box').show();
        });
    }

    $(".modular").on("click",function(){
        $(".win").show();
        $(".window-box").animate({marginLeft:"0"});
        var dis = $(".win").css('display');
        if(dis == 'block'){
            $("body").css("overflow-y","hidden");
        }
    });

    $(".win").on("click",function(){
        $(".window-box").animate({marginLeft:'-70%'});
        $(".win").hide();
        var dis = $(".win").css('display');
        if(dis == 'none'){
            $("body").css("overflow-y","visible");
        }
    });
      function show_list(obj){
        if ($(obj).siblings('.course-class').css("display")=="none") {
              $(obj).siblings('.course-class').show();
        }
        else {
            $(obj).siblings('.course-class').hide();
        }

      };
</script>

<link  href="__APP__/icon/iconfont.css" rel="stylesheet" type="text/css">
<link  href="__APP__/css/style_media.css" rel="stylesheet" type="text/css">
<style>
    header{width: 100%;display: none;}
    .center-box{width: 100%;}
    .icon{background: transparent;}
    .center-box{background: url(__THEME__/icon/bg2.jpg) center no-repeat;}
    .my-service .icon-yue{font-size: 12px;margin-right: 6px;}
</style>
<body style="background:#f0f0f2">
    <header style="display:block">
        <i class="icon icon-zuojiantou"></i>
        <h3>个人中心</h3>
    </header>
    <!--center-box-->
    <div class="center-box">
        <dl>
            <dt>
                <img src="<?php echo getUserFace($user['uid'],'b');?>">
            </dt>
            <dd>
                <h5><?php echo strip_tags(getUserSpace($user['uid']));?></h5>
                <ul>
                    <li>积分：<?php echo (($credit['credit']['score']['value'])?($credit['credit']['score']['value']):0); ?></li>
                    <li>粉丝：<?php echo (($tmp[$uid]['follower'])?($tmp[$uid]['follower']):0); ?></li>
                    <li>课程：<?php echo (($videocont)?($videocont):0); ?></li>
                </ul>
            </dd>
        </dl>
        <a href="<?php echo U('classroom/User/setInfo');?>" class="set-up"><i class="icon icon-youjiantou"></i></a>
    </div>
    <div class="col-all-box">
        <ul class="first-cate-icons">
            <li>
                <a href="<?php echo U('classroom/UserShow/index',array('uid'=>$_SESSION['mid']));?>">
                    <i class="icon icon-mingpian"></i>
                    <p>个人主页</p>
                </a>
            </li>
        <!--     <li>
                <a href="<?php echo U('classroom/Home/video');?>">
                    <i class="icon icon-kecheng2"></i>
                    <p style="line-height: 11px">我的点播</p>
                </a>
            </li> -->
            <li>
                <a href="<?php echo U('classroom/Home/live');?>">
                    <i class="icon icon-zhibo"></i>
                    <p style="line-height: 7px">我的课程</p>
                </a>
            </li>
            <li>
                <a href="<?php echo U('classroom/Home/order');?>">
                    <i class="icon icon-dingdan"></i>
                    <p style="line-height: 12px">我的订单</p>
                </a>
            </li>
        </ul>
    </div>

    <!--my-service-->
    <div class="col-all-box my-service">
        <ul>
            <!-- <li><a href="<?php echo U('classroom/Home/wenti');?>"><i class="icon icon-tiwen"></i>我的提问</a> </li>
            <li><a href="<?php echo U('classroom/Home/review');?>"><i class="icon icon-xiedianping"></i>我的点评</a> </li>
            <li><a href="<?php echo U('classroom/Home/note');?>"><i class="icon icon-biji"></i>我的笔记</a> </li>
            <li><a href="<?php echo U('classroom/Home/note');?>"><i class="icon icon-biji"></i>我的笔记</a> </li> -->
            <li><a href="/single/14.html"><i class="icon icon-biji"></i>使用说明</a> </li>
            <li><a href="<?php echo U('classroom/Home/live');?>"><i class="icon icon-biji"></i>我的课程</a> </li>
            <li><a href="<?php echo U('exams/index/index');?>"><i class="icon icon-kaoshi1"></i>题库</a> </li>
            <li <?php if(empty($health)){echo "style='display:none;'";} ?>><a href="<?php echo U('classroom/Home/exam_predictions');?>"><i class="icon icon-kaoshi1"></i>健康复习</a></li>
            <li><a href="/my/exams.html"><i class="icon icon-kaoshi1"></i>收藏</a> </li>
            <li><a href="<?php echo U('classroom/User/changepsw');?>"><i class="icon icon-mima"></i>修改密码</a> </li>
            <!-- <li><a href="<?php echo U('classroom/Home/exams');?>"><i class="icon icon-kaoshi1"></i>我的考试</a> </li>
            <li><a href="<?php echo U('classroom/Home/mychengji');?>"><i class="icon icon-search"></i>成绩查询</a> </li>
            <li><a href="<?php echo U('classroom/Home/share');?>"><i class="icon icon-fenxiang1"></i>我的分享</a> </li> -->
        </ul>
    </div>
    <!-- <div class="col-all-box my-service">
        <ul>
           <li><a href="<?php echo U('classroom/User/account');?>"><i class="icon icon-yue"></i>我的余额</a></li> 
            <li><a href="<?php echo U('classroom/User/spilt');?>"><i class="icon icon-tubiao-"></i>我的收入</a></li>
            <li><a href="<?php echo U('classroom/User/credit');?>"><i class="icon icon-iconfont"></i>我的积分</a></li>
            <li><a href="<?php echo U('classroom/User/recharge');?>"><i class="icon icon-tubiao-"></i>会员充值</a> </li>
            <?php if($GLOBALS['ts']['teacher'] == 1){ ?>
            <li><a href="<?php echo U('classroom/User/teacherVideo');?>"><i class="icon icon-shangchuan"></i>上传课程</a></li>
            <?php } ?>
            <li><a href="<?php echo U('classroom/User/videoCoupon');?>"><i class="icon icon-youhuiquan"></i>优惠券</a> </li>
        </ul>
    </div> -->
    <!-- <div class="col-all-box my-service">
        <ul>
            <li><a href="<?php echo U('classroom/User/alipay');?>"><i class="icon icon-jikediancanicon23"></i>我的支付宝</a></li>
            <li><a href="<?php echo U('classroom/User/card');?>"><i class="icon icon-14shouyexinyongqia"></i>我的银行卡</a></li>
            <li><a href="<?php echo U('classroom/User/address');?>"><i class="icon icon-dizhi"></i>收货地址</a> </li>
            <li><a href="<?php echo U('classroom/User/changepsw');?>"><i class="icon icon-mima"></i>修改密码</a> </li>
        </ul>
    </div> -->
</body>
<script type="text/javascript">
$(document).ready(function(){
            $(".nav_list ul li:eq(3)").addClass("on");
        });
</script>

    <!--footer start-->
    <!-- <div class="footer">
        <b><?php echo ($site['site_footer']); ?></b>
    </div> -->
    <nav class="nav_list">
        <ul>
            <li>
                <a href="/">
                <i class="icon icon-shouye"></i>
                <p>首页</p>
                </a>
            </li>
            <li>
                <!--<?php if(IS_CHILDS_HOST): ?><a href="<?php echo U('school/School/course',array('id'=>$mhm_id));?>"><?php else: ?><a href="<?php echo U('classroom/Video/index');?>"><?php endif; ?>-->
                <a href="/teacher.html">
                <i class="icon icon-shu"></i>
                <p>名师</p>
                </a>
            </li>
            <li>
                <a href="/course.html?vtype=2">
                <i class="icon icon-faxian"></i>
                <p>课程</p>
                </a>
            </li>
            <li>
                <?php if($_SESSION['mid']): ?><a href="<?php echo U('classroom/User/index');?>">
                        <?php else: ?>
                    <a href="<?php echo U('public/Passport/login_g');?>"><?php endif; ?>
                <i class="icon icon-wode"></i>
                <p>我的</p>
                </a>
            </li>
        </ul>
    </nav>
    <!--footer end-->
    <!--&lt;!&ndash; 统计代码&ndash;&gt;-->
    <div id="site_analytics_code" style="display:none;">
    <?php echo (base64_decode($site["site_analytics_code"])); ?>
    </div>
    <?php if(($site["site_online_count"])  ==  "1"): ?><script src="<?php echo SITE_URL;?>/online_check.php?uid=<?php echo ($mid); ?>&uname=<?php echo ($user["uname"]); ?>&mod=<?php echo MODULE_NAME;?>&app=<?php echo APP_NAME;?>&act=<?php echo ACTION_NAME;?>&action=trace"></script><?php endif; ?>

    <script src="__THEMEW3G__/js/script.js"></script>
</div>
</body>
</html>