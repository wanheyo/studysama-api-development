<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{

    public function index_all(Request $request)
    {
        $lessons = Lesson::all();
        // Return a JSON response
        return response()->json($lessons);
    }

    public function index_lesson_course(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'course_id' => 'required|integer|exists:courses,id', // Ensure course exists in the courses table
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $lessons = Lesson::where('course_id', $validatedData['course_id'])
            ->where('status', 1)
            ->get();

            // Return the token and user data
            return response()->json([
                'message' => 'Fetch successful',
                'lessons' => $lessons,
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Lesson fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'Lesson fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'learn_outcome' => 'nullable|string|max:500',
                'desc' => 'nullable|string|max:500',
                'course_id' => 'required|integer|exists:courses,id', // Ensure course exists in the courses table
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $lesson = Lesson::create([
                'name' => $validatedData['name'],
                'learn_outcome' => $validatedData['learn_outcome'] ?? null,
                'desc' => $validatedData['desc'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
                'course_id' => $validatedData['course_id'],
            ]);

            // Debug log after lesson creation
            \Log::info('Lesson creation result', ['lesson' => $lesson]);

            // Check if lesson exists but don't throw exception yet
            if (!$lesson) {
                \Log::error('Lesson creation returned null');
                throw new \Exception('Lesson creation failed - returned null');
            }

            return response()->json([
                'message' => 'Lesson created successfully.',
                'lesson' => $lesson,
            ], 201);


        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Lesson creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'Lesson creation failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'lesson_id' => 'required|integer|exists:lessons,id', // Ensure course exists in the courses table
                'name' => 'required|string|max:255',
                'learn_outcome' => 'nullable|string|max:500',
                'desc' => 'nullable|string|max:500',
                'status' => 'nullable|integer',
            ]);
    
            $user = User::where('id', Auth::user()->id)
                    ->first();
    
            // Check if the user exists
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }
    
            // Find the lesson to update
            $lesson = Lesson::find($validatedData['lesson_id']);
    
            // Check if the lesson exists
            if (!$lesson) {
                return response()->json([
                    'message' => 'Lesson not found',
                ], 404); // Not Found
            }
    
            // Update the lesson
            $lesson->name = $validatedData['name'];
            $lesson->learn_outcome = $validatedData['learn_outcome'] ?? null;
            $lesson->desc = $validatedData['desc'] ?? null;
            $lesson->status = $validatedData['status'] ?? 1;
            $lesson->updated_at = now();
    
            // Save the changes
            $lesson->save();
    
            // Return a success response
            return response()->json([
                'message' => 'Lesson updated successfully',
                'lesson' => $lesson,
            ], 201);
    
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Lesson update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            // Return an error response
            return response()->json([
                'message' => 'Lesson update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
