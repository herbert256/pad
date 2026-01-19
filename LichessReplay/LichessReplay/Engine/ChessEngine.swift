//
//  ChessEngine.swift
//  LichessReplay
//
//  Protocol and base implementation for UCI chess engines.
//

import Foundation
import Combine

// MARK: - Engine Type
enum EngineType: String, CaseIterable, Identifiable {
    case stockfish = "Stockfish"
    case lc0 = "Leela Chess Zero"

    var id: String { rawValue }

    var defaultPath: String {
        switch self {
        case .stockfish:
            return "/opt/homebrew/bin/stockfish"
        case .lc0:
            return "/opt/homebrew/bin/lc0"
        }
    }

    var icon: String {
        switch self {
        case .stockfish: return "fish"
        case .lc0: return "brain"
        }
    }
}

// MARK: - Engine Configuration
struct EngineConfiguration: Codable, Equatable {
    var depth: Int = 20
    var threads: Int = 4
    var hashMB: Int = 256
    var multiPV: Int = 1

    // Stockfish specific
    var skillLevel: Int = 20
    var limitStrength: Bool = false
    var elo: Int = 3190

    // lc0 specific
    var weightsFile: String = ""
    var backend: String = "metal"
    var minibatchSize: Int = 256

    static var defaultStockfish: EngineConfiguration {
        EngineConfiguration(
            depth: 20,
            threads: ProcessInfo.processInfo.processorCount,
            hashMB: 256,
            multiPV: 1,
            skillLevel: 20,
            limitStrength: false,
            elo: 3190
        )
    }

    static var defaultLc0: EngineConfiguration {
        EngineConfiguration(
            depth: 20,
            threads: 0,  // lc0 uses 0 for auto
            hashMB: 256,
            multiPV: 1,
            backend: "metal",
            minibatchSize: 256
        )
    }
}

// MARK: - Engine State
enum EngineState: Equatable {
    case idle
    case initializing
    case ready
    case analyzing
    case error(String)
}

// MARK: - Chess Engine Protocol
protocol ChessEngine: AnyObject {
    var type: EngineType { get }
    var state: EngineState { get }
    var statePublisher: Published<EngineState>.Publisher { get }
    var analysisPublisher: Published<EngineAnalysis?>.Publisher { get }
    var configuration: EngineConfiguration { get set }

    func start() async throws
    func stop()
    func analyze(fen: String) async
    func stopAnalysis()
    func setOption(name: String, value: String)
}

// MARK: - UCI Engine Base
class UCIEngine: ObservableObject, ChessEngine {
    let type: EngineType
    let executablePath: String

    @Published var state: EngineState = .idle
    @Published var analysis: EngineAnalysis?
    @Published var configuration: EngineConfiguration

    var statePublisher: Published<EngineState>.Publisher { $state }
    var analysisPublisher: Published<EngineAnalysis?>.Publisher { $analysis }

    private var process: Process?
    private var inputPipe: Pipe?
    private var outputPipe: Pipe?
    private var outputBuffer = ""
    private var isAnalyzing = false
    private var currentFEN: String?
    private var analysisTask: Task<Void, Never>?

    private let queue = DispatchQueue(label: "com.lichessreplay.engine", qos: .userInitiated)

    init(type: EngineType, executablePath: String? = nil, configuration: EngineConfiguration? = nil) {
        self.type = type
        self.executablePath = executablePath ?? type.defaultPath
        self.configuration = configuration ?? (type == .stockfish ? .defaultStockfish : .defaultLc0)
    }

    deinit {
        stop()
    }

    func start() async throws {
        guard state == .idle || state == .error("") else { return }

        await MainActor.run {
            state = .initializing
        }

        // Check if executable exists
        guard FileManager.default.fileExists(atPath: executablePath) else {
            await MainActor.run {
                state = .error("Engine not found at: \(executablePath)")
            }
            throw EngineError.notFound(executablePath)
        }

        // Start process
        let process = Process()
        process.executableURL = URL(fileURLWithPath: executablePath)
        process.currentDirectoryURL = URL(fileURLWithPath: NSHomeDirectory())

        let inputPipe = Pipe()
        let outputPipe = Pipe()

        process.standardInput = inputPipe
        process.standardOutput = outputPipe
        process.standardError = outputPipe

        self.process = process
        self.inputPipe = inputPipe
        self.outputPipe = outputPipe

        // Set up output handler
        outputPipe.fileHandleForReading.readabilityHandler = { [weak self] handle in
            let data = handle.availableData
            guard !data.isEmpty, let output = String(data: data, encoding: .utf8) else { return }
            self?.handleOutput(output)
        }

        do {
            try process.run()
        } catch {
            await MainActor.run {
                state = .error("Failed to start engine: \(error.localizedDescription)")
            }
            throw error
        }

        // Initialize UCI
        sendCommand("uci")

        // Wait for uciok
        try await waitForReady()

        // Apply configuration
        applyConfiguration()

        sendCommand("isready")

        // Wait for readyok
        try await waitForReadyOk()

        await MainActor.run {
            state = .ready
        }
    }

    func stop() {
        analysisTask?.cancel()
        analysisTask = nil
        isAnalyzing = false

        if process?.isRunning == true {
            sendCommand("quit")
            process?.terminate()
        }

        outputPipe?.fileHandleForReading.readabilityHandler = nil

        process = nil
        inputPipe = nil
        outputPipe = nil
        outputBuffer = ""

        DispatchQueue.main.async {
            self.state = .idle
            self.analysis = nil
        }
    }

