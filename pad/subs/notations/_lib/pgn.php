<?php
// A single PGN mainline: structural SAN only. Moves are not executed or legalized.
include_once PAD.'subs/notations/_lib/chess.php';
function padPgnParse($text){
 if(strlen($text)>100000||!mb_check_encoding($text,'UTF-8'))return 'expected UTF-8 PGN of at most 100000 bytes';
 $text=trim($text);$tags=[];
 while(str_starts_with($text,'[')){
  if(!preg_match('/^\[([A-Za-z][A-Za-z0-9_]{0,31})\s+"((?:[^"\\\\\r\n]|\\\\["\\\\]){0,500})"\]\s*/u',$text,$m))return 'malformed PGN tag pair';
  if(isset($tags[$m[1]]))return 'duplicate tag '.$m[1];if(count($tags)>=64)return 'at most 64 tags';$tags[$m[1]]=str_replace(['\\"','\\\\'],['"','\\'],$m[2]);$text=substr($text,strlen($m[0]));
 }
 if(isset($tags['Variant'])&&!in_array($tags['Variant'],['Standard','Chess'],TRUE))return 'only standard chess is supported';
 if(isset($tags['SetUp'])&&!in_array($tags['SetUp'],['0','1'],TRUE))return 'SetUp must be 0 or 1';
 if(isset($tags['FEN'])&&($tags['SetUp']??'')!=='1')return 'FEN requires SetUp "1"';if(($tags['SetUp']??'')==='1'&&!isset($tags['FEN']))return 'SetUp "1" requires FEN';
 $fen=$tags['FEN']??'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';$position=padChessFen($fen);if(is_string($position))return 'invalid initial FEN: '.$position;
 $tokens=[];$moves=0;$result=NULL;
 while(($text=ltrim($text))!==''){
  if($text[0]==='{'){$end=strpos($text,'}');if($end===FALSE)return 'unclosed comment';$comment=substr($text,1,$end-1);if(str_contains($comment,'{')||strlen($comment)>2000)return 'nested or overlong comment';$tokens[]=['comment',$comment];$text=substr($text,$end+1);continue;}
  if($text[0]===';'){$end=strpos($text,"\n");$comment=substr($text,1,$end===FALSE?NULL:$end-1);if(strlen($comment)>2000)return 'overlong comment';$tokens[]=['comment',$comment];$text=$end===FALSE?'':substr($text,$end+1);continue;}
  if($text[0]==='('||$text[0]===')')return 'variations are unsupported; supply a single mainline';
  if(preg_match('/^(1-0|0-1|1\/2-1\/2|\*)(?=\s|$|\{)/',$text,$m)){if($result!==NULL)return 'multiple game results';$result=$m[1];$tokens[]=['result',$result];$text=substr($text,strlen($m[0]));continue;}
  if($result!==NULL)return 'moves after result are not supported';
  if(preg_match('/^([1-9][0-9]{0,5})\.(?:\.\.)?/',$text,$m)){$tokens[]=['number',$m[0]];$text=substr($text,strlen($m[0]));continue;}
  if(preg_match('/^\$([0-9]{1,3})(?=\s|$|\{)/',$text,$m)){if((int)$m[1]>255||!$moves)return 'NAG must follow a move and be 0..255';$tokens[]=['nag',$m[0]];$text=substr($text,strlen($m[0]));continue;}
  // SAN shape: castling, piece moves with optional disambiguation, pawn moves and captures.
  if(!preg_match('/^(?:O-O(?:-O)?|[KQRBN](?:[a-h][1-8]|[a-h]|[1-8])?x?[a-h][1-8]|[a-h](?:x[a-h])?[1-8](?:=[QRBN])?)[+#]?[!?]{0,2}(?=\s|$|\{|\$)/',$text,$m))return 'unsupported or malformed SAN token near '.mb_substr($text,0,24);
  if(++$moves>2000)return 'at most 2000 plies';$tokens[]=['move',$m[0]];$text=substr($text,strlen($m[0]));
 }
 if($result===NULL)return 'a terminating result (1-0, 0-1, 1/2-1/2 or *) is required';if(isset($tags['Result'])&&$tags['Result']!==$result)return 'Result tag and movetext disagree';
 return ['tags'=>$tags,'tokens'=>$tokens,'position'=>$position,'plies'=>$moves,'result'=>$result];
}
function padPgnHtml($game,$board=FALSE,$flip=FALSE,$size=360){
 $html='<section class="pad-pgn" aria-label="Annotated chess game"><dl>';foreach($game['tags'] as $key=>$value)$html.='<dt>'.padNotationAttr($key).'</dt><dd>'.padNotationAttr($value).'</dd>';$html.='</dl><p class="pad-pgn-moves">';
 foreach($game['tokens'] as [$kind,$value])$html.='<span class="pad-pgn-'. $kind.'">'.padNotationAttr($kind==='comment'?'{'.$value.'}':$value).'</span> ';
 $html.='</p><p>Move record only; move legality and resulting positions are not computed.</p>';
 if($board)$html.='<figure>'.padChess($game['position'],$flip,[],[],$size,'Initial position from PGN').'<figcaption>Initial position only</figcaption></figure>';
 return $html.'</section>';
}
