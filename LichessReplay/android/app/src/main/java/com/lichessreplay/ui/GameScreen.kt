package com.lichessreplay.ui

import androidx.compose.foundation.*
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.itemsIndexed
import androidx.compose.foundation.lazy.rememberLazyListState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.input.pointer.pointerInput
import androidx.compose.foundation.gestures.detectHorizontalDragGestures
import androidx.compose.foundation.gestures.detectTapGestures
import androidx.compose.ui.platform.LocalFocusManager
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.draw.scale
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.window.Dialog
import androidx.lifecycle.viewmodel.compose.viewModel
import com.lichessreplay.chess.ChessBoard
import com.lichessreplay.chess.PieceColor
import com.lichessreplay.chess.Square
import com.lichessreplay.data.ChessSource
import com.lichessreplay.data.LichessGame
import com.lichessreplay.stockfish.PvLine
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale
import kotlinx.coroutines.launch

// Chess piece Unicode symbols
private const val WHITE_KING = "♔"
private const val WHITE_QUEEN = "♕"
private const val WHITE_ROOK = "♖"
private const val WHITE_BISHOP = "♗"
private const val WHITE_KNIGHT = "♘"
private const val WHITE_PAWN = "♙"
private const val BLACK_KING = "♚"
private const val BLACK_QUEEN = "♛"
private const val BLACK_ROOK = "♜"
private const val BLACK_BISHOP = "♝"
private const val BLACK_KNIGHT = "♞"
private const val BLACK_PAWN = "♟"

// Get just the piece symbol from a SAN move (for use with separate coordinates)
private fun getPieceSymbolFromSan(move: String, isWhite: Boolean): String {
    if (move.isEmpty()) return ""

    // Handle castling
    if (move == "O-O" || move == "O-O-O") {
        return if (isWhite) BLACK_KING else WHITE_KING
    }

    val pieceChar = move.first()
    return when {
        pieceChar == 'K' -> if (isWhite) BLACK_KING else WHITE_KING
        pieceChar == 'Q' -> if (isWhite) BLACK_QUEEN else WHITE_QUEEN
        pieceChar == 'R' -> if (isWhite) BLACK_ROOK else WHITE_ROOK
        pieceChar == 'B' -> if (isWhite) BLACK_BISHOP else WHITE_BISHOP
        pieceChar == 'N' -> if (isWhite) BLACK_KNIGHT else WHITE_KNIGHT
        pieceChar.isLowerCase() -> if (isWhite) BLACK_PAWN else WHITE_PAWN // Pawn move
        else -> if (isWhite) BLACK_PAWN else WHITE_PAWN
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun GameScreen(
    modifier: Modifier = Modifier,
    viewModel: GameViewModel = viewModel()
) {
    val uiState by viewModel.uiState.collectAsState()
    var lichessUsername by remember { mutableStateOf(viewModel.savedLichessUsername) }
    var chessComUsername by remember { mutableStateOf(viewModel.savedChessComUsername) }
    var lichessGamesCount by remember { mutableStateOf(uiState.lichessMaxGames.toString()) }
    var chessComGamesCount by remember { mutableStateOf(uiState.chessComMaxGames.toString()) }
    val focusManager = LocalFocusManager.current

    // Settings dialog
    if (uiState.showSettingsDialog) {
        SettingsDialog(
            stockfishSettings = uiState.stockfishSettings,
            analyseSettings = uiState.analyseSettings,
            onDismiss = { viewModel.hideSettingsDialog() },
            onSaveStockfish = { viewModel.updateStockfishSettings(it) },
            onSaveAnalyse = { viewModel.updateAnalyseSettings(it) }
        )
    }

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(MaterialTheme.colorScheme.background)
            .padding(horizontal = 12.dp)
            .verticalScroll(rememberScrollState())
    ) {
        // Title row with New button (when game loaded) and settings button
        Row(
            modifier = Modifier
                .fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            if (uiState.game != null) {
                // New button - clears game and shows search
                IconButton(onClick = { viewModel.clearGame() }) {
                    Text("↺", fontSize = 24.sp)
                }
            } else {
                Spacer(modifier = Modifier.width(48.dp))
            }
            Text(
                text = "Chess Replay",
                style = MaterialTheme.typography.headlineSmall,
                color = Color.White,
                fontWeight = FontWeight.Light,
                textAlign = TextAlign.Center,
                modifier = Modifier.weight(1f)
            )
            IconButton(onClick = { viewModel.showSettingsDialog() }) {
                Text("⚙", fontSize = 24.sp)
            }
        }

        // Stage toggle button - only show when game is loaded
        if (uiState.game != null) {
            val stageText = if (uiState.isAutoAnalyzing) {
                val totalRounds = uiState.analyseSettings.numberOfRounds
                if (totalRounds > 1) {
                    "Analyse stage active (Round ${uiState.currentAnalysisRound}/$totalRounds)"
                } else {
                    "Analyse stage active"
                }
            } else {
                "Manual stage active"
            }
            val stageColor = if (uiState.isAutoAnalyzing) Color(0xFF6B9BFF) else Color(0xFF4CAF50)

            Button(
                onClick = { viewModel.toggleStage() },
                colors = ButtonDefaults.buttonColors(
                    containerColor = stageColor.copy(alpha = 0.2f),
                    contentColor = stageColor
                ),
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(vertical = 4.dp)
            ) {
                Text(
                    text = stageText,
                    fontWeight = FontWeight.Medium,
                    fontSize = 14.sp
                )
            }
        }

        // Search section - only show when no game is loaded
        if (uiState.game == null) {
            // Error message
            if (uiState.errorMessage != null) {
                Card(
                    colors = CardDefaults.cardColors(
                        containerColor = MaterialTheme.colorScheme.error
                    ),
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(bottom = 8.dp)
                ) {
                    Text(
                        text = uiState.errorMessage!!,
                        color = Color.White,
                        modifier = Modifier.padding(12.dp),
                        textAlign = TextAlign.Center
                    )
                }
            }

            // ===== LICHESS CARD =====
            Card(
                colors = CardDefaults.cardColors(
                    containerColor = MaterialTheme.colorScheme.surfaceVariant
                ),
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(bottom = 12.dp)
            ) {
                Column(
                    modifier = Modifier.padding(12.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    Text(
                        text = "lichess.org",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.SemiBold,
                        color = Color(0xFFE0E0E0)
                    )

                    // Username field
                    OutlinedTextField(
                        value = lichessUsername,
                        onValueChange = { lichessUsername = it },
                        placeholder = { Text("Enter username") },
                        singleLine = true,
                        keyboardOptions = KeyboardOptions(imeAction = ImeAction.Done),
                        keyboardActions = KeyboardActions(onDone = { focusManager.clearFocus() }),
                        modifier = Modifier.fillMaxWidth(),
                        colors = OutlinedTextFieldDefaults.colors(
                            unfocusedBorderColor = Color(0xFF555555),
                            focusedBorderColor = MaterialTheme.colorScheme.primary
                        )
                    )

                    // Games count field
                    OutlinedTextField(
                        value = lichessGamesCount,
                        onValueChange = { newValue ->
                            val filtered = newValue.filter { it.isDigit() }
                            lichessGamesCount = filtered
                            filtered.toIntOrNull()?.let { count ->
                                viewModel.setLichessMaxGames(count)
                            }
                        },
                        label = { Text("Number of games") },
                        singleLine = true,
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                        modifier = Modifier.fillMaxWidth(),
                        colors = OutlinedTextFieldDefaults.colors(
                            unfocusedBorderColor = Color(0xFF555555),
                            focusedBorderColor = MaterialTheme.colorScheme.primary
                        )
                    )

                    // Buttons row
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        val lichessCount = lichessGamesCount.toIntOrNull() ?: uiState.lichessMaxGames
                        Button(
                            onClick = {
                                focusManager.clearFocus()
                                if (lichessUsername.isNotBlank()) {
                                    viewModel.fetchGames(lichessUsername, ChessSource.LICHESS, lichessCount)
                                }
                            },
                            enabled = !uiState.isLoading && lichessUsername.isNotBlank(),
                            modifier = Modifier.weight(1f)
                        ) {
                            Text("Retrieve $lichessCount games")
                        }
                        Button(
                            onClick = {
                                focusManager.clearFocus()
                                if (lichessUsername.isNotBlank()) {
                                    viewModel.fetchGames(lichessUsername, ChessSource.LICHESS, 1)
                                }
                            },
                            enabled = !uiState.isLoading && lichessUsername.isNotBlank(),
                            modifier = Modifier.weight(1f)
                        ) {
                            Text("Retrieve last game")
                        }
                    }
                }
            }

            // ===== CHESS.COM CARD =====
            Card(
                colors = CardDefaults.cardColors(
                    containerColor = MaterialTheme.colorScheme.surfaceVariant
                ),
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(bottom = 12.dp)
            ) {
                Column(
                    modifier = Modifier.padding(12.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    Text(
                        text = "chess.com",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.SemiBold,
                        color = Color(0xFFE0E0E0)
                    )

                    // Username field
                    OutlinedTextField(
                        value = chessComUsername,
                        onValueChange = { chessComUsername = it },
                        placeholder = { Text("Enter username") },
                        singleLine = true,
                        keyboardOptions = KeyboardOptions(imeAction = ImeAction.Done),
                        keyboardActions = KeyboardActions(onDone = { focusManager.clearFocus() }),
                        modifier = Modifier.fillMaxWidth(),
                        colors = OutlinedTextFieldDefaults.colors(
                            unfocusedBorderColor = Color(0xFF555555),
                            focusedBorderColor = MaterialTheme.colorScheme.primary
                        )
                    )

                    // Games count field
                    OutlinedTextField(
                        value = chessComGamesCount,
                        onValueChange = { newValue ->
                            val filtered = newValue.filter { it.isDigit() }
                            chessComGamesCount = filtered
                            filtered.toIntOrNull()?.let { count ->
                                viewModel.setChessComMaxGames(count)
                            }
                        },
                        label = { Text("Number of games") },
                        singleLine = true,
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                        modifier = Modifier.fillMaxWidth(),
                        colors = OutlinedTextFieldDefaults.colors(
                            unfocusedBorderColor = Color(0xFF555555),
                            focusedBorderColor = MaterialTheme.colorScheme.primary
                        )
                    )

                    // Buttons row
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        val chessComCount = chessComGamesCount.toIntOrNull() ?: uiState.chessComMaxGames
                        Button(
                            onClick = {
                                focusManager.clearFocus()
                                if (chessComUsername.isNotBlank()) {
                                    viewModel.fetchGames(chessComUsername, ChessSource.CHESS_COM, chessComCount)
                                }
                            },
                            enabled = !uiState.isLoading && chessComUsername.isNotBlank(),
                            modifier = Modifier.weight(1f)
                        ) {
                            Text("Retrieve $chessComCount games")
                        }
                        Button(
                            onClick = {
                                focusManager.clearFocus()
                                if (chessComUsername.isNotBlank()) {
                                    viewModel.fetchGames(chessComUsername, ChessSource.CHESS_COM, 1)
                                }
                            },
                            enabled = !uiState.isLoading && chessComUsername.isNotBlank(),
                            modifier = Modifier.weight(1f)
                        ) {
                            Text("Retrieve last game")
                        }
                    }
                }
            }

            // Loading indicator
            if (uiState.isLoading) {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(40.dp),
                    contentAlignment = Alignment.Center
                ) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        CircularProgressIndicator(color = MaterialTheme.colorScheme.primary)
                        Spacer(modifier = Modifier.height(16.dp))
                        Text(
                            text = "Fetching games...",
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                }
            }
        }

        // Game selection dialog
        if (uiState.showGameSelection && uiState.gameList.isNotEmpty()) {
            GameSelectionDialog(
                games = uiState.gameList,
                onSelectGame = { viewModel.selectGame(it) },
                onDismiss = { viewModel.dismissGameSelection() }
            )
        }

        // Game content
        if (uiState.game != null) {
            GameContent(uiState = uiState, viewModel = viewModel)
        }
    }
}

