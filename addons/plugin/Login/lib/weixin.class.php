<?php
use EasyWeChat\Foundation\Application;

/**
 * 微信第三方登录
 */
class weixin
{
    private $wx_config;
    private $is_weixin = 0;
    /**
     * 初始化方法
     * @Author MartinSun<syh@sunyonghong.com>
     * @Date   2017-09-22
     */
    public function __construct()
    {
        // 检测是否微信浏览器
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $this->is_weixin = 1;
            // 获取公众号配置
            $this->wx_config = model('Xdata')->get('admin_Config:weixin');
        } else {
            // 获取开放平台配置
            $config          = model('Xdata')->lget('login');
            $this->wx_config = [
                'appid'     => $config['wx_app_id'],
                'appsecret' => $config['wx_app_secret'],
            ];
        }

    }
    /**
     * 获取登录授权跳转地址
     * @Author MartinSun<syh@sunyonghong.com>
     * @Date   2017-09-22
     * @param  string $callback [description]
     * @return [type] [description]
     */
    public function getUrl($callback = '')
    {
        // 清理旧的信息
        session('weixin_user',null);
        //-------配置
        $callback = $callback ?: U('public/Widget/displayAddons', array('addon' => 'Login', 'hook' => 'no_register_display', 'type' => 'weixin')) . '/';

        $config = [
            'app_id' => $this->wx_config['appid'],
            'secret' => $this->wx_config['appsecret'],
        ];
        // 检测是否微信浏览器
        if (!$this->is_weixin) {
            $config['oauth'] = [
                'scopes'   => ['snsapi_login'],
                'callback' => $callback,
            ];
        } else {
            $config['oauth'] = [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => $callback,
            ];
        }
        $app   = new Application($config);
        $oauth = $app->oauth;
        return $oauth->redirect()->send();
    }

    /**
     * 验证微信用户授权
     * @Author MartinSun<syh@sunyonghong.com>
     * @Date   2017-09-22
     * @return [type] [description]
     */
    public function checkUser()
    {
        return true;
    }

    /**
     * 返回用户信息
     * @Author MartinSun<syh@sunyonghong.com>
     * @Date   2017-09-22
     * @return [type] [description]
     */
    public function userInfo()
    {

        $config = [
            'app_id' => $this->wx_config['appid'],
            'secret' => $this->wx_config['appsecret'],
        ];

        $app      = new Application($config);
        $user     = $app->oauth->user();
        $userInfo = $user->toArray();
        $userInfo = $userInfo;
        session('weixin_user', $userInfo);

        $user['id']       = $userInfo['original']['unionid'];
        $user['uname']    = $userInfo['original']['nickname'];
        $user['province'] = $userInfo['original']['province'];
        $user['city']     = $userInfo['original']['city'];
        $user['userface'] = $userInfo['original']['headimgurl'];
        $user['sex']      = ($userInfo['original']['sex'] == '1') ? 1 : 0;
        return $user;
    }

}
