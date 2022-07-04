<?php

namespace App\Models;

use CodeIgniter\Model;

class FotoTempat extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'foto_tempat';
    protected $primaryKey       = 'foto_tempat_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['post_id', 'filename'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
