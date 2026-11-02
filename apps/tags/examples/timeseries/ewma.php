<?php
$result = padTimeSeries('ewma',[['time'=>0,'value'=>2],['time'=>1,'value'=>4],['time'=>4,'value'=>8]],['alpha'=>0.5]);
$answer = padJson($result);
?>
