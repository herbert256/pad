# backgammon

`{notation:backgammon 'start'/}` draws the standard setup. A custom position is space-separated `point:Wcount` or `point:Bcount`, followed by optional semicolon-separated fields:

```
1:B2 6:W5 8:W3 12:B5 13:W5 17:B3 19:B5 24:W2; bar:W0,B0; off:W0,B0; cube:1,C; turn:W
```

Points are 1–24 from White's perspective; White moves toward 1. Counts are 1–15 on points and 0–15 on bar/off. Each color must total exactly 15 including bar and off. A point appears once; opposing checkers cannot occupy it. Cube values are 1, 2, 4, 8, 16, 32 or 64; owner C is centered, W/B held by that color. Defaults are empty bar/off, cube 1 centered, White to move. Input represents positions, not arbitrary game files or moves.

`direction='black'` rotates point placement and numbering; checker text remains upright. At most five checkers are drawn per stack, with the full count on its final checker if larger. Bar counts, off counts, cube value/owner and turn appear in text and the accessible description. `size` is width in pixels; `title` names the SVG.
