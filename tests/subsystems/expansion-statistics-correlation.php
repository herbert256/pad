<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/statistics.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$rows=[['x'=>1,'y'=>5],['x'=>2,'y'=>3],['x'=>3,'y'=>4],['x'=>4,'y'=>1]];near(padStats('correlation',$rows)['correlation'],-.8315218406202999);near(padStats('correlation',$rows,['method'=>'spearman'])['correlation'],-.8);check(padStats('correlation',[['x'=>2,'y'=>1],['x'=>2,'y'=>3]])['status']==='degenerate');$rows[]=['x'=>NULL,'y'=>1];check(padStats('correlation',$rows)['missing']===1);rejects(fn()=>padStats('correlation',$rows,['missing'=>'error']));near(padStats('correlation',[['x'=>1,'y'=>3],['x'=>1,'y'=>3],['x'=>2,'y'=>5]],['method'=>'spearman'])['correlation'],1);
echo "statistics:correlation fixtures passed\n";
