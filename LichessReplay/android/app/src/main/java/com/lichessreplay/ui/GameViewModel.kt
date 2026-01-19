package com.lichessreplay.ui

import android.app.Application
import android.content.Context
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.lichessreplay.chess.ChessBoard
import com.lichessreplay.chess.PgnParser
import com.lichessreplay.data.ChessRepository
import com.lichessreplay.data.ChessSource
import com.lichessreplay.data.LichessGame
import com.lichessreplay.data.Result
import com.lichessreplay.stockfish.AnalysisResult
import com.lichessreplay.stockfish.StockfishEngine
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow

// Settings for auto-analysis stage (time-based)
data class AnalyseStageSettings(
    val analysisTimeMs: Int = 1000,  // Time per move during auto-analysis (ms)
    val threads: Int = 1,
    val hashMb: Int = 64
)

// Settings for manual replay stage (depth-based)
data class ManualStageSettings(
    val depth: Int = 16,
    val threads: Int = 1,
    val hashMb: Int = 64,
    val multiPv: Int = 1
)

// Combined settings for backward compatibility
data class StockfishSettings(
    val analyseStage: AnalyseStageSettings = AnalyseStageSettings(),
    val manualStage: ManualStageSettings = ManualStageSettings()
)

data class MoveScore(
    val score: Float,
    val isMate: Boolean,
    val mateIn: Int,
    val depth: Int = 0,
    val nodes: Long = 0
)

data class MoveDetails(
    val san: String,
    val from: String,
    val to: String,
    val isCapture: Boolean,
    val pieceType: String // K, Q, R, B, N, P
)

data class GameUiState(
    val isLoading: Boolean = false,
    val errorMessage: String? = null,
    // Game list for selection
    val gameList: List<LichessGame> = emptyList(),
    val showGameSelection: Boolean = false,
    // Currently loaded game
    val game: LichessGame? = null,
    val currentBoard: ChessBoard = ChessBoard(),
    val moves: List<String> = emptyList(),
    val moveDetails: List<MoveDetails> = emptyList(),
    val currentMoveIndex: Int = -1,
    val analysisEnabled: Boolean = true,
    val analysisResult: AnalysisResult? = null,
    val stockfishReady: Boolean = false,
    val flippedBoard: Boolean = false,
    val stockfishSettings: StockfishSettings = StockfishSettings(),
    val showSettingsDialog: Boolean = false,
    // Exploring line state
    val isExploringLine: Boolean = false,
    val exploringLineMoves: List<String> = emptyList(),
    val exploringLineMoveIndex: Int = -1,
    val savedGameMoveIndex: Int = -1,
    // Auto-analysis state
    val isAutoAnalyzing: Boolean = false,
    val autoAnalysisIndex: Int = -1,
    val moveScores: Map<Int, MoveScore> = emptyMap(),
    val autoAnalysisCurrentScore: MoveScore? = null,
    // Chess source settings
    val chessSource: ChessSource = ChessSource.LICHESS,
    val maxGames: Int = 10
)

