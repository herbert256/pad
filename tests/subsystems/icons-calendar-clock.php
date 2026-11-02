<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'lib/encoding.php'; function padFileGet($p){return file_get_contents($p);} require PAD.'subs/icons/_lib/icon.php';
$kind='calendar-clock';
foreach([16,20,24] as $size){$svg=padIcon($kind,$size,'A < B');$xml=new DOMDocument();check($xml->loadXML($svg));check(str_contains($svg,'width="'.$size.'"'));check(str_contains($svg,'A &lt; B'));check(!preg_match('/<(script|foreignObject)|on[a-z]+=|href=/i',$svg));}check(str_contains(padIcon($kind),'aria-hidden="true"'));echo "$kind: safe SVG at 16/20/24px OK
";
