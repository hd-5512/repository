<?php

//1.json_decode 再encode 并不能完全互转

$a = '';

$a = json_decode($a); // null

$a = json_encode($a); // "null"


$b = '';

$b = json_encode($b); // ''

$b = json_decode($b); // null

//避免方式 使用统一数组 [] 作为默认值 且 encode 和 decode 前必须先确认数据是否empty 否侧都设置 []


//2.数据压缩和编码

$c = '中文';
$c1 = json_encode($c,JSON_UNESCAPED_SLASHES);//保留原符号转义
$c2 = json_encode($c,JSON_UNESCAPED_UNICODE);//保留原编码

//存储数据长度在数据库变短 并解析时不易出现异常


