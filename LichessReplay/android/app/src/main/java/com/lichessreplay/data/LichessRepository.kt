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

    suspend fun getLastGame(username: String): Result<LichessGame> = withContext(Dispatchers.IO) {
        try {
            val response = api.getLastGame(username)

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

            // Parse NDJSON (first line is the game)
            val gameJson = body.lines().firstOrNull { it.isNotBlank() }
                ?: return@withContext Result.Error("No games found for this user")

            val game = gson.fromJson(gameJson, LichessGame::class.java)
            Result.Success(game)
        } catch (e: Exception) {
            Result.Error(e.message ?: "Unknown error occurred")
        }
    }
}
