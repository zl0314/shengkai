<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_json.php');

    $json = new JSON;

    include_once(ROOT_PATH . 'includes/qiniu/io.php');
    include_once(ROOT_PATH . 'includes/qiniu/rs.php');
    $bucket = 'sk-img';
    $accessKey = 'm0jTgSHh5xm-gvXnYRpplPif7mMTingucklVUJwE';
    $secretKey = 'w8Bew33jUWIN3xWzxgXQIfPDGvU_Z5glX0KYvQLh';
    $qiniu_url = "http://7xjtzw.com2.z0.glb.qiniucdn.com/";

    Qiniu_SetKeys($accessKey, $secretKey);
    $putPolicy = new Qiniu_RS_PutPolicy($bucket);
    $upToken = $putPolicy->Token(null);
    $result = array('uptoken' => $upToken);
    exit($json->encode($result));
   
?>
