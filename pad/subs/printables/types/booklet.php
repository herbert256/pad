<?php
include_once PAD.'subs/printables/_lib/booklet.php';
$binding=padPrintableChoice('binding','left',['left','right']);
$paperName=padPrintableChoice('paper','a4',['a4','a3','letter']);
[$h,$w]=PAD_PRINTABLE_PAPERS[$paperName];$paper=[$w,$h];
$box=padPrintableBox(['pad'=>10,'size'=>11,'align'=>'left','valign'=>'top']);
$numbers=padPrintableFlag('numbers');
return padPrintablePlan(['name'=>['logical page','logical pages'],'paper'=>$paper,'paperName'=>$paperName.' landscape','cells'=>[[0,0,$w/2,$h],[$w/2,0,$w/2,$h]],'documentCopies'=>TRUE,
'prepare'=>function($items)use($binding,$numbers){
 if(!$items)$items=[''];$out=[];
 foreach(padBookletOrder(count($items),$binding) as $i)$out[]=$i===NULL?'':$items[$i].($numbers?'<footer>'.($i+1).'</footer>':'');
 return $out;
},'parts'=>['footer'],
'item'=>function($cell,$parts)use($box){[$x,$y,$w,$h]=$cell;if(!$parts)return [];
 $draw=[['box',$x,$y,$w,$h-10,$parts[''],$box]];
 if(($parts['footer']??'')!=='')$draw[]=['box',$x,$h-12,$w,8,$parts['footer'],['pad'=>0,'size'=>8,'align'=>'center']];return $draw;
}]);
?>
