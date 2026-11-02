# printables: flashcards

`{printable:flashcards data='cards', across=2, down=4, flip='long'}{$question}<footer>{$answer}</footer>{/printable:flashcards}`
prints alternating front/back sheets with back positions mirrored for the declared
printer duplex flip edge. A4 or Letter, 10 mm margins, up to 4 × 8 cards; the final
sheet is padded with empty matching faces. Landscape maps long/short edges to the
physical orientation. Set printer scale to 100%, duplex to the same flip edge,
and disable browser headers/footers. `calibrate` prints face/slot labels; print a
numbered test sheet, hold it to light, and use `backx`/`backy` (−5 to +5 mm) to correct
printer registration. Cut only after checking pair alignment. Footer is the back;
the remaining body is the front. The same physical ordering feeds PAD PDF.
