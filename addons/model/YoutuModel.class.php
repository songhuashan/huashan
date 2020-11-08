<?php
/**
 * 优图接口对接
 */
tsload(implode(DIRECTORY_SEPARATOR, array(SITE_PATH, 'api', 'Youtu', 'include.php')));
use TencentYoutuyun\Conf;
use TencentYoutuyun\Youtu;

class YoutuModel
{
    private $_appid      = '';
    private $_secret_id  = '';
    private $_secret_key = '';
    private $_user_id    = '';
    private $attach_ids  = [];
    protected $error;
    /**
     * 配置
     */
    private $conf;

    /**
     * 初始化方法
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     */
    public function __construct()
    {
        $this->conf['candidates'] = 90;
        // 获取配置
        $conf = model("Xdata")->get("admin_Config:youtu");
        foreach ($conf as $key => $value) {
            $key        = '_' . strtolower($key);
            $this->$key = $value;
        }
        unset($conf);
        Conf::setAppInfo($this->_appid, $this->_secret_id, $this->_secret_key, $this->_user_id, conf::API_YOUTU_END_POINT);
        return $this;
    }

    /**
     * 创建个体
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     * @param    array                          $data 个体数据
     *  $data:[
     *      'uid' => '用户的UID标识'
     *      'tag' => 'tag名称'
     *      'groups' => ['加入组标识'],数组格式
     *      'image' => '待加入的人脸图片',为int时,表示本地图片,否则为远程图片
     *  ]
     * @return   [type]                               [description]
     */
    public function newperson(array $data)
    {
        if (is_numeric($data['image'])) {
            // 使用本地图片
            $attach = model('Attach')->getAttachById($data['image']);
            if ($attach) {
                $data['image'] = UPLOAD_PATH . '/' . $attach['save_path'] . $attach['save_name'];
                $this->attach_ids[] = ['attach_id'=>$attach['attach_id'],'attach_path'=>$data['image']];
            }
            $res = YouTu::newperson($data['image'], (string) $data['uid'], $data['groups'], 'person_' . $data['uid'], $data['tag']);
            $this->_delete();
        } else {
            $res = YouTu::newpersonurl($data['image'], (string) $data['uid'], $data['groups'], 'person_' . $data['uid'], $data['tag']);
        }

        return $this->haddleResponse($res, ['person_id', 'group_ids']);
    }
    /**
     * 获取个人的信息
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     * @param    string                         $person_id 人物ID
     * @return   [type]                                    [description]
     */
    public function getPersonInfoById($person_id = '0')
    {
        //获取信息
        $res = YouTu::getinfo((string) $person_id);
        return $this->haddleResponse($res);
    }

    /**
     * 检测某人脸是否存在
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     * @param    string                         $person_id [description]
     * @return   boolean                                   [description]
     */
    public function isExist($person_id = '0')
    {
        $info = $this->getPersonInfoById($person_id);
        return ($info === false) ? false : true;
    }

    /**
     * 添加人脸
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     */
    public function addFace($person_id = '0', $face_arr = [], $type = 'local')
    {
        if ($type == 'local') {
            $faces_path = [];
            foreach ($face_arr as $k => $attach_id) {
                $attach = model('Attach')->getAttachById($attach_id);
                if (!$attach) {
                    contiune;
                }
                $attach_path = UPLOAD_PATH . '/' . $attach['save_path'] . $attach['save_name'];
                if (!file_exists($attach_path)) {
                    contiune;
                }
                $faces_path[] = $attach_path;
                $this->attach_ids[] = ['attach_id'=>$attach_id,'attach_path'=>$attach_path];
            }
            $uploadRet = YouTu::addface((string) $person_id, $faces_path);
            $this->_delete();
        } else {
            $uploadRet = YouTu::addfaceurl((string) $person_id, $face_arr);
        }

        return $this->haddleResponse($uploadRet);
    }

    /**
     * 获取应用下的分组列表
     * @param string group_id 分组ID
     */
    public function getPersonListByAppId($app_id)
    {
        return YouTu::getgroupids($app_id);
    }

    /**
     * 获取成员列表(个体列表)
     * @param string group_id 分组ID
     */
    public function getPersonListByGroupId($group_id)
    {
        return YouTu::getpersonIds($group_id);
    }
    /**
     * 获取人脸列表
     * @param string person_id 个体ID
     */
    public function getFaceListByPersonId($person_id)
    {
        return YouTu::getfaceIds($person_id);
    }

    /**
     * 人脸匹配
     * @param string face_url 需要匹配的人脸URL
     * @param string group_id 分组ID
     */
    public function getFaceidentify($face = '', $group_id = 'user', $type = 'local',$delete_attach = true)
    {
        if ($type == 'local') {
            $attach      = model('Attach')->getAttachById($face);
            $attach_path = UPLOAD_PATH . '/' . $attach['save_path'] . $attach['save_name'];
            $this->attach_ids[] = ['attach_id'=>$face,'attach_path'=>$attach_path];
            $uploadRet   = YouTu::faceidentify($attach_path, $group_id);
            $delete_attach && $this->_delete();
        } else {
            $uploadRet = YouTu::faceidentifyurl((string) $face, $group_id);
        }

        return $this->haddleResponse($uploadRet);
    }

