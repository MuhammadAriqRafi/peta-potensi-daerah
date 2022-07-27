<?php

namespace App\Models;

use App\Controllers\Interfaces\CRUDInterface;
use App\Controllers\Interfaces\DatatableInterface;
use CodeIgniter\Model;

class Administrator extends Model implements DatatableInterface, CRUDInterface
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

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    public function getAdmins()
    {
        return $this->db->table('administrators')
            ->select('admin_id, nik, nama, username')
            ->get()->getResultArray();
    }

    public function fetchValidationRules(): array
    {
        return $rules = [
            'nik' => 'required|is_unique[administrators.nik,admin_id,{id}]',
            'nama' => 'required',
            'username' => 'required',
            'password' => 'required',
            'passconf' => 'required|matches[password]'
        ];
    }

    public function getRecords($start, $length, $orderColumn, $orderDirection)
    {
        return $this->select('nik, nama, username, admin_id')
            ->orderBy($orderColumn, $orderDirection)
            ->findAll($length, $start);
    }

    public function getTotalRecords()
    {
        return $this->countAllResults() ?? 0;
    }

    public function getRecordSearch($search, $start, $length, $orderColumn, $orderDirection)
    {
        return $this->orderBy($orderColumn, $orderDirection)
            ->like('nik', $search)
            ->orLike('nama', $search)
            ->orLike('username', $search)
            ->findAll($start, $length);
    }

    public function getTotalRecordSearch($search)
    {
        return $this->orderBy('admin_id', 'DESC')
            ->like('nik', $search)
            ->orLike('nama', $search)
            ->orLike('username', $search)
            ->countAllResults();
    }
}
