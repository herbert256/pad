<?php
// Caller supplied service states; no polling, monitoring or incident inference.
padSectionParts(['eyebrow','title','text']);$head=padSectionHead();$items=padSectionItems('name',['name','status','text','link'],'text','services');$html='';
foreach($items as $item){$state=padSectionRaw($item,'status');if(!in_array($state,['operational','degraded','outage','maintenance','unknown'],TRUE))$state='unknown';$name=padSectionField($item,'name');$link=padSectionLink(padSectionRaw($item,'link'));if($link!=='')$name='<a href="'.$link.'">'.$name.'</a>';
 $html.='<li class="ps-status-item"><strong>'.$name.'</strong> <span class="ps-status-'.$state.'">'.ucfirst($state).'</span>'.padSectionTextBlock(padSectionField($item,'text'),'ps-item-text').'</li>';}
return padSection('status',$head.'<ul class="ps-status">'.$html.'</ul>');
?>
