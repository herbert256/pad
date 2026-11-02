<?php
include_once PAD.'subs/printables/_lib/flashcards.php';
$across=padPrintableInt('across',2,1,4);$down=padPrintableInt('down',4,1,8);
$edge=padPrintableChoice('flip','long',['long','short']);
$dx=padPrintableNumber('backx',0,-5,5);$dy=padPrintableNumber('backy',0,-5,5);
$calibrate=padPrintableFlag('calibrate');
[$name,$paper]=padPrintablePaper(['a4'=>PAD_PRINTABLE_PAPERS['a4'],'letter'=>PAD_PRINTABLE_PAPERS['letter']],'a4');
$mirror=$paper[0]>$paper[1]?($edge==='long'?'short':'long'):$edge;
$cells=[];$w=($paper[0]-20)/$across;$h=($paper[1]-20)/$down;
for($r=0;$r<$down;$r++)for($c=0;$c<$across;$c++)$cells[]=[10+$c*$w,10+$r*$h,$w,$h];
$box=padPrintableBox(['pad'=>5,'size'=>14,'align'=>'center','valign'=>'middle']);
return padPrintablePlan(['name'=>['card face','card faces'],'paper'=>$paper,'paperName'=>$name,'cells'=>$cells,
'prepare'=>fn($items)=>padFlashcardPages($items,$across,$down,$mirror),
'item'=>function($cell,$parts,$n)use($box,$across,$down,$dx,$dy,$calibrate){
 [$x,$y,$w,$h]=$cell;$back=intdiv(max(0,$n),$across*$down)%2===1;if($back){$x+=$dx;$y+=$dy;}
 $draw=[padPrintableEdge([$x,$y,$w,$h],TRUE)];
 if($parts)$draw[]=['box',$x,$y,$w,$h,$parts[''],$box];
 if($calibrate)$draw[]=['box',$x,$y,$w,8,($back?'Back':'Front').' '.($n%($across*$down)+1),['size'=>7,'pad'=>1]];
 return $draw;
}]);
?>
