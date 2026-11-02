<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/fake.php';
require PAD.'lib/distributions.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
check(padSample('geometric',3,['p'=>1,'seed'=>1])===[0,0,0]);foreach([[0,0],[.49,0],[.5,1],[.749,1],[.75,2]] as [$u,$n])check(padSample('geometric',1,['p'=>.5,'random'=>fn()=>$u])[0]===$n);$a=padSample('geometric',20000,['p'=>.25,'seed'=>55]);near(array_sum($a)/count($a),3,.04);check($a===padSample('geometric',20000,['p'=>.25,'seed'=>55]));rejects(fn()=>padSample('geometric',1,['p'=>0]));
echo "distributions:geometric fixtures passed\n";
