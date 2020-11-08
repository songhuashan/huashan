<?php
/**
 * 附件模型 - 数据对象模型
 * @author jason <yangjs17@yeah.net>
 * @version TS3.0
 */
// 加载上传操作类
require_once SITE_PATH . '/addons/library/UploadFile.class.php';

class AttachModel extends Model
{

    protected $tableName = 'attach';
    protected $fields    = array(
        0  => 'attach_id',
        1  => 'app_name',
        2  => 'table',
        3  => 'row_id',
        4  => 'attach_type',
        5  => 'uid',
        6  => 'ctime',
        7  => 'name',
        8  => 'type',
        9  => 'size',
        10 => 'extension',
        11 => 'hash',
        12 => 'private',
        13 => 'is_del',
        14 => 'save_path',
        15 => 'save_name',
        16 => 'save_domain',
        17 => 'from',
        18 => 'width',
        19 => 'height',
    );

    /**
     * 通过附件ID获取附件数据 - 不分页型
     * @param array $ids 附件ID数组
     * @param string $field 附件数据显示字段，默认为显示全部
     * @return array 相关附件数据
     */
    public function getAttachByIds($ids, $field = '*')
    {
        if (empty($ids)) {
            return false;
        }
        !is_array($ids) && $ids = explode(',', $ids);
        $map['attach_id']       = array('IN', $ids);
        $data                   = $this->where($map)->field($field)->findAll();

        return $data;
    }

    /**
     * 通过单个附件ID获取其附件信息
     * @param integer $id 附件ID
     * @return array 指定附件ID的附件信息
     */
    public function getAttachById($id)
    {
        if (empty($id)) {
            return false;
        }
        // 获取静态缓存
        $sc = static_cache('attach_infoHash_' . $id);
        if (!empty($sc)) {
            return $sc;
        }
        // 获取缓存
        $sc = model('Cache')->get('Attach_' . $id);
        if (empty($sc)) {
            $map['attach_id'] = $id;
            $sc               = $this->where($map)->find();
            empty($sc) && $sc = array();
            model('Cache')->set('Attach_' . $id, $sc, 3600);
        }
        static_cache('attach_infoHash_' . $id, $sc);

        return $sc;
    }

    /**
     * 获取附件列表 - 分页型
     * @param array $map 查询条件
     * @param string $field 显示字段
     * @param string $order 排序条件，默认为id DESC
     * @param integer $limit 结果集个数，默认为20
     * @return array 附件列表数据
     */
    public function getAttachList($map, $field = '*', $order = 'id DESC', $limit = 20)
    {
        !isset($map['is_del']) && ($map['is_del'] = 0);
        $list = $this->where($map)->field($field)->order($order)->findPage($limit);
        return $list;
    }

    /**
     * 删除附件信息，提供假删除功能
     * @param integer $id 附件ID
     * @param string $type 操作类型，若为delAttach则进行假删除操作，deleteAttach则进行彻底删除操作
     * @param string $title ???
     * @return array 返回操作结果信息
     */
    public function doEditAttach($id, $type, $title)
    {
        $return = array('status' => '0', 'data' => L('PUBLIC_ADMIN_OPRETING_ERROR')); // 操作失败
        if (empty($id)) {
            $return['data'] = L('PUBLIC_ATTACHMENT_ID_NOEXIST'); // 附件ID不能为空
        } else {
            $map['attach_id'] = is_array($id) ? array('IN', $id) : intval($id);
            $save['is_del']   = ($type == 'delAttach') ? 1 : 0; //TODO:1 为用户uid 临时为1
            if ($type == 'deleteAttach') {
                // 彻底删除操作
                $attch_info = D('Attach')->where($map)->select();
                foreach ($attch_info as &$value) {
                    $old_attach = UPLOAD_PATH . '/' . $value['save_path'] . $value['save_name'];
                    //清理旧文件
                    @unlink($old_attach);
                }
                $res = D('Attach')->where($map)->delete();
                // TODO:删除附件文件
            } else {
                // 假删除或者恢复操作
                $res = D('Attach')->where($map)->save($save);
            }
            if ($res) {
                //TODO:是否记录日志，以及后期缓存处理
                $return = array('status' => 1, 'data' => L('PUBLIC_ADMIN_OPRETING_SUCCESS')); // 操作成功
            }
        }

        return $return;
    }

    /**
     * 获取所有附件的扩展名
     * @return array 扩展名数组
     */
    public function getAllExtensions()
    {
        $res = $this->field('`extension`')->group('`extension`')->findAll();
        return getSubByKey($res, 'extension');
    }