@Composable
private fun GameSelectionDialog(
    games: List<LichessGame>,
    onSelectGame: (LichessGame) -> Unit,
    onDismiss: () -> Unit
) {
    Dialog(onDismissRequest = onDismiss) {
        Card(
            modifier = Modifier
                .fillMaxWidth()
                .fillMaxHeight(0.8f),
            colors = CardDefaults.cardColors(
                containerColor = MaterialTheme.colorScheme.surface
            )
        ) {
            Column(
                modifier = Modifier.padding(16.dp)
            ) {
                Text(
                    text = "Select a game",
                    fontWeight = FontWeight.Bold,
                    fontSize = 20.sp,
                    modifier = Modifier.padding(bottom = 16.dp)
                )

                LazyColumn(
                    modifier = Modifier.weight(1f),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    itemsIndexed(games) { index, game ->
                        GameListItem(
                            game = game,
                            index = index + 1,
                            onClick = { onSelectGame(game) }
                        )
                    }
                }

                Spacer(modifier = Modifier.height(16.dp))

                Button(
                    onClick = onDismiss,
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Text("Cancel")
                }
            }
        }
    }
}

@Composable
private fun GameListItem(
    game: LichessGame,
    index: Int,
    onClick: () -> Unit
) {
    val dateFormat = remember { SimpleDateFormat("MMM d, HH:mm", Locale.getDefault()) }
    val gameDate = remember(game.createdAt) {
        game.createdAt?.let { dateFormat.format(Date(it)) } ?: ""
    }

    val whiteName = game.players.white.user?.name
        ?: game.players.white.aiLevel?.let { "Stockfish $it" }
        ?: "Anonymous"
    val blackName = game.players.black.user?.name
        ?: game.players.black.aiLevel?.let { "Stockfish $it" }
        ?: "Anonymous"

    val result = when (game.winner) {
        "white" -> "1-0"
        "black" -> "0-1"
        else -> if (game.status == "draw" || game.status == "stalemate") "½-½" else game.status
    }

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable(onClick = onClick),
        colors = CardDefaults.cardColors(
            containerColor = MaterialTheme.colorScheme.surfaceVariant
        )
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            // Game number
            Text(
                text = "$index.",
                fontWeight = FontWeight.Bold,
                fontSize = 16.sp,
                modifier = Modifier.width(32.dp)
            )

            Column(modifier = Modifier.weight(1f)) {
                // Players - names below each other
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Box(
                        modifier = Modifier
                            .size(12.dp)
                            .background(Color.White, RoundedCornerShape(2.dp))
                            .border(1.dp, Color.Gray, RoundedCornerShape(2.dp))
                    )
                    Spacer(modifier = Modifier.width(4.dp))
                    Text(
                        text = whiteName,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Medium
                    )
                }
                Spacer(modifier = Modifier.height(2.dp))
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Box(
                        modifier = Modifier
                            .size(12.dp)
                            .background(Color.Black, RoundedCornerShape(2.dp))
                    )
                    Spacer(modifier = Modifier.width(4.dp))
                    Text(
                        text = blackName,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Medium
                    )
                }

                Spacer(modifier = Modifier.height(4.dp))

                // Game info
                Row {
                    Text(
                        text = gameDate,
                        fontSize = 12.sp,
                        color = MaterialTheme.colorScheme.onSurfaceVariant
                    )
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(
                        text = "• ${game.speed}",
                        fontSize = 12.sp,
                        color = MaterialTheme.colorScheme.onSurfaceVariant
                    )
                }
            }

            // Result
            Text(
                text = result,
                fontWeight = FontWeight.Bold,
                fontSize = 16.sp,
                color = when (game.winner) {
                    "white" -> Color(0xFF4CAF50)
                    "black" -> Color(0xFFF44336)
                    else -> Color(0xFF2196F3)
                }
            )
        }
    }
}

