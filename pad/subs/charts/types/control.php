<?php
try{return padChartControl(padChartGather(),...padChartSize(600,320));}catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
