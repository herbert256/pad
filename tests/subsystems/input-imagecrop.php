<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/inputs/_lib/imagecrop.php';$im=['id'=>'one','width'=>100,'height'=>50,'orientation'=>6];check(padInputCropImage($im)['width']===50);check(padInputCrop(['image'=>'one','x'=>0,'y'=>0,'width'=>50,'height'=>100],$im)['height']===100);foreach([['image'=>'other','x'=>0,'y'=>0,'width'=>1,'height'=>1],['image'=>'one','x'=>49,'y'=>0,'width'=>2,'height'=>1]] as $v){try{padInputCrop($v,$im);check(false);}catch(InvalidArgumentException $e){}}
