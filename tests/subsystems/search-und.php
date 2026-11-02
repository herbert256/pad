<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/search/_lib/search.php';$l=padSearchLanguage('und');check(padSearchWords('PAD-42 café foo_bar / X.Y',$l)===['pad-42','café','foo_bar','x.y']);check(padSearchTerms('the',$l)===['the']);check(padSearchTerms('café',$l)===['café']);check(padSearchTerms('running',$l)===['running']);
