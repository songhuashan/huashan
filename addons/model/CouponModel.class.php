<?php
/**
 * 优惠券
 * @author martinsun<syh@sunyonghong.com>
 * @version v1.0
 */
class CouponModel extends Model{
	protected $tableName = 'coupon';
    /**
     * @name 获取优惠券列表
     */
    public function getList(array $map,$order = 'ctime desc',$limit = 20){
        $data = $this->where($map)->order($order)->findPage($limit);
        if($data['data']){
            $data['data'] = $this->haddleData($data['data']);
        }
        return $data;
    }
    /**
     * @name 获取线上可领取的卡券列表
     */
    public function getCardReceiptList($type,$order = 'ctime desc',$limit = 20,$uid){
        $time = time();
        $map['count']       = ['egt',0];
        $map['status']      = 1;
        $map['end_time']    = ['egt',$time];
        $map['coupon_type'] = 0;
        $map['is_del']      = 0;
        $map['type']        = $type;

        $data = $this->where($map)->order($order)->findPage($limit);
        if($data['data']){
            $data['data'] = $this->haddleData($data['data']);
            foreach($data['data'] as $key => &$v){
                $v['ctime'] = date('Y.m.d H:i:s',$v["ctime"]);
                $v['end_time'] = date('Y.m.d H:i:s',$v["end_time"]);
                //获取用户已有卡券
                $ext_where = " AND u.etime > $time AND u.cid ={$v['coupon_id']} ";
                $res  = $this->getCanuseCouponList($uid, $v['type'], $ext_where);
                if(!empty($res)){
                    $v['is_receive'] = true;
                }
                $can_use = M('coupon_user')->where(['uid'=>$uid,'cid'=>$v['coupon_id'],'status'=>['neq',0]])->getField();
//                $can_use = $this->canUse($v['coupon_id'],$uid);
                if($can_use){
                    unset($data['data'][$key]);
                }
            }
        }
        return $data;
    }

    /**
     * @name 获取机构线上可领取的卡券列表
     */
    public function getSchoolCardList($map,$order = 'ctime desc',$limit = 20){
        $cuid = M('coupon_user')->where(['uid'=>$this->mid])->field('cid')->findALL();
        $cids = getSubByKey($cuid,'cid');

        if($cids){
            $map['id'] = ['not in',$cids];
        }
        $data = $this->where($map)->order($order)->findPage($limit);
        if($data['data']){
            $data['data'] = $this->haddleData($data['data']);
        }
        return $data;
    }
    /**
     * @name 数据解析
     */
    protected function haddleData($data = array()){
        if(!is_array($data) || empty($data)){
            return [];
        }
        foreach($data as &$v){
            //重置值
            $v['coupon_id'] = (int)$v['id'];
            $v['status'] = (int)$v['status'];
            $v['is_del'] = (int)$v['is_del'];
            $v['type'] = (int)$v['type'];
            $v['exp_date'] = (int)$v['exp_date'];
            $v['sid'] = (int)$v['sid'];
            $v['uid'] = (int)$v['uid'];
            $v['cid'] = (int)$v['cid'];
            $v['school_title'] = model('School')->where(['id'=>$v['sid']])->getField('title') ?:'';
            unset($v['id']);
            //判断优惠券是否已经过期
            $vip_info = M('user_vip')->where(['id'=>$v['vip_grade']])->find();
			$v['vip_grade_list'] = $vip_info ? : (object)array();
			isset($vip_info['cover']) && $vip_info['cover_url'] = getCover($v['vip_grade_list']['cover']);
            //$v['vip_grade_list'] = (object)$v['vip_grade_list'];
            if($v['video_type'] == 1 || $v['video_type'] == 2){
                $v['video_title'] = D('ZyVideo','classroom')->getVideoTitleById($v['video_id']);
            }else if($v['video_type'] == 3){
                $v['video_title'] = D('Album','classroom')->getAlbumTitleById($v['video_id']);
            }
        }
        return $data;
    }
    /**
     * @name 根据优惠券ID获取优惠券信息
     *
     */
    public function getCouponInfoById($id = 0){
        $info = [];
        if($id){
            $info = $this->where(['id'=>$id])->find();
            $info && $info = $this->haddleData([$info]);
        }
        return $info ? $info[0] :[];
    }
    /**
     * @name 获取指定用户的优惠券列表--带分页
     * @param array $map 条件数组
     * map数组中参数说明:
     *  type:优惠券类型
     *  uid:用户uid
     * @param int $status 优惠券使用状态 -1表示所有
     * @param int $limit 获取分页条数
     * @param int $is_app 数据获取对象是否是app
     * @return array
     */
    public function getUserCouponList($map = [],$status = -1,$limit = 20,$is_app = 0){
        $subSql = " AND u.is_del = 0 AND c.status != 3";
        if(in_array($status,[0,1])){
            if($status == 1){
                $subSql .=" AND u.status >= ".$status;
            }else{
                $subSql .=" AND u.status = ".$status;
            }
        }
        if($map['type']){
            $subSql .=" AND c.type = ".$map['type'];
        }
        $subSql .=" AND c.coupon_type = ".$map['coupon_type'];
        if($map['sid']){
            $subSql .=" AND c.sid = ".$map['sid'];
        }
        if($is_app == 1){
            $subSql .=" AND c.video_type != 3";
        }
        if($map['etime']){
            $time = time();
            if($status == 0){
                $subSql .=" AND u.etime>$time";
            }else if($status == 2){
                $subSql .=" AND u.etime<$time AND u.status=0";
            }
        }
        if($map['video_id']){
            $subSql .=" AND c.video_id= ".$map['video_id'];
        }
        if($map['video_type']){
            $subSql .=" AND c.video_type= ".$map['video_type'];
        }
        if($map['maxprice']){
            $subSql .=" AND c.maxprice <= ".$map['maxprice'];
        }
        $list = $this->join("as c INNER JOIN `".C('DB_PREFIX')."coupon_user` u ON u.cid = c.id AND u.uid = ".$map['uid'].$subSql)->order('u.stime desc')->findPage($limit);
        if($list['data']){
            foreach($list['data'] as &$v){
                $v = $this->haddleData([$v])[0];
            }
            unset($v);
        }
        return $list ?:[];
    }
    /**
     * @name 获取指定用户可使用的优惠券--所有
     */
    public function getCanuseCouponList($uid = 0,$type = 0,$ext_where = ''){
        $subSql = " AND u.is_del = 0 AND c.status != 3";
        $subSql .=" AND u.status = 0";
        if($type){
            if(is_array($type)){
                $subSql .=" AND c.type in (".implode(',',$type).')';
            }else{
                $subSql .=" AND c.type = ".$type;
            }
        }
        $ext_where && $subSql.= " ".$ext_where." ";
        $list = $this->join("as c INNER JOIN `".C('DB_PREFIX')."coupon_user` u ON u.cid = c.id AND u.uid = ".$uid.$subSql)->order('u.stime desc')->select();
        if($list){
            foreach($list as &$v){
                $v = $this->haddleData([$v])[0];
            }
            unset($v);
        }
        return $list ?:[];
    }

