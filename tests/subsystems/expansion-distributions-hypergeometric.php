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
check(padSample('hypergeometric',1,['population'=>0,'successes'=>0,'draws'=>0])===[0]);check(padSample('hypergeometric',1,['population'=>4,'successes'=>2,'draws'=>4,'seed'=>1])===[2]);$a=padSample('hypergeometric',20000,['population'=>4,'successes'=>2,'draws'=>2,'seed'=>55]);$h=array_count_values($a);near($h[0]/20000,1/6,.015);near($h[1]/20000,4/6,.015);near($h[2]/20000,1/6,.015);check($a===padSample('hypergeometric',20000,['population'=>4,'successes'=>2,'draws'=>2,'seed'=>55]));rejects(fn()=>padSample('hypergeometric',1,['population'=>2,'successes'=>3]));
echo "distributions:hypergeometric fixtures passed\n";