    /**
     * 获取文件的绝对路径
     * @param $id
     * @return string
     */
    public function getFilePath($id)
    {
        $attch_info = D('Attach')->where(array('attach_id' => $id))->find();
        $old_attach = UPLOAD_PATH . '/' . $attch_info['save_path'] . $attch_info['save_name'];
        return $old_attach;
    }

    /**
     * 上传附件
     * @param array $data 附件相关信息
     * @param array $input_options 配置选项[不推荐修改, 默认使用后台的配置]
     * @param boolean $thumb 是否启用缩略图
     * @return array 上传的附件的信息
     */
    public function upload($data = null, $input_options = null, $thumb = false)
    {
        $system_default = model('Xdata')->get('admin_Config:attach');
        if (empty($system_default['attach_path_rule']) || empty($system_default['attach_max_size']) || empty($system_default['attach_allow_extension'])) {
            $system_default['attach_path_rule']       = 'Y/md/H/';
            $system_default['attach_max_size']        = '2'; // 默认2M
            $system_default['attach_allow_extension'] = 'jpg,gif,png,jpeg,bmp,zip,rar,doc,xls,ppt,docx,xlsx,pptx,pdf';
            model('Xdata')->put('admin_Config:attach', $system_default);
        }

        // 上传若为图片，则修改为图片配置
        if ($data['upload_type'] === 'image') {
            $image_default                            = model('Xdata')->get('admin_Config:attachimage');
            $system_default['attach_max_size']        = $image_default['attach_max_size'];
            $system_default['attach_allow_extension'] = $image_default['attach_allow_extension'];
            $system_default['auto_thumb']             = $image_default['auto_thumb'];
        }

        // 载入默认规则
        $default_options                = array();
        $default_options['custom_path'] = date($system_default['attach_path_rule']); // 应用定义的上传目录规则：'Y/md/H/'
        $default_options['max_size']    = floatval($system_default['attach_max_size']) * 1024 * 1024; // 单位: 兆
        $default_options['allow_exts']  = $system_default['attach_allow_extension']; // 'jpg,gif,png,jpeg,bmp,zip,rar,doc,xls,ppt,docx,xlsx,pptx,pdf'
        $default_options['save_path']   = UPLOAD_PATH . '/' . $default_options['custom_path'];
        $default_options['save_name']   = ''; //指定保存的附件名.默认系统自动生成
        $default_options['save_to_db']  = true;

        // 定制化设这，覆盖默认设置
        $options = is_array($input_options) ? array_merge($default_options, $input_options) : $default_options;
        //云图片
        if ($data['upload_type'] == 'image') {
            $cloud = model('CloudImage');
            if ($cloud->isOpen()) {
                return $this->cloudImageUpload($options);
            } else {
                return $this->localUpload($options);
            }
        }

        //云附件
        else {
            //if($data['upload_type']=='file'){
            $cloud = model('CloudAttach');
            if ($cloud->isOpen()) {
                return $this->cloudAttachUpload($options);
            } else {
                return $this->localUpload($options);
            }
        }
    }

    private function cloudImageUpload($options)
    {

        $upload             = model('CloudImage');
        $upload->maxSize    = $options['max_size'];
        $upload->allowExts  = $options['allow_exts'];
        $upload->customPath = $options['custom_path'];
        $upload->saveName   = $options['save_name'];

        // 执行上传操作
        if (!$upload->upload()) {
            // 上传失败，返回错误
            $return['status'] = false;
            $return['info']   = $upload->getErrorMsg();
            return $return;
        } else {
            $upload_info = $upload->getUploadFileInfo();
            // 保存信息到附件表
            $data = $this->saveInfo($upload_info, $options);
            // 输出信息
            $return['status'] = true;
            $return['info']   = $data;
            // 上传成功，返回信息
            return $return;
        }
    }

    private function cloudAttachUpload($options)
    {

        $upload             = model('CloudAttach');
        $upload->maxSize    = $options['max_size'];
        $upload->allowExts  = $options['allow_exts'];
        $upload->customPath = $options['custom_path'];
        $upload->saveName   = $options['save_name'];

        // 执行上传操作
        if (!$upload->upload()) {
            // 上传失败，返回错误
            $return['status'] = false;
            $return['info']   = $upload->getErrorMsg();
            return $return;
        } else {
            $upload_info = $upload->getUploadFileInfo();
            // 保存信息到附件表
            $data = $this->saveInfo($upload_info, $options);
            // 输出信息
            $return['status'] = true;
            $return['info']   = $data;
            // 上传成功，返回信息
            return $return;
        }
    }

