<?php
/**
 * 拍照渲染控制器.
 *
 * @example W('Webcam',array('width'=>'320','height'=>'240','id'=>'webcam','status'=>0))
 *
 * @version 2.0
 */
class WebcamWidget extends Widget
{
    protected $attach_id;
    protected $redirect_url = '';

    /**
     * 渲染器
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-22
     * @param    integer  width 渲染的宽度
     * @param    integer  height 渲染的高度
     * @param    integer  status  状态
     * @return
     */
    public function render($data)
    {
        $var           = array();
        $var['width']  = 320;
        $var['height'] = 240;
        $var['status'] = 0;
        $var['id']     = 'webcam';
        !empty($data) && $var = array_merge($var, $data);
        switch ($var['status']) {
            case 1:
            case 2:
                $tpl = 'verify';
                break;
            case -1:
                $tpl = 'login';
                break;
            default:
                $tpl = 'default';
                break;
        }
        $tpl = isset($data['tpl']) ? $tpl.'_'.$data['tpl'] : $tpl;
        // 删除空值
        $var = array_filter($var);
        $content = $this->renderFile(dirname(__file__) . '/' . $tpl . '.html', $var);
        unset($var, $data);
        // 输出数据
        return $content;
    }

    /**
     * 保存提交的图片
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-22
     * @return   [type]                         [description]
     */
    public function saveImage()
    {
        if ($_POST['image']) {
            $ymd           = date('Y') . '/' . date('md') . '/' . date('H') . '/';
            $imageFilePath = UPLOAD_PATH . '/' . $ymd;
            // 创建目录
            if (!file_exists($imageFilePath)) {
                mkdir($imageFilePath, 0777, true);
            }
            $filename = 'face_' . date('YmdHis') . time() . '.png';
            $path     = $imageFilePath . $filename;
            if ($_POST['type'] == "pixel") {
                // input is in format 1,2,3...|1,2,3...|...
                $image = imagecreatetruecolor(320, 240);

                foreach (explode("|", $_POST['image']) as $y => $csv) {
                    foreach (explode(";", $csv) as $x => $color) {
                        imagesetpixel($im, $x, $y, $color);
                    }
                }
            } else {
                // input is in format: data:image/png;base64,...
                $image = imagecreatefrompng($_POST['image']);
            }
            imagepng($image, $path);
            $arr = getimagesize($path);
            if ($arr[0]) {
                $callback = $_POST['callback'];
                $map = array(
                    'attach_type' => 'face',
                    'uid'         => ($callback == 'login') ? 0: $_SESSION['mid'],
                    'type'        => 'image/png',
                    'name'        => $filename,
                    'save_path'   => $ymd,
                    'save_name'   => $filename,
                    'from'        => 0,
                    'width'       => $arr[0],
                    'height'      => $arr[1],
                );
                $map['ctime']    = time();
                $this->attach_id = model('Attach')->add($map);
                if ($this->attach_id) {
                    if ($callback) {
                        $this->redirect_url = $_POST['redirect_url'] ?:'';
                        return $this->$callback();
                    }
                    echo json_encode(['status' => 1, 'data' => ['attach_id' => $attach_id, 'info' => '上传照片成功,请等待处理完成']]);exit;
                }
            }
        }

        echo json_encode(['status' => 0, 'message' => '上传照片失败,请重试']);exit;
    }

    /**
     * 人物注册
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-23
     * @return   [type]                         [description]
     */
    private function newperson()
    {
        // 检测是否已经绑定
        $youtu = model('Youtu');
        $uid   = $youtu->getPersonIdByFace($this->attach_id);
        if ($uid) {
            // 如果成功,直接登录
            $count = M('user')->where(['uid' => $uid])->count() > 0 ? true : false;
            if($count){
                echo json_encode(['status' => 0, 'message' => '你已经与其他账号绑定,无法再次绑定']);exit;
            }
        }
        $data = [
            'uid'    => $_SESSION['mid'],
            'tag'    => 'user',
            'groups' => ['user'],
            'image'  => $this->attach_id,
        ];
        
        $res   = $youtu->newperson($data);
        if ($res) {
            // 首次绑定,直接保存验证通过
            session('face_check_face_verify',$res);
            echo json_encode(['status' => 1, 'data' => ['attach_id' => $this->attach_id, 'info' => '人脸绑定成功','jumpurl'=>$this->redirect_url]]);exit;
        } else {
            echo json_encode(['status' => 0, 'message' => '处理失败,请重试']);exit;
        }
    }

    /**
     * 验证人脸
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-23
     * @return   [type]                         [description]
     */
    private function verify()
    {
        $youtu = model('Youtu');
        // 检验状态
        $status = $youtu->getInitFaceStatus($_SESSION['mid']);
        if($status === 0){
            echo json_encode(['status' => 0, 'message' => '请先绑定你的人脸信息']);exit;
        }
        $res   = $youtu->faceverify($_SESSION['mid'], $this->attach_id);
        if ($res && $res['ismatch'] === true) {
            session('face_'.$_POST['verified_module'].'_verify',$res);
            echo json_encode(['status' => 1, 'data' => ['info' => '校验通过','jumpurl'=>$this->redirect_url]]);exit;
        } else if ($res === false) {
            $error = $youtu->getError();
            if ($error['errorcode'] == '-1101') {
                echo json_encode(['status' => 0, 'message' => '人脸识别失败,请重新拍照']);exit;
            }
        }
        echo json_encode(['status' => 0, 'message' => '校验失败,如是本人操作请重新拍照']);exit;
    }

    /**
     * 使用人脸登录
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2017-11-23
     * @return   [type]                         [description]
     */
    private function login()
    {
        // 匹配人脸
        $youtu = model('Youtu');
        $uid   = $youtu->getPersonIdByFace($this->attach_id,'user','local',true);
        if ($uid) {
            // 如果成功,直接登录
            $userInfo = M('user')->where(['uid' => $uid])->find();
            if ($userInfo) {
                // 本地登录
                if (model('Passport')->loginLocal($userInfo['uname'], $userInfo['password'], false, false, true)) {
                    session('face_check_face_verify',$userInfo);
                    M('user')->where(array('uid' => $_SESSION['mid']))->setField('login_num', '1');
                    echo json_encode(['status' => 1, 'data' => ['info' => '登录成功','jumpurl'=>U('classroom/Index/index')]]);exit;
                }
                echo json_encode(['status' => 0, 'message' => '登录失败,请重试']);exit;
            }
            echo json_encode(['status' => 0, 'message' => '未找到你的账号信息,请检查是否绑定']);exit;
        } else if ($uid === false) {
            $error = $youtu->getError();
            if ($error['errorcode'] == '-1101') {
                echo json_encode(['status' => 0, 'message' => '人脸识别失败,请重新拍照']);exit;
            }
        }
        echo json_encode(['status' => 0, 'message' => '未找到你的账号信息,请检查是否绑定']);exit;
    }
}
