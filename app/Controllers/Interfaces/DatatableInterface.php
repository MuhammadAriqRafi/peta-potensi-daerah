<?php

namespace App\Controllers\Interfaces;

interface DatatableInterface
{
    public function getRecords($start, $length, $orderColumn, $orderDirection);
    public function getTotalRecords();
    public function getRecordSearch($search, $start, $length, $orderColumn, $orderDirection);
    public function getTotalRecordSearch($search);
}
