package com.lichessreplay.data

import com.google.gson.Gson
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

sealed class Result<out T> {
    data class Success<T>(val data: T) : Result<T>()
    data class Error(val message: String) : Result<Nothing>()
}

class LichessRepository(
    private val api: LichessApi = LichessApi.create()
) {
    private val gson = Gson()

    suspend fun getRecentGames(username: String): Result<List<LichessGame>> = withContext(Dispatchers.IO) {
        try {
            val response = api.getGames(username)

            if (!response.isSuccessful) {
                return@withContext when (response.code()) {
                    404 -> Result.Error("User not found")
                    else -> Result.Error("Failed to fetch game data")
                }
            }

            val body = response.body()
            if (body.isNullOrBlank()) {
                return@withContext Result.Error("No games found for this user")
            }

            // Parse NDJSON (each line is a game)
            val games = body.lines()
                .filter { it.isNotBlank() }
                .mapNotNull { line ->
                    try {
                        gson.fromJson(line, LichessGame::class.java)
                    } catch (e: Exception) {
                        null
                    }
                }

            if (games.isEmpty()) {
                return@withContext Result.Error("No games found for this user")
            }

            Result.Success(games)
        } catch (e: Exception) {
            Result.Error(e.message ?: "Unknown error occurred")
        }
    }
}
