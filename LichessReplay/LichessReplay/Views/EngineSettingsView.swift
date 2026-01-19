//
//  EngineSettingsView.swift
//  LichessReplay
//
//  Settings view for configuring chess engines.
//

import SwiftUI

struct EngineSettingsView: View {
    @EnvironmentObject var appState: AppState

    var body: some View {
        TabView {
            StockfishSettingsTab()
                .environmentObject(appState)
                .tabItem {
                    Label("Stockfish", systemImage: "fish")
                }

            Lc0SettingsTab()
                .environmentObject(appState)
                .tabItem {
                    Label("Leela Chess Zero", systemImage: "brain")
                }

            AboutTab()
                .tabItem {
                    Label("About", systemImage: "info.circle")
                }
        }
        .frame(width: 500, height: 450)
        .padding()
    }
}

// MARK: - Stockfish Settings
struct StockfishSettingsTab: View {
    @EnvironmentObject var appState: AppState

    var body: some View {
        Form {
            Section("Engine Path") {
                HStack {
                    Text(appState.engineManager.stockfishEngine.executablePath)
                        .font(.system(.body, design: .monospaced))
                        .foregroundColor(.secondary)

                    Spacer()

                    if FileManager.default.fileExists(atPath: appState.engineManager.stockfishEngine.executablePath) {
                        Image(systemName: "checkmark.circle.fill")
                            .foregroundColor(.green)
                    } else {
                        Image(systemName: "xmark.circle.fill")
                            .foregroundColor(.red)
                    }
                }
            }

            Section("Analysis Settings") {
                Stepper("Search Depth: \(appState.engineManager.stockfishConfig.depth)",
                        value: $appState.engineManager.stockfishConfig.depth,
                        in: 1...40)

                Stepper("Threads: \(appState.engineManager.stockfishConfig.threads)",
                        value: $appState.engineManager.stockfishConfig.threads,
                        in: 1...ProcessInfo.processInfo.processorCount)

                Stepper("Hash (MB): \(appState.engineManager.stockfishConfig.hashMB)",
                        value: $appState.engineManager.stockfishConfig.hashMB,
                        in: 16...4096,
                        step: 64)

                Stepper("MultiPV: \(appState.engineManager.stockfishConfig.multiPV)",
                        value: $appState.engineManager.stockfishConfig.multiPV,
                        in: 1...5)
            }

            Section("Skill Settings") {
                Stepper("Skill Level: \(appState.engineManager.stockfishConfig.skillLevel)",
                        value: $appState.engineManager.stockfishConfig.skillLevel,
                        in: 0...20)

                Toggle("Limit Strength", isOn: $appState.engineManager.stockfishConfig.limitStrength)

                if appState.engineManager.stockfishConfig.limitStrength {
                    Stepper("ELO: \(appState.engineManager.stockfishConfig.elo)",
                            value: $appState.engineManager.stockfishConfig.elo,
                            in: 1320...3190,
                            step: 50)
                }
            }

            Section {
                Button("Reset to Defaults") {
                    appState.engineManager.stockfishConfig = .defaultStockfish
                }
            }
        }
        .padding()
    }
}

// MARK: - Lc0 Settings
struct Lc0SettingsTab: View {
    @EnvironmentObject var appState: AppState
    @State private var weightsPath = ""