class GameViewModel(application: Application) : AndroidViewModel(application) {
    private val repository = ChessRepository()
    private val stockfish = StockfishEngine(application)
    private val prefs = application.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)

    private val _uiState = MutableStateFlow(GameUiState())
    val uiState: StateFlow<GameUiState> = _uiState.asStateFlow()

    private var boardHistory = mutableListOf<ChessBoard>()
    private var exploringLineHistory = mutableListOf<ChessBoard>()
    private var autoAnalysisJob: Job? = null

    val savedUsername: String
        get() = prefs.getString(KEY_USERNAME, "") ?: ""

    companion object {
        private const val PREFS_NAME = "chess_replay_prefs"
        private const val KEY_USERNAME = "last_username"
        // Analyse stage settings
        private const val KEY_ANALYSE_TIME = "analyse_time_ms"
        private const val KEY_ANALYSE_THREADS = "analyse_threads"
        private const val KEY_ANALYSE_HASH = "analyse_hash"
        // Manual stage settings
        private const val KEY_MANUAL_DEPTH = "manual_depth"
        private const val KEY_MANUAL_THREADS = "manual_threads"
        private const val KEY_MANUAL_HASH = "manual_hash"
        private const val KEY_MANUAL_MULTIPV = "manual_multipv"
        // Other settings
        private const val KEY_CHESS_SOURCE = "chess_source"
        private const val KEY_MAX_GAMES = "max_games"
    }

    private fun loadStockfishSettings(): StockfishSettings {
        return StockfishSettings(
            analyseStage = AnalyseStageSettings(
                analysisTimeMs = prefs.getInt(KEY_ANALYSE_TIME, 1000),
                threads = prefs.getInt(KEY_ANALYSE_THREADS, 1),
                hashMb = prefs.getInt(KEY_ANALYSE_HASH, 64)
            ),
            manualStage = ManualStageSettings(
                depth = prefs.getInt(KEY_MANUAL_DEPTH, 16),
                threads = prefs.getInt(KEY_MANUAL_THREADS, 1),
                hashMb = prefs.getInt(KEY_MANUAL_HASH, 64),
                multiPv = prefs.getInt(KEY_MANUAL_MULTIPV, 1)
            )
        )
    }

    private fun saveStockfishSettings(settings: StockfishSettings) {
        prefs.edit()
            // Analyse stage
            .putInt(KEY_ANALYSE_TIME, settings.analyseStage.analysisTimeMs)
            .putInt(KEY_ANALYSE_THREADS, settings.analyseStage.threads)
            .putInt(KEY_ANALYSE_HASH, settings.analyseStage.hashMb)
            // Manual stage
            .putInt(KEY_MANUAL_DEPTH, settings.manualStage.depth)
            .putInt(KEY_MANUAL_THREADS, settings.manualStage.threads)
            .putInt(KEY_MANUAL_HASH, settings.manualStage.hashMb)
            .putInt(KEY_MANUAL_MULTIPV, settings.manualStage.multiPv)
            .apply()
    }

    private fun configureForAnalyseStage() {
        val settings = _uiState.value.stockfishSettings.analyseStage
        stockfish.configure(settings.threads, settings.hashMb, 1) // MultiPV=1 for analyse stage
    }

    private fun configureForManualStage() {
        val settings = _uiState.value.stockfishSettings.manualStage
        stockfish.configure(settings.threads, settings.hashMb, settings.multiPv)
    }

    init {
        // Load saved settings
        val settings = loadStockfishSettings()
        val chessSource = loadChessSource()
        val maxGames = prefs.getInt(KEY_MAX_GAMES, 10)
        _uiState.value = _uiState.value.copy(
            stockfishSettings = settings,
            chessSource = chessSource,
            maxGames = maxGames
        )

        // Initialize Stockfish with manual stage settings (default)
        viewModelScope.launch {
            val ready = stockfish.initialize()
            if (ready) {
                configureForManualStage()
            }
            _uiState.value = _uiState.value.copy(stockfishReady = ready)
        }

        // Observe analysis results
        viewModelScope.launch {
            stockfish.analysisResult.collect { result ->
                _uiState.value = _uiState.value.copy(analysisResult = result)
            }
        }
    }

    private fun loadChessSource(): ChessSource {
        val sourceOrdinal = prefs.getInt(KEY_CHESS_SOURCE, ChessSource.LICHESS.ordinal)
        return ChessSource.entries.getOrNull(sourceOrdinal) ?: ChessSource.LICHESS
    }

    fun setChessSource(source: ChessSource) {
        prefs.edit().putInt(KEY_CHESS_SOURCE, source.ordinal).apply()
        _uiState.value = _uiState.value.copy(chessSource = source)
    }

    fun setMaxGames(max: Int) {
        val validMax = max.coerceIn(1, 50)
        prefs.edit().putInt(KEY_MAX_GAMES, validMax).apply()
        _uiState.value = _uiState.value.copy(maxGames = validMax)
    }

    fun fetchGames(username: String) {
        // Save the username for next time
        prefs.edit().putString(KEY_USERNAME, username).apply()

        // Cancel any ongoing auto-analysis
        autoAnalysisJob?.cancel()

        val source = _uiState.value.chessSource
        val maxGames = _uiState.value.maxGames

        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(
                isLoading = true,
                errorMessage = null,
                game = null,
                gameList = emptyList(),
                showGameSelection = false
            )

            when (val result = repository.getRecentGames(username, source, maxGames)) {
                is Result.Success -> {
                    val games = result.data
                    if (games.size == 1) {
                        // Auto-select if only 1 game
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            gameList = games,
                            showGameSelection = false
                        )
                        loadGame(games.first())
                    } else {
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            gameList = games,
                            showGameSelection = true
                        )
                    }
                }
                is Result.Error -> {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        errorMessage = result.message
                    )
                }
            }
        }
    }

    fun selectGame(game: LichessGame) {
        _uiState.value = _uiState.value.copy(showGameSelection = false)
        loadGame(game)
    }

    fun dismissGameSelection() {
        _uiState.value = _uiState.value.copy(showGameSelection = false)
    }

    fun clearGame() {
        // Stop any ongoing auto-analysis
        autoAnalysisJob?.cancel()
        stockfish.stop()

        // Clear game state and return to search screen
        boardHistory.clear()
        exploringLineHistory.clear()
        _uiState.value = _uiState.value.copy(
            game = null,
            gameList = emptyList(),
            showGameSelection = false,
            currentBoard = ChessBoard(),
            moves = emptyList(),
            moveDetails = emptyList(),
            currentMoveIndex = -1,
            analysisResult = null,
            flippedBoard = false,
            isExploringLine = false,
            exploringLineMoves = emptyList(),
            exploringLineMoveIndex = -1,
            savedGameMoveIndex = -1,
            moveScores = emptyMap(),
            isAutoAnalyzing = false,
            autoAnalysisIndex = -1
        )
    }

    private fun loadGame(game: LichessGame) {
        val pgn = game.pgn
        if (pgn == null) {
            _uiState.value = _uiState.value.copy(
                isLoading = false,
                errorMessage = "No PGN data available"
            )
            return
        }

        val moves = PgnParser.parseMoves(pgn)
        val initialBoard = ChessBoard()
        boardHistory.clear()
        exploringLineHistory.clear()
        boardHistory.add(initialBoard.copy())

        // Pre-compute all board positions and move details for efficient navigation
        val tempBoard = ChessBoard()
        val moveDetailsList = mutableListOf<MoveDetails>()

        for (move in moves) {
            // Check if this move is a capture (target square has a piece before the move)
            val boardBeforeMove = tempBoard.copy()
            tempBoard.makeMove(move)
            boardHistory.add(tempBoard.copy())

            // Get move details from the board's last move
            val lastMove = tempBoard.getLastMove()
            if (lastMove != null) {
                val fromSquare = lastMove.from.toAlgebraic()
                val toSquare = lastMove.to.toAlgebraic()
                val capturedPiece = boardBeforeMove.getPiece(lastMove.to)
                val movedPiece = tempBoard.getPiece(lastMove.to)
                val pieceType = when (movedPiece?.type) {
                    com.lichessreplay.chess.PieceType.KING -> "K"
                    com.lichessreplay.chess.PieceType.QUEEN -> "Q"
                    com.lichessreplay.chess.PieceType.ROOK -> "R"
                    com.lichessreplay.chess.PieceType.BISHOP -> "B"
                    com.lichessreplay.chess.PieceType.KNIGHT -> "N"
                    com.lichessreplay.chess.PieceType.PAWN -> "P"
                    else -> "P"
                }
                // Check for en passant capture (pawn capture but no piece on target square)
                val isEnPassant = pieceType == "P" &&
                    lastMove.from.file != lastMove.to.file &&
                    capturedPiece == null
                val isCapture = capturedPiece != null || isEnPassant

                moveDetailsList.add(MoveDetails(
                    san = move,
                    from = fromSquare,
                    to = toSquare,
                    isCapture = isCapture,
                    pieceType = pieceType
                ))
            }
        }

        // Flip board if the searched user played black
        val searchedUser = savedUsername.lowercase()
        val blackPlayerName = game.players.black.user?.name?.lowercase() ?: ""
        val userPlayedBlack = searchedUser.isNotEmpty() && searchedUser == blackPlayerName

        _uiState.value = _uiState.value.copy(
            isLoading = false,
            game = game,
            moves = moves,
            moveDetails = moveDetailsList,
            currentBoard = initialBoard,
            currentMoveIndex = -1,
            flippedBoard = userPlayedBlack,
            // Reset exploring state
            isExploringLine = false,
            exploringLineMoves = emptyList(),
            exploringLineMoveIndex = -1,
            savedGameMoveIndex = -1,
            // Reset auto-analysis state
            moveScores = emptyMap(),
            isAutoAnalyzing = false,
            autoAnalysisIndex = -1
        )

        analyzeCurrentPosition()

        // Start auto-analysis of all moves
        startAutoAnalysis()
    }

    fun goToStart() {
        // Stop auto-analysis when user manually navigates
        if (_uiState.value.isAutoAnalyzing) {
            stopAutoAnalysis()
        }

        if (_uiState.value.isExploringLine) {
            _uiState.value = _uiState.value.copy(
                currentBoard = exploringLineHistory.firstOrNull() ?: ChessBoard(),
                exploringLineMoveIndex = -1
            )
        } else {
            _uiState.value = _uiState.value.copy(
                currentBoard = boardHistory.firstOrNull() ?: ChessBoard(),
                currentMoveIndex = -1
            )
        }
        analyzeCurrentPosition()
    }

    fun goToEnd() {
        // Stop auto-analysis when user manually navigates
        if (_uiState.value.isAutoAnalyzing) {
            stopAutoAnalysis()
        }

        if (_uiState.value.isExploringLine) {
            val moves = _uiState.value.exploringLineMoves
            if (moves.isEmpty()) {
                analyzeCurrentPosition()
                return
            }
            _uiState.value = _uiState.value.copy(
                currentBoard = exploringLineHistory.lastOrNull() ?: ChessBoard(),
                exploringLineMoveIndex = moves.size - 1
            )
        } else {
            val moves = _uiState.value.moves
            if (moves.isEmpty()) {
                analyzeCurrentPosition()
                return
            }
            _uiState.value = _uiState.value.copy(
                currentBoard = boardHistory.lastOrNull() ?: ChessBoard(),
                currentMoveIndex = moves.size - 1
            )
        }
        analyzeCurrentPosition()
    }

    fun goToMove(index: Int) {
        // Stop auto-analysis when user manually navigates
        if (_uiState.value.isAutoAnalyzing) {
            stopAutoAnalysis()
        }

        if (_uiState.value.isExploringLine) {
            val moves = _uiState.value.exploringLineMoves
            if (index < -1 || index >= moves.size) return
            _uiState.value = _uiState.value.copy(
                currentBoard = exploringLineHistory.getOrNull(index + 1) ?: ChessBoard(),
                exploringLineMoveIndex = index
            )
        } else {
            val moves = _uiState.value.moves
            if (index < -1 || index >= moves.size) return
            _uiState.value = _uiState.value.copy(
                currentBoard = boardHistory.getOrNull(index + 1) ?: ChessBoard(),
                currentMoveIndex = index
            )
        }
        analyzeCurrentPosition()
    }

    fun nextMove() {
        // Stop auto-analysis when user manually navigates
        if (_uiState.value.isAutoAnalyzing) {
            stopAutoAnalysis()
        }

        if (_uiState.value.isExploringLine) {
            val currentIndex = _uiState.value.exploringLineMoveIndex
            val moves = _uiState.value.exploringLineMoves
            if (currentIndex >= moves.size - 1) return
            val newIndex = currentIndex + 1
            _uiState.value = _uiState.value.copy(
                currentBoard = exploringLineHistory.getOrNull(newIndex + 1) ?: _uiState.value.currentBoard,
                exploringLineMoveIndex = newIndex
            )
        } else {
            val currentIndex = _uiState.value.currentMoveIndex
            val moves = _uiState.value.moves
            if (currentIndex >= moves.size - 1) return
            val newIndex = currentIndex + 1
            _uiState.value = _uiState.value.copy(
                currentBoard = boardHistory.getOrNull(newIndex + 1) ?: _uiState.value.currentBoard,
                currentMoveIndex = newIndex
            )
        }
        analyzeCurrentPosition()
    }

    fun prevMove() {
        // Stop auto-analysis when user manually navigates
        if (_uiState.value.isAutoAnalyzing) {
            stopAutoAnalysis()
        }

        if (_uiState.value.isExploringLine) {
            val currentIndex = _uiState.value.exploringLineMoveIndex
            if (currentIndex < 0) return
            val newIndex = currentIndex - 1
            _uiState.value = _uiState.value.copy(
                currentBoard = exploringLineHistory.getOrNull(newIndex + 1) ?: ChessBoard(),
                exploringLineMoveIndex = newIndex
            )
        } else {
            val currentIndex = _uiState.value.currentMoveIndex
            if (currentIndex < 0) return
            val newIndex = currentIndex - 1
            _uiState.value = _uiState.value.copy(
                currentBoard = boardHistory.getOrNull(newIndex + 1) ?: ChessBoard(),
                currentMoveIndex = newIndex
            )
        }
        analyzeCurrentPosition()
    }

    fun exploreLine(pv: String, moveIndex: Int = 0) {
        if (pv.isBlank()) return

        // Save current game position
        val savedMoveIndex = _uiState.value.currentMoveIndex

        // Get the starting board (current position before exploring)
        val startBoard = _uiState.value.currentBoard.copy()

        // Parse UCI moves and build board history for the line
        val uciMoves = pv.split(" ").filter { it.isNotBlank() }
        exploringLineHistory.clear()
        exploringLineHistory.add(startBoard)

        val tempBoard = startBoard.copy()
        for (uciMove in uciMoves) {
            if (tempBoard.makeUciMove(uciMove)) {
                exploringLineHistory.add(tempBoard.copy())
            } else {
                break // Invalid move, stop here
            }
        }

        // Go to the specified move index
        val targetIndex = moveIndex.coerceIn(-1, exploringLineHistory.size - 2)

        _uiState.value = _uiState.value.copy(
            isExploringLine = true,
            exploringLineMoves = uciMoves.take(exploringLineHistory.size - 1),
            exploringLineMoveIndex = targetIndex,
            savedGameMoveIndex = savedMoveIndex,
            currentBoard = exploringLineHistory.getOrNull(targetIndex + 1) ?: startBoard
        )

        analyzeCurrentPosition()
    }

    fun backToOriginalGame() {
        val savedIndex = _uiState.value.savedGameMoveIndex
        exploringLineHistory.clear()

        _uiState.value = _uiState.value.copy(
            isExploringLine = false,
            exploringLineMoves = emptyList(),
            exploringLineMoveIndex = -1,
            savedGameMoveIndex = -1,
            currentBoard = boardHistory.getOrNull(savedIndex + 1) ?: ChessBoard(),
            currentMoveIndex = savedIndex
        )

        analyzeCurrentPosition()
    }

    fun setAnalysisEnabled(enabled: Boolean) {
        _uiState.value = _uiState.value.copy(analysisEnabled = enabled)
        if (enabled) {
            analyzeCurrentPosition()
        } else {
            stockfish.stop()
        }
    }

    fun flipBoard() {
        _uiState.value = _uiState.value.copy(flippedBoard = !_uiState.value.flippedBoard)
    }

    fun showSettingsDialog() {
        _uiState.value = _uiState.value.copy(showSettingsDialog = true)
    }

    fun hideSettingsDialog() {
        _uiState.value = _uiState.value.copy(showSettingsDialog = false)
    }

    fun updateStockfishSettings(settings: StockfishSettings) {
        saveStockfishSettings(settings)
        _uiState.value = _uiState.value.copy(
            stockfishSettings = settings,
            showSettingsDialog = false
        )
        // Apply new settings to Stockfish based on current stage
        if (_uiState.value.stockfishReady) {
            if (_uiState.value.isAutoAnalyzing) {
                configureForAnalyseStage()
            } else {
                configureForManualStage()
            }
            analyzeCurrentPosition()
        }
    }

    private fun analyzeCurrentPosition() {
        if (!_uiState.value.analysisEnabled || !_uiState.value.stockfishReady) return

        // Clear old analysis result to prevent showing stale data
        _uiState.value = _uiState.value.copy(analysisResult = null)

        val fen = _uiState.value.currentBoard.getFen()
        val depth = _uiState.value.stockfishSettings.manualStage.depth

        // Use depth-based analysis for manual replay (not time-limited)
        stockfish.analyze(fen, depth)
    }

    private fun startAutoAnalysis() {
        if (!_uiState.value.stockfishReady) return

        // Cancel any previous auto-analysis
        autoAnalysisJob?.cancel()

        // Configure Stockfish for analyse stage
        configureForAnalyseStage()

        autoAnalysisJob = viewModelScope.launch {
            val moves = _uiState.value.moves
            if (moves.isEmpty()) return@launch

            _uiState.value = _uiState.value.copy(
                isAutoAnalyzing = true,
                autoAnalysisCurrentScore = null
            )

            // Analyze each position in reverse order (last move first)
            for (moveIndex in (moves.size - 1) downTo 0) {
                // Get the board position after this move
                val board = boardHistory.getOrNull(moveIndex + 1) ?: continue

                // Update board position and current analysis index, clear previous analysis result
                _uiState.value = _uiState.value.copy(
                    autoAnalysisIndex = moveIndex,
                    currentBoard = board,
                    currentMoveIndex = moveIndex,
                    autoAnalysisCurrentScore = null,
                    analysisResult = null  // Clear previous result to prevent stale data
                )

                val fen = board.getFen()
                val analysisTimeMs = _uiState.value.stockfishSettings.analyseStage.analysisTimeMs

                // Start analysis with time limit
                stockfish.analyzeWithTime(fen, analysisTimeMs)

                // Wait a bit for analysis to start and produce results
                delay(100)

                // Wait for analysis to complete
                delay(analysisTimeMs.toLong())

                // Get the current analysis result and store the score
                val result = stockfish.analysisResult.value
                if (result != null) {
                    val bestLine = result.bestLine
                    if (bestLine != null) {
                        // Use the board's turn to determine score adjustment
                        // Stockfish gives score from the side-to-move's perspective
                        // If white to move: score is already from white's POV, keep it
                        // If black to move: score is from black's POV, negate to get white's POV
                        val isWhiteToMove = board.getTurn() == com.lichessreplay.chess.PieceColor.WHITE
                        val adjustedScore = if (isWhiteToMove) bestLine.score else -bestLine.score
                        val adjustedMateIn = if (isWhiteToMove) bestLine.mateIn else -bestLine.mateIn

                        val score = MoveScore(
                            score = adjustedScore,
                            isMate = bestLine.isMate,
                            mateIn = adjustedMateIn,
                            depth = result.depth,
                            nodes = result.nodes
                        )

                        _uiState.value = _uiState.value.copy(
                            moveScores = _uiState.value.moveScores + (moveIndex to score),
                            autoAnalysisCurrentScore = score
                        )
                    }
                }
            }

            _uiState.value = _uiState.value.copy(
                isAutoAnalyzing = false,
                autoAnalysisIndex = -1,
                autoAnalysisCurrentScore = null
            )

            // Configure for manual stage and resume analysis
            configureForManualStage()
            analyzeCurrentPosition()
        }
    }

    fun stopAutoAnalysis() {
        autoAnalysisJob?.cancel()
        stockfish.stop()  // Stop any running time-based analysis
        _uiState.value = _uiState.value.copy(
            isAutoAnalyzing = false,
            autoAnalysisIndex = -1
        )
        // Configure for manual stage
        configureForManualStage()
    }

    /**
     * Make a manual move on the board (from user drag-and-drop).
     * Only allowed during manual replay (not auto-analyzing).
     */
    fun makeManualMove(from: com.lichessreplay.chess.Square, to: com.lichessreplay.chess.Square) {
        // Don't allow moves during auto-analysis
        if (_uiState.value.isAutoAnalyzing) return

        val currentBoard = _uiState.value.currentBoard

        // Check if move is legal
        if (!currentBoard.isLegalMove(from, to)) return

        // Handle pawn promotion - default to queen for simplicity
        val promotion = if (currentBoard.needsPromotion(from, to)) {
            com.lichessreplay.chess.PieceType.QUEEN
        } else null

        // Make a copy of the board and execute the move
        val newBoard = currentBoard.copy()
        if (!newBoard.makeMoveFromSquares(from, to, promotion)) return

        if (_uiState.value.isExploringLine) {
            // Add the new board position to exploring line history
            exploringLineHistory.add(newBoard.copy())
            val newMoveIndex = _uiState.value.exploringLineMoveIndex + 1
            val uciMove = from.toAlgebraic() + to.toAlgebraic() + (promotion?.let {
                when (it) {
                    com.lichessreplay.chess.PieceType.QUEEN -> "q"
                    com.lichessreplay.chess.PieceType.ROOK -> "r"
                    com.lichessreplay.chess.PieceType.BISHOP -> "b"
                    com.lichessreplay.chess.PieceType.KNIGHT -> "n"
                    else -> ""
                }
            } ?: "")

            _uiState.value = _uiState.value.copy(
                currentBoard = newBoard,
                exploringLineMoves = _uiState.value.exploringLineMoves + uciMove,
                exploringLineMoveIndex = newMoveIndex
            )
        } else {
            // In main game: enter exploring line mode with this move
            exploringLineHistory.clear()
            exploringLineHistory.add(currentBoard.copy()) // Starting position
            exploringLineHistory.add(newBoard.copy())     // After the move

            val uciMove = from.toAlgebraic() + to.toAlgebraic() + (promotion?.let {
                when (it) {
                    com.lichessreplay.chess.PieceType.QUEEN -> "q"
                    com.lichessreplay.chess.PieceType.ROOK -> "r"
                    com.lichessreplay.chess.PieceType.BISHOP -> "b"
                    com.lichessreplay.chess.PieceType.KNIGHT -> "n"
                    else -> ""
                }
            } ?: "")

            _uiState.value = _uiState.value.copy(
                isExploringLine = true,
                exploringLineMoves = listOf(uciMove),
                exploringLineMoveIndex = 0,
                savedGameMoveIndex = _uiState.value.currentMoveIndex,
                currentBoard = newBoard
            )
        }

        // Run Stockfish analysis on the new position
        analyzeCurrentPosition()
    }

    override fun onCleared() {
        super.onCleared()
        autoAnalysisJob?.cancel()
        stockfish.shutdown()
    }
}
