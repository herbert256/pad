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

// Analyse sequence options
enum class AnalyseSequence {
    FORWARDS,   // Analyze from move 1 to end
    BACKWARDS,  // Analyze from end to move 1
    MIXED       // Analyze last 10 moves backwards, then rest forwards from move 1
}

// General analyse settings
data class AnalyseSettings(
    val sequence: AnalyseSequence = AnalyseSequence.BACKWARDS,
    val numberOfRounds: Int = 1,        // 1 or 2 rounds
    val round1TimeMs: Int = 500,        // Time per move for round 1
    val round2TimeMs: Int = 2500        // Time per move for round 2 (only if numberOfRounds = 2)
)

// Settings for auto-analysis stage (time-based)
data class AnalyseStageSettings(
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
    val analyseSettings: AnalyseSettings = AnalyseSettings(),
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
    val autoAnalysisCompleted: Boolean = false,
    val remainingAnalysisMoves: List<Int> = emptyList(),
    val currentAnalysisTimeMs: Int = 1000,
    val currentAnalysisRound: Int = 1,    // Current round (1 or 2)
    // Chess source settings (per source)
    val lichessMaxGames: Int = 10,
    val chessComMaxGames: Int = 10,
    val lastSource: ChessSource = ChessSource.LICHESS
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

    val savedLichessUsername: String
        get() = prefs.getString(KEY_LICHESS_USERNAME, "") ?: ""

    val savedChessComUsername: String
        get() = prefs.getString(KEY_CHESSCOM_USERNAME, "") ?: ""

    companion object {
        private const val PREFS_NAME = "chess_replay_prefs"
        // Per-source settings
        private const val KEY_LICHESS_USERNAME = "lichess_username"
        private const val KEY_LICHESS_MAX_GAMES = "lichess_max_games"
        private const val KEY_CHESSCOM_USERNAME = "chesscom_username"
        private const val KEY_CHESSCOM_MAX_GAMES = "chesscom_max_games"
        private const val KEY_LAST_SOURCE = "last_source"
        // Analyse stage settings
        private const val KEY_ANALYSE_THREADS = "analyse_threads"
        private const val KEY_ANALYSE_HASH = "analyse_hash"
        // Manual stage settings
        private const val KEY_MANUAL_DEPTH = "manual_depth"
        private const val KEY_MANUAL_THREADS = "manual_threads"
        private const val KEY_MANUAL_HASH = "manual_hash"
        private const val KEY_MANUAL_MULTIPV = "manual_multipv"
        // General analyse settings
        private const val KEY_ANALYSE_SEQUENCE = "analyse_sequence"
        private const val KEY_ANALYSE_NUM_ROUNDS = "analyse_num_rounds"
        private const val KEY_ANALYSE_ROUND1_TIME = "analyse_round1_time"
        private const val KEY_ANALYSE_ROUND2_TIME = "analyse_round2_time"
    }

    private fun loadStockfishSettings(): StockfishSettings {
        return StockfishSettings(
            analyseStage = AnalyseStageSettings(
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
            .putInt(KEY_ANALYSE_THREADS, settings.analyseStage.threads)
            .putInt(KEY_ANALYSE_HASH, settings.analyseStage.hashMb)
            // Manual stage
            .putInt(KEY_MANUAL_DEPTH, settings.manualStage.depth)
            .putInt(KEY_MANUAL_THREADS, settings.manualStage.threads)
            .putInt(KEY_MANUAL_HASH, settings.manualStage.hashMb)
            .putInt(KEY_MANUAL_MULTIPV, settings.manualStage.multiPv)
            .apply()
    }

    private fun loadAnalyseSettings(): AnalyseSettings {
        val sequenceOrdinal = prefs.getInt(KEY_ANALYSE_SEQUENCE, AnalyseSequence.BACKWARDS.ordinal)
        val sequence = AnalyseSequence.entries.getOrNull(sequenceOrdinal) ?: AnalyseSequence.BACKWARDS
        return AnalyseSettings(
            sequence = sequence,
            numberOfRounds = prefs.getInt(KEY_ANALYSE_NUM_ROUNDS, 1),
            round1TimeMs = prefs.getInt(KEY_ANALYSE_ROUND1_TIME, 500),
            round2TimeMs = prefs.getInt(KEY_ANALYSE_ROUND2_TIME, 2500)
        )
    }

    private fun saveAnalyseSettings(settings: AnalyseSettings) {
        prefs.edit()
            .putInt(KEY_ANALYSE_SEQUENCE, settings.sequence.ordinal)
            .putInt(KEY_ANALYSE_NUM_ROUNDS, settings.numberOfRounds)
            .putInt(KEY_ANALYSE_ROUND1_TIME, settings.round1TimeMs)
            .putInt(KEY_ANALYSE_ROUND2_TIME, settings.round2TimeMs)
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
        val analyseSettings = loadAnalyseSettings()
        val lastSource = loadLastSource()
        val lichessMaxGames = prefs.getInt(KEY_LICHESS_MAX_GAMES, 10)
        val chessComMaxGames = prefs.getInt(KEY_CHESSCOM_MAX_GAMES, 10)
        _uiState.value = _uiState.value.copy(
            stockfishSettings = settings,
            analyseSettings = analyseSettings,
            lastSource = lastSource,
            lichessMaxGames = lichessMaxGames,
            chessComMaxGames = chessComMaxGames
        )

        // Initialize Stockfish with manual stage settings (default)
        viewModelScope.launch {
            val ready = stockfish.initialize()
            if (ready) {
                configureForManualStage()
            }
            _uiState.value = _uiState.value.copy(stockfishReady = ready)

            // Auto-load the last user's most recent game and start analysis
            if (ready) {
                autoLoadLastGame()
            }
        }

        // Observe analysis results
        viewModelScope.launch {
            stockfish.analysisResult.collect { result ->
                _uiState.value = _uiState.value.copy(analysisResult = result)
            }
        }
    }

    /**
     * Automatically load the last user's most recent game and start analysis.
     * Called on app startup if there's a saved username for the last used source.
     */
    private suspend fun autoLoadLastGame() {
        val lastSource = _uiState.value.lastSource
        val username = when (lastSource) {
            ChessSource.LICHESS -> savedLichessUsername
            ChessSource.CHESS_COM -> savedChessComUsername
        }
        if (username.isBlank()) return

        _uiState.value = _uiState.value.copy(
            isLoading = true,
            errorMessage = null
        )

        // Fetch only 1 game (the most recent)
        when (val result = repository.getRecentGames(username, lastSource, 1)) {
            is Result.Success -> {
                val games = result.data
                if (games.isNotEmpty()) {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        gameList = games,
                        showGameSelection = false
                    )
                    loadGame(games.first())
                    // Start analysis automatically after a short delay to let the game load
                    kotlinx.coroutines.delay(100)
                    startAutoAnalysis(startRound = 1)
                } else {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        errorMessage = null // No error, just no game to auto-load
                    )
                }
            }
            is Result.Error -> {
                // Don't show error on auto-load failure, just continue to manual mode
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    errorMessage = null
                )
            }
        }
    }

    private fun loadLastSource(): ChessSource {
        val sourceOrdinal = prefs.getInt(KEY_LAST_SOURCE, ChessSource.LICHESS.ordinal)
        return ChessSource.entries.getOrNull(sourceOrdinal) ?: ChessSource.LICHESS
    }

    fun setLichessMaxGames(max: Int) {
        val validMax = max.coerceIn(1, 50)
        prefs.edit().putInt(KEY_LICHESS_MAX_GAMES, validMax).apply()
        _uiState.value = _uiState.value.copy(lichessMaxGames = validMax)
    }

    fun setChessComMaxGames(max: Int) {
        val validMax = max.coerceIn(1, 50)
        prefs.edit().putInt(KEY_CHESSCOM_MAX_GAMES, validMax).apply()
        _uiState.value = _uiState.value.copy(chessComMaxGames = validMax)
    }

    fun fetchGames(username: String, source: ChessSource, maxGames: Int) {
        // Save the username and source for next time
        when (source) {
            ChessSource.LICHESS -> prefs.edit().putString(KEY_LICHESS_USERNAME, username).apply()
            ChessSource.CHESS_COM -> prefs.edit().putString(KEY_CHESSCOM_USERNAME, username).apply()
        }
        prefs.edit().putInt(KEY_LAST_SOURCE, source.ordinal).apply()
        _uiState.value = _uiState.value.copy(lastSource = source)

        // Cancel any ongoing auto-analysis
        autoAnalysisJob?.cancel()

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
        val searchedUser = when (_uiState.value.lastSource) {
            ChessSource.LICHESS -> savedLichessUsername
            ChessSource.CHESS_COM -> savedChessComUsername
        }.lowercase()
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

    fun updateAnalyseSettings(settings: AnalyseSettings) {
        saveAnalyseSettings(settings)
        _uiState.value = _uiState.value.copy(
            analyseSettings = settings,
            showSettingsDialog = false
        )
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

    private fun buildMoveIndices(): List<Int> {
        val moves = _uiState.value.moves
        val sequence = _uiState.value.analyseSettings.sequence
        return when (sequence) {
            AnalyseSequence.FORWARDS -> (0 until moves.size).toList()
            AnalyseSequence.BACKWARDS -> (moves.size - 1 downTo 0).toList()
            AnalyseSequence.MIXED -> {
                // First: last 10 moves backwards, then: rest forwards from move 1
                val lastTenBackwards = (moves.size - 1 downTo maxOf(0, moves.size - 10)).toList()
                val restForwards = (0 until maxOf(0, moves.size - 10)).toList()
                lastTenBackwards + restForwards
            }
        }
    }

    private fun startAutoAnalysis(
        moveIndicesToAnalyze: List<Int>? = null,
        analysisTimeMs: Int? = null,
        startRound: Int = 1
    ) {
        if (!_uiState.value.stockfishReady) return

        // Cancel any previous auto-analysis
        autoAnalysisJob?.cancel()

        // Configure Stockfish for analyse stage
        configureForAnalyseStage()

        autoAnalysisJob = viewModelScope.launch {
            val moves = _uiState.value.moves
            if (moves.isEmpty()) return@launch

            val analyseSettings = _uiState.value.analyseSettings
            val numberOfRounds = analyseSettings.numberOfRounds

            // Determine which round we're starting from
            var currentRound = startRound

            // Loop through rounds
            while (currentRound <= numberOfRounds) {
                // Get time for current round
                val timePerMove = if (currentRound == 1) {
                    analysisTimeMs ?: analyseSettings.round1TimeMs
                } else {
                    analyseSettings.round2TimeMs
                }

                // Build or use provided move indices
                val moveIndices = if (currentRound == startRound && moveIndicesToAnalyze != null) {
                    moveIndicesToAnalyze
                } else {
                    buildMoveIndices()
                }

                _uiState.value = _uiState.value.copy(
                    isAutoAnalyzing = true,
                    autoAnalysisCurrentScore = null,
                    autoAnalysisCompleted = false,
                    remainingAnalysisMoves = moveIndices,
                    currentAnalysisTimeMs = timePerMove,
                    currentAnalysisRound = currentRound
                )

                // Analyze each position in the determined order
                val remainingMoves = moveIndices.toMutableList()
                for (moveIndex in moveIndices) {
                    // Get the board position after this move
                    val board = boardHistory.getOrNull(moveIndex + 1) ?: continue

                    // Update remaining moves (remove the one we're about to analyze)
                    remainingMoves.remove(moveIndex)

                    // Update board position and current analysis index, clear previous analysis result
                    _uiState.value = _uiState.value.copy(
                        autoAnalysisIndex = moveIndex,
                        currentBoard = board,
                        currentMoveIndex = moveIndex,
                        autoAnalysisCurrentScore = null,
                        analysisResult = null,  // Clear previous result to prevent stale data
                        remainingAnalysisMoves = remainingMoves.toList()
                    )

                    val fen = board.getFen()

                    // Start analysis with time limit
                    stockfish.analyzeWithTime(fen, timePerMove)

                    // Wait a bit for analysis to start and produce results
                    delay(100)

                    // Wait for analysis to complete
                    delay(timePerMove.toLong())

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

                // Round completed
                currentRound++

                // If there's another round, stop and restart Stockfish
                if (currentRound <= numberOfRounds) {
                    stockfish.stop()
                    delay(100) // Brief pause
                    configureForAnalyseStage() // Reconfigure for next round
                }
            }

            _uiState.value = _uiState.value.copy(
                isAutoAnalyzing = false,
                autoAnalysisIndex = -1,
                autoAnalysisCurrentScore = null,
                autoAnalysisCompleted = true,
                remainingAnalysisMoves = emptyList(),
                currentAnalysisRound = 1
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
            // Keep remainingAnalysisMoves, currentAnalysisRound and autoAnalysisCompleted for potential resume
        )
        // Configure for manual stage
        configureForManualStage()
    }

    /**
     * Toggle between Analyse stage and Manual stage.
     * When switching to Analyse:
     * - If previous analysis was incomplete: resume where it left off (same round)
     * - If previous analysis was complete: restart from round 1
     */
    fun toggleStage() {
        if (_uiState.value.isAutoAnalyzing) {
            // Currently in Analyse stage -> switch to Manual stage
            stopAutoAnalysis()
            analyzeCurrentPosition()
        } else {
            // Currently in Manual stage -> switch to Analyse stage
            val remainingMoves = _uiState.value.remainingAnalysisMoves
            val wasCompleted = _uiState.value.autoAnalysisCompleted
            val currentRound = _uiState.value.currentAnalysisRound

            if (!wasCompleted && remainingMoves.isNotEmpty()) {
                // Previous analysis was not finished -> resume where it left off
                val currentTime = _uiState.value.currentAnalysisTimeMs
                startAutoAnalysis(remainingMoves, currentTime, currentRound)
            } else {
                // Previous analysis was finished -> start fresh from round 1
                _uiState.value = _uiState.value.copy(moveScores = emptyMap())
                startAutoAnalysis(startRound = 1)
            }
        }
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
