<?php

namespace App\Models;

use CodeIgniter\Model;

class GameSessionModel extends Model
{
    protected $table = 'game_sessions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['game_code', 'host_id', 'status'];
}
