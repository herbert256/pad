<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padUnprotectQuotes($s){return $s;} function padWidgetOnce($s){return FALSE;}
require PAD.'subs/diffs/_lib/unified.php';
function check($v,$why){if(!$v)throw new RuntimeException($why);}
check(padDiffUnifiedHunks("same\r\n","same\n")===[],'CRLF normalization');
$h=padDiffUnifiedHunks('',"new\n",0)[0];check([$h['old'],$h['oldCount'],$h['new'],$h['newCount']] === [0,0,1,1],'empty to insertion ranges');
$h=padDiffUnifiedHunks("old\n",'',0)[0];check([$h['old'],$h['oldCount'],$h['new'],$h['newCount']] === [1,1,0,0],'deletion ranges');
$html=padDiffUnified('a',"a\n");check(str_contains($html,'No newline at end of file'),'newline only change');
check(str_contains(padDiffUnified('<script>','safe'),'&lt;script&gt;'),'escaping');
try{padDiffUnifiedHunks("\xc3","\xa9");throw new RuntimeException('Invalid operands repaired each other');}catch(InvalidArgumentException $e){}
$before=implode("\n",range(1,30))."\n";$after=preg_replace(['/^4$/m','/^26$/m'],['four','twenty-six'],$before);
check(count(padDiffUnifiedHunks($before,$after,1))===2,'separated edit hunks');
// Reconstruct both streams from the full-context edit script across randomized line sequences.
mt_srand(771);for($i=0;$i<300;$i++){
 $a=$b='';for($j=0;$j<mt_rand(0,15);$j++)$a.=chr(97+mt_rand(0,5))."\n";for($j=0;$j<mt_rand(0,15);$j++)$b.=chr(97+mt_rand(0,5))."\n";
 $h=padDiffUnifiedHunks($a,$b,100);if($a===$b){check(!$h,'equal has no hunks');continue;}
 check(count($h)===1,'full context single hunk');$old=$new='';foreach($h[0]['lines'] as [$op,$line]){if($op!=='+')$old.=$line;if($op!=='-')$new.=$line;}
 check($old===$a&&$new===$b,'reconstruction');
}
echo "unified diff: ranges, final newlines, escaping and 300 edit reconstructions passed\n";
