<?php
$result = padTimeSeries('difference',[['time'=>0,'value'=>2],['time'=>1,'value'=>4],['time'=>4,'value'=>8]],['rate'=>TRUE]);
$answer = padJson($result);
?>