@Composable
private fun GameContent(
    uiState: GameUiState,
    viewModel: GameViewModel
) {
    val game = uiState.game ?: return

    // Game info
    Card(
        colors = CardDefaults.cardColors(
            containerColor = MaterialTheme.colorScheme.surface
        ),
        modifier = Modifier
            .fillMaxWidth()
            .padding(bottom = 4.dp)
    ) {
        Column(modifier = Modifier.padding(10.dp)) {
            // Players - white on top, black below
            val whiteResult = when (game.winner) {
                "white" -> "1"
                "black" -> "0"
                else -> if (game.status == "draw" || game.status == "stalemate") "½" else ""
            }
            val blackResult = when (game.winner) {
                "black" -> "1"
                "white" -> "0"
                else -> if (game.status == "draw" || game.status == "stalemate") "½" else ""
            }

            // White player
            Row(
                modifier = Modifier.fillMaxWidth(),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Box(
                    modifier = Modifier
                        .size(26.dp)
                        .background(Color.White, RoundedCornerShape(3.dp))
                        .border(1.dp, Color(0xFF444444), RoundedCornerShape(3.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = whiteResult,
                        color = Color.Black,
                        fontWeight = FontWeight.Bold,
                        fontSize = 14.sp
                    )
                }
                Spacer(modifier = Modifier.width(6.dp))
                Text(
                    text = game.players.white.user?.name
                        ?: game.players.white.aiLevel?.let { "SF $it" }
                        ?: "Anonymous",
                    fontWeight = FontWeight.SemiBold,
                    fontSize = 14.sp,
                    modifier = Modifier.weight(1f)
                )
                game.players.white.rating?.let {
                    Text(
                        text = "($it)",
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                        fontSize = 12.sp
                    )
                }
            }

            Spacer(modifier = Modifier.height(4.dp))

            // Black player
            Row(
                modifier = Modifier.fillMaxWidth(),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Box(
                    modifier = Modifier
                        .size(26.dp)
                        .background(Color.Black, RoundedCornerShape(3.dp))
                        .border(1.dp, Color(0xFF444444), RoundedCornerShape(3.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = blackResult,
                        color = Color.White,
                        fontWeight = FontWeight.Bold,
                        fontSize = 14.sp
                    )
                }
                Spacer(modifier = Modifier.width(6.dp))
                Text(
                    text = game.players.black.user?.name
                        ?: game.players.black.aiLevel?.let { "SF $it" }
                        ?: "Anonymous",
                    fontWeight = FontWeight.SemiBold,
                    fontSize = 14.sp,
                    modifier = Modifier.weight(1f)
                )
                game.players.black.rating?.let {
                    Text(
                        text = "($it)",
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                        fontSize = 12.sp
                    )
                }
            }

            // Evaluation graph - inside card, smaller
            if (uiState.moveDetails.isNotEmpty()) {
                Spacer(modifier = Modifier.height(4.dp))
                EvaluationGraph(
                    moveScores = uiState.moveScores,
                    totalMoves = uiState.moveDetails.size,
                    currentMoveIndex = uiState.currentMoveIndex,
                    isAutoAnalyzing = uiState.isAutoAnalyzing,
                    onMoveSelected = { moveIndex -> viewModel.goToMove(moveIndex) },
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(80.dp)
                )
            }

            Spacer(modifier = Modifier.height(4.dp))

            // Move info box - 3 sections: left (move), middle (stockfish info), right (score)
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(
                        MaterialTheme.colorScheme.surfaceVariant,
                        RoundedCornerShape(4.dp)
                    )
                    .padding(horizontal = 8.dp, vertical = 6.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                val moveIndex = uiState.currentMoveIndex
                val currentMove = uiState.moves.getOrNull(moveIndex)
                val isWhiteMove = moveIndex % 2 == 0
                val lastMove = uiState.currentBoard.getLastMove()

                // Determine which score/analysis to show
                // In analyse mode: only show final score (from moveScores)
                // In manual mode: show live analysis result
                val storedScore = uiState.moveScores[moveIndex]
                val liveResult = uiState.analysisResult
                val isManualMode = !uiState.isAutoAnalyzing
                val turn = uiState.currentBoard.getTurn()
                val isWhiteTurn = turn == PieceColor.WHITE

                if (currentMove != null && moveIndex >= 0) {
                    // LEFT: Move info (piece + from-to) - left aligned
                    val pieceSymbol = getPieceSymbolFromSan(currentMove, isWhiteMove)
                    val fromSquare = lastMove?.from?.toAlgebraic() ?: ""
                    val toSquare = lastMove?.to?.toAlgebraic() ?: ""

                    Text(
                        text = "$pieceSymbol  $fromSquare-$toSquare",
                        fontWeight = FontWeight.SemiBold,
                        fontSize = 16.sp,
                        modifier = Modifier.weight(1f)
                    )

                    // MIDDLE: Stockfish info (Depth/Nodes) - centered, smaller font
                    // In analyse mode: show stored depth/nodes after analysis is done
                    // In manual mode: show live from analysisResult
                    if (isManualMode && liveResult != null) {
                        val nodesStr = when {
                            liveResult.nodes >= 1_000_000 -> "%.1fM".format(liveResult.nodes / 1_000_000.0)
                            liveResult.nodes >= 1_000 -> "%.1fK".format(liveResult.nodes / 1_000.0)
                            else -> liveResult.nodes.toString()
                        }
                        Text(
                            text = "D:${liveResult.depth} N:$nodesStr",
                            color = Color(0xFF888888),
                            fontSize = 11.sp,
                            textAlign = TextAlign.Center,
                            modifier = Modifier.weight(1f)
                        )
                    } else if (storedScore != null && storedScore.depth > 0) {
                        // In analyse mode, show stored depth/nodes from completed analysis
                        val nodesStr = when {
                            storedScore.nodes >= 1_000_000 -> "%.1fM".format(storedScore.nodes / 1_000_000.0)
                            storedScore.nodes >= 1_000 -> "%.1fK".format(storedScore.nodes / 1_000.0)
                            else -> storedScore.nodes.toString()
                        }
                        Text(
                            text = "D:${storedScore.depth} N:$nodesStr",
                            color = Color(0xFF888888),
                            fontSize = 11.sp,
                            textAlign = TextAlign.Center,
                            modifier = Modifier.weight(1f)
                        )
                    } else {
                        Spacer(modifier = Modifier.weight(1f))
                    }

                    // RIGHT: Score - right aligned
                    // In analyse mode: show stored score
                    // In manual mode: show live score from analysisResult
                    val displayScore: MoveScore? = if (isManualMode && liveResult != null) {
                        val bestLine = liveResult.bestLine
                        if (bestLine != null) {
                            val adjustedScore = if (isWhiteTurn) bestLine.score else -bestLine.score
                            val adjustedMateIn = if (isWhiteTurn) bestLine.mateIn else -bestLine.mateIn
                            MoveScore(adjustedScore, bestLine.isMate, adjustedMateIn)
                        } else null
                    } else {
                        storedScore
                    }

                    if (displayScore != null) {
                        val scoreText = if (displayScore.isMate) {
                            "M${kotlin.math.abs(displayScore.mateIn)}"
                        } else {
                            val absScore = kotlin.math.abs(displayScore.score)
                            if (displayScore.score >= 0) "+%.1f".format(absScore) else "-%.1f".format(absScore)
                        }
                        val scoreColor = when {
                            displayScore.isMate && displayScore.mateIn > 0 -> Color(0xFF4CAF50)
                            displayScore.isMate && displayScore.mateIn < 0 -> Color(0xFFF44336)
                            displayScore.score > 0.1f -> Color(0xFF4CAF50)
                            displayScore.score < -0.1f -> Color(0xFFF44336)
                            else -> Color(0xFF2196F3)
                        }
                        Text(
                            text = scoreText,
                            color = scoreColor,
                            fontWeight = FontWeight.Bold,
                            fontSize = 16.sp,
                            textAlign = TextAlign.End,
                            modifier = Modifier.weight(1f)
                        )
                    } else {
                        Spacer(modifier = Modifier.weight(1f))
                    }
                } else {
                    Text(
                        text = "Start position",
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                        fontSize = 14.sp,
                        modifier = Modifier.fillMaxWidth(),
                        textAlign = TextAlign.Center
                    )
                }
            }
        }
    }

    // Chess board - click to stop auto-analysis, drag to make moves during manual replay
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(8.dp))
            .clickable {
                if (uiState.isAutoAnalyzing) {
                    viewModel.stopAutoAnalysis()
                }
            }
    ) {
        ChessBoardView(
            board = uiState.currentBoard,
            flipped = uiState.flippedBoard,
            interactionEnabled = !uiState.isAutoAnalyzing,
            onMove = { from, to -> viewModel.makeManualMove(from, to) },
            modifier = Modifier.fillMaxWidth()
        )
    }

    // Controls - hide during auto-analysis
    if (!uiState.isAutoAnalyzing) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(vertical = 8.dp),
            horizontalArrangement = Arrangement.Center,
            verticalAlignment = Alignment.CenterVertically
        ) {
            ControlButton("⏮") { viewModel.goToStart() }
            Spacer(modifier = Modifier.width(6.dp))
            ControlButton("◀") { viewModel.prevMove() }
            Spacer(modifier = Modifier.width(6.dp))
            ControlButton("▶") { viewModel.nextMove() }
            Spacer(modifier = Modifier.width(6.dp))
            ControlButton("⏭") { viewModel.goToEnd() }
            Spacer(modifier = Modifier.width(12.dp))
            ControlButton("↻") { viewModel.flipBoard() }
        }
    }

    // Move counter / Analysis info box (not shown when exploring line)
    if (!uiState.isExploringLine) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(bottom = 4.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            if (uiState.isAutoAnalyzing) {
                // Show analysis progress
                Text(
                    text = "Analyzing: ${uiState.autoAnalysisIndex + 1} / ${uiState.moves.size}",
                    color = Color(0xFF6B9BFF),
                    fontSize = 14.sp,
                    fontWeight = FontWeight.Medium
                )
            } else {
                Text(
                    text = "Move: ${uiState.currentMoveIndex + 1} / ${uiState.moves.size}",
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                    textAlign = TextAlign.Center
                )
            }
        }
    }

    // Back to original game button (shown when exploring a line)
    if (uiState.isExploringLine) {
        Button(
            onClick = { viewModel.backToOriginalGame() },
            colors = ButtonDefaults.buttonColors(
                containerColor = Color(0xFF4A6741)
            ),
            modifier = Modifier
                .fillMaxWidth()
                .padding(bottom = 4.dp)
        ) {
            Text("↩ Back to game", fontSize = 13.sp)
        }
    }

    // Stockfish analysis panel - hide during auto-analysis
    if (!uiState.isAutoAnalyzing) {
        AnalysisPanel(
            uiState = uiState,
            onExploreLine = { pv, moveIndex -> viewModel.exploreLine(pv, moveIndex) },
            modifier = Modifier
                .fillMaxWidth()
                .padding(bottom = 4.dp)
        )
    }

    // Moves list - only show during manual replay (not during auto-analysis)
    if (!uiState.isAutoAnalyzing) {
        Card(
            colors = CardDefaults.cardColors(
                containerColor = MaterialTheme.colorScheme.surface
            ),
            modifier = Modifier
                .fillMaxWidth()
        ) {
            Column(modifier = Modifier.padding(8.dp)) {
                Text(
                    text = "Moves",
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                    fontWeight = FontWeight.Medium,
                    fontSize = 13.sp,
                    modifier = Modifier.padding(bottom = 4.dp)
                )
                MovesList(
                    moveDetails = uiState.moveDetails,
                    currentMoveIndex = uiState.currentMoveIndex,
                    moveScores = uiState.moveScores,
                    isAutoAnalyzing = uiState.isAutoAnalyzing,
                    autoAnalysisIndex = uiState.autoAnalysisIndex,
                    onMoveClick = { viewModel.goToMove(it) }
                )
            }
        }
    }
}

