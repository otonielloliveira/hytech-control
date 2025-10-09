<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    public function index()
    {
        $lectures = Lecture::active()
                          ->ordered()
                          ->paginate(12);
                          
        return view('lectures.index', compact('lectures'));
    }

    public function show(Lecture $lecture)
    {
        if (!$lecture->is_active) {
            abort(404);
        }
        
        // Palestras relacionadas (próximas palestras)
        $relatedLectures = Lecture::active()
                                 ->where('id', '!=', $lecture->id)
                                 ->ordered()
                                 ->take(3)
                                 ->get();
        
        return view('lectures.show', compact('lecture', 'relatedLectures'));
    }
}
