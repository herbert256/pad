<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/highlighters/_lib/highlight.php';check(padHighlightLanguage('kt')==='kotlin');$r=padHighlightRules('kotlin'); foreach($r as $p){check(preg_match('~'.$p.'~us',str_repeat('x',100000))!==false);} $s="fun main() { println(\"<tag>\") } // hi";check(preg_match('~'.$r['str'].'~us',$s,$m)===1);check(str_contains($m[0],'<tag>'));check(preg_match('~'.$r['com'].'~us',$s)===1);
function padUnprotectQuotes($s){return $s;}require PAD.'lib/highlight.php';$html=padHighlightTokens($s,'kotlin');check(!str_contains($html,'<tag>'));check(str_contains($html,'hl-str'));check(str_contains($html,'hl-com'));