@Composable
private fun ControlButton(
    text: String,
    onClick: () -> Unit
) {
    Button(
        onClick = onClick,
        colors = ButtonDefaults.buttonColors(
            containerColor = MaterialTheme.colorScheme.surface
        ),
        contentPadding = PaddingValues(horizontal = 16.dp, vertical = 12.dp)
    ) {
        Text(text = text, fontSize = 18.sp)
    }
}

@Composable
private fun MovesList(
    moveDetails: List<MoveDetails>,
    currentMoveIndex: Int,
    moveScores: Map<Int, MoveScore>,
    isAutoAnalyzing: Boolean,
    autoAnalysisIndex: Int,
    onMoveClick: (Int) -> Unit
) {
    val movePairs = moveDetails.chunked(2)

    Column(modifier = Modifier.fillMaxWidth()) {
        movePairs.forEachIndexed { pairIndex, pair ->
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(vertical = 2.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                // Move number
                Text(
                    text = "${pairIndex + 1}.",
                    color = Color(0xFF666666),
                    fontFamily = FontFamily.Monospace,
                    fontSize = 15.sp,
                    modifier = Modifier.width(32.dp)
                )

                // White move
                val whiteIndex = pairIndex * 2
                MoveChip(
                    moveDetails = pair[0],
                    isWhite = true,
                    isActive = whiteIndex == currentMoveIndex,
                    isAnalyzing = isAutoAnalyzing && autoAnalysisIndex == whiteIndex,
                    score = moveScores[whiteIndex],
                    onClick = { onMoveClick(whiteIndex) },
                    modifier = Modifier.weight(1f)
                )

                // Black move
                if (pair.size > 1) {
                    Spacer(modifier = Modifier.width(4.dp))
                    val blackIndex = pairIndex * 2 + 1
                    MoveChip(
                        moveDetails = pair[1],
                        isWhite = false,
                        isActive = blackIndex == currentMoveIndex,
                        isAnalyzing = isAutoAnalyzing && autoAnalysisIndex == blackIndex,
                        score = moveScores[blackIndex],
                        onClick = { onMoveClick(blackIndex) },
                        modifier = Modifier.weight(1f)
                    )
                } else {
                    Spacer(modifier = Modifier.weight(1f))
                }
            }
        }
    }
}

