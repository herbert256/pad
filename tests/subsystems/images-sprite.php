<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/images/_lib/sprite.php';
$m=['source'=>'atlas.png','dpr'=>2,'sprites'=>['tile'=>[10,20,40,60]]];check(padImageSpriteBox($m,'tile',100,100)===[10,20,40,60,2.0]);foreach([[90,0,20,10],[-1,0,10,10],[0,0,0,10],[0,0,10.5,10]] as $box){$m['sprites']['tile']=$box;try{padImageSpriteBox($m,'tile',100,100);throw new RuntimeException('accepted invalid bounds');}catch(InvalidArgumentException $e){}}echo "sprite atlas bounds, integer crop and DPR OK
";
