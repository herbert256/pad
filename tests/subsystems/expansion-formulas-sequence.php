<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/formulas.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$cells=['A1'=>1,'B1'=>2,'A2'=>3,'B2'=>4];check(padFormula('=SEQUENCE(2,3,10,-2)',$cells)===[[10,8,6],[4,2,0]]);
check(padFormula('=SEQUENCE(0)')==='#CALC!');check(padFormula('=SEQUENCE(101,100)')==='#NUM!');
$s=padSheet(['A1'=>1,'A2'=>2,'B1'=>5,'B2'=>6,'C1'=>'=SEQUENCE(2)','C2'=>'blocked']);check($s['C1']==='#SPILL!');
$s=padSheet(['A1'=>'=SEQUENCE(2)','C1'=>'=A2*10']);check($s['C1']===20);$s=padSheet(['A1'=>'=SEQUENCE(2,1,B1)','B1'=>'=A2']);check($s['A1']==='#REF!');
echo "formulas:sequence fixtures passed\n";
