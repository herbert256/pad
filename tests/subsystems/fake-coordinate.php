<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'lib/fake.php'; padFakeSeed(9);$a=padFake('coordinate',['box'=>[50,3,54,8]]);padFakeSeed(9);check($a===padFake('coordinate',['box'=>[50,3,54,8]])); for($i=0;$i<100;$i++){ $a=padFake('coordinate',['box'=>[50,3,54,8]]);check($a['latitude']>=50&&$a['latitude']<=54&&$a['longitude']>=3&&$a['longitude']<=8); } try{padFake('coordinate',['box'=>[91,0,92,1]]);check(false);}catch(InvalidArgumentException $e){}