@Composable
private fun MoveChip(
    moveDetails: MoveDetails,
    isWhite: Boolean,
    isActive: Boolean,
    isAnalyzing: Boolean = false,
    score: MoveScore? = null,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    val backgroundColor = when {
        isActive -> MaterialTheme.colorScheme.primary
        isAnalyzing -> Color(0xFFFFE082) // Light yellow when analyzing
        else -> Color.Transparent
    }

    // Get piece symbol
    val pieceSymbol = when (moveDetails.pieceType) {
        "K" -> if (isWhite) BLACK_KING else WHITE_KING
        "Q" -> if (isWhite) BLACK_QUEEN else WHITE_QUEEN
        "R" -> if (isWhite) BLACK_ROOK else WHITE_ROOK
        "B" -> if (isWhite) BLACK_BISHOP else WHITE_BISHOP
        "N" -> if (isWhite) BLACK_KNIGHT else WHITE_KNIGHT
        else -> if (isWhite) BLACK_PAWN else WHITE_PAWN
    }

    // Build move text: piece from-to or piece fromxto
    val separator = if (moveDetails.isCapture) "x" else "-"
    val moveText = "$pieceSymbol ${moveDetails.from}$separator${moveDetails.to}"

    Row(
        modifier = modifier
            .clip(RoundedCornerShape(4.dp))
            .background(backgroundColor)
            .clickable(onClick = onClick)
            .padding(horizontal = 6.dp, vertical = 4.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = moveText,
            fontSize = 15.sp,
            color = if (isActive) Color.White else MaterialTheme.colorScheme.onSurface
        )

        // Show score if available
        if (score != null) {
            Spacer(modifier = Modifier.width(4.dp))
            val scoreText = if (score.isMate) {
                "M${kotlin.math.abs(score.mateIn)}"
            } else {
                "%.1f".format(kotlin.math.abs(score.score))
            }
            val scoreColor = when {
                isActive -> Color.White.copy(alpha = 0.9f)
                score.isMate && score.mateIn > 0 -> Color(0xFF4CAF50) // Green for white winning mate
                score.isMate && score.mateIn < 0 -> Color(0xFFF44336) // Red for black winning mate
                score.score > 0.1f -> Color(0xFF4CAF50) // Green for white better
                score.score < -0.1f -> Color(0xFFF44336) // Red for black better
                else -> Color(0xFF2196F3) // Blue for equal (0)
            }
            Text(
                text = scoreText,
                fontSize = 15.sp,
                color = scoreColor
            )
        } else if (isAnalyzing) {
            Spacer(modifier = Modifier.width(4.dp))
            Text(
                text = "...",
                fontSize = 15.sp,
                color = Color(0xFF666666)
            )
        }
    }
}

