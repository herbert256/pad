# xiangqi

`{notation:xiangqi 'start'/}` draws the standard starting position. Custom text is a FEN-style placement: ten ranks from Black's back rank to Red's, each with nine intersections, `/` between ranks, digits 1–9 for empty intersections. Letters `r n b a k c p` mean chariot, horse, elephant, advisor, general, cannon and soldier; uppercase is Red, lowercase Black. Optional trailing `w` means Red to move, `b` Black. Other FEN fields are not accepted.

The parser validates widths, material limits, exactly one general per side, palace membership of generals/advisors and the elephant's side of the river. It does not validate move history or checks. `direction='black'` rotates the position and coordinate labels while keeping pieces upright. `size` sets pixel width, `title` the accessible name. Every piece is described by side, name and square (`a0` is Red's lower-left intersection in the normal orientation).

[Piece reference](https://www.xiangqi.com/help/pieces-and-moves). Unicode CJK glyphs require a font with Chinese characters.
