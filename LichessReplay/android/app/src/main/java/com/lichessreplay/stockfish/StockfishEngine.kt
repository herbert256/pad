package com.lichessreplay.stockfish

import android.content.Context
import kotlinx.coroutines.*
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import java.io.*

data class PvLine(
    val score: Float,
    val isMate: Boolean,
    val mateIn: Int,
    val pv: String,
    val multipv: Int
)

data class AnalysisResult(
    val depth: Int,
    val lines: List<PvLine>
) {
    // Convenience properties for backward compatibility
    val bestLine: PvLine? get() = lines.firstOrNull()
    val score: Float get() = bestLine?.score ?: 0f
    val isMate: Boolean get() = bestLine?.isMate ?: false
    val mateIn: Int get() = bestLine?.mateIn ?: 0
    val bestMove: String get() = bestLine?.pv?.split(" ")?.firstOrNull() ?: ""
    val pv: String get() = bestLine?.pv ?: ""
}

class StockfishEngine(private val context: Context) {
    private var process: Process? = null
    private var processWriter: BufferedWriter? = null
    private var processReader: BufferedReader? = null

    private val _analysisResult = MutableStateFlow<AnalysisResult?>(null)
    val analysisResult: StateFlow<AnalysisResult?> = _analysisResult

    private val _isReady = MutableStateFlow(false)
    val isReady: StateFlow<Boolean> = _isReady

    private var analysisJob: Job? = null
    private val scope = CoroutineScope(Dispatchers.IO + SupervisorJob())

    private var stockfishPath: String? = null
    private var currentMultiPv = 1
    private val pvLines = mutableMapOf<Int, PvLine>()

    suspend fun initialize(): Boolean = withContext(Dispatchers.IO) {
        try {
            // Use Stockfish from native library directory (avoids SELinux restrictions)
            val nativeLibDir = context.applicationInfo.nativeLibraryDir
            val stockfishFile = File(nativeLibDir, "libstockfish.so")

            android.util.Log.d("StockfishEngine", "Looking for Stockfish at: ${stockfishFile.absolutePath}")
            android.util.Log.d("StockfishEngine", "File exists: ${stockfishFile.exists()}")

            if (!stockfishFile.exists()) {
                android.util.Log.e("StockfishEngine", "Stockfish not found!")
                return@withContext false
            }

            stockfishPath = stockfishFile.absolutePath
            android.util.Log.d("StockfishEngine", "Starting Stockfish process...")

            // Start the process
            startProcess()

            android.util.Log.d("StockfishEngine", "Stockfish initialized, isReady: ${_isReady.value}")
            true
        } catch (e: Exception) {
            android.util.Log.e("StockfishEngine", "Error initializing Stockfish", e)
            e.printStackTrace()
            false
        }
    }

    private suspend fun startProcess() {
        val path = stockfishPath ?: return

        try {
            android.util.Log.d("StockfishEngine", "Starting process: $path")
            process = ProcessBuilder(path)
                .redirectErrorStream(true)
                .start()

            android.util.Log.d("StockfishEngine", "Process started, setting up streams...")
            processWriter = BufferedWriter(OutputStreamWriter(process!!.outputStream))
            processReader = BufferedReader(InputStreamReader(process!!.inputStream))

            // Initialize UCI
            android.util.Log.d("StockfishEngine", "Sending uci command...")
            sendCommand("uci")

            // Read until uciok
            var line = processReader?.readLine()
            android.util.Log.d("StockfishEngine", "First line from stockfish: $line")
            while (line != null && line != "uciok") {
                line = processReader?.readLine()
            }
            android.util.Log.d("StockfishEngine", "Got uciok")

            // Send isready and wait for readyok
            sendCommand("isready")
            line = processReader?.readLine()
            while (line != null && line != "readyok") {
                line = processReader?.readLine()
            }
            android.util.Log.d("StockfishEngine", "Got readyok - Stockfish is ready!")

            _isReady.value = true

        } catch (e: Exception) {
            android.util.Log.e("StockfishEngine", "Error in startProcess", e)
            e.printStackTrace()
            _isReady.value = false
        }
    }

