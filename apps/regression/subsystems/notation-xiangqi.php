<?php
define('PAD',dirname(__DIR__,3).'/pad/');require PAD.'lib/encoding.php';require PAD.'subs/notations/_lib/notation.php';require PAD.'subs/notations/_lib/xiangqi.php';
function verify($ok,$why){if(!$ok)throw new RuntimeException($why);}
$p=padXiangqiParse('start');verify(is_array($p)&&count($p[0])===32,'starting material');foreach(['9/9/9','9/9/9/9/9/9/9/9/9/9','rnbakabnr/9/1c5c1/p1p1p1p1p/9/9/P1P1P1P1P/1C5C1/9/RNBAKABN9','<script>'] as $s)verify(is_string(padXiangqiParse($s)),'reject invalid');$a=padXiangqiSvg($p,FALSE,420,'<board>');verify(str_contains($a,'red general at e0'),'description');verify($a!==padXiangqiSvg($p,TRUE,420,'<board>'),'flip');$xml=new DOMDocument();verify($xml->loadXML($a),'SVG');verify(!str_contains($a,'<board>'),'escape');echo "notation xiangqi: OK\n";
