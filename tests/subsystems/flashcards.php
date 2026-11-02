<?php
function padPrintableParts($s,$names){$p=explode('|',$s);return [''=>$p[0],'footer'=>$p[1]??''];}
require __DIR__.'/../../pad/subs/printables/_lib/flashcards.php';
$in=['A|a','B|b','C|c'];
if(padFlashcardPages($in,2,2,'long')!==['A','B','C','','b','a','','c'])throw new Exception('Long-edge pairing');
if(padFlashcardPages($in,2,2,'short')!==['A','B','C','','c','','a','b'])throw new Exception('Short-edge pairing');
echo "flashcards: both duplex edges and blank padding passed\n";
