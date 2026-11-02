<?php
function padWidgetAttr($s){return htmlspecialchars((string)$s,ENT_QUOTES);}
function padWidgetLink($s){return str_starts_with($s,'javascript:')?'':padWidgetAttr($s);}
function padWidgetSheet($s){return '';}function padWidgetScript($a,$b){return '';}function padWidgetFile($a,$b){return '';}
require __DIR__.'/../../pad/subs/widgets/_lib/treeview.php';
$x=padTreeview([['id'=>'a','label'=>'<x>'],['id'=>'b','parent'=>'a','label'=>'child']]);
if(!str_contains($x,'&lt;x&gt;')||substr_count($x,'<ul>')!==2)throw new Exception('Tree shape or escaping failed');
try{padTreeview([['id'=>'a','parent'=>'b'],['id'=>'b','parent'=>'a']]);throw new Exception('Cycle accepted');}catch(InvalidArgumentException $e){}
echo "treeview: hierarchy, escaping and cycles passed\n";
