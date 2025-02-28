<?php

namespace App\Controllers;

use App\Models\GameSessionModel;
use App\Models\GamePlayerModel;
use App\Models\UserModel;

class GameLobby extends BaseController
{
    public function index()
    {
        return view('game_lobby/join_lobby');
    }

    public function createLobby()
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/login')->with('error', 'You must be logged in to create a lobby.');
        }

        $gameModel = new GameSessionModel();

        // Generate a random 6-character game code
        $gameCode = strtoupper(substr(md5(uniqid()), 0, 6));

        // Create the game session
        $gameId = $gameModel->insert([
            'game_code' => $gameCode,
            'host_id' => session()->get('user_id'),
            'status' => 'waiting'
        ], true);

        // **Host is NOT added as a player now**
        return redirect()->to("/game_lobby/$gameCode");
    }


    public function joinLobby()
    {
        $gameCode = $this->request->getPost('game_code');
        $gameModel = new GameSessionModel();
        $game = $gameModel->where('game_code', $gameCode)->where('status', 'waiting')->first();

        if (!$game) {
            return redirect()->back()->with('error', 'Invalid or full game code.');
        }

        $playerModel = new GamePlayerModel();

        // If logged in, use session username
        if (session()->has('user_id')) {
            $userModel = new UserModel();
            $user = $userModel->find(session()->get('user_id'));
            $playerName = $user['username'];
            $userId = $user['id'];
        } else {
            // If guest, use the name they entered
            $playerName = $this->request->getPost('guest_name');
            $userId = null;
        }

        // Insert player into game
        $playerModel->insert([
            'game_id' => $game['id'],
            'user_id' => $userId,
            'guest_name' => $userId ? null : $playerName
        ]);

        return redirect()->to("/game_lobby/$gameCode");
    }


    public function showLobby($gameCode)
    {
        $gameModel = new GameSessionModel();
        $playerModel = new GamePlayerModel();

        $game = $gameModel->where('game_code', $gameCode)->first();
        if (!$game) {
            return redirect()->to('/')->with('error', 'Lobby not found.');
        }

        $players = $playerModel->where('game_id', $game['id'])->findAll();

        return view('game_lobby/lobby', [
            'game' => $game,
            'players' => $players,
            'isHost' => session()->get('user_id') == $game['host_id']
        ]);
    }

    public function getLobbyPlayers($gameCode)
    {
        log_message('debug', 'getLobbyPlayers() called for game: ' . $gameCode);

        $gameModel = new GameSessionModel();
        $playerModel = new GamePlayerModel();
        $userModel = new UserModel();

        $game = $gameModel->where('game_code', $gameCode)->first();
        if (!$game) {
            log_message('error', 'Game not found for getLobbyPlayers: ' . $gameCode);
            return $this->response->setJSON(["error" => "Game not found"]);
        }

        try {
            $players = $playerModel->where('game_id', $game['id'])->findAll();
            if (empty($players)) {
                log_message('debug', 'No players found for game: ' . $gameCode);
                return $this->response->setJSON([]);
            }

            $playerNames = array_map(function ($player) use ($userModel) {
                if ($player['user_id']) {
                    $user = $userModel->find($player['user_id']);
                    return $user ? $user['username'] : 'Unknown Player';
                }
                return $player['guest_name'] ?? 'Guest Player';
            }, $players);

            return $this->response->setJSON($playerNames);
        } catch (Exception $e) {
            log_message('error', 'Error fetching players: ' . $e->getMessage());
            return $this->response->setJSON(["error" => "Server error"]);
        }
    }

    public function checkHostStatus($gameCode)
    {
        $gameModel = new GameSessionModel();
        $game = $gameModel->where('game_code', $gameCode)->first();

        if (!$game) {
            return $this->response->setJSON(["status" => "closed"]);
        }

        if (!session()->has('user_id') || session()->get('user_id') != $game['host_id']) {
            return $this->response->setJSON(["status" => "closed"]);
        }

        return $this->response->setJSON(["status" => "active"]);
    }



    public function closeLobby($gameCode)
    {
        $gameModel = new GameSessionModel();
        $game = $gameModel->where('game_code', $gameCode)->first();

        if (!$game || session()->get('user_id') != $game['host_id']) {
            return redirect()->to('/')->with('error', 'Unauthorized action.');
        }

        // Remove all players & delete the game
        $playerModel = new GamePlayerModel();
        $playerModel->where('game_id', $game['id'])->delete();
        $gameModel->where('game_code', $gameCode)->delete();

        return redirect()->to('/')->with('success', 'Lobby closed.');
    }

    public function leaveLobby($gameCode)
    {
        log_message('debug', 'leaveLobby() called for game: ' . $gameCode);

        $gameModel = new GameSessionModel();
        $playerModel = new GamePlayerModel();

        $game = $gameModel->where('game_code', $gameCode)->first();
        if (!$game) {
            log_message('error', 'Game not found for leaveLobby: ' . $gameCode);
            return $this->response->setJSON(["status" => "error", "message" => "Lobby not found"]);
        }

        if (session()->has('user_id')) {
            log_message('debug', 'Removing logged-in user: ' . session()->get('user_id'));
            $affectedRows = $playerModel->where('game_id', $game['id'])->where('user_id', session()->get('user_id'))->delete();
            log_message('debug', 'Rows affected: ' . $affectedRows);
            session()->remove('user_id'); // Clear session ID to prevent duplicates
        } else {
            log_message('debug', 'Removing guest user');
            $guestName = session()->get('guest_name') ?? $this->request->getPost('guest_name');
            if ($guestName) {
                log_message('debug', 'Guest name found: ' . $guestName);
                $affectedRows = $playerModel->where('game_id', $game['id'])->where('guest_name', $guestName)->delete();
                log_message('debug', 'Rows affected: ' . $affectedRows);
                session()->remove('guest_name');
            }
        }

        return $this->response->setJSON(["status" => "success"]);
    }








}
