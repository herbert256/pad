//
//  LichessReplayApp.swift
//  LichessReplay
//
//  A native macOS application for replaying Lichess games with chess engine analysis.
//  Supports both Stockfish and Leela Chess Zero (lc0) engines.
//

import SwiftUI

@main
struct LichessReplayApp: App {
    @StateObject private var appState = AppState()

    var body: some Scene {
        WindowGroup {
            ContentView()
                .environmentObject(appState)
                .frame(minWidth: 1000, minHeight: 700)
        }
        .windowStyle(.hiddenTitleBar)
        .commands {
            CommandGroup(replacing: .newItem) {}
        }

        Settings {
            EngineSettingsView()
                .environmentObject(appState)
        }
    }
}

// MARK: - App State
class AppState: ObservableObject {
    @Published var engineManager: EngineManager
    @Published var lichessClient: LichessClient
    @Published var currentGame: ChessGame?
    @Published var isLoading = false
    @Published var errorMessage: String?

    init() {
        self.engineManager = EngineManager()
        self.lichessClient = LichessClient()
    }
}
