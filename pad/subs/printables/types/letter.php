<?php

  // {printable:letter data='customers', sender='PAD, Main Street 1, 3511 AB Utrecht'}...{/printable:letter}
  // - a letter for a window envelope, one to an A4 sheet, laid out as DIN 5008 lays out its
  // form B: the content between the tags is the letter, its <address> the address, which
  // stands in the window of a DL envelope when the letter is folded in three at its fold
  // marks, under the return line of sender=; its <aside> the block beside it - the date, a
  // reference - its <header> the letterhead and its <footer> the foot of the page, the rest
  // the body. A letter for every row of the data - a mail merge. size= (10 pt) the body. A
  // letter is one page: a longer body is cut at the foot.
  //
  //   {printable:letter data='customers', sender='PAD, Main Street 1, 3511 AB Utrecht'}
  //     <address>{$name}<br>{$street}<br>{$postcode} {$city}</address>
  //     <aside>Utrecht, 1 November 2026</aside>
  //     <p>Dear {$name},</p><p>...</p>
  //   {/printable:letter}

  return padPrintableDin5008 ( [ 'letter', 'letters' ], '' );

?>
