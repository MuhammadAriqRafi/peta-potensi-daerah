<?php

namespace App\Models;

use CodeIgniter\Model;

class Administrator extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'administrators';
    protected $primaryKey       = 'admin_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nik', 'nama', 'username', 'password'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getAdmins()
    {
        return $this->db->table('administrators')
            ->select('admin_id, nik, nama, username')
            ->get()->getResultArray();
    }
}
