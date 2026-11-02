<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/timelines/_lib/rangecompare.php';$r=['id'=>'x','label'=>'<x>','plannedstart'=>'2026-01-01T00:00Z','plannedend'=>'2026-01-03T00:00Z'];$h=padTimelineRangeCompare([$r]);check(str_contains($h,'actual: missing'));check(str_contains($h,'<rect'));check(!str_contains($h,'<x>'));try{padTimelineRangeCompare([$r,$r]);check(false);}catch(InvalidArgumentException $e){}
