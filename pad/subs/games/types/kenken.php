<?php
$puzzle=padTagParm('puzzle',padKenkenDefault());
try{$answer=padKenkenSolve($puzzle);if($answer['status']!=='unique')throw new InvalidArgumentException('KenKen puzzle status: '.$answer['status']);return padGame('kenken','Arithmetic cages',$puzzle,'<p>Fill each row and column with 1 to the board size, once each. Every labelled cage must meet its target and operation.</p>');}catch(InvalidArgumentException $e){padError($e->getMessage());return '';}
