<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/format.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$padCheckSyntax=TRUE;
$html='<table id="wanted"><tr><th>Name</th><th>A</th><th>B</th></tr><tr><td rowspan="2"> é &amp; </td><td colspan="2">x</td></tr><tr><td>y</td><td>z<script>bad()</script></td></tr></table>';$r=padFormatRead('htmltable',$html,['table'=>'#wanted']);check($r===[['Name'=>' é & ','A'=>'x','B'=>'x'],['Name'=>' é & ','A'=>'y','B'=>'z']]);check(padFormatRead('htmltable','<table><tr><td>A</td><td>B</td></tr></table>',['header'=>'none'])===[['column1'=>'A','column2'=>'B']]);rejects(fn()=>padFormatRead('htmltable','<table><tr><td rowspan="2">a</td></tr></table>'));rejects(fn()=>padFormatRead('htmltable','<!DOCTYPE html SYSTEM "file:///etc/passwd"><table></table>'));rejects(fn()=>padFormatRead('htmltable','<table><tr><th>A</th><th>A</th></tr></table>'));
echo "formats:htmltable fixtures passed\n";
