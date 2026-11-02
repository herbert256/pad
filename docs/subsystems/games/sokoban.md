# games: sokoban

`{game:sokoban level=1/}` provides three original PAD-authored levels under the
repository license. A pair can supply a rectangular text level: # wall, space floor,
. goal, $ box, @ player, * box on goal, + player on goal. Levels require closed walls,
one player, equal nonzero box/goal counts and at most 30 × 30 cells. The engine
checks syntax; it does not claim arbitrary custom puzzles are solvable.
Arrow keys or direction buttons move; boxes can only be pushed into a free cell.
U/Undo restores a previous move (1000-step bound), including after winning; Restart
restores the initial level. All boxes on goals completes the puzzle.
