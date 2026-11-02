<?php
function padFlashcardPages($items,$across,$down,$edge='long'){
 $per=$across*$down;$out=[];
 foreach(array_chunk($items,$per) as $cards){
  $cards=array_pad($cards,$per,'');$front=[];$back=array_fill(0,$per,'');
  foreach($cards as $i=>$card){$parts=padPrintableParts($card,['footer']);$front[]=$parts[''];$r=intdiv($i,$across);$c=$i%$across;
   $target=$edge==='long'?$r*$across+($across-1-$c):($down-1-$r)*$across+$c;
   $back[$target]=$parts['footer']??'';
  }
  array_push($out,...$front,...$back);
 }
 return $out;
}
?>
