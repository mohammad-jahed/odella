<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentSubscriptionController extends Controller
{
    public function unActiveStudent(): JsonResponse
    {
        $students = User::role('Student')->where('status', 0)->get();
        return $this->getJsonResponse($students, "Students Fetch Successfully");
    }
}
