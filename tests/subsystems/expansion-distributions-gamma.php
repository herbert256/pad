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
$a=padSample('gamma',20000,['shape'=>2,'scale'=>3,'seed'=>123]);check($a===padSample('gamma',20000,['shape'=>2,'scale'=>3,'seed'=>123]));near(array_sum($a)/count($a),6,.025);$m=array_sum($a)/count($a);$v=array_sum(array_map(fn($x)=>($x-$m)**2,$a))/count($a);near($v,18,.06);rejects(fn()=>padSample('gamma',1,['shape'=>0]));
echo "distributions:gamma fixtures passed\n";
