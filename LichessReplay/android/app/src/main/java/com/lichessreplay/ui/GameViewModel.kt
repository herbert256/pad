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
    val numberOfRounds: Int = 2,        // 1 or 2 rounds
    val round1TimeMs: Int = 50,         // Time per move for round 1 (default: 0.05s)
    val round2TimeMs: Int = 1000        // Time per move for round 2 (only if numberOfRounds = 2)
)

// Settings for auto-analysis stage (time-based)
data class AnalyseStageSettings(
    val threads: Int = 1,
    val hashMb: Int = 64
)

// Settings for manual replay stage (depth-based)
data class ManualStageSettings(
    val depth: Int = 24,
    val threads: Int = 4,
    val hashMb: Int = 256,
    val multiPv: Int = 3
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
    val pieceType: String, // K, Q, R, B, N, P
    val clockTime: String? = null  // Format: "H:MM:SS" or "M:SS" or null if not available
)

data class GameUiState(
    val isLoading: Boolean = false,
    val errorMessage: String? = null,
    // Game list for selection
    val gameList: List<LichessGame> = emptyList(),
    val showGameSelection: Boolean = false,
    // Currently loaded game
    val game: LichessGame? = null,
    val openingName: String? = null,  // Extracted from PGN headers
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
    val moveScores: Map<Int, MoveScore> = emptyMap(),        // Round 1 scores
    val moveScoresRound2: Map<Int, MoveScore> = emptyMap(),  // Round 2 scores
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

    // Track settings when dialog opens to detect changes
    private var settingsOnDialogOpen: SettingsSnapshot? = null

    private data class SettingsSnapshot(
        val analyseSettings: AnalyseSettings,
        val analyseStageSettings: AnalyseStageSettings,
        val manualStageSettings: ManualStageSettings
    )

    val savedLichessUsername: String
        get() = prefs.getString(KEY_LICHESS_USERNAME, "DrNykterstein") ?: "DrNykterstein"

    val savedChessComUsername: String
        get() = prefs.getString(KEY_CHESSCOM_USERNAME, "magnuscarlsen") ?: "magnuscarlsen"

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
        // First run tracking - stores the app version code when user first made a choice
        private const val KEY_FIRST_GAME_RETRIEVED_VERSION = "first_game_retrieved_version"
    }

    private fun loadStockfishSettings(): StockfishSettings {
        return StockfishSettings(
            analyseStage = AnalyseStageSettings(
                threads = prefs.getInt(KEY_ANALYSE_THREADS, 4),
                hashMb = prefs.getInt(KEY_ANALYSE_HASH, 256)
            ),
            manualStage = ManualStageSettings(
                depth = prefs.getInt(KEY_MANUAL_DEPTH, 16),
                threads = prefs.getInt(KEY_MANUAL_THREADS, 4),
                hashMb = prefs.getInt(KEY_MANUAL_HASH, 256),
                multiPv = prefs.getInt(KEY_MANUAL_MULTIPV, 3)
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
            numberOfRounds = prefs.getInt(KEY_ANALYSE_NUM_ROUNDS, 2),
            round1TimeMs = prefs.getInt(KEY_ANALYSE_ROUND1_TIME, 50),
            round2TimeMs = prefs.getInt(KEY_ANALYSE_ROUND2_TIME, 1000)
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

    /**
     * Get the current app version code.
     */
    private fun getAppVersionCode(): Long {
        return try {
            val packageInfo = getApplication<Application>().packageManager
                .getPackageInfo(getApplication<Application>().packageName, 0)
            if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.P) {
                packageInfo.longVersionCode
            } else {
                @Suppress("DEPRECATION")
                packageInfo.versionCode.toLong()
            }
        } catch (e: Exception) {
            0L
        }
    }

    /**
     * Check if this is a first run (fresh install or app update).
     * Returns true if user hasn't made a game retrieval choice for this app version.
     */
    private fun isFirstRun(): Boolean {
        val savedVersionCode = prefs.getLong(KEY_FIRST_GAME_RETRIEVED_VERSION, 0L)
        return savedVersionCode != getAppVersionCode()
    }

    /**
     * Mark that the user has made their first game retrieval choice for this app version.
     */
    private fun markFirstRunComplete() {
        prefs.edit().putLong(KEY_FIRST_GAME_RETRIEVED_VERSION, getAppVersionCode()).apply()
    }

    /**
     * Reset all settings to their default values.
     * Called on first run after fresh install or app update.
     */
    private fun resetSettingsToDefaults() {
        prefs.edit()
            // Clear all settings (except version tracking)
            .remove(KEY_ANALYSE_THREADS)
            .remove(KEY_ANALYSE_HASH)
            .remove(KEY_MANUAL_DEPTH)
            .remove(KEY_MANUAL_THREADS)
            .remove(KEY_MANUAL_HASH)
            .remove(KEY_MANUAL_MULTIPV)
            .remove(KEY_ANALYSE_SEQUENCE)
            .remove(KEY_ANALYSE_NUM_ROUNDS)
            .remove(KEY_ANALYSE_ROUND1_TIME)
            .remove(KEY_ANALYSE_ROUND2_TIME)
            .remove(KEY_LICHESS_MAX_GAMES)
            .remove(KEY_CHESSCOM_MAX_GAMES)
            .remove(KEY_LAST_SOURCE)
            .apply()
    }

    init {
        // Reset settings to defaults on first run (fresh install or app update)
        if (isFirstRun()) {
            resetSettingsToDefaults()
        }

        // Load saved settings (will use defaults if reset or not previously set)
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
            // Skip on first run (after install or update) - user must make a choice first
            if (ready && !isFirstRun()) {
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
                    // loadGame() already calls startAutoAnalysis()
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

    /**
     * Reload the last game from the active chess server.
     * Called when user clicks the reload button.
     */
    fun reloadLastGame() {
        viewModelScope.launch {
            autoLoadLastGame()
        }
    }

    fun setLichessMaxGames(max: Int) {
        val validMax = max.coerceIn(1, 25)
        prefs.edit().putInt(KEY_LICHESS_MAX_GAMES, validMax).apply()
        _uiState.value = _uiState.value.copy(lichessMaxGames = validMax)
    }

    fun setChessComMaxGames(max: Int) {
        val validMax = max.coerceIn(1, 25)
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

        // Mark first run complete - user has made their game retrieval choice
        markFirstRunComplete()

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
            moveScoresRound2 = emptyMap(),
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

        // Extract opening name from PGN headers
        val pgnHeaders = PgnParser.parseHeaders(pgn)
        val openingName = pgnHeaders["Opening"] ?: pgnHeaders["ECO"]

        val parsedMoves = PgnParser.parseMovesWithClock(pgn)
        val initialBoard = ChessBoard()
        boardHistory.clear()
        exploringLineHistory.clear()
        boardHistory.add(initialBoard.copy())

        // Pre-compute all board positions and move details for efficient navigation
        val tempBoard = ChessBoard()
        val moveDetailsList = mutableListOf<MoveDetails>()
        val validMoves = mutableListOf<String>()

        for (parsedMove in parsedMoves) {
            val move = parsedMove.san
            // Check if this move is a capture (target square has a piece before the move)
            val boardBeforeMove = tempBoard.copy()
            val moveSuccess = tempBoard.makeMove(move)
            if (!moveSuccess) {
                // Skip invalid moves (e.g., malformed PGN artifacts)
                continue
            }
            validMoves.add(move)
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
                    pieceType = pieceType,
                    clockTime = parsedMove.clockTime
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
            openingName = openingName,
            moves = validMoves,
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
            moveScoresRound2 = emptyMap(),
            isAutoAnalyzing = false,
            autoAnalysisIndex = -1
        )

        // Start auto-analysis of all moves (will analyze current position as part of the run)
        startAutoAnalysis()
    }

    fun goToStart() {
        // Stop auto-analysis when user manually navigates
        if (_uiState.value.isAutoAnalyzing) {
            stopAutoAnalysis()
        }

        if (_uiState.value.isExploringLine) {
            _uiState.value = _uiState.value.copy(
                currentBoard = exploringLineHistory.firstOrNull()?.copy() ?: ChessBoard(),
                exploringLineMoveIndex = -1
            )
        } else {
            _uiState.value = _uiState.value.copy(
                currentBoard = boardHistory.firstOrNull()?.copy() ?: ChessBoard(),
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
                currentBoard = exploringLineHistory.lastOrNull()?.copy() ?: ChessBoard(),
                exploringLineMoveIndex = moves.size - 1
            )
        } else {
            val moves = _uiState.value.moves
            if (moves.isEmpty()) {
                analyzeCurrentPosition()
                return
            }
            _uiState.value = _uiState.value.copy(
                currentBoard = boardHistory.lastOrNull()?.copy() ?: ChessBoard(),
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
                currentBoard = exploringLineHistory.getOrNull(index + 1)?.copy() ?: ChessBoard(),
                exploringLineMoveIndex = index
            )
        } else {
            val moves = _uiState.value.moves
            if (index < -1 || index >= moves.size) return
            _uiState.value = _uiState.value.copy(
                currentBoard = boardHistory.getOrNull(index + 1)?.copy() ?: ChessBoard(),
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
                currentBoard = exploringLineHistory.getOrNull(newIndex + 1)?.copy() ?: _uiState.value.currentBoard,
                exploringLineMoveIndex = newIndex
            )
        } else {
            val currentIndex = _uiState.value.currentMoveIndex
            val moves = _uiState.value.moves
            if (currentIndex >= moves.size - 1) return
            val newIndex = currentIndex + 1
            _uiState.value = _uiState.value.copy(
                currentBoard = boardHistory.getOrNull(newIndex + 1)?.copy() ?: _uiState.value.currentBoard,
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
                currentBoard = exploringLineHistory.getOrNull(newIndex + 1)?.copy() ?: ChessBoard(),
                exploringLineMoveIndex = newIndex
            )
        } else {
            val currentIndex = _uiState.value.currentMoveIndex
            if (currentIndex < 0) return
            val newIndex = currentIndex - 1
            _uiState.value = _uiState.value.copy(
                currentBoard = boardHistory.getOrNull(newIndex + 1)?.copy() ?: ChessBoard(),
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
            currentBoard = exploringLineHistory.getOrNull(targetIndex + 1)?.copy() ?: startBoard
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
            currentBoard = boardHistory.getOrNull(savedIndex + 1)?.copy() ?: ChessBoard(),
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
        // Store current settings to detect changes when dialog closes
        settingsOnDialogOpen = SettingsSnapshot(
            analyseSettings = _uiState.value.analyseSettings,
            analyseStageSettings = _uiState.value.stockfishSettings.analyseStage,
            manualStageSettings = _uiState.value.stockfishSettings.manualStage
        )
        _uiState.value = _uiState.value.copy(showSettingsDialog = true)
    }

    fun hideSettingsDialog() {
        _uiState.value = _uiState.value.copy(showSettingsDialog = false)

        // Check what settings changed
        val originalSettings = settingsOnDialogOpen
        val currentAnalyseSettings = _uiState.value.analyseSettings
        val currentAnalyseStageSettings = _uiState.value.stockfishSettings.analyseStage
        val currentManualStageSettings = _uiState.value.stockfishSettings.manualStage

        val analyseSettingsChanged = originalSettings?.analyseSettings != currentAnalyseSettings
        val analyseStageSettingsChanged = originalSettings?.analyseStageSettings != currentAnalyseStageSettings
        val manualStageSettingsChanged = originalSettings?.manualStageSettings != currentManualStageSettings

        // Clear the snapshot
        settingsOnDialogOpen = null

        // If no game loaded or no settings changed, nothing to do
        if (_uiState.value.game == null) return
        if (!analyseSettingsChanged && !analyseStageSettingsChanged && !manualStageSettingsChanged) return

        viewModelScope.launch {
            // Stop any ongoing analysis
            autoAnalysisJob?.cancel()
            stockfish.stop()

            // Set stockfishReady to false while restarting
            _uiState.value = _uiState.value.copy(stockfishReady = false)

            // Kill and restart Stockfish engine
            val ready = stockfish.restart()

            // Verify Stockfish is truly ready by checking isReady flow
            if (ready) {
                // Wait a moment for the engine to stabilize
                kotlinx.coroutines.delay(200)
                val confirmedReady = stockfish.isReady.value
                _uiState.value = _uiState.value.copy(stockfishReady = confirmedReady)

                if (!confirmedReady) {
                    return@launch
                }
            } else {
                _uiState.value = _uiState.value.copy(stockfishReady = false)
                return@launch
            }

            // Decide which mode to activate based on what changed
            if (analyseSettingsChanged || analyseStageSettingsChanged) {
                // Analyse settings or Stockfish Analyse Stage settings changed
                // -> Start Analyse stage from the beginning
                configureForAnalyseStage()
                _uiState.value = _uiState.value.copy(
                    moveScores = emptyMap(),
                    moveScoresRound2 = emptyMap(),
                    isAutoAnalyzing = false,  // Will be set true by startAutoAnalysis
                    autoAnalysisCompleted = false
                )
                startAutoAnalysis(startRound = 1)
            } else if (manualStageSettingsChanged) {
                // Only Manual stage settings changed
                // -> Switch to Manual stage and show analysis
                if (_uiState.value.isAutoAnalyzing) {
                    _uiState.value = _uiState.value.copy(
                        isAutoAnalyzing = false,
                        autoAnalysisIndex = -1
                    )
                }
                configureForManualStage()
                analyzeCurrentPosition()
            }
        }
    }

    fun updateStockfishSettings(settings: StockfishSettings) {
        saveStockfishSettings(settings)
        _uiState.value = _uiState.value.copy(
            stockfishSettings = settings
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
            analyseSettings = settings
        )
    }

    private var manualAnalysisJob: Job? = null

    private fun analyzeCurrentPosition() {
        if (!_uiState.value.analysisEnabled) return

        // Cancel any previous manual analysis job
        manualAnalysisJob?.cancel()

        // Clear old analysis result
        _uiState.value = _uiState.value.copy(analysisResult = null)

        // If in auto-analyzing mode, just do simple analysis
        if (_uiState.value.isAutoAnalyzing) {
            if (_uiState.value.stockfishReady) {
                val fen = _uiState.value.currentBoard.getFen()
                val depth = _uiState.value.stockfishSettings.manualStage.depth
                stockfish.analyze(fen, depth)
            }
            return
        }

        // In manual stage: ensure Stockfish card is shown
        manualAnalysisJob = viewModelScope.launch {
            ensureStockfishAnalysis()
        }
    }

    /**
     * Ensure Stockfish analysis is running and producing results in manual stage.
     * If no results come back, restart Stockfish and try again.
     */
    private suspend fun ensureStockfishAnalysis() {
        val maxRetries = 2
        var attempt = 0

        while (attempt < maxRetries) {
            // Check if Stockfish is ready, restart if not
            if (!_uiState.value.stockfishReady) {
                val ready = stockfish.restart()
                _uiState.value = _uiState.value.copy(stockfishReady = ready)
                if (!ready) {
                    attempt++
                    continue
                }
                configureForManualStage()
            }

            // Start analysis
            val fen = _uiState.value.currentBoard.getFen()
            val depth = _uiState.value.stockfishSettings.manualStage.depth
            stockfish.analyze(fen, depth)

            // Wait for results (up to 2 seconds)
            var waitTime = 0
            val maxWaitTime = 2000
            val checkInterval = 100L

            while (waitTime < maxWaitTime) {
                delay(checkInterval)
                waitTime += checkInterval.toInt()

                // Check if we got results
                if (_uiState.value.analysisResult != null) {
                    return // Success - analysis is running
                }

                // If we're no longer in manual stage, abort
                if (_uiState.value.isAutoAnalyzing) {
                    return
                }
            }

            // No results after waiting - restart Stockfish and try again
            android.util.Log.w("GameViewModel", "No Stockfish results after ${maxWaitTime}ms, restarting (attempt ${attempt + 1})")

            stockfish.stop()
            _uiState.value = _uiState.value.copy(stockfishReady = false)

            val ready = stockfish.restart()
            _uiState.value = _uiState.value.copy(stockfishReady = ready)

            if (ready) {
                configureForManualStage()
            }

            attempt++
        }

        android.util.Log.e("GameViewModel", "Failed to get Stockfish analysis after $maxRetries attempts")
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
            // Small delay to ensure UCI configuration is processed
            kotlinx.coroutines.delay(50)

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

                // Clear round 1 scores when starting fresh (round 1), keep them when continuing to round 2
                val clearRound1Scores = currentRound == 1 && startRound == 1
                _uiState.value = _uiState.value.copy(
                    isAutoAnalyzing = true,
                    autoAnalysisCurrentScore = null,
                    autoAnalysisCompleted = false,
                    remainingAnalysisMoves = moveIndices,
                    currentAnalysisTimeMs = timePerMove,
                    currentAnalysisRound = currentRound,
                    moveScores = if (clearRound1Scores) emptyMap() else _uiState.value.moveScores,
                    moveScoresRound2 = if (clearRound1Scores) emptyMap() else _uiState.value.moveScoresRound2
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

                    // Wait for analysis to actually complete (not just a fixed delay)
                    // Add extra buffer time for processing
                    val completed = stockfish.waitForCompletion(timePerMove.toLong() + 2000)
                    if (!completed) {
                        stockfish.stop()
                        delay(100)
                    }

                    // Get the current analysis result and store the score
                    val result = stockfish.analysisResult.value
                    if (result != null) {
                        val bestLine = result.bestLine
                        if (bestLine != null) {
                            // Score adjustment: Stockfish gives score from side-to-move's perspective
                            // We want score from WHITE's perspective (positive = good for white)
                            // After white's move (black to move): keep score (Stockfish evaluates for black, but we flip)
                            // After black's move (white to move): negate score
                            val isWhiteToMove = board.getTurn() == com.lichessreplay.chess.PieceColor.WHITE
                            val adjustedScore = if (isWhiteToMove) -bestLine.score else bestLine.score
                            val adjustedMateIn = if (isWhiteToMove) -bestLine.mateIn else bestLine.mateIn

                            val score = MoveScore(
                                score = adjustedScore,
                                isMate = bestLine.isMate,
                                mateIn = adjustedMateIn,
                                depth = result.depth,
                                nodes = result.nodes
                            )

                            // Store score based on current round
                            // Round 1: goes to moveScores (green/red graph)
                            // Round 2: goes to moveScoresRound2 (yellow line)
                            if (currentRound == 1) {
                                _uiState.value = _uiState.value.copy(
                                    moveScores = _uiState.value.moveScores + (moveIndex to score),
                                    autoAnalysisCurrentScore = score
                                )
                            } else {
                                _uiState.value = _uiState.value.copy(
                                    moveScoresRound2 = _uiState.value.moveScoresRound2 + (moveIndex to score),
                                    autoAnalysisCurrentScore = score
                                )
                            }
                        }
                    }
                }

                // Round completed
                currentRound++

                // If there's another round, stop and restart Stockfish
                if (currentRound <= numberOfRounds) {
                    stockfish.stop()
                    delay(100) // Brief pause

                    // Clear round 2 scores for fresh data (round 1 scores stay in moveScores)
                    _uiState.value = _uiState.value.copy(
                        moveScoresRound2 = emptyMap()
                    )

                    configureForAnalyseStage() // Reconfigure for next round
                }
            }

            // Find the move with the biggest score change
            val biggestChangeMoveIndex = findBiggestScoreChangeMove()

            _uiState.value = _uiState.value.copy(
                isAutoAnalyzing = false,
                autoAnalysisIndex = -1,
                autoAnalysisCurrentScore = null,
                autoAnalysisCompleted = true,
                remainingAnalysisMoves = emptyList(),
                currentAnalysisRound = 1
            )

            // Go to the position with the biggest score change
            if (biggestChangeMoveIndex >= 0) {
                goToMove(biggestChangeMoveIndex)
            }

            // Configure for manual stage and resume analysis
            configureForManualStage()
            analyzeCurrentPosition()
        }
    }

    /**
     * Find the move index with the biggest score change compared to the previous move.
     * Uses round 2 scores if available, otherwise round 1 scores.
     */
    private fun findBiggestScoreChangeMove(): Int {
        val scores = _uiState.value.moveScoresRound2.ifEmpty { _uiState.value.moveScores }
        if (scores.size < 2) return 0

        var maxChange = 0f
        var maxChangeIndex = 0

        val sortedIndices = scores.keys.sorted()
        for (i in 1 until sortedIndices.size) {
            val currentIndex = sortedIndices[i]
            val prevIndex = sortedIndices[i - 1]
            val currentScore = scores[currentIndex]?.score ?: continue
            val prevScore = scores[prevIndex]?.score ?: continue

            val change = kotlin.math.abs(currentScore - prevScore)
            if (change > maxChange) {
                maxChange = change
                maxChangeIndex = currentIndex
            }
        }

        return maxChangeIndex
    }

    fun stopAutoAnalysis() {
        autoAnalysisJob?.cancel()
        stockfish.stop()  // Stop any running time-based analysis
        _uiState.value = _uiState.value.copy(
            isAutoAnalyzing = false,
            autoAnalysisIndex = -1,
            stockfishReady = false,  // Force restart on next analysis
            analysisResult = null
            // Keep remainingAnalysisMoves, currentAnalysisRound and autoAnalysisCompleted for potential resume
        )
    }

    /**
     * Toggle between Analyse stage and Manual stage.
     * When switching stages, restart Stockfish to ensure clean state.
     * When switching to Analyse:
     * - If previous analysis was incomplete: resume where it left off (same round)
     * - If previous analysis was complete: restart from round 1
     */
    fun toggleStage() {
        if (_uiState.value.isAutoAnalyzing) {
            // Currently in Analyse stage -> switch to Manual stage
            switchToManualStage()
        } else {
            // Currently in Manual stage -> switch to Analyse stage
            switchToAnalyseStage()
        }
    }

    private fun switchToManualStage() {
        // Cancel any ongoing jobs
        autoAnalysisJob?.cancel()
        manualAnalysisJob?.cancel()

        _uiState.value = _uiState.value.copy(
            isAutoAnalyzing = false,
            autoAnalysisIndex = -1,
            stockfishReady = false,
            analysisResult = null
        )

        viewModelScope.launch {
            // Kill and restart Stockfish
            stockfish.stop()
            val ready = stockfish.restart()
            _uiState.value = _uiState.value.copy(stockfishReady = ready)

            if (ready) {
                configureForManualStage()
                // Use ensureStockfishAnalysis to guarantee the card shows
                ensureStockfishAnalysis()
            }
        }
    }

    /**
     * Exit analyse mode by tapping on the graph.
     * Stops current analysis, switches to manual mode, and goes to the specified move.
     */
    fun exitAnalysisToMove(moveIndex: Int) {
        // Cancel any ongoing jobs
        autoAnalysisJob?.cancel()
        manualAnalysisJob?.cancel()

        // Stop Stockfish immediately
        stockfish.stop()

        // Go to the specified move position
        val moves = _uiState.value.moves
        val board = ChessBoard()
        for (i in 0..moveIndex.coerceAtMost(moves.size - 1)) {
            board.makeMove(moves[i])
        }

        _uiState.value = _uiState.value.copy(
            isAutoAnalyzing = false,
            autoAnalysisIndex = -1,
            currentMoveIndex = moveIndex,
            currentBoard = board.copy(),
            stockfishReady = false,
            analysisResult = null
        )

        // Restart Stockfish for manual mode
        viewModelScope.launch {
            val ready = stockfish.restart()
            _uiState.value = _uiState.value.copy(stockfishReady = ready)

            if (ready) {
                configureForManualStage()
                ensureStockfishAnalysis()
            }
        }
    }

    private fun switchToAnalyseStage() {
        // Cancel any ongoing jobs
        autoAnalysisJob?.cancel()
        manualAnalysisJob?.cancel()

        _uiState.value = _uiState.value.copy(
            stockfishReady = false,
            analysisResult = null
        )

        viewModelScope.launch {
            // Kill and restart Stockfish
            stockfish.stop()
            val ready = stockfish.restart()
            _uiState.value = _uiState.value.copy(stockfishReady = ready)

            if (ready) {
                configureForAnalyseStage()

                // Determine whether to resume or start fresh
                val remainingMoves = _uiState.value.remainingAnalysisMoves
                val wasCompleted = _uiState.value.autoAnalysisCompleted
                val currentRound = _uiState.value.currentAnalysisRound

                if (!wasCompleted && remainingMoves.isNotEmpty()) {
                    // Previous analysis was not finished -> resume where it left off
                    val currentTime = _uiState.value.currentAnalysisTimeMs
                    startAutoAnalysis(remainingMoves, currentTime, currentRound)
                } else {
                    // Previous analysis was finished -> start fresh from round 1
                    _uiState.value = _uiState.value.copy(
                        moveScores = emptyMap(),
                        moveScoresRound2 = emptyMap()
                    )
                    startAutoAnalysis(startRound = 1)
                }
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
        manualAnalysisJob?.cancel()
        stockfish.shutdown()
    }
}
