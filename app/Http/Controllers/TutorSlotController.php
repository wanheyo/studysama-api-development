<?php

namespace App\Http\Controllers;

use App\Models\TutorSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class TutorSlotController extends Controller
{
    public function index_all(Request $request)
    {
        $tutorslots = TutorSlot::all();
        // Return a JSON response
        return response()->json($tutorslots);
    }

    public function index_tutorslot_course(Request $request)
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

            $tutorslots = TutorSlot::where('course_id', $validatedData['course_id'])
            ->where('status', 1)
            ->get();

            // Return the token and user data
            return response()->json([
                'message' => 'Tutor Slot fetch successful',
                'tutorslots' => $tutorslots,
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Tutor Slot fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'Tutor Slot fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate input

            \Log::info('Incoming request data', ['data' => $request->all()]);
            
            $validatedData = $request->validate([
                'course_id' => 'required|integer|exists:courses,id',
                'name' => 'required|string|max:255',
                'desc' => 'nullable|string|max:500',
                'type' => 'required|string|max:255',
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'location' => 'required|string|max:500',
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $tutorslot = TutorSlot::create([
                'course_id' => $validatedData['course_id'],
                'name' => $validatedData['name'],
                'desc' => $validatedData['desc'] ?? null,
                'type' => $validatedData['type'],
                'date' => $validatedData['date'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'location' => $validatedData['location'],
                'created_at' => now(),
                'updated_at' => now(),
            
            ]);

            // Debug log after lesson creation
            \Log::info('TutorSlot creation result', ['tutorslot' => $tutorslot]);

            // Check if lesson exists but don't throw exception yet
            if (!$tutorslot) {
                \Log::error('TutorSlot creation returned null');
                throw new \Exception('TutorSlot creation failed - returned null');
            }

            return response()->json([
                'message' => 'TutorSlot created successfully.',
                'tutorslot' => $tutorslot,
            ], 201);


        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('TutorSlot creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'TutorSlot creation failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'tutor_slot_id' => 'required|integer|exists:tutor_slots,id',
                'name' => 'nullable|string|max:255',
                'desc' => 'nullable|string|max:500',
                'type' => 'nullable|string|max:255',
                'date' => 'nullable|date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i|after:start_time',
                'location' => 'nullable|string|max:500',
                'status' => 'nullable|integer',
            ]);

            \Log::info('Validated Data', $validatedData);

            $user = User::where('id', Auth::user()->id)->first();

            // Check if the user exists
            if (!$user) {
                return response()->json([
                    'message' => 'User does not exist',
                ], 401); // Unauthorized
            }

            // Find the tutor slot to update
            $tutorSlot = TutorSlot::find($validatedData['tutor_slot_id']);

            // Check if the tutor slot exists
            if (!$tutorSlot) {
                return response()->json([
                    'message' => 'Tutor slot not found',
                ], 404); // Not Found
            }

            // Update the tutor slot
            $tutorSlot->name = $validatedData['name'] ?? $tutorSlot->name;
            $tutorSlot->desc = $validatedData['desc'] ?? null;
            $tutorSlot->type = $validatedData['type'] ?? $tutorSlot->type;
            $tutorSlot->date = $validatedData['date'] ?? $tutorSlot->date;
            $tutorSlot->start_time = $validatedData['start_time'] ?? $tutorSlot->start_time;
            $tutorSlot->end_time = $validatedData['end_time'] ?? $tutorSlot->end_time;
            $tutorSlot->location = $validatedData['location'] ?? $tutorSlot->location;
            $tutorSlot->status = $validatedData['status'] ?? $tutorSlot->status;
            $tutorSlot->updated_at = now();

            // Save the changes
            $tutorSlot->save();

            // Return a success response
            return response()->json([
                'message' => 'Tutor slot updated successfully',
                'tutor_slot' => $tutorSlot,
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Tutor slot update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return an error response
            return response()->json([
                'message' => 'Tutor slot update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
