<?php

  // Pipe function slug(separator): a readable URL part - 'Crème Brûlée & Co.' becomes
  // 'creme-brulee-co'. Accented letters are transliterated to plain ASCII (intl's
  // transliterator when PHP has it, iconv otherwise), the rest of anything that is not a
  // letter or digit becomes one separator, '-' unless another is given, and the result is
  // lower case with no separator at either end.

  $separator = (string) ( $parm [0] ?? '-' );
  $text      = (string) $value;

  if ( function_exists ( 'transliterator_transliterate' ) )
    $text = transliterator_transliterate ( 'Any-Latin; Latin-ASCII', $text ) ?: $text;
  elseif ( function_exists ( 'iconv' ) )
    $text = @iconv ( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $text ) ?: $text;

  // The runs are cut to one space and trimmed before the separator goes in, so it is used
  // as it is written: handed to preg_replace as the replacement, slug('$0') put each run
  // back where it was, and handed to trim as a list of characters, slug('..') ended the
  // request on an invalid range and slug('x') ate an x at either end of the text itself.

  $text = strtolower ( $text );
  $text = trim ( preg_replace ( '/[^a-z0-9]+/', ' ', $text ) );

  return str_replace ( ' ', $separator, $text );

?>
