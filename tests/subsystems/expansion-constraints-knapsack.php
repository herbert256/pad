<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/constraints.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
for($capacity=0;$capacity<=12;$capacity++){$items=[['id'=>'a','weight'=>0,'value'=>2],['id'=>'b','weight'=>3,'value'=>5],['id'=>'c','weight'=>4,'value'=>7],['id'=>'d','weight'=>5,'value'=>8]];$best=0;for($mask=0;$mask<16;$mask++){$w=0;$v=0;foreach($items as $i=>$it)if($mask&(1<<$i)){$w+=$it['weight'];$v+=$it['value'];}if($w<=$capacity)$best=max($best,$v);}$r=padSolve('knapsack',['items'=>$items,'capacity'=>$capacity]);check($r['value']===$best&&$r['weight']<=$capacity);}
check(padSolve('knapsack',['items'=>$items,'capacity'=>10],['maxSteps'=>1])['status']==='limit');check(padSolve('knapsack',['items'=>$items,'capacity'=>10],['cancel'=>fn()=>true])['status']==='cancelled');rejects(fn()=>padSolve('knapsack',['items'=>[['id'=>'a','weight'=>1.2,'value'=>2]],'capacity'=>3]));
echo "constraints:knapsack fixtures passed\n";
