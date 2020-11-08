<?php
   $spark_config = array(
       'user_id' => '',//CC视频用户id
       'key' => '',//CC视频用户key
       'charset' => 'utf-8',
       'api_videos' => 'http://spark.bokecc.com/api/videos',//api获取多个视频信息接口
       'api_user' => 'http://spark.bokecc.com/api/user',//api获取用户信息接口
       'api_playcode' => 'http://spark.bokecc.com/api/video/playcode',//api获取视频播放代码接口
       'api_deletevideo' => 'http://spark.bokecc.com/api/video/delete',//api删除视频接口
       'api_editvideo' => 'http://spark.bokecc.com/api/video/update',//api编辑视频接口
       'api_video' => 'http://spark.bokecc.com/api/video',//api获取单一视频接口
       'api_category' => 'http://spark.bokecc.com/api/video/category',//api获取视频分类接口
       'notify_url' => 'http://127.0.0.5/api/cc/notify.php',//验证地址
   );