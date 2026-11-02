<?php
function padSectionContents(array $items){
 if(count($items)>1000)throw new LengthException('contents supports at most 1000 entries');
 $html='';$group=NULL;
 foreach($items as $item){
  if(!is_array($item)||!is_scalar($item['name']??NULL)||trim((string)$item['name'])==='')throw new InvalidArgumentException('contents entries require a name');
  $name=padSectionEscape((string)$item['name']);$text=padSectionEscape(is_scalar($item['text']??NULL)?(string)$item['text']:'');$link=padSectionLink($item['link']??'');
  $html.='<li>'.($link!==''?'<a href="'.$link.'">'.$name.'</a>':$name).($text!==''?'<p>'.$text.'</p>':'').'</li>';
 }
 return $html!==''?'<nav aria-label="Contents"><ol>'.$html.'</ol></nav>':'<p>No chapters.</p>';
}
