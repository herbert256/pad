<?php
padSectionParts(['eyebrow','title','text']);
$items=padSectionItems ( 'role',['role','location','arrangement','employment','link','text','button'],'text','jobs');
$level=min(6,padSectionLevel()+1);$html='';
foreach($items as $item){
 $html.='<li class="ps-card"><h'.$level.' class="ps-item-title">'.padSectionField($item,'role').'</h'.$level.'><dl>';
 foreach(['location'=>'Location','arrangement'=>'Working arrangement','employment'=>'Employment'] as $key=>$label)if(padSectionHas($item,$key))$html.='<dt>'.$label.'</dt><dd>'.padSectionField($item,$key).'</dd>';
 $html.='</dl>'.padSectionTextBlock(padSectionField($item,'text'),'ps-item-text');
 $href=padSectionLink(padSectionRaw($item,'link'));
 if($href!=='')$html.='<a class="ps-button" href="'.$href.'" aria-label="Apply for '.padSectionPlain($item,'role').'">'.(padSectionHas($item,'button')?padSectionField($item,'button'):'Apply').'</a>';
 $html.='</li>';
}
return padSection('careers',padSectionHead().'<ul class="ps-careers">'.$html.'</ul>');
?>
