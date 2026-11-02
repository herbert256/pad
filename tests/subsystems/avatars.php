<?php
const PAD = __DIR__.'/../../pad/';
function padWidgetOnce($s){return false;}
function padUnprotectQuotes($s){return $s;}
require PAD.'subs/avatars/_lib/avatar.php';
function check($ok){if(!$ok)throw new Exception('Avatar assertion failed');}
foreach(['rings','mosaic','pixelbot'] as $kind){
 $a=padAvatarMake($kind,'secret@example.com',['label'=>'<Ada & Jo>']);
 check($a===padAvatarMake($kind,'secret@example.com',['label'=>'<Ada & Jo>']));
 check(!str_contains($a,'secret@') && str_contains($a,'&lt;Ada &amp; Jo&gt;'));
 $xml=new DOMDocument();check($xml->loadXML($a));
 check($a!==padAvatarMake($kind,'other',['label'=>'<Ada & Jo>']));
}
check(padAvatarMake('initials','Ada Lovelace',['size'=>48])===padAvatar('Ada Lovelace'));
check(padAvatarMake('identicon',' ADA ',['size'=>64])===padIdenticon(' ADA '));
try{padAvatarMake('rings','a',['palette'=>['url(x)','#ffffff']]);throw new Exception('Accepted unsafe color');}catch(InvalidArgumentException $e){}
echo "avatars: stable, private, escaped, compatible SVG passed\n";