    var body: some View {
        Form {
            Section("Engine Path") {
                HStack {
                    Text(appState.engineManager.lc0Engine.executablePath)
                        .font(.system(.body, design: .monospaced))
                        .foregroundColor(.secondary)

                    Spacer()

                    if FileManager.default.fileExists(atPath: appState.engineManager.lc0Engine.executablePath) {
                        Image(systemName: "checkmark.circle.fill")
                            .foregroundColor(.green)
                    } else {
                        Image(systemName: "xmark.circle.fill")
                            .foregroundColor(.red)
                    }
                }
            }

            Section("Analysis Settings") {
                Stepper("Search Depth: \(appState.engineManager.lc0Config.depth)",
                        value: $appState.engineManager.lc0Config.depth,
                        in: 1...40)

                HStack {
                    Text("Threads:")
                    Spacer()
                    Text(appState.engineManager.lc0Config.threads == 0 ? "Auto" : "\(appState.engineManager.lc0Config.threads)")
                        .foregroundColor(.secondary)
                    Stepper("",
                            value: $appState.engineManager.lc0Config.threads,
                            in: 0...128)
                    .labelsHidden()
                }

                Stepper("Hash (MB): \(appState.engineManager.lc0Config.hashMB)",
                        value: $appState.engineManager.lc0Config.hashMB,
                        in: 16...4096,
                        step: 64)

                Stepper("MultiPV: \(appState.engineManager.lc0Config.multiPV)",
                        value: $appState.engineManager.lc0Config.multiPV,
                        in: 1...5)
            }

            Section("Neural Network") {
                Picker("Backend", selection: $appState.engineManager.lc0Config.backend) {
                    Text("Metal (GPU)").tag("metal")
                    Text("BLAS (CPU)").tag("blas")
                    Text("Eigen (CPU)").tag("eigen")
                }

                Stepper("Minibatch Size: \(appState.engineManager.lc0Config.minibatchSize)",
                        value: $appState.engineManager.lc0Config.minibatchSize,
                        in: 1...1024,
                        step: 32)

                HStack {
                    TextField("Weights File (optional)", text: $weightsPath)
                        .textFieldStyle(.roundedBorder)

                    Button("Browse") {
                        browseForWeights()
                    }
                }

                Text("Leave empty to use auto-discovered weights")
                    .font(.caption)
                    .foregroundColor(.secondary)
            }

            Section {
                Button("Reset to Defaults") {
                    appState.engineManager.lc0Config = .defaultLc0
                    weightsPath = ""
                }
            }
        }
        .padding()
        .onAppear {
            weightsPath = appState.engineManager.lc0Config.weightsFile
        }
        .onChange(of: weightsPath) { _, newValue in
            appState.engineManager.lc0Config.weightsFile = newValue
        }
    }

    private func browseForWeights() {
        let panel = NSOpenPanel()
        panel.allowsMultipleSelection = false
        panel.canChooseDirectories = false
        panel.canChooseFiles = true
        panel.allowedContentTypes = [.data]
        panel.message = "Select Leela Chess Zero weights file"

        if panel.runModal() == .OK, let url = panel.url {
            weightsPath = url.path
        }
    }
}

// MARK: - About Tab
struct AboutTab: View {
    var body: some View {
        VStack(spacing: 20) {
            Image(systemName: "crown.fill")
                .font(.system(size: 60))
                .foregroundColor(.yellow)

            Text("Lichess Game Replay")
                .font(.title)
                .fontWeight(.bold)

            Text("Version 1.0.0")
                .foregroundColor(.secondary)

            Divider()

            VStack(alignment: .leading, spacing: 10) {
                Text("Chess Engines:")
                    .font(.headline)

                HStack {
                    Image(systemName: "fish")
                    Text("Stockfish 17")
                    Spacer()
                    Link("Website", destination: URL(string: "https://stockfishchess.org")!)
                }

                HStack {
                    Image(systemName: "brain")
                    Text("Leela Chess Zero")
                    Spacer()
                    Link("Website", destination: URL(string: "https://lczero.org")!)
                }

                Divider()

                Text("Game Data:")
                    .font(.headline)

                HStack {
                    Image(systemName: "globe")
                    Text("Lichess.org")
                    Spacer()
                    Link("Website", destination: URL(string: "https://lichess.org")!)
                }
            }
            .frame(maxWidth: 300)

            Spacer()

            Text("Install engines with Homebrew:")
                .font(.caption)
                .foregroundColor(.secondary)

            Text("brew install stockfish lc0")
                .font(.system(.caption, design: .monospaced))
                .padding(8)
                .background(Color.black.opacity(0.1))
                .cornerRadius(4)
        }
        .padding()
    }
}

#Preview {
    EngineSettingsView()
        .environmentObject(AppState())
}
