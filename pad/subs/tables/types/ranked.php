<?php

  // ranked - Ranks finite score values with `method=dense|competition|ordinal`, descending by default. Equal scores use stable ID lexical order. Optional previous rank yields signed change (positive means up), absent means New. `filter=` on label field runs before ranking; pagination runs after ranking. Defaults id=id,label=name,score=score,previous=previous. Bounds 10000 rows and pagesize <=1000.
  //
  // ## Example
  //
  // The paired PHP page supplies the application data:
  //
  // ```php
  // <?php
  // $rankingRecords=[['id'=>'a','name'=>'Ada','score'=>90,'previous'=>3],['id'=>'b','name'=>'Bo','score'=>90,'previous'=>1],['id'=>'c','name'=>'Cy','score'=>75]];
  // ```
  //
  // The PAD template renders it:
  //
  // ```pad
  // {table:ranked data='rankingRecords', method='competition'/}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/table-ranked.php`.

include_once PAD.'subs/tables/_lib/ranked.php';
try{$rows=array_values(array_map('padChartRow',(array)padChartRows($padContent,'table')));$padContent='';$padData[$pad]=padDefaultData();$name=(string)padTagParm('label','name');$filter=(string)padTagParm('filter','');if($filter!=='')$rows=array_values(array_filter($rows,fn($r)=>is_scalar($r[$name]??NULL)&&mb_stripos((string)$r[$name],$filter)!==FALSE));$score=(string)padTagParm('score','score');$rows=padTableRanked($rows,$score,(string)padTagParm('method','competition'),(bool)padTagParm('descending',TRUE),(string)padTagParm('id','id'),(string)padTagParm('previous','previous'));$size=padTagParm('pagesize',25);$page=padTagParm('page',1);if(filter_var($size,FILTER_VALIDATE_INT)===FALSE||$size<1||$size>1000||filter_var($page,FILTER_VALIDATE_INT)===FALSE||$page<1)throw new InvalidArgumentException('invalid ranking page');$pages=max(1,(int)ceil(count($rows)/$size));$page=min($page,$pages);$rows=array_slice($rows,($page-1)*$size,$size);$h='<table class="pad-ranked"><caption>'.padChartAttr((string)padTagParm('caption','Ranking')).'</caption><thead><tr><th>Rank</th><th>Name</th><th>Score</th><th>Change</th></tr></thead><tbody>';foreach($rows as $r)$h.='<tr><td>'.$r['_rank'].'</td><th scope="row">'.padChartAttr(is_scalar($r[$name]??NULL)?$r[$name]:'').'</th><td>'.padChartAttr($r[$score]).'</td><td>'.($r['_change']===NULL?'New':($r['_change']>0?'+':'').$r['_change']).'</td></tr>';return $h.'</tbody></table><p>Page '.$page.' of '.$pages.'</p>';}catch(Exception $e){padError($e->getMessage());return '';}
