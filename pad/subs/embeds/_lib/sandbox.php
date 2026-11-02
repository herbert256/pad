<?php
function padEmbedSandbox($html,$title,$opts=[]){
 $height=$opts['height']??240;$scripts=$opts['scripts']??FALSE;
 if(!is_string($html)||strlen($html)>262144||!is_scalar($height)||filter_var($height,FILTER_VALIDATE_INT)===FALSE||$height<80||$height>2000||!in_array($scripts,[TRUE,FALSE,0,1,'0','1'],TRUE))throw new InvalidArgumentException('sandbox HTML is at most 256 KiB, height 80..2000 and scripts boolean');
 $esc=fn($s)=>htmlspecialchars((string)$s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
 $csp="default-src 'none'; img-src data:; style-src 'unsafe-inline'; script-src ".($scripts?"'unsafe-inline'":"'none'")."; base-uri 'none'; form-action 'none'";
 $source='<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="Content-Security-Policy" content="'.$esc($csp).'"></head><body>'.$html.'</body></html>';
 return '<figure class="pad-embed-sandbox"><iframe title="'.$esc($title).'" height="'.(int)$height.'" width="100%" sandbox="'.($scripts?'allow-scripts':'').'" referrerpolicy="no-referrer" srcdoc="'.$esc($source).'"></iframe><figcaption>'.$esc($title).'</figcaption><details><summary>HTML source</summary><pre>'.$esc($html).'</pre></details></figure>';
}
