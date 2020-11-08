<?php
/**
 * 导航模型 - 数据对象模型
 * @author jason <renjianchao@zhishisoft.com> 
 * @version TS3.0
 */
class NaviModel extends Model {

	protected static $host;
	protected static $school_id;
	protected $tableName = 'navi';
	protected $fields = array(0=>'navi_id',1=>'navi_name',2=>'app_name',3=>'url',4=>'target',5=>'status',6=>'position',7=>'guest',8=>'is_app_navi',9=>'parent_id',10=>'order_sort');
	protected function _initialize(){
		parent::_initialize();
		//处理泛域名
	    if(isset($_SERVER['HTTP_X_HOST'])){
	        // 拼接地址
	        $config = model ( 'Xdata' )->get( "school_AdminDomaiName:domainConfig" );
	        if(!$config){
	            // 默认
	            $config = ['openHttps'=>0,'domainConfig'=>1];
	        }
	        self::$host =  ($config['openHttps'] ? 'https://' : 'http://').$_SERVER['HTTP_X_HOST'];
	        $domain = substr($_SERVER['HTTP_X_HOST'],0,stripos($_SERVER['HTTP_X_HOST'],'.'));
            self::$school_id = model('School')->where(array('doadmin' => t($domain), 'status' => 1, 'is_del' => 0))->getField('id');

	    }else{
	        self::$host = SITE_URL;
	        self::$school_id = $_GET['school_id'];
	    }
	}
	
	/**
	 * 获取头部导航
	 * @return array 头部导航 
	 */
	public function getTopNav() {
		// 如果是主站,加载缓存,如果不是,加载机构导航
		//$is_school = (strtolower(APP_NAME) == 'school' && strtolower(MODULE_NAME) != 'index') ? true : false;
		if(self::$host == SITE_URL){
			// && !$is_school && !self::$school_id
			$topNav = model('Cache')->get('topNav');
			if(!$topNav){
				$map['status'] = 1;
				$map['position'] = 0;
				$list = $this->where($map)->order('order_sort ASC')->findAll();
		    	foreach($list as $v){
		    		$v['url'] = empty($v['url']) ? 'javascript:;' : str_replace('{website}', self::$host, $v['url']);
		    		if ( $v['parent_id'] == 0 ){
		    			$navlist[$v['navi_id']] = $v;
		    		}
		    	}
		    	foreach($list as $v){
		    		if ( $v['parent_id'] > 0 ){
						$v['url'] = empty($v['url']) ? 'javascript:;' : str_replace('{website}', self::$host, $v['url']);
		    			$navlist[$v['parent_id']]['child'][] = $v;
		    		}
		    	}
				$topNav = $navlist;
				empty($topNav) && $topNav = array();
				// 主站缓存
				model('Cache')->set('topNav', $topNav);
			}
		}else{
			$topNav = [];
			$topNav[] = [
				'navi_name' => '首页',
				'navi_key'  => 'index',
				'url' => (self::$host == SITE_URL) ? U('school/school/index',['id'=>self::$school_id]) : self::$host,
			];
			$topNav[] = [
				'navi_name' => '课程',
				'navi_key'  => 'course',
				'url' => U('school/school/course',['id'=>self::$school_id]),
			];
			$topNav[] = [
				'navi_name' => '直播',
				'navi_key'  => 'live',
				'url' => U('school/school/live',['id'=>self::$school_id]),
			];
			$topNav[] = [
				'navi_name' => '班级',
				'navi_key'  => 'album',
				'url' => U('school/school/album',['id'=>self::$school_id]),
			];
			$topNav[] = [
				'navi_name' => '讲师',
				'navi_key'  => 'teacher_index',
				'url' => U('school/school/teacher_index',['id'=>self::$school_id]),
			];
		}
		return $topNav;
	}
	/**
	 * 游客导航
	 * @return multitype:
	 */
	public function getGuestNav(){
		$guestNav = model('Cache')->get('guestNav');
		$cache =  (self::$host != SITE_URL) || ((self::$host == SITE_URL) && !$guestNav);
		// 如果是主站,加载缓存,如果不是,不论是否有缓存,都重新加载数据
		if($cache){
			$map['status'] = 1;
			$map['position'] = 2;
			$list = $this->where($map)->order('order_sort ASC')->findAll();
			foreach($list as $v){
				$v['url'] = empty($v['url']) ? 'javascript:;' : str_replace('{website}', self::$host, $v['url']);
				if ( $v['parent_id'] == 0 ){
					$navlist[$v['navi_id']] = $v;
				}
			}
			foreach($list as $v){
				if ( $v['parent_id'] > 0 ){
					$navlist[$v['parent_id']]['child'][] = $v;
				}
			}
			$guestNav = $navlist;
			empty($guestNav) && $guestNav = array();
			model('Cache')->set('guestNav', $guestNav);
		}
		
		return $guestNav;
	}
	/**
	 * 获取底部导航
	 * @return array 底部导航
	 */
	public function getBottomNav() {
		$bottomNav = model('Cache')->get('bottomNav');
		$cache =  (self::$host != SITE_URL) || ((self::$host == SITE_URL) && !$bottomNav);
		// 如果是主站,加载缓存,如果不是,不论是否有缓存,都重新加载数据
		if($cache){
			$map['status'] = 1;
			$map['position'] = 1;
			$list = $this->where($map)->order('order_sort ASC')->findAll();
	    	foreach($list as $v){
	    		$v['url'] = empty($v['url']) ? 'javascript:;' : str_replace('{website}', self::$host, $v['url']);
	    		if ( $v['parent_id'] == 0 ){
	    			$navlist[$v['navi_id']] = $v;
	    		}
	    	}
	    	foreach($list as $v){
	    		if ( $v['parent_id'] > 0 ){
	    			$navlist[$v['parent_id']]['child'][] = $v;
	    		}
	    	}
			$bottomNav = $navlist;
			empty($bottomNav) && $bottomNav = array();
			model('Cache')->set('bottomNav', $bottomNav);
		}

		return $bottomNav;
	}
	
	public function getBottomChildNav($bottomNav){
		foreach ($bottomNav as $v){
			if(isset($v['child']) && !empty($bottomNav)){
				return true;
			}
		}
		return false;
	}
	/**
	 * 清除导航缓存
	 * @return void
	 */
	public function cleanCache() {
		model('Cache')->rm('topNav');
		model('Cache')->rm('guestNav');
		model('Cache')->rm('bottomNav');
	}	
}