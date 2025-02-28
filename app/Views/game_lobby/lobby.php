<?= $this->extend('layout/layout') ?>

<?= $this->section('content') ?>
<div class="container text-center">
    <h2>Game Lobby: <?= strtoupper($game['game_code']) ?></h2>

    <h4>Players:</h4>
    <ul id="player-list" class="list-group">
        <?php foreach ($players as $player): ?>
            <li class="list-group-item">
                <?= $player['username'] ?? $player['guest_name'] ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (!$isHost): ?>
        <button id="leaveLobbyBtn" class="btn btn-warning mt-3">Leave Lobby</button>
    <?php endif; ?>

    <?php if ($isHost): ?>
        <!-- Start Game Button -->
        <button id="startGameBtn" class="btn btn-danger mt-3">Start Game</button>

        <!-- Close Lobby Button -->
        <button id="closeLobbyBtn" class="btn btn-secondary mt-3">Close Lobby</button>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const gameCode = "<?= $game['game_code'] ?>";
        const isHost = <?= $isHost ? 'true' : 'false' ?>;
        const ws = new WebSocket("ws://localhost:8080?game_code=" + gameCode);

        ws.onopen = () => console.log("Connected to WebSocket server.");

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);

            if (data.type === "update_players") {
                updatePlayerList(data.players);
            } else if (data.type === "lobby_closed") {
                alert("The host has closed the lobby.");
                window.location.href = "<?= base_url('/') ?>";
            } else if (data.type === "game_started") {
                window.location.href = "<?= base_url('/game/' . $game['game_code']) ?>";
            }
        };

        function updatePlayerList() {
            fetch("<?= base_url('/get_lobby_players/') ?>" + gameCode)
                .then(response => response.json())
                .then(players => {
                    if (!Array.isArray(players)) {
                        console.error("Invalid players data:", players);
                        return;
                    }
                    document.getElementById("player-list").innerHTML = players.map(player =>
                        `<li class='list-group-item'>${player}</li>`
                    ).join("");
                    ws.send(JSON.stringify({ type: "update_players", game_code: gameCode, players }));
                })
                .catch(error => console.error("Error fetching player list:", error));
        }

        function checkHost() {
            fetch("<?= base_url('/check_host_status/') ?>" + gameCode)
                .then(response => response.json())
                .then(response => {
                    if (response.status === "closed") {
                        alert("The host has left. The lobby is now closed.");
                        window.location.href = "<?= base_url('/') ?>";
                    }
                })
                .catch(error => console.error("Error checking host status:", error));
        }

        setInterval(updatePlayerList, 3000); // Auto-refresh player list
        setInterval(checkHost, 5000); // Check host status

        // LEAVE LOBBY BUTTON (For players)
        const leaveLobbyBtn = document.getElementById("leaveLobbyBtn");
        if (leaveLobbyBtn) {
            leaveLobbyBtn.addEventListener("click", function () {
                if (confirm("Are you sure you want to leave the lobby?")) {
                    fetch("<?= base_url('/leave_lobby/' . $game['game_code']) ?>")
                        .then(() => ws.send(JSON.stringify({ type: "player_left", game_code: gameCode })))
                        .then(() => window.location.href = "<?= base_url('/') ?>");
                }
            });
        }

        // Ensure player is removed if they close the tab
        window.addEventListener("beforeunload", function () {
            navigator.sendBeacon("<?= base_url('/leave_lobby/' . $game['game_code']) ?>");
        });

        // Only add event listeners for the host
        if (isHost) {
            const startGameBtn = document.getElementById("startGameBtn");
            const closeLobbyBtn = document.getElementById("closeLobbyBtn");

            if (startGameBtn) {
                startGameBtn.addEventListener("click", function () {
                    if (confirm("Start the game?")) {
                        ws.send(JSON.stringify({ type: "game_started", game_code: gameCode }));
                        window.location.href = "<?= base_url('/game/' . $game['game_code']) ?>";
                    }
                });
            } else {
                console.warn("Warning: startGameBtn not found. This is expected if you're not the host.");
            }

            if (closeLobbyBtn) {
                closeLobbyBtn.addEventListener("click", function () {
                    if (confirm("Are you sure you want to close the lobby?")) {
                        fetch("<?= base_url('/close_lobby/' . $game['game_code']) ?>")
                            .then(() => ws.send(JSON.stringify({ type: "lobby_closed", game_code: gameCode })))
                            .then(() => window.location.href = "<?= base_url('/') ?>");
                    }
                });
            }
        }
    });
</script>

<?= $this->endSection() ?>