    /**
     * 检测某用户的某优惠券是否可用
     * @param int $coupon_id
     * @param int $uid
     * @return array|bool
     */
    public function canUse($coupon_id = 0,$uid = 0){
        //检测已经失效的订单
        $this->checkOverdueOrder($uid);
        //获取优惠券
        $map['is_del'] = 0;
        $map['status'] = ['neq',1];
        $map['id'] = $coupon_id;
        $map['uid'] = $uid;
        $map['etime'] = ['egt',time()];
        $uCoupon = M('coupon_user')->where($map)->find();
        $fCoupon = [];
        if($uCoupon){
            //如果领取的优惠券可用，检测优惠券出处
            $fCoupon = $this->getCouponInfoById($uCoupon['cid']);
            if($fCoupon['is_del'] == 1){
                return false;
            }
            $etime = 0;
            //设置了有效期
            if($fCoupon['exp_date'] > 0){
                $etime = $uCoupon['stime'] + $fCoupon['exp_date'] * 86400;
            }
            //设置了过期时间
            /*if($fCoupon['end_time'] > 0){
                $etime = $fCoupon['end_time'];
            }*/
            if($etime && $etime < time()){
                return false;
            }
        }
        return $fCoupon;
    }
    /**
     * @name 更改优惠券状态
     * @param int $coupon_id 优惠券ID
     * @param int $status 更改的状态值 【0已过期，1未领取，2已领取，3已作废】
     * @return boolean true:更改成功 false:更改失败
     */
    public function setStatus($coupon_id = 0 ,$status = -1){
        $status = (int)$status;
        if(!in_array($status,[0,1,2,3])){
            return false;
        }
        return $this->where(['id'=>$coupon_id])->setField('status',$status) ? true : false;
    }
    
    /**
     * @name 搜索优惠券
     */
    public function getSearchCoupon($map = []){
        $list = $this->where($map)->limit(10)->select();
        if($list){
            $time = strtotime(date('Y-m-d'));
            foreach($list as $k=>$val){
                //是否过期
                if($val['end_time'] < $time){
                    unset($list[$k]);
    				$this->setStatus($val['id'],0);
                }
                
            }
            $list = $this->haddleData($list);
        }
        return $list;
    }
    
