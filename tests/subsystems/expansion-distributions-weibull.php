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
near(padSample('weibull',1,['shape'=>2,'scale'=>3,'random'=>fn()=>1-exp(-4)])[0],6);check(padSample('weibull',1,['random'=>fn()=>0])===[0.0]);$a=padSample('weibull',20000,['seed'=>55]);near(array_sum($a)/count($a),1,.025);check($a===padSample('weibull',20000,['seed'=>55]));rejects(fn()=>padSample('weibull',1,['scale'=>0]));
echo "distributions:weibull fixtures passed\n";
