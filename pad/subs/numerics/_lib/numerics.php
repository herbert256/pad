<?php
class PadNumericLimit extends RuntimeException {}
function padNumericTick(&$state,$amount=1){if($state['work']+$amount>$state['maxWork'])throw new PadNumericLimit('work limit');$state['work']+=$amount;}
function padNumericFinite($value,$name='value'){if((!is_int($value)&&!is_float($value))||!is_finite((float)$value))throw new InvalidArgumentException($name.' must be a finite native number');return (float)$value;}
function padNumericSamples($input,&$state){
 if(!is_array($input)||!array_is_list($input)||count($input)<2||count($input)>10000)throw new InvalidArgumentException('samples must be a list of 2..10000 [x,y] pairs');$rows=[];$last=NULL;foreach($input as $row){padNumericTick($state);if(!is_array($row)||!array_is_list($row)||count($row)!==2)throw new InvalidArgumentException('each sample must be [x,y]');$x=padNumericFinite($row[0],'sample x');$y=padNumericFinite($row[1],'sample y');if($last!==NULL&&$x<=$last)throw new InvalidArgumentException('sample x values must be strictly increasing');$rows[]=[$x,$y];$last=$x;}return $rows;
}
function padNumericTolerance($options){$t=padNumericFinite($options['tolerance']??1e-12,'tolerance');if($t<=0||$t>1)throw new InvalidArgumentException('tolerance must be in (0,1]');return $t;}
