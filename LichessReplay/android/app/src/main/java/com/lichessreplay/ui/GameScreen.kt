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
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalFocusManager
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.lichessreplay.chess.PieceColor
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun GameScreen(
    modifier: Modifier = Modifier,
    viewModel: GameViewModel = viewModel()
) {
    val uiState by viewModel.uiState.collectAsState()
    var username by remember { mutableStateOf(viewModel.savedUsername) }
    val focusManager = LocalFocusManager.current

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(MaterialTheme.colorScheme.background)
            .padding(16.dp)
            .verticalScroll(rememberScrollState())
    ) {
        // Title
        Text(
            text = "Lichess Game Replay",
            style = MaterialTheme.typography.headlineMedium,
            color = Color.White,
            fontWeight = FontWeight.Light,
            modifier = Modifier
                .fillMaxWidth()
                .padding(bottom = 24.dp),
            textAlign = TextAlign.Center
        )

        // Search section
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(bottom = 24.dp),
            horizontalArrangement = Arrangement.Center,
            verticalAlignment = Alignment.CenterVertically
        ) {
            OutlinedTextField(
                value = username,
                onValueChange = { username = it },
                placeholder = { Text("Enter Lichess username") },
                singleLine = true,
                keyboardOptions = KeyboardOptions(imeAction = ImeAction.Search),
                keyboardActions = KeyboardActions(onSearch = {
                    focusManager.clearFocus()
                    if (username.isNotBlank()) {
                        viewModel.fetchLastGame(username)
                    }
                }),
                modifier = Modifier.weight(1f),
                colors = OutlinedTextFieldDefaults.colors(
                    unfocusedBorderColor = Color(0xFF333333),
                    focusedBorderColor = MaterialTheme.colorScheme.primary
                )
            )

            Spacer(modifier = Modifier.width(8.dp))

            Button(
                onClick = {
                    focusManager.clearFocus()
                    if (username.isNotBlank()) {
                        viewModel.fetchLastGame(username)
                    }
                },
                enabled = !uiState.isLoading,
                modifier = Modifier.height(56.dp)
            ) {
                Text("Fetch")
            }
        }

        // Error message
        if (uiState.errorMessage != null) {
            Card(
                colors = CardDefaults.cardColors(
                    containerColor = MaterialTheme.colorScheme.error
                ),
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(bottom = 16.dp)
            ) {
                Text(
                    text = uiState.errorMessage!!,
                    color = Color.White,
                    modifier = Modifier.padding(16.dp),
                    textAlign = TextAlign.Center
                )
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
                        text = "Fetching game data...",
                        color = MaterialTheme.colorScheme.onSurfaceVariant
                    )
                }
            }
        }

        // Game content
        if (uiState.game != null) {
            GameContent(uiState = uiState, viewModel = viewModel)
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
            .padding(bottom = 16.dp)
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            // Players row
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                // White player
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Box(
                        modifier = Modifier
                            .size(20.dp)
                            .background(Color.White, RoundedCornerShape(4.dp))
                            .border(2.dp, Color(0xFF444444), RoundedCornerShape(4.dp))
                    )
                    Spacer(modifier = Modifier.width(8.dp))
                    Column {
                        Text(
                            text = game.players.white.user?.name
                                ?: game.players.white.aiLevel?.let { "Stockfish Level $it" }
                                ?: "Anonymous",
                            fontWeight = FontWeight.SemiBold,
                            fontSize = 16.sp
                        )
                        game.players.white.rating?.let {
                            Text(
                                text = "($it)",
                                color = MaterialTheme.colorScheme.onSurfaceVariant,
                                fontSize = 14.sp
                            )
                        }
                    }
                }

                // Black player
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Column(horizontalAlignment = Alignment.End) {
                        Text(
                            text = game.players.black.user?.name
                                ?: game.players.black.aiLevel?.let { "Stockfish Level $it" }
                                ?: "Anonymous",
                            fontWeight = FontWeight.SemiBold,
                            fontSize = 16.sp
                        )
                        game.players.black.rating?.let {
                            Text(
                                text = "($it)",
                                color = MaterialTheme.colorScheme.onSurfaceVariant,
                                fontSize = 14.sp
                            )
                        }
                    }
                    Spacer(modifier = Modifier.width(8.dp))
                    Box(
                        modifier = Modifier
                            .size(20.dp)
                            .background(Color.Black, RoundedCornerShape(4.dp))
                            .border(2.dp, Color(0xFF444444), RoundedCornerShape(4.dp))
                    )
                }
            }

            Spacer(modifier = Modifier.height(16.dp))

            // Result
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(
                        MaterialTheme.colorScheme.surfaceVariant,
                        RoundedCornerShape(8.dp)
                    )
                    .padding(12.dp),
                contentAlignment = Alignment.Center
            ) {
                Text(
                    text = when (game.winner) {
                        "white" -> "1-0 (White wins)"
                        "black" -> "0-1 (Black wins)"
                        else -> if (game.status == "draw" || game.status == "stalemate") {
                            "½-½ (Draw)"
                        } else {
                            game.status.replaceFirstChar { it.uppercase() }
                        }
                    },
                    fontWeight = FontWeight.SemiBold,
                    fontSize = 18.sp
                )
            }
        }
    }

    // Chess board
    ChessBoardView(
        board = uiState.currentBoard,
        flipped = uiState.flippedBoard,
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(8.dp))
    )

    // Controls
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 16.dp),
        horizontalArrangement = Arrangement.Center,
        verticalAlignment = Alignment.CenterVertically
    ) {
        ControlButton("⏮") { viewModel.goToStart() }
        Spacer(modifier = Modifier.width(8.dp))
        ControlButton("◀") { viewModel.prevMove() }
        Spacer(modifier = Modifier.width(8.dp))
        ControlButton(if (uiState.isPlaying) "⏸" else "▶") { viewModel.toggleAutoPlay() }
        Spacer(modifier = Modifier.width(8.dp))
        ControlButton("▶") { viewModel.nextMove() }
        Spacer(modifier = Modifier.width(8.dp))
        ControlButton("⏭") { viewModel.goToEnd() }
        Spacer(modifier = Modifier.width(16.dp))
        ControlButton("↻") { viewModel.flipBoard() }
    }

    // Move counter
    Text(
        text = "Move: ${uiState.currentMoveIndex + 1} / ${uiState.moves.size}",
        color = MaterialTheme.colorScheme.onSurfaceVariant,
        modifier = Modifier
            .fillMaxWidth()
            .padding(bottom = 8.dp),
        textAlign = TextAlign.Center
    )

    // Speed selector
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.Center,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = "Speed:",
            color = MaterialTheme.colorScheme.onSurfaceVariant,
            fontSize = 14.sp
        )
        Spacer(modifier = Modifier.width(8.dp))
        SpeedDropdown(
            currentSpeed = uiState.playSpeed,
            onSpeedChange = { viewModel.setPlaySpeed(it) }
        )
    }

    Spacer(modifier = Modifier.height(16.dp))

    // Moves list and analysis panel
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        // Moves list
        Card(
            colors = CardDefaults.cardColors(
                containerColor = MaterialTheme.colorScheme.surface
            ),
            modifier = Modifier
                .weight(1f)
                .height(200.dp)
        ) {
            Column(modifier = Modifier.padding(12.dp)) {
                Text(
                    text = "Moves",
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                    fontWeight = FontWeight.Medium,
                    modifier = Modifier.padding(bottom = 8.dp)
                )
                MovesList(
                    moves = uiState.moves,
                    currentMoveIndex = uiState.currentMoveIndex,
                    onMoveClick = { viewModel.goToMove(it) }
                )
            }
        }

        // Analysis panel
        AnalysisPanel(
            uiState = uiState,
            onToggleAnalysis = { viewModel.setAnalysisEnabled(it) },
            onDepthChange = { viewModel.setAnalysisDepth(it) },
            modifier = Modifier.width(160.dp)
        )
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

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun SpeedDropdown(
    currentSpeed: Long,
    onSpeedChange: (Long) -> Unit
) {
    var expanded by remember { mutableStateOf(false) }

    val speeds = listOf(
        2000L to "Slow",
        1000L to "Normal",
        500L to "Fast",
        250L to "Very Fast"
    )

    val currentLabel = speeds.find { it.first == currentSpeed }?.second ?: "Normal"

    ExposedDropdownMenuBox(
        expanded = expanded,
        onExpandedChange = { expanded = it }
    ) {
        OutlinedTextField(
            value = currentLabel,
            onValueChange = {},
            readOnly = true,
            modifier = Modifier
                .menuAnchor()
                .width(120.dp),
            trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = expanded) },
            colors = OutlinedTextFieldDefaults.colors(
                unfocusedBorderColor = Color(0xFF333333)
            )
        )

        ExposedDropdownMenu(
            expanded = expanded,
            onDismissRequest = { expanded = false }
        ) {
            speeds.forEach { (speed, label) ->
                DropdownMenuItem(
                    text = { Text(label) },
                    onClick = {
                        onSpeedChange(speed)
                        expanded = false
                    }
                )
            }
        }
    }
}

