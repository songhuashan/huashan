<?php
/**
 * 用户组关联模型 - 数据对象模型
 * @author jason <yangjs17@yeah.net> 
 * @version TS3.0
 */
class UserGroupLinkModel extends Model {

	protected $tableName = 'user_group_link';
	protected $fields =	array(0 =>'id',1=>'uid',2=>'user_group_id');
	

	
	/**
	 * 后期改正
	 * @param array 判定用户组 课程 类别是否有对应的订单
	 * @return arra
	 */
	public function getUserOrder($uids,$kid,$type,$leibie) {
	    
	    
 if($leibie=='add'){ 
	   $addtime=time();
	   if($type==1){     //dianbo
	       
	       $addsqlv="INSERT INTO `el_zy_order_course`(`id`, `uid`, `muid`, `video_id`, `old_price`, `discount`, `discount_type`, `price`, `order_album_id`, `learn_status`, `ctime`, `ptime`, `is_del`, `pay_status`, `rel_id`, `mhm_id`, `term`, `time_limit`, `coupon_id`, `order_mhm_id`, `refund_reason`, `reject_info`) VALUES ('','$uids','1','$kid','0','0','4','0','0','0','$addtime','$addtime','0','3','0','1','0','0','0','0','','')";
	       $akid=M()->query($addsqlv); //点播
          // echo $addsqlv;      

          }else{
		   $addsql="INSERT INTO `el_zy_order_live`(`id`, `uid`, `live_id`, `old_price`, `discount`, `discount_type`, `price`, `order_album_id`, `learn_status`, `ctime`, `ptime`, `is_del`, `pay_status`, `rel_id`, `mhm_id`,  `coupon_id`, `order_mhm_id`, `refund_reason`, `reject_info`) VALUES ('','$uids','$kid','0','0','4','0','0','0','$addtime','$addtime','0','3','0','1','0','0','','')";
           $akid=M()->query($addsql);
           echo $addsql;
		  
		  } 
	   }elseif($leibie=="del"){
	       

	        
	        $delsqk="DELETE FROM `el_zy_order_course` WHERE   uid='$uids' ";
	        $akid=M()->query($delsqk); //点播
	        //echo $delsqk;
	    
			$sqlsql="DELETE FROM `el_zy_order_live` WHERE  uid='$uids' ";
			//echo $sqlsql;
	        $akid=M()->query($sqlsql);
        
	   }         
	    return 1;
	}
	
	
	/**
	 * 转移用户的用户组
	 * @param string $uids 用户UID，多个用“，”分割
	 * @param string $user_group_id 用户组ID，多个用“，”分割
	 * @return boolean 是否转移成功
	 */
	public function domoveUsergroup($uids, $user_group_id) {
	    
	    
		// 验证数据
		if(empty($uids) && empty($user_group_id)) {
			$this->error = L('PUBLIC_USER_GROUP_EMPTY');			// 用户组或用户不能为空
			return false;
		}
		$uids = explode(',', $uids);
		$user_group_id = explode(',', $user_group_id);
		$uids = array_unique(array_filter($uids));
		$user_group_id = array_unique(array_filter($user_group_id));
		
		
		//dump($user_group_id);
		
		//exit();
		// 过滤掉认证组
		if(!$uids || !$user_group_id) {
			return false;
		}
		$map['uid'] = array('IN', $uids);
		// 认证用户组
		/*$veritfiedHash = model('UserGroup')->getHashUserGroupVertified();
		if (!empty($veritfiedHash)) {
			$map['user_group_id'] = array('NOT IN', array_keys($veritfiedHash));
		}*/
        $veritfiedHash = $this->getUserGroup($uids);
        

        
        if (!empty($veritfiedHash)) {
            foreach($uids as $v){
                $map['user_group_id'] = array('IN', $veritfiedHash[$v]);
            }
        }
        


        $this->where($map)->delete();
		foreach($uids as $v) {
			$save = array();
			$save['uid'] = $v;
			
            //删除已经有的客户订单
			$this->getUserOrder($v,'','','del');

			foreach($user_group_id as $gv){

			   
			    $ikid=M()->query("select kid,ctype from el_group_giving  where gid='$gv'");
			    //添加新的的分组课程
			
			    foreach ($ikid as $iid){
			        
			        $this->getUserOrder($v,$iid['kid'],$iid['ctype'],'add');
			   
			    } 
		
				$save['user_group_id'] = $gv;
				
				
				$this->add($save);
			}
		
			// 清除权限缓存
			model('Cache')->rm('perm_user_'.$v);
			model ( 'Cache' )->rm ( 'user_group_' . $v );
		}

		model('User')->cleanCache($uids);

		return true;
	}
	
	
	
	
	

	/**
	 * 获取用户的用户组信息
	 * @param array $uids 用户UID数组
	 * @return array 用户的用户组信息
	 */
	public function getUserGroup($uids) {
		$uids = !is_array($uids) ? explode(',', $uids) : $uids;
		$uids = array_unique(array_filter($uids));
		if(!$uids) return false;
	
		$return = array();
		foreach ($uids as $uid){
			$return[$uid] = model ( 'Cache' )->get ( 'user_group_' . $uid);
			if($return[$uid]==false){
				$map['uid'] = $uid;
				$list = $this->where($map)->findAll();
				$return[$uid] = getSubByKey($list, 'user_group_id');
				model ( 'Cache' )->set ( 'user_group_' . $uid, $return[$uid]);
			}
		}
		return $return;
	}	

	/**
	 * 获取用户所在用户组详细信息
	 * @param array $uids 用户UID数组
	 * @return array 用户的用户组详细信息
	 */
	public function getUserGroupData($uids){
		$uids = !is_array($uids) ? explode(',', $uids) : $uids;
		$uids = array_unique(array_filter($uids));
		if(!$uids) return false;
		$userGids = $this->getUserGroup($uids);
		//return $userGids;exit;
		$uresult = array();
		foreach ( $userGids as $ug){
			if ( $uresult ){
				$ug && $uresult = array_merge( $uresult , $ug );
			} else {
				$uresult = $ug;
			}
		}
		//把所有用户组信息查询出来
		$ugresult = model('UserGroup')->getUserGroupByGids(array_unique($uresult));
		$groupresult = array();
		foreach ( $ugresult as $ur ){
			$groupresult[$ur['user_group_id']] = $ur;
		}
		foreach($userGids as $k=>$v){
			$ugroup = array();
			foreach ( $userGids[$k] as $userg){
				$ugroup[] = $groupresult[$userg];
			}
			$userGroupData[$k] = $ugroup;
			foreach($userGroupData[$k] as $key => $value) {
				if(isset($value['user_group_icon']) && $value['user_group_icon'] == -1) {
					unset($userGroupData[$k][$key]);
					continue;
				}
				$userGroupData[$k][$key]['user_group_icon_url'] = THEME_PUBLIC_URL.'/image/usergroup/'.$value['user_group_icon'];
			}
		}
		return $userGroupData;
	}
}