    func analyze(fen: String) async {
        guard state == .ready || state == .analyzing else { return }

        // Cancel previous analysis
        stopAnalysis()

        currentFEN = fen
        isAnalyzing = true

        await MainActor.run {
            state = .analyzing
            analysis = nil
        }

        sendCommand("position fen \(fen)")
        sendCommand("go depth \(configuration.depth)")
    }

    func stopAnalysis() {
        if isAnalyzing {
            sendCommand("stop")
            isAnalyzing = false
        }
        analysisTask?.cancel()
    }

    func setOption(name: String, value: String) {
        sendCommand("setoption name \(name) value \(value)")
    }

    // MARK: - Private Methods

    private func sendCommand(_ command: String) {
        guard let inputPipe = inputPipe else { return }
        let data = (command + "\n").data(using: .utf8)!
        inputPipe.fileHandleForWriting.write(data)
    }

    private func handleOutput(_ output: String) {
        outputBuffer += output
        let lines = outputBuffer.components(separatedBy: "\n")

        // Process complete lines
        for i in 0..<(lines.count - 1) {
            processLine(lines[i])
        }

        // Keep incomplete line in buffer
        outputBuffer = lines.last ?? ""
    }

    private func processLine(_ line: String) {
        let trimmed = line.trimmingCharacters(in: .whitespacesAndNewlines)
        guard !trimmed.isEmpty else { return }

        if trimmed == "uciok" {
            // UCI initialization complete
        } else if trimmed == "readyok" {
            // Engine ready
        } else if trimmed.hasPrefix("info depth") {
            parseInfoLine(trimmed)
        } else if trimmed.hasPrefix("bestmove") {
            DispatchQueue.main.async {
                self.isAnalyzing = false
                if self.state == .analyzing {
                    self.state = .ready
                }
            }
        }
    }

    private func parseInfoLine(_ line: String) {
        guard line.contains(" score ") else { return }

        var analysis = EngineAnalysis.empty

        // Parse depth
        if let depthMatch = line.range(of: #"depth (\d+)"#, options: .regularExpression) {
            let depthStr = String(line[depthMatch]).replacingOccurrences(of: "depth ", with: "")
            analysis.depth = Int(depthStr) ?? 0
        }

        // Parse score
        if let mateMatch = line.range(of: #"score mate (-?\d+)"#, options: .regularExpression) {
            let mateStr = String(line[mateMatch]).replacingOccurrences(of: "score mate ", with: "")
            analysis.mate = Int(mateStr)
            analysis.score = (analysis.mate ?? 0) > 0 ? 100 : -100
        } else if let cpMatch = line.range(of: #"score cp (-?\d+)"#, options: .regularExpression) {
            let cpStr = String(line[cpMatch]).replacingOccurrences(of: "score cp ", with: "")
            let centipawns = Double(cpStr) ?? 0
            analysis.score = centipawns / 100.0
        }

        // Parse nodes
        if let nodesMatch = line.range(of: #" nodes (\d+)"#, options: .regularExpression) {
            let nodesStr = String(line[nodesMatch]).replacingOccurrences(of: " nodes ", with: "")
            analysis.nodes = Int(nodesStr) ?? 0
        }

        // Parse nps
        if let npsMatch = line.range(of: #" nps (\d+)"#, options: .regularExpression) {
            let npsStr = String(line[npsMatch]).replacingOccurrences(of: " nps ", with: "")
            analysis.nps = Int(npsStr) ?? 0
        }

        // Parse time
        if let timeMatch = line.range(of: #" time (\d+)"#, options: .regularExpression) {
            let timeStr = String(line[timeMatch]).replacingOccurrences(of: " time ", with: "")
            analysis.time = Int(timeStr) ?? 0
        }

        // Parse PV
        if let pvMatch = line.range(of: #" pv (.+)$"#, options: .regularExpression) {
            let pvStr = String(line[pvMatch]).replacingOccurrences(of: " pv ", with: "")
            analysis.pv = pvStr.components(separatedBy: " ")
            analysis.bestMove = analysis.pv.first ?? ""
        }

        DispatchQueue.main.async {
            self.analysis = analysis
        }
    }

    private func applyConfiguration() {
        setOption(name: "Threads", value: "\(configuration.threads)")
        setOption(name: "Hash", value: "\(configuration.hashMB)")
        setOption(name: "MultiPV", value: "\(configuration.multiPV)")

        if type == .stockfish {
            setOption(name: "Skill Level", value: "\(configuration.skillLevel)")
            setOption(name: "UCI_LimitStrength", value: configuration.limitStrength ? "true" : "false")
            if configuration.limitStrength {
                setOption(name: "UCI_Elo", value: "\(configuration.elo)")
            }
        } else if type == .lc0 {
            if !configuration.weightsFile.isEmpty {
                setOption(name: "WeightsFile", value: configuration.weightsFile)
            }
            setOption(name: "Backend", value: configuration.backend)
            if configuration.minibatchSize > 0 {
                setOption(name: "MinibatchSize", value: "\(configuration.minibatchSize)")
            }
        }
    }

    private func waitForReady() async throws {
        // Simple timeout-based wait for uciok
        try await Task.sleep(nanoseconds: 500_000_000)  // 0.5 seconds
    }

    private func waitForReadyOk() async throws {
        try await Task.sleep(nanoseconds: 200_000_000)  // 0.2 seconds
    }
}

// MARK: - Engine Error
enum EngineError: LocalizedError {
    case notFound(String)
    case startFailed(String)
    case timeout

    var errorDescription: String? {
        switch self {
        case .notFound(let path):
            return "Engine not found at: \(path)"
        case .startFailed(let reason):
            return "Failed to start engine: \(reason)"
        case .timeout:
            return "Engine communication timeout"
        }
    }
}
