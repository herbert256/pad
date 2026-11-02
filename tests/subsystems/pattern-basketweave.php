<?php
require dirname ( __DIR__, 2 ).'/pad/lib/encoding.php';
require dirname ( __DIR__, 2 ).'/pad/subs/images/patterns/_lib/pattern.php';
function verify($ok,$why){if(!$ok)throw new RuntimeException($why);}
$spec=include dirname ( __DIR__, 2 ).'/pad/subs/images/patterns/types/basketweave.php';
$p=['size'=>48,'w'=>480,'h'=>240,'id'=>'test','bg'=>'#fff','ink'=>['#444','#888']];
foreach($spec['options'] as $k=>$v)$p[$k]=$v[0];
$a=$spec['draw']($p);verify($a===$spec['draw']($p),'deterministic');
foreach($spec['options'] as $k=>$v)foreach([$v[1],$v[2]] as $n){$q=$p;$q[$k]=$n;$out=implode('',$spec['draw']($q));verify(!preg_match('/\b(?:NAN|INF)\b/',$out),'finite');$xml=new DOMDocument();verify($xml->loadXML('<svg xmlns="http://www.w3.org/2000/svg">'.$out.'</svg>'),'valid SVG');verify(strlen($out)<15000,'bounded');}
echo "pattern basketweave: OK\n";