@Composable
private fun MovesList(
    moves: List<String>,
    currentMoveIndex: Int,
    onMoveClick: (Int) -> Unit
) {
    val listState = rememberLazyListState()
    val scope = rememberCoroutineScope()

    // Auto-scroll to current move
    LaunchedEffect(currentMoveIndex) {
        if (currentMoveIndex >= 0) {
            scope.launch {
                listState.animateScrollToItem(currentMoveIndex / 2)
            }
        }
    }

    LazyColumn(
        state = listState,
        modifier = Modifier.fillMaxSize()
    ) {
        val movePairs = moves.chunked(2)

        itemsIndexed(movePairs) { pairIndex, pair ->
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
                    fontSize = 13.sp,
                    modifier = Modifier.width(32.dp)
                )

                // White move
                val whiteIndex = pairIndex * 2
                MoveChip(
                    move = pair[0],
                    isActive = whiteIndex == currentMoveIndex,
                    onClick = { onMoveClick(whiteIndex) },
                    modifier = Modifier.weight(1f)
                )

                // Black move
                if (pair.size > 1) {
                    Spacer(modifier = Modifier.width(4.dp))
                    val blackIndex = pairIndex * 2 + 1
                    MoveChip(
                        move = pair[1],
                        isActive = blackIndex == currentMoveIndex,
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
    move: String,
    isActive: Boolean,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Box(
        modifier = modifier
            .clip(RoundedCornerShape(4.dp))
            .background(
                if (isActive) MaterialTheme.colorScheme.primary
                else Color.Transparent
            )
            .clickable(onClick = onClick)
            .padding(horizontal = 8.dp, vertical = 4.dp)
    ) {
        Text(
            text = move,
            fontFamily = FontFamily.Monospace,
            fontSize = 13.sp,
            color = if (isActive) Color.White else MaterialTheme.colorScheme.onSurface
        )
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun AnalysisPanel(
    uiState: GameUiState,
    onToggleAnalysis: (Boolean) -> Unit,
    onDepthChange: (Int) -> Unit,
    modifier: Modifier = Modifier
) {
    Card(
        colors = CardDefaults.cardColors(
            containerColor = MaterialTheme.colorScheme.surface
        ),
        modifier = modifier.height(200.dp)
    ) {
        Column(modifier = Modifier.padding(12.dp)) {
            // Header
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "Stockfish",
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                    fontWeight = FontWeight.Medium,
                    fontSize = 14.sp
                )

                Switch(
                    checked = uiState.analysisEnabled,
                    onCheckedChange = onToggleAnalysis,
                    modifier = Modifier.height(24.dp)
                )
            }

            Spacer(modifier = Modifier.height(8.dp))

            if (!uiState.analysisEnabled) {
                Box(
                    modifier = Modifier.fillMaxSize(),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = "Analysis disabled",
                        color = Color(0xFF666666),
                        fontSize = 13.sp
                    )
                }
            } else if (!uiState.stockfishReady) {
                Box(
                    modifier = Modifier.fillMaxSize(),
                    contentAlignment = Alignment.Center
                ) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        CircularProgressIndicator(
                            modifier = Modifier.size(24.dp),
                            strokeWidth = 2.dp
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Loading engine...",
                            color = MaterialTheme.colorScheme.onSurfaceVariant,
                            fontSize = 12.sp
                        )
                    }
                }
            } else {
                val result = uiState.analysisResult
                val turn = uiState.currentBoard.getTurn()

                // Evaluation score
                val displayScore = if (result != null) {
                    val adjustedScore = if (turn == PieceColor.BLACK) -result.score else result.score
                    if (result.isMate) {
                        val mateValue = if (turn == PieceColor.BLACK) -result.mateIn else result.mateIn
                        if (mateValue > 0) "M$mateValue" else "M${kotlin.math.abs(mateValue)}"
                    } else {
                        if (adjustedScore >= 0) "+%.1f".format(adjustedScore)
                        else "%.1f".format(adjustedScore)
                    }
                } else "..."

                Text(
                    text = displayScore,
                    fontSize = 22.sp,
                    fontWeight = FontWeight.Bold,
                    color = when {
                        result?.isMate == true -> Color(0xFFFF6B6B)
                        result != null -> {
                            val adjusted = if (turn == PieceColor.BLACK) -result.score else result.score
                            when {
                                adjusted > 0.3f -> Color.White
                                adjusted < -0.3f -> Color(0xFF888888)
                                else -> MaterialTheme.colorScheme.primary
                            }
                        }
                        else -> MaterialTheme.colorScheme.onSurface
                    }
                )

                Spacer(modifier = Modifier.height(4.dp))

                // Depth and best move
                Text(
                    text = "Depth: ${result?.depth ?: 0}",
                    fontSize = 11.sp,
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )

                Text(
                    text = "Best: ${result?.bestMove ?: "-"}",
                    fontSize = 11.sp,
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )

                // PV line
                if (result?.pv?.isNotBlank() == true) {
                    Spacer(modifier = Modifier.height(4.dp))
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .background(Color(0xFF0F1629), RoundedCornerShape(4.dp))
                            .padding(6.dp)
                    ) {
                        Text(
                            text = result.pv,
                            fontSize = 10.sp,
                            fontFamily = FontFamily.Monospace,
                            color = Color(0xFFAAAAAA),
                            maxLines = 2
                        )
                    }
                }
            }
        }
    }
}
