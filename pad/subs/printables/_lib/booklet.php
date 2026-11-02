<?php
// Return logical page indices in physical sheet-face order, zero based; null is padding.
function padBookletOrder($count,$binding='left'){
 if($count<1||$count>1000||!in_array($binding,['left','right'],TRUE))throw new InvalidArgumentException('Invalid booklet size or binding');
 $n=(int)(ceil($count/4)*4);$order=[];
 for($sheet=0;$sheet<$n/4;$sheet++){
  foreach([[$n-1-2*$sheet,2*$sheet],[2*$sheet+1,$n-2-2*$sheet]] as $pair){
   if($binding==='right')$pair=array_reverse($pair);
   foreach($pair as $page)$order[]=$page<$count?$page:NULL;
  }
 }
 return $order;
}
?>
