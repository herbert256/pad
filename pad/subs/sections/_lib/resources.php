<?php
function padSectionResources(array $items){
 if(count($items)>1000)throw new LengthException('resources supports at most 1000 entries');
 $html='';$group=NULL;
 foreach($items as $item){
  if(!is_array($item)||!is_scalar($item['name']??NULL)||trim((string)$item['name'])==='')throw new InvalidArgumentException('resources entries require a name');
  $name=padSectionEscape((string)$item['name']);$text=padSectionEscape(is_scalar($item['text']??NULL)?(string)$item['text']:'');$link=padSectionLink($item['link']??'');
  $next=is_scalar($item['category']??NULL)?(string)$item['category']:'Resources';if($next!==$group){if($group!==NULL)$html.='</ul></section>';$html.='<section><h3>'.padSectionEscape($next).'</h3><ul>';$group=$next;}
  $html.='<li>'.($link!==''?'<a href="'.$link.'">'.$name.'</a>':$name).($text!==''?'<p>'.$text.'</p>':'').'</li>';
 }
 return $html!==''?$html.'</ul></section>':'<p>No resources.</p>';
}