    private function localUpload($options)
    {
        // 初始化上传参数
        $upload = new UploadFile($options['max_size'], $options['allow_exts'], $options['allow_types']);

        // 启用子目录
        $upload->autoSub = false;
        if (isset($options['chunk']) && isset($options['chunks'])) {

            // 设置切片保存目录
            $upload->savePath = UPLOAD_PATH . DIRECTORY_SEPARATOR . 'filechunks' . DIRECTORY_SEPARATOR;
            if (isset($_REQUEST["name"])) {
                $fileName = $_REQUEST["name"];
            } elseif (!empty($_FILES)) {
                $fileName = $_FILES["file"]["name"];
            } else {
                $fileName = uniqid("file_");
            }
            $upload->saveName = $fileName . '_' . $options['chunk'] . '.part';
            // 检测是否上传完成
            $done = ($options['chunks'] == ($options['chunk'] + 1)) ? true : false;
            // 检测文件是否存在
            $f = iconv('UTF-8', 'GB2312', $upload->savePath . $upload->saveName);
            if (!$done && file_exists($f)) {
                return ['status' => true, 'info' => [$_FILES['file']]];
            }
            $upload->upload();
            if ($done === true) {
                for ($index = 0; $index < $options['chunks']; $index++) {
                    $f = iconv('UTF-8', 'GB2312', $upload->savePath . "{$fileName}_{$index}.part");
                    if (!file_exists($f)) {
                        $done = false;
                        break;
                    }
                }
            }
            if ($done) {
                // 合并分片文件
                // 取得最后一片的信息
                $info     = $upload->getUploadFileInfo();
                $info     = $info[0];
                $savename = uniqid() . '.' . $info['extension'];
                $outfile  = $options['save_path'] . $savename;
                if (!is_dir(dirname($outfile))) {
                    @mkdir(dirname($outfile), 0777, true);
                }
                // 合并操作
                if (!$out = @fopen($outfile, "w+")) {
                    return ['status' => false, 'info' => '合并分片文件失败'];
                }
                if (flock($out, LOCK_EX)) {
                    for ($index = 0; $index < $options['chunks']; $index++) {
                        $f = iconv('UTF-8', 'GB2312', $upload->savePath . "{$fileName}_{$index}.part");
                        if (!$in = @fopen($f, "r+")) {
                            break;
                        }
                        while ($buff = fread($in, 4096)) {
                            fwrite($out, $buff);
                        }
                        @fclose($in);
                        @unlink($f);
                    }
                    flock($out, LOCK_UN);
                }
                @fclose($out);
                $info['savepath'] = $options['save_path'];
                $info['savename'] = $savename;
                if (function_exists($upload->hashType)) {
                    $fun          = $upload->hashType;
                    $info['hash'] = $fun(auto_charset($info['savepath'] . $info['savename'], 'utf-8', 'gbk'));
                }
                $data = $this->saveInfo([$info], $options);
                // 输出信息
                $return['status'] = true;
                $return['info']   = $data;
                // 上传成功，返回信息
                return $return;
            }
            return ['status' => true, 'info' => $upload->getUploadFileInfo()];
        } else {
            // 设置上传路径
            $upload->savePath = $options['save_path'];
            // 保存的名字
            $upload->saveName = $options['save_name'];
            // 默认文件名规则
            $upload->saveRule = isset($options['save_rule']) ? $options['save_rule'] : uniqid();
            // 是否缩略图
            if ($options['auto_thumb'] == 1) {
                $upload->thumb = true;
            }
        }
        // 执行上传操作
        if (!$upload->upload()) {
            // 上传失败，返回错误
            $return['status'] = false;
            $return['info']   = $upload->getErrorMsg();
            return $return;
        } else {
            //此处有问题
            $upload_info = $upload->getUploadFileInfo();
            // 保存信息到附件表
            $data = $this->saveInfo($upload_info, $options);
            // 输出信息
            $return['status'] = true;
            $return['info']   = $data;
            // 上传成功，返回信息
            return $return;
        }
    }

