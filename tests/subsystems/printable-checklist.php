<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function padPrintableParts($s,$names){preg_match('~<aside>(.*?)</aside>~s',$s,$m);return [''=>preg_replace('~<aside>.*?</aside>~s','',$s),'aside'=>$m[1]??''];}require PAD.'subs/printables/_lib/checklist.php';$p=padPrintableChecklistPages(array_fill(0,100,'<aside>Group</aside>Inspect the package'),[210,297],2);check(count($p)>1);check($p[1][0]['heading']===TRUE);check(array_sum(array_map('count',$p))===102);
