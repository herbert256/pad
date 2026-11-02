<?php
include_once PAD.'subs/printables/_lib/registers.php';
function padPrintableChecklistPages($items,$paper,$blank=0){
 if(count($items)>2000||!is_int($blank)||$blank<0||$blank>100)throw new InvalidArgumentException('checklist max 2000 items and 100 blank rows');$rows=[];
 foreach($items as $item){$parts=padPrintableParts($item,['aside']);$text=padRegisterPlain($parts['']);$group=padRegisterPlain($parts['aside']);if(mb_strlen($text)>2000||mb_strlen($group)>80)throw new InvalidArgumentException('checklist text too long');$lines=padRegisterWrap($text,max(20,(int)(($paper[0]-40)/2.15)));$rows[]=['lines'=>$lines,'group'=>$group,'height'=>max(10,count($lines)*4.5+4)];}
 for($i=0;$i<$blank;$i++)$rows[]=['lines'=>[''],'group'=>'','height'=>10];$pages=[];$page=[];$used=0;$group=NULL;
 foreach($rows as $row){$header=$row['group']!==$group&&$row['group']!==''?8:0;if($row['height']+$header>$paper[1]-60)throw new InvalidArgumentException('checklist row exceeds a page');if($used+$row['height']+$header>$paper[1]-60&&$page){$pages[]=$page;$page=[];$used=0;$header=$row['group']!==''?8:0;}$row['heading']=$header>0;$page[]=$row;$used+=$row['height']+$header;$group=$row['group'];}if($page||!$pages)$pages[]=$page;return $pages;
}
function padPrintableChecklistPlan(){
 [$name,$paper]=padPrintablePaper(['a4'=>PAD_PRINTABLE_PAPERS['a4'],'letter'=>PAD_PRINTABLE_PAPERS['letter']],'a4');$title=padPrintableText('title','Checklist');$blank=padTagParm('blankrows',0);if(filter_var($blank,FILTER_VALIDATE_INT)===FALSE)throw new InvalidArgumentException('blankrows must be integer');$count=0;
 return padPrintablePlan(['name'=>['page','pages'],'paper'=>$paper,'paperName'=>$name,'font'=>'sans','documentCopies'=>TRUE,
 'prepare'=>function($items)use($paper,$blank,&$count){$pages=padPrintableChecklistPages($items,$paper,(int)$blank);$count=count($pages);return array_map(fn($rows)=>json_encode($rows,JSON_THROW_ON_ERROR),$pages);},
 'item'=>function($cell,$parts,$n)use($paper,$title,&$count){if(!$parts)return [];$rows=json_decode($parts[''],TRUE);$draw=[['box',10,10,$paper[0]-20,12,padWidgetAttr($title),['size'=>14,'pad'=>0]],['box',10,$paper[1]-15,$paper[0]-20,6,'Page '.($n%max(1,$count)+1).' of '.$count,['size'=>8,'pad'=>0]]];$y=28;
 foreach($rows as $row){if($row['heading']){$draw[]=['box',10,$y,$paper[0]-20,8,padWidgetAttr($row['group']),['size'=>10,'pad'=>1]];$y+=8;}$draw[]=['rect',12,$y+3,4,4,['stroke'=>'#333333','width'=>.3]];foreach($row['lines'] as $i=>$line)$draw[]=['box',20,$y+2+$i*4.5,$paper[0]-30,4.5,padWidgetAttr($line),['size'=>8,'pad'=>0]];$draw[]=['line',20,$y+$row['height'],$paper[0]-10,$y+$row['height'],['stroke'=>'#cccccc','width'=>.2]];$y+=$row['height'];}return $draw;}]);
}
