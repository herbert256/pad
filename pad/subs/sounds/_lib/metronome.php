<?php
function padMetronomeRaw($bpm=120,$meter=4,$measures=1,$accent=1,$level=.4){
 foreach(['bpm'=>[$bpm,30,300],'meter'=>[$meter,1,12],'measures'=>[$measures,1,4],'accent'=>[$accent,0,1],'level'=>[$level,0,1]] as $k=>[$v,$min,$max])if(!is_numeric($v)||!is_finite((float)$v)||$v<$min||$v>$max)throw new InvalidArgumentException('Invalid metronome '.$k);
 if((int)$meter!=$meter||(int)$measures!=$measures)throw new InvalidArgumentException('Meter and measures must be integers');
 $duration=60/$bpm*$meter*$measures;if($duration>PAD_SOUND_LONGEST)throw new InvalidArgumentException('Metronome exceeds the four-second sound budget; increase BPM or reduce beats/measures');
 $voices=[];for($i=0;$i<$meter*$measures;$i++)$voices[]=['wave'=>'sine','note'=>$i%$meter===0?1600:1000,'start'=>$i*60/$bpm,'length'=>.04,'attack'=>.001,'release'=>.035,'gain'=>$i%$meter===0?$accent:.6];
 return ['title'=>'Metronome '.$bpm.' BPM, '.$meter.' beats, '.$measures.' measures','level'=>(float)$level,'voices'=>$voices,'duration'=>$duration];
}
function padMetronomeRecipe(...$args){$raw=padMetronomeRaw(...$args);$recipe=padSoundSettle('metronome',$raw);$recipe['length']=$raw['duration'];$recipe['level']=$raw['level'];return $recipe;}
function padSoundMetronomeTag(){
 $args=[];foreach(['bpm'=>120,'meter'=>4,'measures'=>1,'accent'=>1,'level'=>.4] as $k=>$v)$args[]=padTagParm($k,$v);
 try{$recipe=padMetronomeRecipe(...$args);}catch(InvalidArgumentException $e){if($GLOBALS['padCheckSyntax'])padError($e->getMessage());return '';}
 $key=substr(hash('sha256',serialize($args)),0,16);
 if(($_GET['padMetronome']??'')===$key&&($_SERVER['REQUEST_METHOD']??'GET')==='GET')return padSoundAnswer('metronome',FALSE,$recipe);
 $url=htmlspecialchars_decode(padSoundUrl('metronome'),ENT_QUOTES);$url=preg_replace('/([?&])padSound=metronome/','$1padMetronome='.$key,$url);padFragmentKeepNot();
 // Shared button, binding, event, volume and download semantics; the per-recipe key
 // prevents two differently configured metronomes from sharing the runtime buffer.
 $controls=padSoundTag('metronome',$recipe,padWidgetAttr($url),'metronome-'.$key);
 return $controls.'<figure class="pad-sound-metronome"><figcaption>'.padWidgetAttr($recipe['title']).'</figcaption><audio controls preload="none" aria-label="'.padWidgetAttr($recipe['title']).'" src="'.padWidgetAttr($url).'"></audio></figure>';
}
