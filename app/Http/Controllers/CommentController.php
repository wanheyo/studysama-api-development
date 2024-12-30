<?php

namespace App\Http\Controllers;

//use PhpParser\Comment;
use App\Models\Resource;
use App\Models\ResourceFile;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index_all(Request $request)
    {
        $comments = Comment::all();
        // Return a JSON response
        return response()->json($comments);
    }

    public function index_comment_resource(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'resource_id' => 'required|integer|exists:resources,id', // Ensure resource exists in the resources table
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $data = DB::table('comments as c')
            ->join('user_courses as uc', 'uc.id', '=', 'c.user_course_id')     
            ->join('users as u', 'u.id', '=', 'uc.user_id')
            ->join('resources as r', 'r.id', '=', 'c.resource_id')
            ->leftjoin('resource_files as rf', 'rf.id', '=', 'r.file_id')
            ->where('c.resource_id', $validatedData['resource_id'])
            ->where('c.status', 1);
            // ->where('uc.status', 1);

            $comments = $data->select('c.*')->get();
            $user_courses = $data->select('uc.*')->get();
            $users = $data->select('u.*')->get();

            // Return the token and user data
            return response()->json([
                'message' => 'Fetch successful',
                'comments' => $comments,
                'user_courses' => $user_courses,
                'users' => $users,
                // 'resource_files' => $resource_files,
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Resource fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'Resource fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        \Log::info('Received request data', $request->all());

        try {
            // Validate input
            $validatedData = $request->validate([
                'user_course_id' => 'required|integer|exists:user_courses,id', 
                'resource_id' => 'required|integer|exists:resources,id',
                'comment_text' => 'required|string|max:500',

                'file_name' => 'nullable|string|max:255',
                'file_type' => 'nullable|string|max:255',
                'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,bmp,tiff,doc,docx,pdf,txt,rtf,odt,zip,rar,7z|max:5120',
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            return DB::transaction(function () use ($validatedData, $user, $request) {
                // Debug log before course creation
                \Log::info('Attempting to create comment', $validatedData);

                $resource_file = null;
                $resourceFilePath = null;

                if (isset($validatedData['file_name']) && isset($validatedData['file_type'])) {
                    // ...

                    if ($request->hasFile('file')) {
                        \Log::info('File uploaded successfully: ' . $request->file('file')->getClientOriginalName());
                        $resourceFilePath = $request->file('file')->store('resources/storage/resource_file', 'public');
                    } else {
                        \Log::error('No file uploaded');
                    }

                    $resource_file = ResourceFile::create([
                        'name' => $resourceFilePath, //path
                        'type' => $validatedData['file_type'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Debug log after file creation
                    \Log::info('File creation result', ['resource_file' => $resource_file]);
                }

                $comment = Comment::create([
                    'user_course_id' => $validatedData['user_course_id'],
                    'resource_id' => $validatedData['resource_id'],
                    'comment_text' => $validatedData['comment_text'],
                    'file_id' => $resource_file->id ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Debug log after comment creation
                \Log::info('Comment creation result', ['comment' => $comment]);

                return response()->json([
                    'message' => 'Comment created successfully.',
                    'comment' => $comment,
                ], 201);

            });
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Resource creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return a JSON error response
            return response()->json([
                'message' => 'Comment creation failed',
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'comment_id' => 'required|integer|exists:comments,id',
                'comment_text' => 'nullable|string|max:500',
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
    
            // Find the comment to update
            $comment = Comment::find($validatedData['comment_id']);
    
            // Check if the comment exists
            if (!$comment) {
                return response()->json([
                    'message' => 'Comment not found',
                ], 404); // Not Found
            }
    
            // Update the comment
            // $comment->comment_text = $validatedData['comment_text'] ?? null;
            $comment->status = $validatedData['status'] ?? 1;
            $comment->updated_at = now();
    
            // Save the changes
            $comment->save();
    
            // Return a success response
            return response()->json([
                'message' => 'Comment updated successfully',
                'comment' => $comment,
            ], 201);
    
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Comment update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            // Return an error response
            return response()->json([
                'message' => 'Comment update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
