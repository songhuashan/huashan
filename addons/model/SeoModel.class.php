<?php
/**
 * Seo模型 - 业务逻辑模型
 * @author zivss <guolee226@gmail.com>
 * @version TS3.0
 */
class SeoModel extends Model{
	protected $tableName = 'seo';

    /**
     * 模型初始化
     * @return void
     */
    public function _initialize(){
    }

    public function installSeo($info,$this_seo){
        $info['_keywords'] = msubstr($info['_keywords'],0,80);
        //设置seo详情
        $seo['_title'] = $info['_keywords'] ? $info['_title'].' — '.$info['_keywords'] : $info['_title'].$this_seo['_bak_title'];
        $seo['_keywords'] = $info['_keywords'] ? : $this_seo['_description'];
        $seo['_description'] = $this_seo['_description'];

        return $seo;
    }

}