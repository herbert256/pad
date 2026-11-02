<?php
// Adapted from Apache Lucene CzechStemmer, Apache-2.0; see CZECH-LICENSE.txt.
// Light stemming algorithm of Dolamic and Savoy, "Indexing and stemming approaches
// for the Czech language" (2009). Input is lowercase, retaining Czech diacritics.
function padSearchCzechStem($word){
 $n=mb_strlen($word);
 foreach([[7,5,['atech']],[6,4,['ětem','etem','atům']],[5,3,['ech','ich','ích','ého','ěmi','emi','ému','ěte','ete','ěti','eti','ího','iho','ími','ímu','imu','ách','ata','aty','ých','ama','ami','ové','ovi','ými']],[4,2,['em','es','ém','ím','ům','at','ám','os','us','ým','mi','ou']],[3,1,['a','e','i','o','u','ů','y','á','é','í','ý','ě']]] as [$min,$cut,$endings]){
  if($n<=$min)continue;
  foreach($endings as $end)if(str_ends_with($word,$end)){$word=mb_substr($word,0,$n-$cut);break 2;}
 }
 $n=mb_strlen($word);
 if($n>5&&(str_ends_with($word,'ov')||str_ends_with($word,'in')||str_ends_with($word,'ův')))$word=mb_substr($word,0,$n-2);
 $n=mb_strlen($word);if(!$n)return $word;
 if(str_ends_with($word,'čt'))return mb_substr($word,0,-2).'ck';
 if(str_ends_with($word,'št'))return mb_substr($word,0,-2).'sk';
 $last=mb_substr($word,-1);
 if($last==='c'||$last==='č')return mb_substr($word,0,-1).'k';
 if($last==='z'||$last==='ž')return mb_substr($word,0,-1).'h';
 if($n>1&&mb_substr($word,-2,1)==='e')return mb_substr($word,0,-2).$last;
 if($n>2&&mb_substr($word,-2,1)==='ů')return mb_substr($word,0,-2).'o'.$last;
 return $word;
}
