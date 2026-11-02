<?php
// Empty-state message with an optional application-owned action link.
$title=padWidgetText('title',is_scalar($padParm)?(string)$padParm:'Nothing here yet');$text=padWidgetText('text');
$link=padWidgetLink(padWidgetText('link'));$action=padWidgetText('action','Continue');
$e=fn($v)=>htmlspecialchars($v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
return padWidgetSheet(padWidgetFile('widgets/empty.css')).'<section class="pad-empty" aria-label="'.$e($title).'"><h2>'.$e($title).'</h2>'.($text!==''?'<p>'.$e($text).'</p>':'').($link!==''?'<a href="'.$link.'">'.$e($action).'</a>':'').'</section>';
?>
