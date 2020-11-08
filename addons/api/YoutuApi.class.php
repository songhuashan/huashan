<?php

class YoutuApi extends Api
{
    protected $youtu;
    /**
     * 初始化模型
     */
    public function _initialize()
    {
        $this->youtu = model('Youtu');
    }
    /**
     * 创建一个新的人人物
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     * @return
     */
    public function newperson()
    {
        $uid = $this->youtu->getPersonIdByFace($this->attach_id);
        if ($uid) {
            // 如果成功,直接登录
            $count = M('user')->where(['uid' => $uid])->count() > 0 ? true : false;
            if ($count) {
                $this->exitJson((object) [], 0, '你已经与其他账号绑定,无法再次绑定');
            }
        }
        $data = [
            'uid'    => $this->mid,
            'tag'    => 'user',
            'groups' => ['user'],
            'image'  => $this->attach_id,
        ];
        $res = $this->youtu->newperson($data);
        if ($res) {
            $this->exitJson($res, 1);
        } else {
            $this->exitJson($this->youtu->getError(), 0, '处理失败,请稍后再试');
        }
    }

    /**
     * 获取一个人物信息
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     * @return   [type]                         [description]
     */
    public function getinfo()
    {
        $res = $this->youtu->getPersonInfoById($this->mid);
        if ($res) {
            $this->exitJson($res, 1);
        } else {
            $this->exitJson($this->youtu->getError(), 0, '处理失败,请稍后再试');
        }
    }

    /**
     * 检测某个人物是否已经存在
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     * @return   [type]                         [description]
     */
    public function checkIsExist()
    {
        $res = $this->youtu->isExist($this->mid);
        if ($res) {
            $this->exitJson(['is_exist' => 1], 1);
        } else {
            $this->exitJson(['is_exist' => 0], 1);
        }
    }

    /**
     * 获取状态
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-23
     * @return   [type]                         [description]
     */
    public function getFaceStatus()
    {
        $status = $this->youtu->getInitFaceStatus($this->mid);
        $this->exitJson(['status' => $status], 1);
    }

    /**
     * 添加人脸(用户成功创建用户,其后在添加更多的人脸)
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-21
     */
    public function addFace()
    {
        $face_ids = is_array($this->attach_ids) ? $this->attach_ids : array_unique(array_filter(explode(',', $this->attach_ids)));
        // 检测是否本人操作
        if(!$this->youtu->isOwn($this->mid,$face_ids)){
            $this->exitJson((object)[],0,$this->youtu->getError());
        }
        $res      = $this->youtu->addFace($this->mid, $face_ids);
        if ($res) {
            $this->exitJson($res, 1);
        } else {
            $this->exitJson($this->youtu->getError(), 0, '处理失败,请稍后再试');
        }
    }

    /**
     * 人脸匹配--扫脸登录
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-21
     * @return   [type]                         [description]
     */
    public function faceLogin()
    {
        // 匹配人脸
        $uid = $this->youtu->getPersonIdByFace($this->attach_id);
        if ($uid) {
            // 如果成功,直接登录
            $passport = model('Passport');
            $userInfo = M('user')->where(['uid' => $uid])->find();
            if ($userInfo) {
                $result = $passport->loginLocal($userInfo['uname'], $userInfo['password'], false, true, true);
                if ($result) {
                    //查询有无token
                    $token = M('login')->where(array('uid' => $result['uid']))->find();

                    if (!$token) {
                        $data['oauth_token']        = getOAuthToken($result['uid']); //添加app认证
                        $data['oauth_token_secret'] = getOAuthTokenSecret();
                        $data['uid']                = $result['uid'];

                        $result['oauth_token']        = $data['oauth_token'];
                        $result['oauth_token_secret'] = $data['oauth_token_secret'];
                        M('login')->add($data);
                    }
                    //操作积分
                    model('Credit')->getCreditInfo($result['uid'], 1);
                    $this->exitJson($result, 1);
                } else {
                    $this->exitJson((object) [], 0, '登录失败,请重试');
                }
            }
            $this->exitJson((object) [], 0, '未找到你的账号信息,请检查是否绑定');

        } else if ($uid === false) {
            $error = $this->youtu->getError();
            if ($error['errorcode'] == '-1101') {
                $this->exitJson((object) [], 0, '人脸识别失败,请重新拍照');
            }
        }
        $this->exitJson((object) [], 0, '未找到你的账号信息,请检查是否绑定');
    }

    /**
     * 人脸验证(用于用户扫描登录)
     * @param string person_id 个体ID
     * @param string face 待验证人脸
     * @param int type 1:使用本地图片 2:使用链接 default:2
     */
    public function faceverify()
    {
        $res = $this->youtu->faceverify($this->mid, $this->attach_id);
        if ($res && $res['ismatch'] === true) {
            $this->exitJson($res, 1);
        } else if ($res === false) {
            $error = $this->youtu->getError();
            if ($error['errorcode'] == '-1101') {
                $this->exitJson($this->youtu->getError(), 0, '人脸识别失败,请重试');
            }
        }
        $this->exitJson((object) [], 0, '校验失败,请确认你是本人操作');
    }

    /**
     * 删除人脸
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2018-01-18
     * @return   [type]                         [description]
     */
    public function delPerson()
    {
        $user_id = $this->uid;
        if($user_id){
            $this->exitJson($this->youtu->doDelPerson($user_id), 1);
        }
    }
}