<?php
class OfficeModel extends Model {
	protected $ext = [
		'wp','sword','excel','ppt','png','jpg','gif','bmp','xls','xlsx','doc','docx','pptx','txt'
	];
	//将文档转换为pdf
	public function transfer_office() {
		// 筛选本地未转码的文档
		$map = ['transcoding_status'=>2,'is_del'=>0,'type'=>4,'video_type' => 0];
		$list = M('zy_video_data')->where($map)->order('ctime desc')->findPage();
		if($list['data']){

			set_time_limit(0);
			foreach ($list['data'] as $k => $v) {
				// 转码非pdf
				$extension = substr(strrchr($v['video_address'], '.'), 1);
				if(in_array($extension,$this->ext)){
					$path = parse_url($v['video_address']);
					$file = SITE_PATH.$path['path'];
					$turnFileName = substr($file,0,strrpos($file,'.')).'.pdf';
					// 待转码文件存在
					if(file_exists($file)){
						if(!file_exists($turnFileName)){
							$command = 'PATH=$PATH unoconv -l -f pdf '.$file.'> /dev/null &';
							exec($command);
						}else{
							// 存在文件 表示转码完成
							M('zy_video_data')->where('id='.$v['id'])->save(['transcoding_status'=>1]);
						}
						// 转码后的文件
						//if(file_exists($turnFileName)){
						//M('zy_video_data')->where('id='.$v['id'])->save(['transcoding_status'=>2]);
						//}else{
						//D('Resource','classroom')->where('resource_id='.$v['resource_id'])->save(['is_turnpdf'=>2]);
						//}
					}else{
						M('zy_video_data')->where('id='.$v['id'])->save(['transcoding_status'=>0]);
					}

				}else{
					// 无需转码
					M('zy_video_data')->where('id='.$v['id'])->save(['transcoding_status'=>1]);
				}


			}
		}
	}
}