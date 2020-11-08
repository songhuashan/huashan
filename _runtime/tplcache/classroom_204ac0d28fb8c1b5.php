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


    <style>
        .maskbox {position: fixed;left: 0;top: 100px;width: 100%;height: 100%;z-index: 99;display: none;}
    </style>
    <div class="cover"></div>
    <div class="min-height-wrap">
        <div class="maskbox"></div>
        <div class="body">
            <ul class="selectul">
                
                <li class="r02 border_sx" name="list_dl">
                    <div class="hd"><?php if($cateId){ ?><?php echo limitNumber($title,6);?><?php }else{ ?>全部<?php } ?><i></i></div>
                    <div class="bd">
                        <dl>
                            <dd class="clearfix n1">
                                <div class="item <?php if(($cateId)  ==  ""): ?>active<?php endif; ?>"><a href="<?php echo U('classroom/Teacher/index');?>"  id="cate0">全部</a></div>
                                <?php if(is_array($selCate)): ?><?php $i = 0;?><?php $__LIST__ = $selCate?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = ($i % 2 )?><div class="item <?php if(($vo["zy_teacher_category_id"])  ==  $cateId): ?>active<?php endif; ?>"><a href="javascript:;" class="screen" data-type="cateId" data-value="<?php echo ($vo["zy_teacher_category_id"]); ?>" ><?php echo ($vo["title"]); ?></a></div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
                            </dd>
                            <dd class="n2">
                            <?php if(is_array($cate)): ?><?php $i = 0;?><?php $__LIST__ = $cate?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = ($i % 2 )?><div class="item <?php if(($vo["zy_teacher_category_id"])  ==  $cate_id): ?>active<?php endif; ?>"><a href="javascript:;"  class="screen" data-type="cateId" data-value="<?php echo ($cateId); ?>%2C<?php echo ($vo["zy_teacher_category_id"]); ?>" ><?php echo ($vo["title"]); ?></a></div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
                            </dd>
                            <dd class="n3">
                            <?php if(is_array($childCate)): ?><?php $i = 0;?><?php $__LIST__ = $childCate?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = ($i % 2 )?><div class="item <?php if(($vo["zy_teacher_category_id"])  ==  $cate_ids): ?>active<?php endif; ?>"><a href="javascript:;"  class="screen" data-type="cateId" data-value="<?php echo ($cateId); ?>%2C<?php echo ($cate_id); ?>%2C<?php echo ($vo["zy_teacher_category_id"]); ?>" ><?php echo ($vo["title"]); ?></a></div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </li>
                <li class="r03" name="list_dl">
                    <div class="hd">筛选<i></i></div>
                    <div class="bd">
                        <dl>
                            <dt>课程类型</dt>
                            <dd name="group-list">
                                <a href="javascript:;" class="screen <?php if(($course_type)  ==  "1"): ?>checked<?php endif; ?>" data-type="course_type" data-value="1" >点播<span class="icon-ck"></span></a>
                                <a href="javascript:;" class="screen <?php if(($live_type)  ==  "2"): ?>checked<?php endif; ?>" data-type="live_type" data-value="2" >直播<span class="icon-ck"></span></a>
                            </dd>
                        </dl>
                    </div>
                </li>
                <li class="r01" name="list_dl">
                    <div class="hd"><?php switch($sort_type): ?><?php case "1":  ?>最热<?php break;?><?php case "2":  ?>最新<?php break;?><?php default: ?>综合排序<?php endswitch;?><i></i></div>
                    <div class="bd">
                        <dl>
                            <dd <?php if(($sort_type)  ==  ""): ?>class="active"<?php endif; ?>><a href="javascript:;" class="screen" data-type="sort_type" data-value="">综合排序</a></dd>
                            <dd <?php if(($sort_type)  ==  "1"): ?>class="active"<?php endif; ?>><a href="javascript:;" class="screen" data-type="sort_type" data-value="1">最热</a></dd>
                            <dd <?php if(($sort_type)  ==  "2"): ?>class="active"<?php endif; ?>><a href="javascript:;" class="screen" data-type="sort_type" data-value="2">最新</a></dd>
                        </dl>
                    </div>
                </li>
            </ul>
            <?php if($cateId){ ?>
            <div class="searchdiv">在“<span><?php echo msubstr(t($title),0,10,'utf-8',true);?></span>”分类下，找到<?php echo ($data["count"]); ?>讲师</div>
            <?php } ?>
            <section class="bg_box">
            <ul class="teacherlist clearfix" >
                <?php if($listData){ ?>
                    <?php if(is_array($listData)): ?><?php $i = 0;?><?php $__LIST__ = $listData?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = ($i % 2 )?><li>
                            <a href="<?php echo U('classroom/Teacher/view',array('id'=>$vo['id']));?>" class="name">
                              <img src="<?php echo cutImg($vo['head_id'],70,70);?>" alt="<?php echo ($vo["name"]); ?>">
                                <div class="tit">
                                    <h3><?php echo msubstr(t($vo['name']),0,15,'utf-8',true);?></h3>
                                    <span><?php echo msubstr(t($vo['school']),0,10,'utf-8',true);?></span>
                                </div>
                                <div class="dis"><?php echo limitNumber($vo['inro'],35);?></div>
                            </a>
                        </li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
                <?php }else{ ?>
                    <span style="font-size: 14px;padding: 10px;">已经没有相关老师啦~~</span>
                <?php } ?>
            </ul>
          </section>
        </div>
        <?php if($listData){ ?>
            <div class="loadding">
                <?php if($data['nowPage'] == $data['totalPages']): ?><div>ᐠ(￣▽￣)ᐟ我可是有底线的</div><?php else: ?>正在加载更多。。<?php endif; ?>
            </div>
            <a class="backtop"></a>
        <?php } ?>
    </div>
