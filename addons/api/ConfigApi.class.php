<?php
/**
 * @name 获取配置接口
 * @author martinsun<syh@sunyonghong.com>
 * @version v1.0
 */

class ConfigApi extends Api
{
    public function __construct()
    {
        parent::__construct(true);
    }
    /**
     * @name 初始化检测方法
     */
    protected function _initialize()
    {
        //加密算法验证
        $ctime = hexdec($this->hextime);
        //收到请求的时间不能大于60s
        if (time() - 60 > $ctime) {
            $this->exitJson((object) [], 0, '口令已经失效');
        }
        //转为小写
        $_token = strtolower(md5($ctime . $this->hextime));
        if (strtolower($this->token != $_token)) {
            $this->exitJson((object) [], 0, '口令非法');
        }
    }

    /**
     * @name 获取API下载地址
     */
    public function getAppVersion()
    {
        $down_url_android = model('Xdata')->getconfig('download_url', 'appConfig');
        $down_url_ios     = model('Xdata')->getconfig('download_url_ios', 'appConfig');
        if ($down_url_android) {
            preg_match('/version=([^&]+)/', $down_url_android, $data);
            $version           = $data[1] ?: '';
            $return['android'] = [
                'down_url' => $down_url_android,
                'version'  => $version,
            ];
        }

        if ($down_url_ios) {
            preg_match('/version=([^&]+)/', $down_url_ios, $data);
            $version       = $data[1] ?: '';
            $return['ios'] = [
                'down_url' => $down_url_ios,
                'version'  => $version,
            ];
        }
        $this->exitJson($return, 1);
    }

    //支付开关配置
    public function paySwitch()
    {
        $payConfig     = model('Xdata')->get("admin_Config:payConfig");
        $config['pay'] = $payConfig['pay'];

        $this->exitJson($config, 1);
    }

    /**
     * 获取人脸识别应用场景
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-12-13
     * @return   [type]                         [description]
     */
    public function getFaceScene()
    {
        $youtuConf = model("Xdata")->get("admin_Config:youtu");
        if (!$youtuConf || $youtuConf['is_open'] != 1) {
            $this->exitJson([
                'is_open'    => 0,
                'open_scene' => [],
            ], 1);
        }
        $config = model('Xdata')->get('admin_Config:youtuscene') ?: [];
        if ($config) {
            if (isset($config['login_force_verify']) && $config['login_force_verify'] == 1) {
                $config['scene'][] = 'login_force_verify';
            }

            $config = $config['scene'];
        }
        $this->exitJson(['is_open' => 1, 'open_scene' => array_values($config)], 1);
    }

    /**
     * 获取人脸识别是否开启
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2018-01-08
     * @return   [type]                         [description]
     */
    public function getFaceStatus()
    {
        $youtuConf = model("Xdata")->get("admin_Config:youtu");
        if (!$youtuConf || $youtuConf['is_open'] != 1) {
            $this->exitJson(['is_open' => 0], 1);
        }

        $this->exitJson(['is_open' => 1], 1);
    }

    /**
     * 获取视频加密key
     * @Author   Martinsun<syh@sunyonghong.com>
     * @DateTime 2018-03-01
     * @return   [type]                         [description]
     */
    public function getVideoKey()
    {
        $key = C('QINIU_TS_KEY');
        if(!$key){
            // 写入默认的加密key
            $config = include CONF_PATH.'/config.inc.php';
            $config['QINIU_TS_KEY'] = $key = 'eduline201701010';
            file_put_contents(CONF_PATH.'/config.inc.php', ("<?php \r\n return " . var_export($config, true) . "; \r\n ?>") );
        }

        $this->exitJson(['video_key'=>$key],1);
    }


    /**
     * 营销数据开关
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2018-01-08
     * @return   [type]                         [description]
     */
    public function getMarketStatus()
    {
        $marketConf = model("Xdata")->get("admin_Config:marketConfig");
        if (!$marketConf || $youtuConf['order_switch'] != 1) {
            $this->exitJson(['order_switch' => 0], 1);
        }

        $this->exitJson(['order_switch' => 1], 1);
    }
}
