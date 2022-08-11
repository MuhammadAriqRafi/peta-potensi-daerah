<?php

namespace App\Models;

use App\Controllers\Interfaces\CRUDInterface;
use App\Controllers\Interfaces\DatatableInterface;
use CodeIgniter\Model;

class Guestbook extends Model implements DatatableInterface, CRUDInterface
{
    protected $DBGroup          = 'default';
    protected $table            = 'guestbooks';
    protected $primaryKey       = 'guestbook_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'title', 'messages', 'status'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Callbacks
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = ['hashPassword'];
    // protected $beforeUpdate   = ['hashPassword'];

    // protected function hashPassword(array $data)
    // {
    //     $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    //     return $data;
    // }

    public function getRecords($start, $length, $orderColumn, $orderDirection)
    {
        return $this->select('guestbook_id, title, name, email, date_create, status')
            ->orderBy($orderColumn, $orderDirection)
            ->findAll($length, $start);
    }
    public function getRecordSearch($search, $start, $length, $orderColumn, $orderDirection)
    {
        return $this->select('guestbook_id, title, name, email, date_create, status')
            ->orderBy($orderColumn, $orderDirection)
            ->like('title', $search)
            ->orLike('name', $search)
            ->orLike('email', $search)
            ->findAll($length, $start);
    }

    public function getTotalRecords()
    {
        return $this->countAllResults() ?? 0;
    }

    public function getTotalRecordSearch($search)
    {
        return $this->select('guestbook_id, title, name, email, date_create, status')
            ->like('title', $search)
            ->orLike('name', $search)
            ->orLike('email', $search)
            ->countAllResults();
    }

    public function fetchValidationRules(): array
    {
        return $rules = [
            'name' =>  'required',
            'email' => 'required|valid_email',
            'title' => 'required',
            'messages' => 'required'
        ];
    }
}
