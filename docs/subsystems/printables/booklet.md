# printables: booklet

`{printable:booklet data='pages', paper='a4', binding='left', numbers}{$content}{/printable:booklet}`
imposes one logical page per data row onto landscape sheets with two panels per face.
A4 sheets are 297 × 210 mm and yield 148.5 × 210 mm folded pages. A3 and Letter
are supported. Pages pad to a multiple of four; eight logical pages become
`8,1 / 2,7 / 6,3 / 4,5`. Right binding reverses each pair. Print in the emitted
order, duplex **short edge**, 100% scale, without headers/footers; fold and nest
the sheets. Do not enable the printer's own booklet imposition as well.
`copies` repeats the complete imposed document, not each page. `numbers` adds
logical page numbers. Limit: 1000 logical pages and 5000 physical panels. Fixed
panels clip overflow; paginate the application's content explicitly. No automatic
text flow, creep compensation or signature splitting is promised.
