<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/formulas.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$cells=['A1'=>1,'B1'=>2,'A2'=>3,'B2'=>4];check(padFormula('=CHOOSEROWS(A1:B2,-1,1)',$cells)===[[3,4],[1,2]]);
check(padFormula('=CHOOSEROWS(A1:B2,0)',$cells)==='#VALUE!');
check(padFormula('=CHOOSEROWS(A1:B2,3)',$cells)==='#VALUE!');
echo "formulas:chooserows fixtures passed\n";
