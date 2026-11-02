<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'lib/encoding.php';require PAD.'subs/codes/_lib/barcode.php';require_once PAD.'subs/codes/_lib/qr.php';require PAD.'subs/codes/_lib/pharmacode.php';
$fixtures=json_decode(file_get_contents(__DIR__.'/fixtures/pharmacode-zint.json'),TRUE);foreach($fixtures as $n=>$bits){$code=padPharmacode((string)$n);check($code[0]===$bits,'independent Zint modules');$decoded=0;foreach(explode('00',$bits) as $bar)$decoded=2*$decoded+(strlen($bar)===1?1:2);check($decoded===$n,'independent decoder');check($code[3]===12);}
foreach(['2','131071','1e3','-4','12.3',''] as $bad)check(is_string(padPharmacode($bad)));$svg=padPharmacodeSvg('42');check(str_contains($svg,'height="8mm"'));check(str_contains($svg,'<title>Pharmacode 42</title>'));echo "pharmacode independent encoder/decoder fixtures and physical size OK
";
