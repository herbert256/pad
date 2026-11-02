<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/filters/_lib/filter.php';
$s=padFilterKind('unsharp');
function pixel($v){$i=imagecreatetruecolor(1,1);imagealphablending($i,FALSE);imagesavealpha($i,TRUE);imagesetpixel($i,0,0,$v);return $i;}
foreach([1,2] as $scale)foreach([0x00808080,0x3f808080,0x7f808080] as $p)check(imagecolorat($s['apply'](pixel($p),['amount'=>0,'radius'=>1,'threshold'=>0],$scale),0,0)===$p,'identity alpha');
check((imagecolorat($s['apply'](pixel(0x3f808080),['amount'=>2,'radius'=>1,'threshold'=>255],1),0,0)&0xffffff)===0x808080);
$i=imagecreatetruecolor(3,1);imagealphablending($i,FALSE);foreach([0,0xffffff,0] as $x=>$v)imagesetpixel($i,$x,0,$v);$out=$s['apply']($i,['amount'=>4,'radius'=>1,'threshold'=>0],1);check((imagecolorat($out,1,0)&0xffffff)===0xffffff,'clip white');check((imagecolorat($out,0,0)&0xffffff)===0,'clip black');
echo "filter unsharp: reference pixels and alpha OK\n";