@Composable
private fun EvaluationGraph(
    moveScores: Map<Int, MoveScore>,
    totalMoves: Int,
    currentMoveIndex: Int,
    isAutoAnalyzing: Boolean,
    onMoveSelected: (Int) -> Unit,
    modifier: Modifier = Modifier
) {
    val greenColor = Color(0xFF4CAF50)
    val redColor = Color(0xFFF44336)
    val lineColor = Color(0xFF666666)
    val currentMoveColor = Color(0xFF2196F3)

    // Track the graph width for calculating move index from drag position
    var graphWidth by remember { mutableStateOf(0f) }

    Canvas(
        modifier = modifier
            .background(Color(0xFF1A1A1A), RoundedCornerShape(8.dp))
            .padding(8.dp)
            .pointerInput(isAutoAnalyzing, totalMoves) {
                if (!isAutoAnalyzing && totalMoves > 0) {
                    detectHorizontalDragGestures { change, _ ->
                        change.consume()
                        val x = change.position.x.coerceIn(0f, graphWidth)
                        val moveIndex = if (totalMoves > 1) {
                            ((x / graphWidth) * (totalMoves - 1)).toInt().coerceIn(0, totalMoves - 1)
                        } else {
                            0
                        }
                        onMoveSelected(moveIndex)
                    }
                }
            }
            .pointerInput(isAutoAnalyzing, totalMoves) {
                if (!isAutoAnalyzing && totalMoves > 0) {
                    detectTapGestures { offset ->
                        val x = offset.x.coerceIn(0f, graphWidth)
                        val moveIndex = if (totalMoves > 1) {
                            ((x / graphWidth) * (totalMoves - 1)).toInt().coerceIn(0, totalMoves - 1)
                        } else {
                            0
                        }
                        onMoveSelected(moveIndex)
                    }
                }
            }
    ) {
        if (totalMoves == 0) return@Canvas

        val width = size.width
        val height = size.height
        graphWidth = width
        val centerY = height / 2
        val maxScore = 5f // Cap the display at +/- 5 pawns

        // Draw center line (x-axis)
        drawLine(
            color = lineColor,
            start = Offset(0f, centerY),
            end = Offset(width, centerY),
            strokeWidth = 1f
        )

        // Calculate point spacing
        val pointSpacing = if (totalMoves > 1) width / (totalMoves - 1) else width / 2

        // Draw the evaluation line
        var previousPoint: Offset? = null

        for (moveIndex in 0 until totalMoves) {
            val score = moveScores[moveIndex]
            if (score != null) {
                val x = if (totalMoves > 1) moveIndex * pointSpacing else width / 2

                // Clamp score to maxScore range and calculate y position
                val clampedScore = score.score.coerceIn(-maxScore, maxScore)
                // Positive score (white better) goes UP (negative y direction)
                val y = centerY - (clampedScore / maxScore) * (height / 2 - 4)

                val currentPoint = Offset(x, y)

                // Draw line from previous point
                if (previousPoint != null) {
                    // Determine color based on whether score is positive or negative
                    val segmentColor = if (score.score >= 0) greenColor else redColor
                    drawLine(
                        color = segmentColor,
                        start = previousPoint,
                        end = currentPoint,
                        strokeWidth = 2f
                    )
                }

                // Draw point
                val pointColor = if (score.score >= 0) greenColor else redColor
                drawCircle(
                    color = pointColor,
                    radius = 3f,
                    center = currentPoint
                )

                previousPoint = currentPoint
            } else {
                // No score yet for this move, break the line
                previousPoint = null
            }
        }

        // Draw current move indicator (only when not auto-analyzing)
        if (!isAutoAnalyzing && currentMoveIndex >= 0 && currentMoveIndex < totalMoves) {
            val x = if (totalMoves > 1) currentMoveIndex * pointSpacing else width / 2
            drawLine(
                color = currentMoveColor,
                start = Offset(x, 0f),
                end = Offset(x, height),
                strokeWidth = 2f
            )
        }
    }
}

@Composable
private fun AnalysisPanel(
    uiState: GameUiState,
    onExploreLine: (String, Int) -> Unit,
    modifier: Modifier = Modifier
) {
    val result = uiState.analysisResult
    val turn = uiState.currentBoard.getTurn()
    val isWhiteTurn = turn == PieceColor.WHITE

    // Only show if analysis is enabled and ready with results
    if (!uiState.analysisEnabled || !uiState.stockfishReady || result == null) {
        return
    }

    Card(
        colors = CardDefaults.cardColors(
            containerColor = MaterialTheme.colorScheme.surface
        ),
        modifier = modifier
    ) {
        Column(
            modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp),
            verticalArrangement = Arrangement.spacedBy(6.dp)
        ) {
            result.lines.forEach { line ->
                PvLineRow(
                    line = line,
                    board = uiState.currentBoard,
                    isWhiteTurn = isWhiteTurn,
                    isFirst = line.multipv == 1,
                    onMoveClick = { moveIndex ->
                        onExploreLine(line.pv, moveIndex)
                    }
                )
            }
        }
    }
}

@Composable
private fun PvLineRow(
    line: PvLine,
    board: ChessBoard,
    isWhiteTurn: Boolean,
    isFirst: Boolean,
    onMoveClick: (Int) -> Unit
) {
    // Score display: always from white's perspective (positive = white better, negative = black better)
    // If white's turn to move: Stockfish gives score from white's POV, keep it
    // If black's turn to move: Stockfish gives score from black's POV, negate it
    val adjustedScore = if (isWhiteTurn) line.score else -line.score
    val adjustedMateIn = if (isWhiteTurn) line.mateIn else -line.mateIn

    val displayScore = if (line.isMate) {
        if (adjustedMateIn > 0) "M$adjustedMateIn" else "M${kotlin.math.abs(adjustedMateIn)}"
    } else {
        if (adjustedScore >= 0) "+%.1f".format(adjustedScore)
        else "%.1f".format(adjustedScore)
    }

    val scoreColor = when {
        line.isMate -> Color(0xFFFF6B6B)
        else -> {
            when {
                adjustedScore > 0.3f -> Color.White
                adjustedScore < -0.3f -> Color(0xFF888888)
                else -> Color(0xFF6B9BFF)
            }
        }
    }

    // Format UCI moves with piece symbols and - or x for captures
    val formattedMoves = remember(line.pv, board) {
        formatUciMovesWithCaptures(line.pv, board, isWhiteTurn)
    }

    Row(
        modifier = Modifier.fillMaxWidth(),
        verticalAlignment = Alignment.CenterVertically
    ) {
        // Score box
        Box(
            modifier = Modifier
                .width(50.dp)
                .background(
                    if (isFirst) Color(0xFF1A2744) else Color(0xFF151D30),
                    RoundedCornerShape(4.dp)
                )
                .padding(horizontal = 6.dp, vertical = 4.dp),
            contentAlignment = Alignment.Center
        ) {
            Text(
                text = displayScore,
                fontSize = if (isFirst) 14.sp else 12.sp,
                fontWeight = if (isFirst) FontWeight.Bold else FontWeight.Medium,
                color = scoreColor
            )
        }

        Spacer(modifier = Modifier.width(8.dp))

        // PV moves with piece symbols - clickable
        Row(
            modifier = Modifier
                .weight(1f)
                .horizontalScroll(rememberScrollState())
                .background(Color(0xFF0F1629), RoundedCornerShape(4.dp))
                .padding(horizontal = 4.dp, vertical = 2.dp),
            horizontalArrangement = Arrangement.spacedBy(2.dp)
        ) {
            formattedMoves.forEachIndexed { index, formattedMove ->
                Text(
                    text = formattedMove,
                    fontSize = 14.sp,
                    color = Color(0xFFCCCCCC),
                    modifier = Modifier
                        .clip(RoundedCornerShape(3.dp))
                        .clickable { onMoveClick(index) }
                        .background(Color(0xFF1A2744))
                        .padding(horizontal = 6.dp, vertical = 3.dp)
                )
            }
        }
    }
}

