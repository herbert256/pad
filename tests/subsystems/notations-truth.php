<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/notations/_lib/notation.php';function padMakeSafe($v,$n){return substr($v,0,$n);}require PAD.'subs/notations/_lib/truth.php';
$t=padTruthRead('X = A xor B; F = X and C');check(count($t['rows'])===8);foreach($t['rows'] as $r){check($r['X']===($r['A']!==$r['B']));check($r['F']===(($r['A']!==$r['B'])&&$r['C']));}check(str_contains(padTruthHtml($t,'<truth>'),'&lt;truth&gt;'));foreach(['F = system("x")','F = A or B or C or D or E or G or H','F=A;F=B','F=F and A'] as $bad){try{padTruthRead($bad);throw new RuntimeException('accepted invalid input');}catch(InvalidArgumentException $e){}}echo "truth independent XOR/AND assignments, intermediates, limits OK
";
