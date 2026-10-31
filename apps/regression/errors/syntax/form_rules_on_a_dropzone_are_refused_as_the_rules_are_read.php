<?php

  // The rules of a form are read from its template before the page's PHP runs - a post is
  // checked against them there - and a dropzone's rules= is refused at that reading too, as a
  // file field's is: refused only as the field rendered, they were kept, and under an error
  // action that goes on every post failed on a field the post never has.

  padFormRulesOf ( "{form 'f'}{dropzone 'cv', rules='required'/}<button>Send</button>{/form}" );

?>
