<?php
function padTreeview($rows,$label='Navigation') {
 $nodes=[];$children=[];
 foreach(array_slice((array)$rows,0,2000) as $row){
  if(!is_array($row)||!isset($row['id']))continue;$id=(string)$row['id'];
  if(isset($nodes[$id]))throw new InvalidArgumentException('Duplicate tree node');
  $nodes[$id]=$row;
 }
 foreach($nodes as $id=>$row){$parent=(string)($row['parent']??'');if($parent!==''&&!isset($nodes[$parent]))$parent='';$children[$parent][]=$id;}
 $seen=[];
 $draw=function($parent,$depth)use(&$draw,&$seen,$nodes,$children){
  if($depth>32)throw new InvalidArgumentException('Tree is too deep');$html='<ul>';
  foreach($children[$parent]??[] as $id){
   if(isset($seen[$id]))throw new InvalidArgumentException('Tree cycle');$seen[$id]=true;$row=$nodes[$id];
   $href=padWidgetLink($row['href']??'#');$title=padWidgetAttr($row['label']??$id);
   $html.='<li><a href="'.$href.'" data-tree-id="'.padWidgetAttr($id).'">'.$title.'</a>';
   if(isset($children[$id]))$html.=$draw($id,$depth+1);
   $html.='</li>';
  }return $html.'</ul>';
 };
 $html=$draw('',0);
 if(count($seen)!==count($nodes))throw new InvalidArgumentException('Unreachable tree cycle');
 return padWidgetSheet(padWidgetFile('widgets/treeview.css','raw')).'<nav class="pad-treeview" aria-label="'.padWidgetAttr($label).'">'.$html.'</nav>'.padWidgetScript('treeview',padWidgetFile('widgets/treeview.js','raw'));
}
?>
