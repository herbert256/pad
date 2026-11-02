<?php
$result = padTimeSeries('resample',[['time'=>0,'value'=>2],['time'=>1,'value'=>4],['time'=>4,'value'=>8]],['stepSeconds'=>2]);
$answer = padJson($result);
?>
