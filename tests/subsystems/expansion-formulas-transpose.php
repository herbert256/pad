<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/formulas.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$cells=['A1'=>1,'B1'=>2,'A2'=>3,'B2'=>4];check(padFormula('=TRANSPOSE(A1:B2)',$cells)===[[1,3],[2,4]]);
$s=padSheet(['A1'=>1,'A2'=>2,'B1'=>5,'B2'=>6,'C1'=>'=TRANSPOSE(A1:A2)','D1'=>'blocked']);check($s['C1']==='#SPILL!');
echo "formulas:transpose fixtures passed\n";
