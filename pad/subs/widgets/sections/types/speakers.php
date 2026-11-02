<?php
// Speaker profile plus session information, distinct from the general team section.
padSectionParts(['eyebrow','title','text']);$head=padSectionHead();$items=padSectionItems('name',['name','role','topic','time','text','image','link'],'text','speakers');$cols=padSectionInt('columns',3,1,6);$level=min(6,padSectionLevel()+1);$html='';
foreach($items as $item){$name=padSectionField($item,'name');$link=padSectionLink(padSectionRaw($item,'link'));$image=padSectionLink(padSectionRaw($item,'image'));if($link!=='')$name='<a href="'.$link.'">'.$name.'</a>';
 $html.='<li class="ps-speaker">'.($image!==''?'<img src="'.$image.'" alt="" width="96" height="96" loading="lazy">':'').'<h'.$level.'>'.$name.'</h'.$level.'><p>'.padSectionField($item,'role').'</p><p><strong>'.padSectionField($item,'topic').'</strong> '.padSectionField($item,'time').'</p>'.padSectionTextBlock(padSectionField($item,'text'),'ps-item-text').'</li>';}
return padSection('speakers',$head.'<ul class="ps-grid ps-cols-'.$cols.' ps-speakers">'.$html.'</ul>');
?>
