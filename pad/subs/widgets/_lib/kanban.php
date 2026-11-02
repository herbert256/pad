<?php
function padKanban($rows,$columns=['todo'=>'To do','doing'=>'In progress','done'=>'Done'],$label='Board'){
 if(!is_array($columns)||count($columns)<1||count($columns)>12)throw new InvalidArgumentException('Kanban needs 1 to 12 columns');
 $groups=array_fill_keys(array_keys($columns),[]);$seen=[];
 foreach(array_slice((array)$rows,0,1000) as $row){
  if(!is_array($row)||!isset($row['id']))continue;$id=(string)$row['id'];
  if(isset($seen[$id]))throw new InvalidArgumentException('Duplicate kanban card');$seen[$id]=true;
  $column=$row['status']??array_key_first($columns);
  if(!array_key_exists($column,$groups))throw new InvalidArgumentException('Unknown kanban column');$groups[$column][]=$row;
 }
 $html='<div class="pad-kanban" role="region" aria-label="'.padWidgetAttr($label).'"><div class="pk-columns">';
 foreach($columns as $key=>$title){
  $html.='<section data-column="'.padWidgetAttr($key).'"><h3>'.padWidgetAttr($title).'</h3><ul>';
  foreach($groups[$key] as $row){
   $html.='<li data-card="'.padWidgetAttr($row['id']).'"><strong>'.padWidgetAttr($row['title']??$row['id']).'</strong><p>'.padWidgetAttr($row['text']??'').'</p><label hidden>Move to <select aria-label="Move '.padWidgetAttr($row['title']??$row['id']).'">';
   foreach($columns as $dest=>$caption)$html.='<option value="'.padWidgetAttr($dest).'"'.($dest===$key?' selected':'').'>'.padWidgetAttr($caption).'</option>';
   $html.='</select></label></li>';
  }$html.='</ul></section>';
 }
 return padWidgetSheet(padWidgetFile('widgets/kanban.css','raw')).$html.'</div><p role="status"></p></div>'.padWidgetScript('kanban',padWidgetFile('widgets/kanban.js','raw'));
}
?>
