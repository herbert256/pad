<?php
define('PAD',dirname ( __DIR__, 2 ).'/pad/');require PAD.'lib/encoding.php';require PAD.'subs/notations/_lib/notation.php';require PAD.'subs/notations/_lib/fretboard.php';
function verify($ok,$why){if(!$ok)throw new RuntimeException($why);}
$s=padFretboardParse('C major','E2 A2 D3 G3 B3 E4');verify(is_array($s),'scale');verify(is_string(padFretboardParse('C nonsense','E2')),'invalid scale');verify(is_string(padFretboardParse('C major','Q2')),'invalid tuning');
$a=padFretboardSvg($s,0,12,'notes',FALSE,720,'<scale>');$b=padFretboardSvg($s,0,12,'notes',TRUE,720,'<scale>');verify($a!==$b,'flip');verify(str_contains($a,'string 1, fret 0: E'),'open string');verify(!str_contains($a,'<scale>'),'escaped');$xml=new DOMDocument();verify($xml->loadXML($a),'SVG');echo "notation fretboard: OK\n";
