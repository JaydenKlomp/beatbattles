# 🎵 Beat Battle Royale
A real-time **music trivia game** inspired by Kahoot, where players can join lobbies, answer trivia questions, and compete to be the ultimate music master! Built with **CodeIgniter 4**, **PHP**, **MySQL**, **WebSockets (Ratchet)**, **AJAX**, and **Bootstrap**.

---

## 🚀 Features
- **🎮 Multiplayer Lobbies** – Join a game with a unique code.
- **👥 Host & Player System** – Host controls the game, players compete.
- **📡 Real-Time Updates** – WebSockets keep the game lobby and trivia synced.
- **🎵 Music Trivia** – Answer music-related questions within a time limit.
- **🏆 Leaderboard** – Track scores and see who wins.
- **🛠️ User Accounts & Guest Play** – Registered users have stats, guests can play instantly.

---

## 📌 Setup & Installation

### 1️⃣ **Clone Repository**
```sh
git clone https://github.com/JaydenKlomp/beat-battle-royale.git
cd beat-battle-royale
```

### 2️⃣ **Install Dependencies**
Ensure you have **Composer** and **npm** installed.
```sh
composer install
```

### 3️⃣ **Setup Database**
1. Create a MySQL database (e.g., `beatbattle`).
2. Import the `beatbattle.sql` file.
3. Configure `.env` file with database credentials:
   ```ini
   database.default.hostname = localhost
   database.default.database = beatbattle
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   ```

### 4️⃣ **Run WebSocket Server**
Start the WebSocket server for real-time updates.
```sh
php app/WebSocketServerRunner.php
```

### 5️⃣ **Start Local Development Server**
```sh
php spark serve
```

### 6️⃣ **Access the App**
Open in browser:
```
http://localhost:8080
```

---

## 🔥 Feature Updates
### ✅ **Latest Features:**
✔️ Improved **lobby stability** (Players removed properly on disconnect).
✔️ WebSockets **optimized** for smoother game interactions.
✔️ **Dynamic player list** updates in real-time.
✔️ Host-only **game control system**.

### 🛠️ **Upcoming Features:**
🔜 **Custom Trivia Questions** – Users can create and share questions.
🔜 **Live Music Guessing** – Play short music clips for questions.
🔜 **Game Stats & Achievements** – Track lifetime trivia stats.
🔜 **Mobile-Friendly UI** – Improved layout for all devices.

---

## 👨‍💻 Contributing
Want to contribute? Feel free to submit a PR! Follow these steps:
1. **Fork the repository**
2. Create a new branch: `git checkout -b feature-branch`
3. Make your changes and **commit**: `git commit -m 'New feature added'`
4. Push your changes: `git push origin feature-branch`
5. Open a **Pull Request**

---

## 🛠️ Technologies Used
- **Backend:** CodeIgniter 4, PHP, MySQL
- **Frontend:** HTML, CSS, Bootstrap, JavaScript (AJAX, WebSockets)
- **WebSockets:** Ratchet (PHP WebSocket Server)
- **Database:** MySQL

---

## 📜 License
This project is open-source under the **MIT License**.

---


**_Made with ❤️ by Jayden Klomp_**