    /**
     * 获取人脸初始化情况
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-23
     * @return   int 1:已经完成 2:需要上传更多的人脸 0:不存在该人脸信息
     */
    public function getInitFaceStatus($person_id = '0')
    {
        $info = $this->getPersonInfoById($person_id);
        if ($info === false) {
            return 0;
        }
        if (count($info['face_ids']) < 2) {
            return 2;
        }
        return 1;
    }

    /**
     * 通过人脸获取用户所属ID
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-22
     * @param    string                         $face     [description]
     * @param    string                         $group_id [description]
     * @param    string                         $type     [description]
     * @return   [type]                                   [description]
     */
    public function getPersonIdByFace($face = '', $group_id = 'user', $type = 'local',$delete_attach = false)
    {
        $info = $this->getFaceidentify($face, $group_id, $type,false);
        if (isset($info['candidates'])) {
            $candidates = $info['candidates'];
            
            // 获取匹配度最高的人脸
            $faceInfo = $candidates[0];
            // 进行系统可信度验证
            $res = $this->faceverify($faceInfo['person_id'],$face,$type,false);
            if($res && $res['ismatch'] === true){
                $this->_delete();
                return $faceInfo['person_id'];
            }elseif($delete_attach === true){
                $this->_delete();
            }

            return false;
        }
        return $info;
    }

    /**
     * 人脸验证(用于验证是否为同一人)
     * @param string person_id 个体ID
     * @param string face 待验证人脸
     * @param int type 1:使用本地图片 2:使用链接 default:2
     */
    public function faceverify($person_id = '', $face = '', $type = 'local',$delete_attach = true)
    {
        if ($type == 'local') {
            $attach      = model('Attach')->getAttachById($face);
            $attach_path = UPLOAD_PATH . '/' . $attach['save_path'] . $attach['save_name'];
            $this->attach_ids[] = ['attach_id'=>$face,'attach_path'=>$attach_path];
            $uploadRet   = YouTu::faceverify($attach_path, (string) $person_id);
            $delete_attach && $this->_delete();
        } else {
            $uploadRet = YouTu::faceverifyurl($face, (string) $person_id);
        }

        return $this->haddleResponse($uploadRet);
    }

    /**
     * 处理返回结果
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     * @param    [type]                         $res    [description]
     * @param    string                         $fields [description]
     * @return   [type]                                 [description]
     */
    private function haddleResponse($res, $fields = '*')
    {
        if (!is_array($res)) {
            $this->error = $res;
            return false;
        }
        if ($res['errorcode'] == 0) {
            unset($res['errorcode'], $res['errormsg']);
            if ($fields == '*') {
                return $res;
            }
            $fields = array_flip($fields);
            return array_intersect_key($res, $fields);
        }
        $this->setError($res);
        return false;
    }

    /**
     * 是否本人操作
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-12-14
     * @param    string                         $person_id [description]
     * @param    string                         $face      [description]
     * @param    string                         $type      [description]
     * @return   boolean                                   [description]
     */
    public function isOwn($person_id = '', $face_arr = '', $type = 'local')
    {
        if ($type == 'local') {
            foreach ($face_arr as $k => $attach_id) {
                $attach = model('Attach')->getAttachById($attach_id);
                if (!$attach) {
                    contiune;
                }
                $attach_path = UPLOAD_PATH . '/' . $attach['save_path'] . $attach['save_name'];
                if (!file_exists($attach_path)) {
                    contiune;
                }
                $uploadRet = YouTu::faceverify($attach_path, (string) $person_id,$type,false);
                $res       = $this->haddleResponse($uploadRet);
                if ($res && $res['ismatch'] !== true) {
                    $this->_delete();
                    $this->error = '请确认你是本人操作';
                    return false;
                } else if (!$res) {
                    $error = $this->getError();
                    if ($error['errorcode'] == '-1101') {
                        $this->_delete();
                        $this->error = '人脸识别失败,请重试';
                        return false;
                    }

                }
            }
        } else {
            // 待开发
            return false;
        }

        return true;
    }

    /**
     * 设置错误信息
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     * @param    array  $res 数据信息
     */
    private function setError($res)
    {
        $this->error = [
            'errorcode' => $res['errorcode'],
            'errormsg'  => $res['errormsg'],
        ];
    }

    /**
     * 返回错误信息
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-17
     * @return   [type]                         [description]
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 用于销毁用户上传的附件
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2018-01-12
     */
    private function _delete()
    {
        if(!empty($this->attach_ids)){
            model('Attach')->deleteAttach($this->attach_ids,true);
        }
    }

    /**
     * 删除人脸数据
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2018-01-18
     * @param    integer                        $person_id [description]
     * @return   [type]                                    [description]
     */
    public function doDelPerson($person_id = 0)
    {
        return Youtu::delPerson((string)$person_id,'user');
    }
}
