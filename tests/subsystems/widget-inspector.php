<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/widgets/_lib/inspector.php';$h=padWidgetInspector(['a'=>NULL,'b'=>'','secret'=>'hidden'],['a'=>'A','b'=>'B','c'=>['label'=>'C','action'=>'javascript:bad']]);check(str_contains($h,'Null'));check(str_contains($h,'Empty'));check(str_contains($h,'Absent'));check(!str_contains($h,'hidden'));check(!str_contains($h,'javascript:'));