    private function saveInfo($upload_info, $options)
    {
        $data = array(
            'table'       => t($data['table']),
            'row_id'      => t($data['row_id']),
            'app_name'    => t($data['app_name']),
            'attach_type' => t($options['attach_type']),
            'uid'         => (int) $data['uid'] ? $data['uid'] : $GLOBALS['ts']['mid'],
            'ctime'       => time(),
            'private'     => $data['private'] > 0 ? 1 : 0,
            'is_del'      => 0,
            'from'        => isset($data['from']) ? intval($data['from']) : getVisitorClient(),
        );
        if ($options['save_to_db']) {
            foreach ($upload_info as $u) {
                $name              = t($u['name']);
                $data['name']      = $name ? $name : $u['savename'];
                $data['type']      = $u['type'];
                $data['size']      = $u['size'];
                $data['extension'] = strtolower($u['extension']);
                $data['hash']      = $u['hash'];
                $data['save_path'] = $options['custom_path'];
                $data['save_name'] = $u['savename'];
                if (in_array($data['extension'], array('jpg', 'gif', 'png', 'jpeg', 'bmp')) && !in_array($options['attach_type'], array('feed_file', 'weiba_attach'))) {
                    list($width, $height) = getImageInfo($data['save_path'] . $data['save_name']);
                    $data['width']        = $width;
                    $data['height']       = $height;
                } else {
                    $data['width']  = 0;
                    $data['height'] = 0;
                }
                $aid               = $this->add($data);
                $data['attach_id'] = intval($aid);
                $data['key']       = $u['key'];
                $data['size']      = byte_format($data['size']);
                $infos[]           = $data;
            }
        } else {
            foreach ($upload_info as $u) {
                $name              = t($u['name']);
                $data['name']      = $name ? $name : $u['savename'];
                $data['type']      = $u['type'];
                $data['size']      = byte_format($u['size']);
                $data['extension'] = strtolower($u['extension']);
                $data['hash']      = $u['hash'];
                $data['save_path'] = $options['custom_path'];
                $data['save_name'] = $u['savename'];
                //$data['save_domain'] = C('ATTACH_SAVE_DOMAIN');     //如果做分布式存储，需要写方法来分配附件的服务器domain
                $data['key'] = $u['key'];
                $infos[]     = $data;
            }
        }
        return $infos;
    }

    public function saveAttach($file)
    {
        # code...
    }

    /**
     * 更新附件图片，保存图片的宽度和高度
     * @return void
     */
    public function upImageAttach($page = 1, $count = 100)
    {
        set_time_limit(0);
        $where = "`extension` IN ('jpg', 'gif', 'png', 'jpeg', 'bmp')";
        $limit = ($page - 1) * $count . ', ' . $count;
        $list  = $this->field('attach_id, save_path, save_name')->where($where)->limit($limit)->order('attach_id DESC')->findAll();
        if (empty($list)) {
            return false;
        }
        foreach ($list as $value) {
            $url       = getImageUrl($value['save_path'] . $value['save_name']);
            $imageInfo = getimagesize($url);
            if ($imageInfo === false) {
                continue;
            }
            list($width, $height) = $imageInfo;
            $data['width']        = $width;
            $data['height']       = $height;
            $map['attach_id']     = $value['attach_id'];
            $this->where($map)->save($data);
        }
        return true;
    }

    public function upImageAttachCloud($page = 1, $count = 100)
    {
        set_time_limit(0);
        $where = "`extension` IN ('jpg', 'gif', 'png', 'jpeg', 'bmp') AND attach_type != 'feed_file'";
        $limit = ($page - 1) * $count . ', ' . $count;
        $list  = $this->field('attach_id, save_path, save_name')->where($where)->limit($limit)->order('attach_id DESC')->findAll();
        if (empty($list)) {
            return false;
        }
        foreach ($list as $value) {
            $imageInfo = json_decode(file_get_contents(getImageUrl($value['save_path'] . $value['save_name']) . '!exif'));
            settype($imageInfo, 'array');
            if (empty($imageInfo)) {
                continue;
            }
            $data['width']    = $imageInfo['width'];
            $data['height']   = $imageInfo['height'];
            $map['attach_id'] = $value['attach_id'];
            $this->where($map)->save($data);
        }
        return true;
    }

    /**
     * 删除附件
     * @Author   MartinSun<syh@sunyonghong.com>
     * @DateTime 2018-01-12
     * @param    array                         $attach_ids 需要删除的数据信息
     * @param    boolean                       $is_really  是否真删除,真删除不可恢复
     * @return   int                           被删除的记录数
     */
    public function deleteAttach($attach_ids = [], $is_really = false)
    {
        // 检测是否真删除
        $del_count = 0;
        if (!empty($attach_ids)) {
            foreach ($attach_ids as $attach_id) {
                $del_path = null;
                $del_id   = $attach_id;
                if (is_array($attach_id) && isset($attach_id['attach_id'])) {
                    $del_id = $attach_id['attach_id'];
                }
                $is_really && $del_path = isset($attach_id['attach_path']) ? $attach_id['attach_path'] : $this->getFilePath($del_id);

                if ($del_id && $is_really) {
                    $res = $this->where(['attach_id' => $del_id])->delete();
                    @unlink($del_path);
                } else {
                    $res = $this->where(['attach_id' => $del_id])->save(['is_del' => 1]);
                }

                $res && $del_count++;
            }
        }
        return $del_count;
    }
}
