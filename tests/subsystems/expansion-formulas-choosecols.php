<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/formulas.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$cells=['A1'=>1,'B1'=>2,'A2'=>3,'B2'=>4];check(padFormula('=CHOOSECOLS(A1:B2,-1,1)',$cells)===[[2,1],[4,3]]);
$s=padSheet(['A1'=>1,'A2'=>2,'B1'=>5,'B2'=>6,'C1'=>'=CHOOSECOLS(A1:B2,1)','C2'=>'blocked']);check($s['C1']==='#SPILL!');
check(padFormula('=CHOOSECOLS(A1:B2,3)',$cells)==='#VALUE!');
echo "formulas:choosecols fixtures passed\n";
