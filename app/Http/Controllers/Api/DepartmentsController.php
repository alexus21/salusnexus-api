<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DepartmentsController extends Controller {
    public function index(): Collection {
        return DB::table('departments')->get(['id', 'name']);
    }
}
