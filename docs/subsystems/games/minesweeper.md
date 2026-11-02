# games: minesweeper

`{game:minesweeper size=9, mines=10, seed=7/}` has a seeded board and a safe first
reveal, including its neighboring eight cells. Size 5–24; mines 1 through size²−9.
Arrow keys navigate the grid, Enter/Space reveal, F or right click toggles a flag;
a visible Flag mode button also supports touch. Revealing all non-mines wins;
flags alone do not. Zero regions flood-fill iteratively. Status and cell names describe
counts without relying on colors. New game resets state, with no global board state.
