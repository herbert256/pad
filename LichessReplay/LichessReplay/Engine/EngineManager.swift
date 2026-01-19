//
//  EngineManager.swift
//  LichessReplay
//
//  Manages multiple chess engines and coordinates analysis.
//

import Foundation
import Combine

class EngineManager: ObservableObject {
    @Published var selectedEngineType: EngineType = .stockfish
    @Published var stockfishEngine: UCIEngine
    @Published var lc0Engine: UCIEngine
    @Published var isAnalysisEnabled = true
    @Published var currentAnalysis: EngineAnalysis?

    @Published var stockfishConfig: EngineConfiguration {
        didSet {
            saveConfiguration()
            stockfishEngine.configuration = stockfishConfig
        }
    }

    @Published var lc0Config: EngineConfiguration {
        didSet {
            saveConfiguration()
            lc0Engine.configuration = lc0Config
        }
    }

    private var cancellables = Set<AnyCancellable>()
    private let configKey = "com.lichessreplay.engineconfig"

    var activeEngine: UCIEngine {
        selectedEngineType == .stockfish ? stockfishEngine : lc0Engine
    }

    var activeConfig: EngineConfiguration {
        get {
            selectedEngineType == .stockfish ? stockfishConfig : lc0Config
        }
        set {
            if selectedEngineType == .stockfish {
                stockfishConfig = newValue
            } else {
                lc0Config = newValue
            }
        }
    }

    init() {
        // Load saved configurations
        let (sfConfig, lc0Config) = Self.loadConfigurations()
        self.stockfishConfig = sfConfig
        self.lc0Config = lc0Config

        self.stockfishEngine = UCIEngine(type: .stockfish, configuration: sfConfig)
        self.lc0Engine = UCIEngine(type: .lc0, configuration: lc0Config)

        setupBindings()
    }

    private func setupBindings() {
        // Subscribe to active engine's analysis
        stockfishEngine.$analysis
            .receive(on: DispatchQueue.main)
            .sink { [weak self] analysis in
                guard let self = self, self.selectedEngineType == .stockfish else { return }
                self.currentAnalysis = analysis
            }
            .store(in: &cancellables)

        lc0Engine.$analysis
            .receive(on: DispatchQueue.main)
            .sink { [weak self] analysis in
                guard let self = self, self.selectedEngineType == .lc0 else { return }
                self.currentAnalysis = analysis
            }
            .store(in: &cancellables)
    }

    func startActiveEngine() async throws {
        try await activeEngine.start()
    }

    func stopActiveEngine() {
        activeEngine.stop()
    }

    func switchEngine(to type: EngineType) async throws {
        // Stop current engine
        activeEngine.stopAnalysis()

        selectedEngineType = type

        // Start new engine if not already running
        if activeEngine.state == .idle {
            try await activeEngine.start()
        }
    }

    func analyze(fen: String) async {
        guard isAnalysisEnabled else { return }

        if activeEngine.state == .idle {
            do {
                try await activeEngine.start()
            } catch {
                print("Failed to start engine: \(error)")
                return
            }
        }

        await activeEngine.analyze(fen: fen)
    }

    func stopAnalysis() {
        activeEngine.stopAnalysis()
        DispatchQueue.main.async {
            self.currentAnalysis = nil
        }
    }

    func toggleAnalysis() {
        isAnalysisEnabled.toggle()
        if !isAnalysisEnabled {
            stopAnalysis()
        }
    }

    // MARK: - Configuration Persistence

    private func saveConfiguration() {
        let configs = SavedConfigurations(stockfish: stockfishConfig, lc0: lc0Config)
        if let data = try? JSONEncoder().encode(configs) {
            UserDefaults.standard.set(data, forKey: configKey)
        }
    }

    private static func loadConfigurations() -> (EngineConfiguration, EngineConfiguration) {
        let configKey = "com.lichessreplay.engineconfig"
        guard let data = UserDefaults.standard.data(forKey: configKey),
              let configs = try? JSONDecoder().decode(SavedConfigurations.self, from: data) else {
            return (.defaultStockfish, .defaultLc0)
        }
        return (configs.stockfish, configs.lc0)
    }

    private struct SavedConfigurations: Codable {
        let stockfish: EngineConfiguration
        let lc0: EngineConfiguration
    }
}
