<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/statistics.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$o=['seed'=>123,'resamples'=>500,'interval'=>'percentile'];$r=padStats('bootstrap',[1,2,3,4],$o);check($r===padStats('bootstrap',[1,2,3,4],$o));near($r['estimate'],2.5);check($r['lower']>=1&&$r['upper']<=4&&$r['lower']<2.5&&$r['upper']>2.5);$r=padStats('bootstrap',[7,7,7],$o+['estimator'=>'median']);near($r['lower'],7);near($r['upper'],7);rejects(fn()=>padStats('bootstrap',[1,2,3],['seed'=>1]));rejects(fn()=>padStats('bootstrap',[1,2,3],$o+['maxWork'=>2]));
echo "statistics:bootstrap fixtures passed\n";
