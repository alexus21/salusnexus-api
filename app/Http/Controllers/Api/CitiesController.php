<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CitiesController extends Controller {
    public function index(): Collection {
        return DB::table('cities')->get(['id', 'name', 'department_id']);
    }

    public function getByDepartment($department_id): Collection {
        return DB::table('cities')->where('department_id', $department_id)->get(['id', 'name', 'department_id']);
    }
}