    /**
     * @name 获取优惠券(由用户填写优惠券后检测优惠券的合法性)
     */
    public function grantCouponByCode($code = '',$mid = 0){
        $map['code'] = $code;
        $info = $this->where($map)->find();
        $cuid = M('coupon_user')->where(['uid'=>$mid,'cid'=>$info['id']])->getField('id');
        if($cuid){
            $this->error = '你已领取改卡券，请勿重复领取';
            return false;
        }
        if($info){
            //查询到了优惠券
            switch($info['status']){
                case 0:
                    //已经过期
                    $this->error = '该卡券已过期';
                    break;
                case 1:
                    $time = strtotime(date('Y-m-d'));
                    //正常,检测卡券是否过期
                    if($info['end_time'] < $time){
                        $info['status'] = 0;
                        $this->error = '该卡券已过期';
                        //更新状态
        				$this->setStatus($info['id'],0);
                    }
                    break;
                case 2:
                    //已经使用
                    $this->error = '该卡券已被领取';
                    break;
                case 3:
                    //已经废弃
                    $this->error = '该卡券已失效';
                    break;
            }
        }else{
            $this->error = '未能查询到卡券';
        }
        return ($info && $info['status'] == 1) ? $this->addUserCoupon($info,$mid): false;
    }
    /**
     * @name 发放卡券(给用户添加合法的卡券)
     * @param array $info 单条卡券的详细信息
     * @return boolean true:成功 false:失败
     */
    final private function addUserCoupon(array $info,$mid = 0){

//        if(!$this->mid) return false;
        if($this->mid){
            $mid = $this->mid;
        }
        if(!$mid) return false;
        $coupon = [
//            'uid'       => $this->mid,
            'uid'       => $mid,
            'cid'       => $info['id'],
            'stime'     => time(),
            'etime'     => time()+(int)$info['exp_date'] * 86400,
            'status'    => 0,
            'is_del'    => 0,
            'mhm_id'    => $info['sid'],
        ];
        if(M('coupon_user')->add($coupon)){
            //设置为已经被使用
            $this->setStatus($info['id'],2);
            return true;
        }
        return false;
     }
    
    /**
     * @name 检测过期的订单
     */
    public function checkOverdueOrder($uid = 0){
        $order = D('ZyOrderCourse','classroom')->where(['pay_status'=>1,'coupon_id'=>['gt','0'],'time_limit'=>['elt',time()]])->select();
        if($order){
            $order_ids = getSubByKey($order,'id');
            $coupon_ids = getSubByKey($order,'coupon_id');
            //更新状态
            //D('ZyOrderCourse','classroom')->where(['id'=>['in',$order_ids]])->save(['pay_status'=>6]);
            M('coupon_user')->where(['id'=>['in',$coupon_ids]])->save(['status'=>0]);
        }
    }

    /**
     * @name 检测优惠券是否领取完
     * @param int $coupon_id 优惠券ID
     * @param int $status 是否更改卡券状态（1是,0否）
     * @return boolean true:领取完 false:未领取完
     */
    public function checkCouponCount($coupon_id,$status = 1){
        $count = $this->where(['id'=>$coupon_id])->getField('count');
        $user_count = M('coupon_user')->where(['cid'=>$coupon_id])->count();
        if($user_count >= $count && $status == 1){
            $this->setStatus($coupon_id,2);
        }
        return false;
    }

    /*
     * @name 取消使用优惠券
     * @param int $coupon_id 优惠券ID
     */
    public function cancelExchangeCard($coupon_id){
        $coupon = $this->getCouponInfoById($coupon_id);
        $map['cid'] = $coupon['coupon_id'];
        $status = M('coupon_user')->where($map)->getField('status');
        if($status == 2){
            if($coupon['coupon_type'] == 1){
                $res = M('coupon_user')->where($map)->delete();
                if($res){
                    $data['status'] = 1;
                    $result = $this->where($map)->save($data);
                }
            }else{
                $data['status'] = 0;
                $res = M('coupon_user')->where($map)->save($data);
                if($res){
                    $result = $this->checkCouponCount($coupon['coupon_id']);
                }
            }
        }
        return $result  ? true: false;
    }

    /**
     * @name 领取卡券
     * @param int $cid 卡券id
     * @return boolean true:成功 false:失败
     */
    public function saveUSerCoupon($cid,$uid){
        if($this->mid){
            $uid = $this->mid;
        }

        $coupon = $this->getCouponInfoById($cid);
        //获取用户已有卡券
        $time = time();
        $ext_where = " AND u.etime > $time AND u.cid =$cid ";
        $res  = $this->getCanuseCouponList($uid, $coupon['type'], $ext_where);
        if (!empty($res)) {
            $this->error = '您已经拥有该类型优惠券,请使用后再领取';
            return 1;
        }
        //查询用户是否已有相同类型卡券
        $user_coupon = M('coupon_user')->where(['uid'=>$uid,'cid'=>$cid])->count();
        if($user_coupon > 0){
            $this->error = '您已经领取过该类型优惠券';
            return 1;
        }

        $data = array(
            'uid'    => $uid,
            'cid'    => $cid,
            'stime'  => time(),
            'etime'  => time()+(int)$coupon['exp_date'] * 86400,
            'mhm_id' => $coupon['sid'],
        );

        $cuid = M('coupon_user')->add($data);

        if($cuid){
            $this->checkCouponCount($cid);
        }

        return $cuid  ? true: false;
    }
}