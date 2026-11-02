<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/units.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
near(padConvert(1,'lb/ft3','kg/m3'),16.01846337396014);
foreach(padUnits('density')['units'] as $symbol=>$u){near(padConvert(padConvert(12.5,$symbol,'kg/m3'),'kg/m3',$symbol),12.5);foreach($u['aliases'] as $a)near(padConvert(1,$a,'kg/m3'),$u['scale']);}
rejects(fn()=>padConvert(1,'kg/m3','J'));rejects(fn()=>padConvert(INF,'kg/m3','lb/ft3'));
echo "units:density fixtures passed\n";
