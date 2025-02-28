<?php

namespace App\Models;

use CodeIgniter\Model;

class GamePlayerModel extends Model
{
    protected $table = 'game_players';
    protected $primaryKey = 'id';
    protected $allowedFields = ['game_id', 'user_id', 'guest_name', 'score', 'is_eliminated'];
}
