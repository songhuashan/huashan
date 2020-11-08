<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML><head>
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

<style media="screen">
  .fl{float: left;margin: 5px 0px 0 10px;width: 30px;height: 30px;}
  .fr{display: block;margin-top: 5px;}
</style>
<!--container-->
<div class="content">
  <div class="loginbox">
    	<a class="icon-colse" href="/"></a>
        <div class="login-hd">
        	<li class="on"><a href="javascript:;">登录</a></li>
            <li>·</li>
            <li>
                <?php if($this_mhm_id)  { ?>
                    <a href="<?php echo U('public/Passport/reg',array('this_mhm_id'=> $this_mhm_id));?>">注册</a>
                <?php }else{ ?>
                    <a href="<?php echo U('public/Passport/reg');?>">注册</a>
                <?php } ?>
            </li>
        </div>
        <div class="login-bd">
            <form id="ajax_login_form" method="POST"  id="account_input" name="login_email"  action="<?php echo U('public/Passport/doLogin');?>">
        	<div class="item code">
            	<div class="num"><select><option>+86</option></select></div>
            	<input type="text" name="login_email" placeholder="请输入<?php echo ($site['site_keyword']); ?>账号/手机/邮箱">
            </div>
            <div class="item">
                <?php if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false): ?>
                <input id="pwd_input" name="login_password" type="text" class="inp" autocomplete="off" placeholder="请输入登录密码" style="border:1px solid #e6e6e6"/>
                <?php else: ?>
                <input id="pwd_input" name="login_password" type="password" class="inp" autocomplete="off" placeholder="请输入登录密码" style="border:1px solid #e6e6e6"/>
                <?php endif; ?>
            </div>
                </form>
        </div>
        <a class="login_btn" href="javascript:;"   onclick="check_login();" >登录</a>

        <?php if(($face_login)  ==  "1"): ?><a href="<?php echo U('public/Passport/faceLogin');?>" style="background: url('__THEME__/icon/face_scanning.png') no-repeat;" class="face_icon"></a><?php endif; ?>
        <?php if(Addons::requireHooks('login_input_footer') && Addons::hook('login_input_footer')): ?>
            <?php echo Addons::hook('login_input_footer');?>
        <?php endif; ?>

      <a class="fr" href="<?php echo U('home/Repwd/index');?>">忘记密码</a>

  </div>
</div>

<script>
    $(function(){
        $("#preloader").hide();
    });
</script>

<script type="text/javascript">

    function check_login() {
        if($('#account_input').val() == ''){
            alert('登录名或登录账号不能为空');
            return;
        }
        if($('#pwd_input').val() == ''){
            alert('登录密码不能为空');
            return;
        }
        $.post(U('public/Passport/doLogin'),$('#ajax_login_form').serialize(),function(data){
            if(data.status == 1){
                alert('登录成功');
                var reurl = "<?php echo ($reurl); ?>";
                if(reurl) {
                    window.location.href = reurl;
                } else {
                    window.location.href = data.data;;
                }
            } else {
                alert(data.info);
                return false;
            }
        },'json');
    }
</script>