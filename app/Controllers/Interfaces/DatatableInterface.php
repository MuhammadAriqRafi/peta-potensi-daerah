<?php

namespace App\Controllers\Interfaces;

interface DatatableInterface
{
    public function getRecords($start, $length);
    public function getTotalRecords();
    public function getRecordSearch($search, $start, $length);
    public function getTotalRecordSearch($search);
}
