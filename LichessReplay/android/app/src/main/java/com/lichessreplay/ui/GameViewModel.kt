package com.lichessreplay.ui

import android.app.Application
import android.content.Context
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.lichessreplay.chess.ChessBoard
import com.lichessreplay.chess.PgnParser
import com.lichessreplay.data.LichessGame
import com.lichessreplay.data.LichessRepository
import com.lichessreplay.data.Result
import com.lichessreplay.stockfish.AnalysisResult
import com.lichessreplay.stockfish.StockfishEngine
import kotlinx.coroutines.*
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow

data class GameUiState(
    val isLoading: Boolean = false,
    val errorMessage: String? = null,
    val game: LichessGame? = null,
    val currentBoard: ChessBoard = ChessBoard(),
    val moves: List<String> = emptyList(),
    val currentMoveIndex: Int = -1,
    val isPlaying: Boolean = false,
    val playSpeed: Long = 1000L,
    val analysisEnabled: Boolean = true,
    val analysisDepth: Int = 16,
    val analysisResult: AnalysisResult? = null,
    val stockfishReady: Boolean = false,
    val flippedBoard: Boolean = false
)

class GameViewModel(application: Application) : AndroidViewModel(application) {
    private val repository = LichessRepository()
    private val stockfish = StockfishEngine(application)
    private val prefs = application.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)

    private val _uiState = MutableStateFlow(GameUiState())
    val uiState: StateFlow<GameUiState> = _uiState.asStateFlow()

    private var autoPlayJob: Job? = null
    private var boardHistory = mutableListOf<ChessBoard>()

    val savedUsername: String
        get() = prefs.getString(KEY_USERNAME, "") ?: ""

    companion object {
        private const val PREFS_NAME = "lichess_replay_prefs"
        private const val KEY_USERNAME = "last_username"
    }

    init {
        // Initialize Stockfish
        viewModelScope.launch {
            val ready = stockfish.initialize()
            _uiState.value = _uiState.value.copy(stockfishReady = ready)
        }

        // Observe analysis results
        viewModelScope.launch {
            stockfish.analysisResult.collect { result ->
                _uiState.value = _uiState.value.copy(analysisResult = result)
            }
        }
    }

    fun fetchLastGame(username: String) {
        // Save the username for next time
        prefs.edit().putString(KEY_USERNAME, username).apply()

        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(
                isLoading = true,
                errorMessage = null,
                game = null
            )

            when (val result = repository.getLastGame(username)) {
                is Result.Success -> {
                    loadGame(result.data)
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
        boardHistory.add(initialBoard.copy())

        // Pre-compute all board positions for efficient navigation
        val tempBoard = ChessBoard()
        for (move in moves) {
            tempBoard.makeMove(move)
            boardHistory.add(tempBoard.copy())
        }

        _uiState.value = _uiState.value.copy(
            isLoading = false,
            game = game,
            moves = moves,
            currentBoard = initialBoard,
            currentMoveIndex = -1
        )

        analyzeCurrentPosition()
    }

    fun goToStart() {
        stopAutoPlay()
        _uiState.value = _uiState.value.copy(
            currentBoard = boardHistory.firstOrNull() ?: ChessBoard(),
            currentMoveIndex = -1
        )
        analyzeCurrentPosition()
    }

    fun goToEnd() {
        stopAutoPlay()
        val moves = _uiState.value.moves
        if (moves.isEmpty()) return

        _uiState.value = _uiState.value.copy(
            currentBoard = boardHistory.lastOrNull() ?: ChessBoard(),
            currentMoveIndex = moves.size - 1
        )
        analyzeCurrentPosition()
    }

    fun goToMove(index: Int) {
        stopAutoPlay()
        val moves = _uiState.value.moves
        if (index < -1 || index >= moves.size) return

        _uiState.value = _uiState.value.copy(
            currentBoard = boardHistory.getOrNull(index + 1) ?: ChessBoard(),
            currentMoveIndex = index
        )
        analyzeCurrentPosition()
    }

    fun nextMove(): Boolean {
        val currentIndex = _uiState.value.currentMoveIndex
        val moves = _uiState.value.moves
        if (currentIndex >= moves.size - 1) return false

        val newIndex = currentIndex + 1
        _uiState.value = _uiState.value.copy(
            currentBoard = boardHistory.getOrNull(newIndex + 1) ?: _uiState.value.currentBoard,
            currentMoveIndex = newIndex
        )
        analyzeCurrentPosition()
        return true
    }

    fun prevMove() {
        stopAutoPlay()
        val currentIndex = _uiState.value.currentMoveIndex
        if (currentIndex < 0) return

        val newIndex = currentIndex - 1
        _uiState.value = _uiState.value.copy(
            currentBoard = boardHistory.getOrNull(newIndex + 1) ?: ChessBoard(),
            currentMoveIndex = newIndex
        )
        analyzeCurrentPosition()
    }

    fun toggleAutoPlay() {
        if (_uiState.value.isPlaying) {
            stopAutoPlay()
        } else {
            startAutoPlay()
        }
    }

    private fun startAutoPlay() {
        // If at the end, go to start first
        if (_uiState.value.currentMoveIndex >= _uiState.value.moves.size - 1) {
            goToStart()
        }

        _uiState.value = _uiState.value.copy(isPlaying = true)

        autoPlayJob = viewModelScope.launch {
            while (isActive && _uiState.value.isPlaying) {
                delay(_uiState.value.playSpeed)
                if (!nextMove()) {
                    stopAutoPlay()
                    break
                }
            }
        }
    }

    fun stopAutoPlay() {
        autoPlayJob?.cancel()
        _uiState.value = _uiState.value.copy(isPlaying = false)
    }

    fun setPlaySpeed(speed: Long) {
        _uiState.value = _uiState.value.copy(playSpeed = speed)
        if (_uiState.value.isPlaying) {
            stopAutoPlay()
            startAutoPlay()
        }
    }

    fun setAnalysisEnabled(enabled: Boolean) {
        _uiState.value = _uiState.value.copy(analysisEnabled = enabled)
        if (enabled) {
            analyzeCurrentPosition()
        } else {
            stockfish.stop()
        }
    }

    fun setAnalysisDepth(depth: Int) {
        _uiState.value = _uiState.value.copy(analysisDepth = depth)
        analyzeCurrentPosition()
    }

    fun flipBoard() {
        _uiState.value = _uiState.value.copy(flippedBoard = !_uiState.value.flippedBoard)
    }

    private fun analyzeCurrentPosition() {
        if (!_uiState.value.analysisEnabled || !_uiState.value.stockfishReady) return

        val fen = _uiState.value.currentBoard.getFen()
        stockfish.analyze(fen, _uiState.value.analysisDepth)
    }

    override fun onCleared() {
        super.onCleared()
        stockfish.shutdown()
    }
}