// Format UCI moves with piece symbols and capture notation
private fun formatUciMovesWithCaptures(pv: String, startBoard: ChessBoard, isWhiteTurn: Boolean): List<String> {
    if (pv.isBlank()) return emptyList()

    val moves = pv.split(" ").filter { it.isNotBlank() }
    val result = mutableListOf<String>()
    val tempBoard = startBoard.copy()
    var currentIsWhite = isWhiteTurn

    for (uciMove in moves) {
        if (uciMove.length < 4) continue

        val fromStr = uciMove.substring(0, 2)
        val toStr = uciMove.substring(2, 4)
        val promotion = if (uciMove.length > 4) uciMove.substring(4).uppercase() else ""

        val fromSquare = Square.fromAlgebraic(fromStr)
        val toSquare = Square.fromAlgebraic(toStr)

        // Get the piece on the from square to determine the correct symbol
        val piece = fromSquare?.let { tempBoard.getPiece(it) }
        val symbol = if (piece != null) {
            val isWhitePiece = piece.color == com.lichessreplay.chess.PieceColor.WHITE
            getPieceSymbol(piece.type, isWhitePiece)
        } else {
            // Fallback - inverted because Unicode symbols are visually inverted
            if (currentIsWhite) BLACK_PAWN else WHITE_PAWN
        }

        // Check for capture: either there's a piece on target square, or it's en passant
        val targetPiece = toSquare?.let { tempBoard.getPiece(it) }
        val isPawn = piece?.type == com.lichessreplay.chess.PieceType.PAWN
        val isEnPassant = isPawn && fromSquare != null && toSquare != null &&
            fromSquare.file != toSquare.file && targetPiece == null
        val isCapture = targetPiece != null || isEnPassant

        val separator = if (isCapture) "x" else "-"
        val formatted = "$symbol $fromStr$separator$toStr$promotion"

        result.add(formatted)

        // Make the move on temp board for next iteration
        tempBoard.makeUciMove(uciMove)
        currentIsWhite = !currentIsWhite
    }

    return result
}

