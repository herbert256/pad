<?php

  // Type handler for a PAD function used as a tag ({function:upper}...{/function:upper}): applies the function to
  // the level's content, with the tag's parameters as the function's arguments.
  //
  // On the closing half of a {start}/{pad:close} pair it only switches the level's walk to 'end'
  // and returns TRUE; walk/end.php then re-enters this file with the collected output in
  // $padContent, so the function runs once, over everything the pair produced.

  if ( padStartAndClose ('end') )
    return TRUE;

  // The content has to be cleared once the function has been given it, or the level emits both:
  // {function:upper}ab{/function:upper} rendered ABab, the answer followed by the source it was worked out from.
  // tags/code.php and tags/sandbox.php had the same fault and are written the same way now.

  $padFunctionResult = padFunctionAsTag ( $padTag [$pad], $padContent, $padOpt[$pad] );

  $padContent = '';

  return $padFunctionResult;

?>
