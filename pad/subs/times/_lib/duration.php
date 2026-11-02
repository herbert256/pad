<?php
function padTimeDuration($seconds,$units=4,$rounding='nearest') {
 if(!is_numeric($seconds)||!is_finite((float)$seconds)||abs((float)$seconds)>1000000000000||!is_int($units)||$units<1||$units>4||!in_array($rounding,['nearest','floor','ceil','truncate'],TRUE))throw new InvalidArgumentException('duration expects finite seconds within +/- 1e12, units 1..4 and nearest/floor/ceil/truncate');
 $seconds=(int)match($rounding){'floor'=>floor((float)$seconds),'ceil'=>ceil((float)$seconds),'truncate'=>(float)$seconds,default=>round((float)$seconds,0,PHP_ROUND_HALF_UP)};
 $text=padCountdownText(abs($seconds),$units,'0 seconds'); if($seconds<0)$text='−'.$text;
 return $seconds<0?'<span class="pad-time-duration" data-seconds="'.$seconds.'">'.padChartAttr($text).'</span>':'<time class="pad-time-duration" datetime="PT'.$seconds.'S">'.padChartAttr($text).'</time>';
}
