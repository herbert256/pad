<?php
// Exercise the real tag parser so unconsumed common options fail here as in sounds/index.
$root=dirname(__DIR__,2);
function renderMetronome($source,$query='') {
 global $root;
 $command=[PHP_BINARY,$root.'/editors/render.php','tags','metronome-options-check','--virtual','--source=-'];
 if($query!=='')$command[]='--query='.$query;
 $p=proc_open($command,[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes,$root);
 fwrite($pipes[0],$source);fclose($pipes[0]);$out=stream_get_contents($pipes[1]);fclose($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[2]);$code=proc_close($p);
 if($code!==0)throw new RuntimeException($out.$err);return $out;
}
function verifyOption($ok,$message){if(!$ok)throw new RuntimeException($message);}
$source="{sound:metronome bpm=120, meter=4, measures=2, download='Save beat', button='Practice', volume=25, on='pointerdown'/}\n<button id='hit'>Hit</button>{sound:metronome bpm=180, meter=4, target='#hit', on='click', button='Go', download/}";
$html=renderMetronome($source);$doc=new DOMDocument();@$doc->loadHTML($html);$xp=new DOMXPath($doc);
$buttons=$xp->query('//button[@data-pad-sound]');verifyOption($buttons->length===2,'both configured buttons');
$first=$buttons->item(0);$second=$buttons->item(1);verifyOption($first->getAttribute('data-pad-sound')!==$second->getAttribute('data-pad-sound'),'isolated recipe identities');
verifyOption(str_contains($first->textContent,'Practice')&&$first->getAttribute('data-pad-sound-volume')==='0.25'&&$first->getAttribute('data-pad-sound-on')==='pointerdown','shared button/event/volume semantics');
$binding=$xp->query('//span[@data-pad-sound-bind]')->item(0);verifyOption($binding->getAttribute('data-pad-sound-target')==='#hit'&&$binding->getAttribute('data-pad-sound-bind')===$second->getAttribute('data-pad-sound'),'target binding uses second recipe');
$downloads=$xp->query('//a[@download="metronome.wav"]');verifyOption($downloads->length===2&&$downloads->item(0)->textContent==='Save beat','shared download label');
$audios=$xp->query('//audio');verifyOption($audios->length===2,'native no-script controls');foreach($audios as $i=>$audio){verifyOption($audio->hasAttribute('controls')&&!$audio->hasAttribute('autoplay'),'manual playback only');verifyOption($audio->getAttribute('src')===$downloads->item($i)->getAttribute('href'),'native playback/download configuration parity');}
verifyOption($xp->query('//noscript/a[contains(@href,"padMetronome=")]')->length===2,'no-script fallback links');
parse_str(parse_url($audios->item(0)->getAttribute('src'),PHP_URL_QUERY),$query);$key=$query['padMetronome']??'';verifyOption(strlen($key)===16,'configuration URL');
$wav=renderMetronome($source,'padMetronome='.$key);verifyOption(substr($wav,0,4)==='RIFF'&&substr($wav,8,4)==='WAVE'&&strlen($wav)===44+4*22050*2,'configured four-second WAV');
echo "metronome common options, distinct recipes, native/no-script controls and configured WAV passed\n";
