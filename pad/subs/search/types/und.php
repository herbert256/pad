<?php
return ['name'=>'Language neutral','stopwords'=>'','stem'=>fn($word)=>$word,'verbatim'=>TRUE,
 'tokenPattern'=>"/[\\p{L}\\p{N}\\p{M}]+(?:[._:'’-][\\p{L}\\p{N}\\p{M}]+)*/u"];
