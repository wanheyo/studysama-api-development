<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Comment;
use App\Models\Resource;
use App\Models\ResourceFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ResourceController extends Controller
{
    public function index_all(Request $request)
    {
        $resources = Resource::all();
        // Return a JSON response
        return response()->json($resources);
    }

    public function index_resource_lesson(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'lesson_id' => 'required|integer|exists:lessons,id', // Ensure course exists in the lessons table
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $resources = Resource::where('lesson_id', $validatedData['lesson_id'])
            ->where('resources.status', 1)
            // ->leftjoin('resource_files', 'resource_files.id', '=', 'resources.file_id')
            ->get();

            $resource_files = Resource::join('resource_files', 'resource_files.id', '=', 'resources.file_id')
            ->where('resources.lesson_id', $validatedData['lesson_id'])
            ->get();

            $comments = collect();

            foreach ($resources as $resource) {
                // Fetch comments for each resource
                $comment = Comment::where('resource_id', $resource->id)
                    ->where('status', 1)
                    ->get();

                $comments = $comments->merge($comment);
            }

            // Return the token and user data
            return response()->json([
                'message' => 'Fetch successful',
                'resources' => $resources,
                'resource_files' => $resource_files,
                'comments' => $comments
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
                'name' => 'required|string|max:255',
                'desc' => 'nullable|string|max:500',
                'category' => 'required|integer',
                'link' => 'nullable|string|max:255',
                'lesson_id' => 'required|integer|exists:lessons,id', // Ensure lesson_id exists in the lessons table

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
                \Log::info('Attempting to create resource', $validatedData);

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

                $resource = Resource::create([
                    'name' => $validatedData['name'],
                    'desc' => $validatedData['desc'] ?? null,
                    'category' => $validatedData['category'],
                    'link' => $validatedData['link'] ?? null,
                    'lesson_id' => $validatedData['lesson_id'],
                    'file_id' => $resource_file->id ?? null,
                ]);

                // Debug log after resource creation
                \Log::info('Resource creation result', ['resource' => $resource]);

                return response()->json([
                    'message' => 'Resource created successfully.',
                    'resource' => $resource,
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
                'message' => 'Resource creation failed',
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'isUpdateFile' => 'required|integer', //0 = false, 1 = true
                'resource_id' => 'required|integer|exists:resources,id', 
                'name' => 'required|string|max:255',
                'desc' => 'nullable|string|max:500',
                'category' => 'required|integer',
                'link' => 'nullable|string|max:255',
                'status' => 'nullable|integer',
                
                'file_id' => 'nullable|integer',
                'file_name' => 'nullable|string|max:255',
                'file_type' => 'nullable|string|max:255',
                'file_status' => 'nullable|integer',
                'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,bmp,tiff,doc,docx,pdf,txt,rtf,odt,zip,rar,7z|max:5120',
            ]);
    
            $user = User::where('id', Auth::user()->id)
                    ->first();
    
            // Check if the user exists
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            // Find the resource to update
            $resource = Resource::find($validatedData['resource_id']);
    
            // Check if the resource exists
            if (!$resource) {
                return response()->json([
                    'message' => 'Resource not found',
                ], 404); // Not Found
            }

            $resource_file = null;

            if($validatedData['isUpdateFile'] == 1) {

                if(!empty($validatedData['file'])) {
                    if ($request->hasFile('file')) {
                        \Log::info('File uploaded successfully: ' . $request->file('file')->getClientOriginalName());
                        $resourceFilePath = $request->file('file')->store('resources/storage/resource_file', 'public');
                    } else {
                        \Log::error('No file uploaded');
                    }
    
                    if(isset($validatedData['file_id'])) {
                        $resource_file = ResourceFile::find($validatedData['file_id']);
    
                        if ($resource_file) {
                            $resource_file->name = $resourceFilePath;
                            $resource_file->type = $validatedData['file_type'];
                            $resource_file->status = $validatedData['file_status'] ?? 1;
                            $resource_file->updated_at = now();
        
                            // dd($resource_file);
                            $resource_file->save();
        
                        } 
                    } else {
                        $resource_file = ResourceFile::create([
                            'name' => $resourceFilePath, //path
                            'type' => $validatedData['file_type'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }    
                }           
            }

            // Debug log after file creation
            \Log::info('File creation result', ['resource_file' => $resource_file]);
    
            // Update the resource
            $resource->name = $validatedData['name'];
            $resource->desc = $validatedData['desc'] ?? null;
            $resource->category = $validatedData['category'];
            $resource->link = $validatedData['link'] ?? null;
            $resource->status = $validatedData['status'] ?? 1;
            $resource->updated_at = now();

            //check if link exists
            if(isset($validatedData['link'])) {
                //check if file_id exists, if yes, then update resource_file status to 0, resource->file_id to null
                //means that resource contains a file before updating to link
                if(isset($validatedData['file_id'])) {
                    $resource_file = ResourceFile::find($validatedData['file_id']);

                    if ($resource_file) {
                        $resource_file->status = 0;
                        $resource_file->updated_at = now();
                        $resource_file->save();
                    } 
                }
                $resource->file_id = null;

            } else {
                $resource->file_id = $validatedData['file_id'] ?? ($resource_file ? $resource_file->id : null) ?? null;
            }
            
            // Save the changes
            $resource->save();

            // Return a success response
            return response()->json([
                'message' => 'Resource updated successfully',
                'resource' => $resource,
            ], 201);
    
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Resource update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            // Return an error response
            return response()->json([
                'message' => 'Resource update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
