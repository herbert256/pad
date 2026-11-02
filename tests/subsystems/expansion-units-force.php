<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/units.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
near(padConvert(1,'kgf','N'),9.80665);
foreach(padUnits('force')['units'] as $symbol=>$u){near(padConvert(padConvert(12.5,$symbol,'N'),'N',$symbol),12.5);foreach($u['aliases'] as $a)near(padConvert(1,$a,'N'),$u['scale']);}
rejects(fn()=>padConvert(1,'N','J'));rejects(fn()=>padConvert(INF,'N','kgf'));
echo "units:force fixtures passed\n";
