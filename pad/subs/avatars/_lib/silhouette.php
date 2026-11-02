<?php
function padAvatarSilhouette($variant='person',$size=64,$label='') {
 if(!in_array($variant,['person','organisation'],TRUE))throw new InvalidArgumentException('Silhouette variant must be person or organisation');
 if(!is_numeric($size)||!is_finite((float)$size))throw new InvalidArgumentException('Silhouette size must be finite');
 $size=max(8,min(2048,(int)$size));$label=htmlspecialchars($label?:ucfirst($variant).' placeholder',ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
 $body=$variant==='person'?'<circle cx="50" cy="34" r="16"/><path d="M20 84v-9c0-18 13-26 30-26s30 8 30 26v9Z"/>':'<path d="M23 84V29l27-13 27 13v55H23Zm10-10h8V63h-8Zm0-20h8V43h-8Zm26 20h8V63h-8Zm0-20h8V43h-8Z" fill-rule="evenodd"/>';
 return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-avatar pad-avatar-silhouette" width="'.$size.'" height="'.$size.'" viewBox="0 0 100 100" role="img" aria-label="'.$label.'"><title>'.$label.'</title><rect width="100" height="100" rx="20" fill="currentColor" fill-opacity=".12"/><g fill="currentColor">'.$body.'</g></svg>';
}
