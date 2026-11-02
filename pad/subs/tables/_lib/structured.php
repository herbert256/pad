<?php
// Stable hierarchy: duplicate IDs, orphans, cycles and depths over 64 are explicit errors.
function padTableTree(array $rows,$id='id',$parent='parent',$sort='', $descending=FALSE){
 if(count($rows)>10000)throw new LengthException('Tree tables support at most 10000 rows');$nodes=[];$children=[];
 foreach($rows as $row){if(!isset($row[$id])||!is_scalar($row[$id])||(string)$row[$id]==='')throw new InvalidArgumentException('Tree row needs a stable ID');$key=(string)$row[$id];if(isset($nodes[$key]))throw new InvalidArgumentException('Duplicate tree ID');$nodes[$key]=$row;}
 foreach($nodes as $key=>$row){$p=$row[$parent]??'';if(!is_scalar($p)&&$p!==NULL)throw new InvalidArgumentException('Invalid tree parent');$p=(string)$p;if($p!==''&&!isset($nodes[$p]))throw new InvalidArgumentException('Orphan tree row');$children[$p][]=$key;}
 $state=[];$depths=[];$walk=function($key)use(&$walk,&$state,&$depths,$nodes,$parent){if(($state[$key]??0)===1)throw new InvalidArgumentException('Tree cycle');if(($state[$key]??0)===2)return $depths[$key];$state[$key]=1;$p=(string)($nodes[$key][$parent]??'');$depth=$p!==''?$walk($p)+1:0;if($depth>64)throw new LengthException('Tree exceeds 64 levels');$state[$key]=2;return $depths[$key]=$depth;};foreach($nodes as $key=>$row)$walk($key);
 if($sort!=='')foreach($children as &$siblings)usort($siblings,function($a,$b)use($nodes,$sort,$descending){$x=$nodes[$a][$sort]??'';$y=$nodes[$b][$sort]??'';$order=is_numeric($x)&&is_numeric($y)?$x<=>$y:strnatcasecmp((string)$x,(string)$y);return $descending?-$order:$order;});unset($siblings);
 $roots=[];$flatten=function($key,$depth,&$branch)use(&$flatten,$nodes,$children){$branch[]=['row'=>$nodes[$key],'depth'=>$depth];foreach($children[$key]??[] as $child)$flatten($child,$depth+1,$branch);};foreach($children['']??[] as $key){$branch=[];$flatten($key,0,$branch);$roots[]=$branch;}return $roots;
}
function padTableStructuredColumns($rows){
 $fields=padDatatableColumns($rows,padTagParm('columns'));$labels=padDatatableSplit(padTagParm('labels'));$formats=[];
 foreach(padDatatableSplit(padTagParm('format')) as $one){$at=strpos($one,':');if($at!==FALSE)$formats[trim(substr($one,0,$at))]=trim(substr($one,$at+1));}
 $columns=[];foreach($fields as $i=>$field)$columns[]=['field'=>$field,'label'=>$labels[$i]??padStrHeadline($field),'numeric'=>padDatatableNumeric($rows,$field),'decimals'=>padDatatableDecimals($rows,$field),'plain'=>padDatatableIdentifier($field),'format'=>$formats[$field]??'','total'=>FALSE];return $columns;
}
function padTableStructuredTag($kind){
 global $pad,$padData,$padContent,$padHandDone;
 $rows=array_values(array_map('padChartRow',(array)padChartRows($padContent,'table')));$padData[$pad]=padDefaultData();$padContent='';$padHandDone[$pad]=TRUE;
 $columns=padTableStructuredColumns($rows);$caption=(string)padTagParm('caption');$html=padDatatableStyle().'<div class="pad-datatable"><table><caption>'.padChartAttr($caption).'</caption>';
 if(!$rows)return $html.'<tbody><tr><td>No records.</td></tr></tbody></table></div>';
 if($kind==='comparison'){
  if(count($rows)>20)throw new LengthException('Comparison supports at most 20 records');$label=(string)padTagParm('recordlabel','name');$html.='<thead><tr><th scope="col">Field</th>';foreach($rows as $i=>$row)$html.='<th scope="col">'.padChartAttr((is_scalar($row[$label]??NULL)?(string)$row[$label]:'Record '.($i+1))).'</th>';$html.='</tr></thead><tbody>';
  foreach($columns as $column){$html.='<tr><th scope="row">'.padChartAttr($column['label']).'</th>';$first=$rows[0][$column['field']]??NULL;foreach($rows as $i=>$row){$value=$row[$column['field']]??NULL;$changed=$i>0&&$value!==$first;$text=padChartAttr(padDatatableCell($value,$column));$html.='<td>'.($changed?'<strong>'.$text.'</strong> <small>(different)</small>':$text).'</td>';}$html.='</tr>';}
 }else{
  $sort=(string)padTagParm('sort');if($sort!==''&&!in_array($sort,array_column($columns,'field'),TRUE))throw new InvalidArgumentException('Tree sort must name a visible column');
  $roots=padTableTree($rows,(string)padTagParm('id','id'),(string)padTagParm('parent','parent'),$sort,padTagParm('direction','asc')==='desc');
  $pageSize=padTagParm('rows',0);if(filter_var($pageSize,FILTER_VALIDATE_INT)===FALSE||$pageSize<0||$pageSize>10000)throw new InvalidArgumentException('Tree rows is a whole root count');
  $query=(string)padTagParm('query','tree_');$page=max(1,(int)($_GET[$query.'page']??1));$pages=$pageSize?max(1,(int)ceil(count($roots)/$pageSize)):1;$page=min($page,$pages);if($pageSize)$roots=array_slice($roots,($page-1)*$pageSize,$pageSize);
  $html.='<thead><tr>';foreach($columns as $column)$html.='<th scope="col">'.padChartAttr($column['label']).'</th>';$html.='</tr></thead><tbody>';
  foreach($roots as $branch)foreach($branch as $entry){$html.='<tr>';foreach($columns as $i=>$column){$text=padChartAttr(padDatatableCell($entry['row'][$column['field']]??NULL,$column));$html.=($i===0?'<th scope="row"><span aria-hidden="true">'.str_repeat('↳ ',$entry['depth']).'</span><small>Level '.($entry['depth']+1).': </small>'.$text.'</th>':'<td>'.$text.'</td>');}$html.='</tr>';}
 }
 $html.='</tbody></table>';
 if($kind==='tree'&&$pages>1){$html.='<nav aria-label="Tree root pages">';for($n=1;$n<=$pages;$n++)$html.='<a href="'.padChartAttr(padDatatableHref([$query.'page'=>$n])).'"'.($n===$page?' aria-current="page"':'').'>'.$n.'</a> ';$html.='</nav>';}
 return $html.'</div>';
}
?>