<script>
    $(function(){
        $(".hd").click(function(){
            if($(".maskbox").css("display")=="none") {
                $(".maskbox").css({"display": "block"});
            }else{
                $(".maskbox").css({"display": "none"});
            }
        });

        //同步请求
        $(".screen").click(function(){
            var _this = this;
            var type = $(_this).data("type"),
                value = $(_this).data("value"),
                baseUrl = "<?php echo U('classroom/Teacher/index');?>";

            window.location.href = getRequestUrl(type,value,baseUrl);
        });
        function getRequestUrl(field,value,baseUrl){
            if(field){
                //匹配是否有该参数
                var reg = new RegExp("(^|&)" + field + "=([^&]*)(&|$)", "i");
                var r = window.location.search.substr(1).match(reg);
                //已经存在参数
                var in_params = false;
                if (r != null){
                    in_params = true;
                }
                //获取参数部分
                var url = window.location.search;
                var replaceReg = new RegExp(field+'=[^&]+','g');
                if(value){
                    //合法参数传递方式
                    if(in_params){
                        url = url.replace(replaceReg,field+'='+value);
                    }else{
                        if(url.indexOf("?") != -1){
                            url += '&'+field+'='+value;
                        }else{
                            url += '?'+field+'='+value;
                        }
                    }

                }else{
                    //如果value不存在,移除该参数
                    url = url.replace(replaceReg,'');
                }
            }
            url = url.replace(/&{2,}/,'&').replace(/&$/,'');
            setsearchUrl = url;
            return baseUrl ? baseUrl+url: document.domain + url;
        }
        var p = 1;
        $(window).scroll(function(){
            //已经滚动到上面的页面高度
            var scrollTop = $(this).scrollTop();
            //页面高度
            var scrollHeight = $(document).height();
            //浏览器窗口高度
            var windowHeight = $(this).height();
            //此处是滚动条到底部时候触发的事件，在这里写要加载的数据，或者是拉动滚动条的操作
            if (scrollTop + windowHeight == scrollHeight) {
                if(!p || p >= "<?php echo ($data['totalPages']); ?>"){
                    $('.loadding').html('<div>ᐠ(￣▽￣)ᐟ我可是有底线的</div>');
                    return false;
                }else{
                    p = p+1;
                    ajaxBang();
                }
            }
        });
        //请求事件
        function ajaxBang(){
            $.ajax({
                type: "GET",
                url:"<?php echo U('classroom/Teacher/getTeacherList');?>",
                data:"p="+p+"&sort_type=<?php echo ($_GET['sort_type']); ?>&cateId=<?php echo ($_GET['cateId']); ?>&course_type=<?php echo ($_GET['course_type']); ?>&mhm_id=<?php echo ($_GET['mhm_id']); ?>",
                dataType:"json",
                success:function(data){
                    appendHtml(data);
                }
            });
        }
        //追加html
        function appendHtml(data){
            $(".teacherlist").append(data.data);
            if(!p || p >= data.totalPages){
                $('.loadding').html('<div>ᐠ(￣▽￣)ᐟ我可是有底线的</div>');
                return false;
            }
        }
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