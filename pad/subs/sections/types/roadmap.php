<?php

  // {section:roadmap data='items'/} - Roadmap entries grouped as planned, in progress and released.
  //

padSectionParts(['eyebrow','title','text']);
$items=padSectionItems ( 'title',['title','status','date','text','link'],'text','roadmap items');
$groups=['planned'=>[],'in-progress'=>[],'released'=>[]];
foreach($items as $item){$status=(string)padSectionRaw($item,'status','planned');if(!isset($groups[$status])){padError('roadmap status must be planned, in-progress or released');continue;}$groups[$status][]=$item;}
$level=min(6,padSectionLevel()+1);$child=min(6,$level+1);$html='';
foreach($groups as $status=>$rows){
 $title=['planned'=>'Planned','in-progress'=>'In progress','released'=>'Released'][$status];
 $html.='<section><h'.$level.'>'.$title.'</h'.$level.'><ol class="ps-steps">';
 foreach($rows as $item){$title=padSectionField($item,'title');$href=padSectionLink(padSectionRaw($item,'link'));$date=padSectionPlain($item,'date');
  $html.='<li class="ps-step"><h'.$child.' class="ps-item-title">'.($href!==''?'<a href="'.$href.'">'.$title.'</a>':$title).'</h'.$child.'>';
  if($date!=='')$html.='<p class="ps-note">'.$date.'</p>';
  $html.=padSectionTextBlock(padSectionField($item,'text'),'ps-item-text').'</li>';
 }
 if(!$rows)$html.='<li>No items</li>';
 $html.='</ol></section>';
}
return padSection('roadmap',padSectionHead().'<div class="ps-roadmap">'.$html.'</div>');
?>