    private fun sendCommand(command: String) {
        try {
            processWriter?.write(command)
            processWriter?.newLine()
            processWriter?.flush()
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    fun configure(threads: Int, hashMb: Int, multiPv: Int) {
        if (!_isReady.value) return

        currentMultiPv = multiPv
        sendCommand("setoption name Threads value $threads")
        sendCommand("setoption name Hash value $hashMb")
        sendCommand("setoption name MultiPV value $multiPv")
        android.util.Log.d("StockfishEngine", "Configured: Threads=$threads, Hash=$hashMb, MultiPV=$multiPv")
    }

    fun analyze(fen: String, depth: Int = 16) {
        if (!_isReady.value) return

        analysisJob?.cancel()
        analysisJob = scope.launch {
            try {
                // Stop any ongoing analysis
                sendCommand("stop")
                delay(50)

                // Clear previous lines and reset result
                pvLines.clear()
                _analysisResult.value = null

                // Set position and start analysis
                sendCommand("position fen $fen")
                sendCommand("go depth $depth")

                // Read analysis output
                var line = processReader?.readLine()
                while (line != null && isActive) {
                    when {
                        line.startsWith("info depth") && line.contains("score") -> {
                            parseInfoLine(line)
                        }
                        line.startsWith("bestmove") -> {
                            break
                        }
                    }
                    line = processReader?.readLine()
                }
            } catch (e: Exception) {
                if (e !is CancellationException) {
                    e.printStackTrace()
                }
            }
        }
    }

    fun analyzeWithTime(fen: String, timeMs: Int) {
        if (!_isReady.value) return

        analysisJob?.cancel()
        analysisJob = scope.launch {
            try {
                // Stop any ongoing analysis
                sendCommand("stop")
                delay(50)

                // Clear previous lines and reset result
                pvLines.clear()
                _analysisResult.value = null

                // Set position and start analysis with time limit
                sendCommand("position fen $fen")
                sendCommand("go movetime $timeMs")

                // Read analysis output
                var line = processReader?.readLine()
                while (line != null && isActive) {
                    when {
                        line.startsWith("info depth") && line.contains("score") -> {
                            parseInfoLine(line)
                        }
                        line.startsWith("bestmove") -> {
                            break
                        }
                    }
                    line = processReader?.readLine()
                }
            } catch (e: Exception) {
                if (e !is CancellationException) {
                    e.printStackTrace()
                }
            }
        }
    }

    private fun parseInfoLine(line: String) {
        try {
            // Extract depth
            val depthMatch = Regex("depth (\\d+)").find(line)
            val depth = depthMatch?.groupValues?.get(1)?.toIntOrNull() ?: 0

            // Extract multipv (defaults to 1)
            val multipvMatch = Regex("multipv (\\d+)").find(line)
            val multipv = multipvMatch?.groupValues?.get(1)?.toIntOrNull() ?: 1

            // Extract score
            var score = 0f
            var isMate = false
            var mateIn = 0

            val mateMatch = Regex("score mate (-?\\d+)").find(line)
            val cpMatch = Regex("score cp (-?\\d+)").find(line)

            if (mateMatch != null) {
                isMate = true
                mateIn = mateMatch.groupValues[1].toIntOrNull() ?: 0
                score = if (mateIn > 0) 100f else -100f
            } else if (cpMatch != null) {
                score = (cpMatch.groupValues[1].toIntOrNull() ?: 0) / 100f
            }

            // Extract PV
            val pvMatch = Regex(" pv (.+)$").find(line)
            val pv = pvMatch?.groupValues?.get(1) ?: ""

            // Store this PV line
            val pvLine = PvLine(
                score = score,
                isMate = isMate,
                mateIn = mateIn,
                pv = pv.split(" ").take(8).joinToString(" "),
                multipv = multipv
            )
            pvLines[multipv] = pvLine

            // Update result with all collected lines sorted by multipv
            _analysisResult.value = AnalysisResult(
                depth = depth,
                lines = pvLines.values.sortedBy { it.multipv }
            )
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    fun stop() {
        analysisJob?.cancel()
        sendCommand("stop")
    }

    fun shutdown() {
        analysisJob?.cancel()
        scope.cancel()
        try {
            sendCommand("quit")
            processWriter?.close()
            processReader?.close()
            process?.destroy()
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }
}
