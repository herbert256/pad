<?php

  // {section:casestudy data='studies'/} - Case studies describing a problem, approach, result and supporting metrics.
  //

padSectionParts(['eyebrow','title','text']);
$items=padSectionItems ( 'title',['title','problem','approach','result','metrics','image','alt','attribution'],'result','case studies');
$level=min(6,padSectionLevel()+1);$html='';
foreach($items as $item){
 $html.='<article class="ps-card ps-case"><h'.$level.' class="ps-item-title">'.padSectionField($item,'title').'</h'.$level.'>';
 $src=padSectionLink(padSectionRaw($item,'image'));
 if($src!=='')$html.='<img loading="lazy" src="'.$src.'" alt="'.padSectionPlain($item,'alt').'">';
 $html.='<dl>';
 foreach(['problem'=>'Problem','approach'=>'Approach','result'=>'Result'] as $key=>$label)if(padSectionHas($item,$key))$html.='<dt>'.$label.'</dt><dd>'.padSectionField($item,$key).'</dd>';
 $html.='</dl>';
 if(padSectionHas($item,'metrics'))$html.='<p class="ps-case-metrics">'.padSectionField($item,'metrics').'</p>';
 if(padSectionHas($item,'attribution'))$html.='<footer>'.padSectionField($item,'attribution').'</footer>';
 $html.='</article>';
}
return padSection('casestudy',padSectionHead().'<div class="ps-case-list">'.$html.'</div>');
?>
