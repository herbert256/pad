<?php
// Pagination for writing forms. The template's text becomes one logical row;
// <aside> is packing quantity. Text is wrapped before page division, never clipped.
function padRegisterPlain($text){return trim(preg_replace('/\s+/u',' ',html_entity_decode(strip_tags($text),ENT_QUOTES|ENT_HTML5,'UTF-8')));}
function padRegisterWrap($text,$columns){
 $words=preg_split('/\s+/u',$text);$lines=[];$line='';
 foreach($words as $word){while(mb_strwidth($word)>$columns){if($line!==''){$lines[]=$line;$line='';}$piece=mb_strimwidth($word,0,$columns,'','UTF-8');$lines[]=$piece;$word=mb_substr($word,mb_strlen($piece));}if($word==='')continue;if(mb_strwidth($line.($line!==''?' ':'').$word)>$columns){$lines[]=$line;$line=$word;}else $line.=($line!==''?' ':'').$word;}
 if($line!==''||!$lines)$lines[]=$line;return $lines;
}
function padRegisterPages($items,$kind,$paper,$nameWidth){
 $pages=[];$page=[];$used=0;$available=$paper[1]-55;$columns=max(8,(int)floor(($nameWidth-4)/2.15));
 foreach($items as $i=>$html){$parts=padPrintableParts($html,['aside']);$name=padRegisterPlain($parts['']);$qty=padRegisterPlain($parts['aside']);if(mb_strlen($name)>2000||mb_strlen($qty)>24)throw new InvalidArgumentException('register row '.($i+1).' exceeds text limits');
  $lines=padRegisterWrap($name,$columns);$quantityLines=padRegisterWrap($qty,13);$height=max(10,max(count($lines),count($quantityLines))*4.5+4);if($height>$available)throw new InvalidArgumentException('register row '.($i+1).' is too tall for one page');
  if($used+$height>$available&&$page){$pages[]=$page;$page=[];$used=0;}$page[]=['lines'=>$lines,'quantity'=>$quantityLines,'height'=>$height];$used+=$height;
 }
 if($page||!$pages)$pages[]=$page;return $pages;
}
function padRegisterDates($text){
 $dates=array_map('trim',explode(',',$text));if(count($dates)>7||!$dates)return 'one to seven dates are required';$seen=[];
 foreach($dates as $date){$dt=DateTimeImmutable::createFromFormat('!Y-m-d',$date,new DateTimeZone('UTC'));if(!$dt||$dt->format('Y-m-d')!==$date)return 'dates must be YYYY-MM-DD';if(isset($seen[$date]))return 'dates must be unique';$seen[$date]=TRUE;}return $dates;
}
function padRegisterPlan($kind){
 [$name,$paper]=padPrintablePaper(['a4'=>PAD_PRINTABLE_PAPERS['a4'],'letter'=>PAD_PRINTABLE_PAPERS['letter']],'a4');$title=padPrintableText('title',$kind==='packinglist'?'Packing list':'Attendance');if(mb_strlen($title)>55){padError('register title must have at most 55 characters');$title=mb_substr($title,0,55);}
 $dates=[];if($kind==='attendance'){$dates=padRegisterDates(padPrintableText('dates',gmdate('Y-m-d')));if(is_string($dates)){padError($dates);return [];}}
 $w=$paper[0];$nameWidth=$kind==='packinglist'?$w-85:($w-20)*.43;$pageCount=0;
 return padPrintablePlan(['name'=>['page','pages'],'paper'=>$paper,'paperName'=>$name,'font'=>'sans','documentCopies'=>TRUE,
 'prepare'=>function($items)use($kind,$paper,$nameWidth,&$pageCount){try{$pages=padRegisterPages($items,$kind,$paper,$nameWidth);}catch(InvalidArgumentException $error){padError($error->getMessage());return [];}$pageCount=count($pages);return array_map(fn($rows)=>json_encode($rows,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$pages);},
 'item'=>function($cell,$parts,$n)use($kind,$paper,$title,$dates,$nameWidth,&$pageCount){if(!$parts)return [];$rows=json_decode($parts[''],TRUE);if(!is_array($rows))return [];[$w,$h]=$paper;
  $draw=[['box',10,10,$w-20,10,padWidgetAttr($title),['size'=>min(16,($w-20)/max(1,mb_strwidth($title))/.352778),'pad'=>0]],['box',10,$h-15,$w-20,6,'Page '.($n%max(1,$pageCount)+1).' of '.$pageCount,['size'=>8,'pad'=>0,'align'=>'right']]];
  $columns=$kind==='packinglist'?[['Item',$nameWidth],['Quantity',35],['Packed',30]]:array_merge([['Name',$nameWidth]],array_map(fn($date)=>[substr($date,5),($w-20-$nameWidth)/count($dates)],$dates));
  $x=10;$y=27;foreach($columns as [$label,$cw]){$draw[]=['rect',$x,$y,$cw,9,['stroke'=>'#555555','width'=>.3,'fill'=>'#eeeeee']];$draw[]=['box',$x,$y,$cw,9,padWidgetAttr($label),['size'=>8,'pad'=>1,'align'=>$x===10?'left':'center']];$x+=$cw;}$y+=9;
  foreach($rows as $row){$rh=$row['height'];$x=10;foreach($columns as $index=>[$label,$cw]){$draw[]=['rect',$x,$y,$cw,$rh,['stroke'=>'#aaaaaa','width'=>.2]];
   if($index===0){foreach($row['lines'] as $j=>$line)$draw[]=['box',$x+2,$y+2+$j*4.5,$cw-4,4.5,padWidgetAttr($line),['size'=>8,'pad'=>0,'font'=>'sans']];}
   elseif($kind==='packinglist'&&$index===1){foreach($row['quantity'] as $j=>$line)$draw[]=['box',$x+2,$y+2+$j*4.5,$cw-4,4.5,padWidgetAttr($line),['size'=>8,'pad'=>0,'align'=>'center']];}
   elseif($kind==='packinglist')$draw[]=['rect',$x+($cw-4)/2,$y+($rh-4)/2,4,4,['stroke'=>'#555555','width'=>.3]];$x+=$cw;
  }$y+=$rh;}
  if($kind==='attendance')$draw[]=['box',10,20,$w-20,6,padWidgetAttr(implode(' · ',$dates)),['size'=>7,'pad'=>0]];
  return $draw;
 }]);
}
