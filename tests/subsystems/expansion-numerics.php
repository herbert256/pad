<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/numerics.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$r=padNumeric('interpolate',[[0,1],[2,5],[4,9]],['at'=>[0,1,3,4]]);check(array_column($r['values'],'y')===[1.0,3.0,7.0,9.0]);near(padNumeric('interpolate',[[0,1],[2,5]],['at'=>-1,'extrapolate'=>'linear'])['values'][0]['y'],-1);rejects(fn()=>padNumeric('interpolate',[[0,1],[0,2]],['at'=>0]));
near(padNumeric('integrate',[[0,0],[1,2],[2,4]])['value'],4);$samples=[];for($i=0;$i<=100;$i++)$samples[]=[$i/100,($i/100)**2];near(padNumeric('integrate',$samples)['value'],.33335,1e-12);
$r=padNumeric('root',['function'=>fn($x)=>$x*$x-2,'lower'=>0,'upper'=>2]);check($r['status']==='ok');near($r['value'],sqrt(2),1e-10);check($r['residual']<1e-10);check(padNumeric('root',['function'=>fn($x)=>$x*$x+1,'lower'=>-1,'upper'=>1])['status']==='unbracketed');check(padNumeric('root',['function'=>fn($x)=>$x*$x-2,'lower'=>0,'upper'=>2],['maxIterations'=>1])['status']==='limit');
$r=padNumeric('linear-system',['matrix'=>[[0,2,1],[1,-2,-3],[3,-1,2]],'rhs'=>[7,-12,7]]);check($r['status']==='ok');foreach($r['value'] as $i=>$v)near($v,[1,2,3][$i]);near($r['residual'],0);check(padNumeric('linear-system',['matrix'=>[[1,2],[2,4]],'rhs'=>[3,6]])['status']==='singular');check(padNumeric('linear-system',['matrix'=>[[1,1],[1,1+1e-14]],'rhs'=>[2,2+1e-14]])['status']==='ill-conditioned');
foreach(['interpolate'=>['at'=>1],'integrate'=>[]] as $kind=>$o)check(padNumeric($kind,[[0,0],[1,1]],$o+['maxWork'=>1])['status']==='limit');rejects(fn()=>padNumeric('root',['function'=>fn($x)=>INF,'lower'=>0,'upper'=>1]));rejects(fn()=>padNumeric('linear-system',['matrix'=>[[1]],'rhs'=>['2']]));for($a=1;$a<=5;$a++)for($b=1;$b<=5;$b++){if($a*7===$b*3)continue;$r=padNumeric('linear-system',['matrix'=>[[$a,$b],[3,7]],'rhs'=>[$a*2-$b*4,6-28]]);check($r['status']==='ok');near($r['value'][0],2);near($r['value'][1],-4);}
echo "numerics: analytic interpolation/integration, bracket statuses, pivoting, singular/ill-conditioned and work fixtures passed\n";