// Get the correct piece symbol based on piece type and color
// Note: Unicode chess symbols are inverted - "white" symbols appear filled, "black" appear hollow
private fun getPieceSymbol(pieceType: com.lichessreplay.chess.PieceType, isWhite: Boolean): String {
    return when (pieceType) {
        com.lichessreplay.chess.PieceType.KING -> if (isWhite) BLACK_KING else WHITE_KING
        com.lichessreplay.chess.PieceType.QUEEN -> if (isWhite) BLACK_QUEEN else WHITE_QUEEN
        com.lichessreplay.chess.PieceType.ROOK -> if (isWhite) BLACK_ROOK else WHITE_ROOK
        com.lichessreplay.chess.PieceType.BISHOP -> if (isWhite) BLACK_BISHOP else WHITE_BISHOP
        com.lichessreplay.chess.PieceType.KNIGHT -> if (isWhite) BLACK_KNIGHT else WHITE_KNIGHT
        com.lichessreplay.chess.PieceType.PAWN -> if (isWhite) BLACK_PAWN else WHITE_PAWN
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun SettingsDialog(
    stockfishSettings: StockfishSettings,
    analyseSettings: AnalyseSettings,
    onDismiss: () -> Unit,
    onSaveStockfish: (StockfishSettings) -> Unit,
    onSaveAnalyse: (AnalyseSettings) -> Unit
) {
    // Analyse stage settings
    var analyseThreads by remember { mutableStateOf(stockfishSettings.analyseStage.threads.toString()) }
    var analyseHashMb by remember { mutableStateOf(stockfishSettings.analyseStage.hashMb.toString()) }

    // Manual stage settings
    var manualDepth by remember { mutableStateOf(stockfishSettings.manualStage.depth.toString()) }
    var manualThreads by remember { mutableStateOf(stockfishSettings.manualStage.threads.toString()) }
    var manualHashMb by remember { mutableStateOf(stockfishSettings.manualStage.hashMb.toString()) }
    var manualMultiPv by remember { mutableStateOf(stockfishSettings.manualStage.multiPv.toString()) }

    // Analyse settings
    var analyseSequence by remember { mutableStateOf(analyseSettings.sequence) }
    var sequenceExpanded by remember { mutableStateOf(false) }
    var numberOfRounds by remember { mutableStateOf(analyseSettings.numberOfRounds) }
    var roundsExpanded by remember { mutableStateOf(false) }
    var round1TimeMs by remember { mutableStateOf(analyseSettings.round1TimeMs) }
    var round1Expanded by remember { mutableStateOf(false) }
    var round2TimeMs by remember { mutableStateOf(analyseSettings.round2TimeMs) }
    var round2Expanded by remember { mutableStateOf(false) }

    // Sequence options
    val sequenceOptions = listOf(
        AnalyseSequence.FORWARDS to "Forwards",
        AnalyseSequence.BACKWARDS to "Backwards",
        AnalyseSequence.MIXED to "Mixed"
    )

    // Number of rounds options
    val roundsOptions = listOf(1, 2)

    // Round 1 time options: 0.10, 0.25, 0.50, 0.75, 1.00, 1.50, 2.50, 5.00 seconds
    val round1TimeOptions = listOf(
        100 to "0.10s",
        250 to "0.25s",
        500 to "0.50s",
        750 to "0.75s",
        1000 to "1.00s",
        1500 to "1.50s",
        2500 to "2.50s",
        5000 to "5.00s"
    )

    // Round 2 time options: 1.00, 1.50, 2.50, 5.00, 7.50, 10.00, 15.00 seconds
    val round2TimeOptions = listOf(
        1000 to "1.00s",
        1500 to "1.50s",
        2500 to "2.50s",
        5000 to "5.00s",
        7500 to "7.50s",
        10000 to "10.00s",
        15000 to "15.00s"
    )

    // Filter round 2 options to only show values higher than round 1
    val filteredRound2Options = round2TimeOptions.filter { it.first > round1TimeMs }

    // If current round2 selection is no longer valid, update it to the first valid option
    if (numberOfRounds == 2 && filteredRound2Options.isNotEmpty() && round2TimeMs <= round1TimeMs) {
        round2TimeMs = filteredRound2Options.first().first
    }

    Dialog(onDismissRequest = onDismiss) {
        Card(
            modifier = Modifier
                .fillMaxWidth()
                .padding(8.dp),
            colors = CardDefaults.cardColors(
                containerColor = MaterialTheme.colorScheme.surface
            )
        ) {
            Column(
                modifier = Modifier
                    .padding(16.dp)
                    .verticalScroll(rememberScrollState()),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                // ===== ANALYSE SETTINGS CARD =====
                Card(
                    colors = CardDefaults.cardColors(
                        containerColor = MaterialTheme.colorScheme.surfaceVariant
                    ),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(
                        modifier = Modifier.padding(12.dp),
                        verticalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        Text(
                            text = "Analyse Settings",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.SemiBold
                        )

                        // Analyse Sequence dropdown
                        ExposedDropdownMenuBox(
                            expanded = sequenceExpanded,
                            onExpandedChange = { sequenceExpanded = it }
                        ) {
                            OutlinedTextField(
                                value = sequenceOptions.find { it.first == analyseSequence }?.second ?: "Backwards",
                                onValueChange = {},
                                readOnly = true,
                                label = { Text("Analyse sequence") },
                                trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = sequenceExpanded) },
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .menuAnchor()
                            )
                            ExposedDropdownMenu(
                                expanded = sequenceExpanded,
                                onDismissRequest = { sequenceExpanded = false }
                            ) {
                                sequenceOptions.forEach { (seq, label) ->
                                    DropdownMenuItem(
                                        text = { Text(label) },
                                        onClick = {
                                            analyseSequence = seq
                                            sequenceExpanded = false
                                        }
                                    )
                                }
                            }
                        }

                        // Number of rounds dropdown
                        ExposedDropdownMenuBox(
                            expanded = roundsExpanded,
                            onExpandedChange = { roundsExpanded = it }
                        ) {
                            OutlinedTextField(
                                value = "$numberOfRounds round${if (numberOfRounds > 1) "s" else ""}",
                                onValueChange = {},
                                readOnly = true,
                                label = { Text("Number of analyse rounds") },
                                trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = roundsExpanded) },
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .menuAnchor()
                            )
                            ExposedDropdownMenu(
                                expanded = roundsExpanded,
                                onDismissRequest = { roundsExpanded = false }
                            ) {
                                roundsOptions.forEach { rounds ->
                                    DropdownMenuItem(
                                        text = { Text("$rounds round${if (rounds > 1) "s" else ""}") },
                                        onClick = {
                                            numberOfRounds = rounds
                                            roundsExpanded = false
                                        }
                                    )
                                }
                            }
                        }

                        // Round 1 time dropdown
                        ExposedDropdownMenuBox(
                            expanded = round1Expanded,
                            onExpandedChange = { round1Expanded = it }
                        ) {
                            OutlinedTextField(
                                value = round1TimeOptions.find { it.first == round1TimeMs }?.second ?: "0.50s",
                                onValueChange = {},
                                readOnly = true,
                                label = { Text("Round 1 time per move") },
                                trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = round1Expanded) },
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .menuAnchor()
                            )
                            ExposedDropdownMenu(
                                expanded = round1Expanded,
                                onDismissRequest = { round1Expanded = false }
                            ) {
                                round1TimeOptions.forEach { (ms, label) ->
                                    DropdownMenuItem(
                                        text = { Text(label) },
                                        onClick = {
                                            round1TimeMs = ms
                                            round1Expanded = false
                                            // Auto-adjust round 2 if it's now invalid
                                            if (round2TimeMs <= ms) {
                                                val validOptions = round2TimeOptions.filter { it.first > ms }
                                                if (validOptions.isNotEmpty()) {
                                                    round2TimeMs = validOptions.first().first
                                                }
                                            }
                                        }
                                    )
                                }
                            }
                        }

                        // Round 2 time dropdown (only show if 2 rounds selected)
                        if (numberOfRounds == 2) {
                            ExposedDropdownMenuBox(
                                expanded = round2Expanded,
                                onExpandedChange = { round2Expanded = it }
                            ) {
                                OutlinedTextField(
                                    value = round2TimeOptions.find { it.first == round2TimeMs }?.second ?: "2.50s",
                                    onValueChange = {},
                                    readOnly = true,
                                    label = { Text("Round 2 time per move") },
                                    trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = round2Expanded) },
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .menuAnchor()
                                )
                                ExposedDropdownMenu(
                                    expanded = round2Expanded,
                                    onDismissRequest = { round2Expanded = false }
                                ) {
                                    filteredRound2Options.forEach { (ms, label) ->
                                        DropdownMenuItem(
                                            text = { Text(label) },
                                            onClick = {
                                                round2TimeMs = ms
                                                round2Expanded = false
                                            }
                                        )
                                    }
                                }
                            }
                        }
                    }
                }

                // ===== STOCKFISH ANALYSE STAGE CARD =====
                Card(
                    colors = CardDefaults.cardColors(
                        containerColor = MaterialTheme.colorScheme.surfaceVariant
                    ),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(
                        modifier = Modifier.padding(12.dp),
                        verticalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        Text(
                            text = "Stockfish - Analyse Stage",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.SemiBold
                        )

                        // Threads
                        OutlinedTextField(
                            value = analyseThreads,
                            onValueChange = { analyseThreads = it.filter { c -> c.isDigit() } },
                            label = { Text("Threads") },
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth()
                        )

                        // Hash
                        OutlinedTextField(
                            value = analyseHashMb,
                            onValueChange = { analyseHashMb = it.filter { c -> c.isDigit() } },
                            label = { Text("Hash Memory (MB)") },
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth()
                        )
                    }
                }

                // ===== STOCKFISH MANUAL STAGE CARD =====
                Card(
                    colors = CardDefaults.cardColors(
                        containerColor = MaterialTheme.colorScheme.surfaceVariant
                    ),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(
                        modifier = Modifier.padding(12.dp),
                        verticalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        Text(
                            text = "Stockfish - Manual Stage",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.SemiBold
                        )

                        // Depth
                        OutlinedTextField(
                            value = manualDepth,
                            onValueChange = { manualDepth = it.filter { c -> c.isDigit() } },
                            label = { Text("Analysis Depth") },
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth()
                        )

                        // Threads
                        OutlinedTextField(
                            value = manualThreads,
                            onValueChange = { manualThreads = it.filter { c -> c.isDigit() } },
                            label = { Text("Threads") },
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth()
                        )

                        // Hash
                        OutlinedTextField(
                            value = manualHashMb,
                            onValueChange = { manualHashMb = it.filter { c -> c.isDigit() } },
                            label = { Text("Hash Memory (MB)") },
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth()
                        )

                        // MultiPV
                        OutlinedTextField(
                            value = manualMultiPv,
                            onValueChange = { manualMultiPv = it.filter { c -> c.isDigit() } },
                            label = { Text("MultiPV (lines to show)") },
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth()
                        )
                    }
                }

                // Buttons
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.End,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    TextButton(onClick = onDismiss) {
                        Text("Cancel")
                    }
                    Spacer(modifier = Modifier.width(8.dp))
                    Button(
                        onClick = {
                            // Save analyse settings
                            val newAnalyseSettings = AnalyseSettings(
                                sequence = analyseSequence,
                                numberOfRounds = numberOfRounds,
                                round1TimeMs = round1TimeMs,
                                round2TimeMs = round2TimeMs
                            )
                            onSaveAnalyse(newAnalyseSettings)

                            // Save stockfish settings
                            val newStockfishSettings = StockfishSettings(
                                analyseStage = AnalyseStageSettings(
                                    threads = analyseThreads.toIntOrNull() ?: 1,
                                    hashMb = analyseHashMb.toIntOrNull() ?: 64
                                ),
                                manualStage = ManualStageSettings(
                                    depth = manualDepth.toIntOrNull() ?: 16,
                                    threads = manualThreads.toIntOrNull() ?: 1,
                                    hashMb = manualHashMb.toIntOrNull() ?: 64,
                                    multiPv = manualMultiPv.toIntOrNull() ?: 1
                                )
                            )
                            onSaveStockfish(newStockfishSettings)
                        }
                    ) {
                        Text("Save")
                    }
                }
            }
        }
    }
}
