<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/constraints.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$p=['nodes'=>['a','b','c'],'edges'=>[['a','b'],['b','c'],['c','a']],'colors'=>2];check(padSolve('coloring',$p)['status']==='infeasible');$p['colors']=3;$r=padSolve('coloring',$p);$colors=array_column($r['solution'],'color','id');foreach($p['edges'] as [$a,$b])check($colors[$a]!==$colors[$b]);check(padSolve('coloring',$p,['maxSteps'=>1])['status']==='limit');check(padSolve('coloring',$p,['cancel'=>fn()=>true])['status']==='cancelled');rejects(fn()=>padSolve('coloring',$p+['unknown'=>true]));
echo "constraints:coloring fixtures passed\n";
