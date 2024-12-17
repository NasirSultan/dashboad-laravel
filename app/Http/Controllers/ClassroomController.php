<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// method 1

class ClassroomController extends Controller
{
  
    public function index()
    {
        $classrooms = DB::table('classrooms')
            ->join('students', 'classrooms.student_id', '=', 'students.id')
            ->join('subjects', 'classrooms.subject_id', '=', 'subjects.id') 
            // ->select('classrooms.id as classroom_id', 'classrooms.name as classroom_name', 
            //          'students.name as student_name', 'subjects.name as subject_name')
            ->get(); 

        return response()->json($classrooms); 
    }

    public function show(Request $request)
    {
        $search = $request->input('search', ''); 
    
        $query = DB::table('classrooms')
            ->leftJoin('students', 'classrooms.student_id', '=', 'students.id')
            ->leftJoin('subjects', 'classrooms.subject_id', '=', 'subjects.id')
            ->select('classrooms.id as classroom_id', 
                     'classrooms.name as classroom_name', 
                     DB::raw('COALESCE(students.name, "No student assigned") as student_name'),
                     DB::raw('COALESCE(subjects.name, "No subject assigned") as subject_name'))
            ->distinct(); 
    
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('classrooms.name', 'like', '%'.$search.'%')
                  ->orWhere('students.name', 'like', '%'.$search.'%')
                  ->orWhere('subjects.name', 'like', '%'.$search.'%');
            });
        }
    
       
        $classrooms = $query->get();
    
        
        if ($classrooms->isEmpty()) {
            return response()->json(['message' => 'No classrooms found'], 404);
        }
    
        return response()->json($classrooms); 
    }
    

}


// Method 2

// class ClassroomController extends Controller
// {
//     // Reusable query to fetch classroom data
//     private function getClassroomsQuery()
//     {
//         return DB::table('classrooms')
//             ->join('students', 'classrooms.student_id', '=', 'students.id')
//             ->join('subjects', 'classrooms.subject_id', '=', 'subjects.id')
//             ->select(
//                 'classrooms.id as classroom_id',
//                 'classrooms.name as classroom_name',
//                 'students.name as student_name',
//                 'subjects.name as subject_name'
//             );
//     }

//     // Display all classrooms
//     public function index()
//     {
//         $classrooms = $this->getClassroomsQuery()->get();

//         return response()->json($classrooms);
//     }

//     // Search classrooms
//     public function show(Request $request)
//     {
//         $search = $request->input('search');

//         $query = $this->getClassroomsQuery();

//         if ($search) {
//             $query->where(function ($q) use ($search) {
//                 $q->where('classrooms.name', 'like', '%' . $search . '%')
//                   ->orWhere('students.name', 'like', '%' . $search . '%')
//                   ->orWhere('subjects.name', 'like', '%' . $search . '%');
//             });
//         }

//         $classrooms = $query->get();

//         if ($classrooms->isEmpty()) {
//             return response()->json(['message' => 'No classrooms found'], 404);
//         }

//         return response()->json($classrooms);
//     }
// }
