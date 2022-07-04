<?php

namespace App\Models;

use CodeIgniter\Model;

class Visitor extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'visitors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['session_id', 'ip', 'user_agent'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function countVisitorInAWeek()
    {
        return $this->db->table('visitors')
            ->where('YEARWEEK(`date_time`, 1) = YEARWEEK(CURDATE(), 1)')
            ->countAllResults();
    }

    public function countVisitorInAMonth()
    {
        return $this->db->table('visitors')
            ->where('YEAR(date_time)', date('Y'))
            ->where('MONTH(date_time)', date('m'))
            ->countAllResults();
    }

    public function countDailyVisitor()
    {
        $today = date("Y-m-d", strtotime('today'));

        return $this->db->table('visitors')
            ->where('DATE(date_time)', $today)
            ->countAllResults();
    }

    public function hasSessionId($session_id): bool
    {
        $visitor = $this->db->table('visitors')
            ->where('session_id', $session_id)
            ->countAllResults();

        if ($visitor > 0) return true;
        return false;
    }
}
