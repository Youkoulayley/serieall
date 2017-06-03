<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\LogRepository;

class AdminController extends Controller
{
    protected $nbPerPage = 20;
    protected $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function index() {
        $logs = $this->logRepository->getTenDistinctLogs();

        return view('admin/index', compact('logs'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewLog($id){
        $log = $this->logRepository->getLogsByID($id);

        return view('admin/system/logs/view', compact('log'));
    }



}
