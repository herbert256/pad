<?php
function padTableMasterDetail($rows,$columns,$opts=[]){
 if(!is_array($rows)||count($rows)>10000||!is_array($columns)||!$columns||count($columns)>50)throw new InvalidArgumentException('masterdetail supports 10000 rows and 1..50 columns');$esc=fn($s)=>htmlspecialchars((string)$s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');$id=$opts['id']??'id';$seen=[];
 foreach($rows as $r){if(!is_array($r)||!is_scalar($r[$id]??NULL)||(string)$r[$id]===''||isset($seen[(string)$r[$id]]))throw new InvalidArgumentException('masterdetail requires unique stable IDs');$seen[(string)$r[$id]]=TRUE;}
 $filter=(string)($opts['filter']??'');if($filter!=='')$rows=array_values(array_filter($rows,fn($r)=>array_filter($columns,fn($c)=>is_scalar($r[$c]??NULL)&&mb_stripos((string)$r[$c],$filter)!==FALSE)));
 $sort=$opts['sort']??'';if($sort!==''&&!in_array($sort,$columns,TRUE))throw new InvalidArgumentException('masterdetail sort must name a visible column');if($sort!=='')usort($rows,fn($a,$b)=>((is_numeric($a[$sort]??NULL)&&is_numeric($b[$sort]??NULL))?($a[$sort]<=>$b[$sort]):strnatcasecmp((string)($a[$sort]??''),(string)($b[$sort]??'')))*(!empty($opts['descending'])?-1:1));
 $size=$opts['pagesize']??25;$page=$opts['page']??1;if(filter_var($size,FILTER_VALIDATE_INT)===FALSE||$size<1||$size>1000||filter_var($page,FILTER_VALIDATE_INT)===FALSE||$page<1)throw new InvalidArgumentException('masterdetail pagesize 1..1000 and page >=1');$total=count($rows);$pages=max(1,(int)ceil($total/$size));$page=min($page,$pages);$rows=array_slice($rows,($page-1)*$size,$size);
 $html='<table class="pad-masterdetail"><caption>'.$esc($opts['caption']??'Records').'</caption><thead><tr>';foreach($columns as $c)$html.='<th scope="col">'.$esc($c).'</th>';$html.='<th scope="col">Details</th></tr></thead><tbody>';
 foreach($rows as $r){$html.='<tr data-row-id="'.$esc($r[$id]).'">';foreach($columns as $c)$html.='<td>'.$esc(is_scalar($r[$c]??NULL)?$r[$c]:'').'</td>';
 $template=$opts['template']??NULL;if($template!==NULL){if(!is_string($template)||strlen($template)>10000)throw new InvalidArgumentException('masterdetail template limit');$detail=preg_replace_callback('/\[\[([A-Za-z_][A-Za-z0-9_]*)\]\]/',fn($m)=>$esc(is_scalar($r[$m[1]]??NULL)?$r[$m[1]]:''),$template);}else{$v=$r[$opts['detail']??'detail']??'';$detail=$esc(is_scalar($v)?$v:'');}
 $html.='<td><details><summary>Details for '.$esc($r[$id]).'</summary>'.$detail.'</details></td></tr>';}
 return $html.(!$rows?'<tr><td colspan="'.(count($columns)+1).'">No records.</td></tr>':'').'</tbody></table><p>Page '.$page.' of '.$pages.'; '.$total.' records.</p>';
}
