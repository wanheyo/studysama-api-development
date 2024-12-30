<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\UserFollow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    // Method to get all users
    public function index_all(Request $request)
    {

        try {
            // Validate input
            // $validatedData = $request->validate([
            //     'id' => 'required|integer|exists:users,id',
            // ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $users = User::where('status', 1)->get();

            // Return the token and user data
            return response()->json([
                'message' => 'Fetch successful',
                'users' => $users,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('User fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Fetch failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index_user(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'user_id' => 'nullable|integer|exists:users,id',
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            if(isset($validatedData['user_id'])) {
                $user = User::where('id', $validatedData['user_id'])->first();
            }

            // Return the token and user data
            return response()->json([
                'message' => 'Fetch successful',
                'user' => $user,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('User fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Fetch failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Using validate() method returns the validated data, not a Validator instance
            // So we don't need to check fails()
            $validatedData = $request->validate([
                'username' => 'required|string|max:16|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);
    
            // Create a new user instance using validated data
            $user = User::create([
                'name' => $validatedData['username'],
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);
    
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('User registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        \Log::info('Update request received', [
            'files' => $request->allFiles(),
            'data' => $request->all()
        ]);

        try {
            // Validate input
            $validatedData = $request->validate([
                'username' => 'nullable|string|max:16|unique:users,username,' . Auth::id(),
                'name' => 'nullable|string|max:70',
                'bio' => 'nullable|string|max:500',
                'email' => 'nullable|string|email|max:255|unique:users,email,' . Auth::id(),
                'phone_num' => [
                    'nullable',
                    'regex:/^\+60\d{9,10}$/',
                    'unique:users,phone_num,' . Auth::id(),
                ],

                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            ]);            
    
            $user = User::where('id', Auth::user()->id)
                    ->first();
    
            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $imagePath = null;

            if (isset($validatedData['image'])) {
                // ...
                if ($request->hasFile('image')) {
                    \Log::info('Image uploaded successfully: ' . $request->file('image')->getClientOriginalName());
                    $imagePath = $request->file('image')->store('resources/storage/profile_picture', 'public');
                } else {
                    \Log::error('No image uploaded');
                }
            }

            // Update the user
            if(isset($validatedData['username'])) {
                if($user->username != $validatedData['username']) {
                    $user->last_changed_at = now();
                }
            }
            $user->username = $validatedData['username'] ?? $user->username;
        
            $user->name = $validatedData['name'] ?? $user->name;
            $user->bio = $validatedData['bio'] ?? $user->bio;
            $user->email = $validatedData['email'] ?? $user->email;
            $user->phone_num = $validatedData['phone_num'] ?? $user->phone_num;
            $user->image = $imagePath ?? $user->image;
            $user->updated_at = now();
    
            // Save the changes
            $user->save();
    
            // Return a success response
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user,
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors and return detailed messages
            $errors = $e->validator->errors();
    
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errors->all(), // Returns all error messages
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('User update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            // Return an error response
            return response()->json([
                'message' => 'User update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'login' => 'required|string', // This will be either email or username
            'password' => 'required|string|min:8',
        ]);

        // Try to find the user by email or username
        $user = User::where('email', $request->login)
                    ->orWhere('username', $request->login)
                    ->first();

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401); // Unauthorized
        }

        // Create a token (using Laravel's Passport or Sanctum for API token generation)
        // Assuming you're using Laravel Sanctum for API authentication, you can use:
        $token = $user->createToken('StudySamaApp')->plainTextToken;

        // Return the token and user data
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,  // Provide the API token
            'user' => $user,    // Provide user data
        ], 200);
    }

    public function logout(Request $request) {
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete();
        });

        return response()->json([
            'message' => 'Logout success',
        ], 200);
    }

    public function index_follow(Request $request) {
        try {
            // Validate input
            $validatedData = $request->validate([
                'user_id' => 'nullable|integer|exists:users,id',
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            if(isset($validatedData['user_id'])) {
                $user = User::where('id', $validatedData['user_id'])->first();
            }

            $user_following = UserFollow::where('user_follower_id', $user->id)
                ->where('status', 1)
                ->get();

            $user_follower = UserFollow::where('user_followed_id', $user->id)
                ->where('status', 1)
                ->get();

            // Fetch user data for followers and following
            $followers = $user_follower->map(function ($follow) {
                return User::where('id', $follow->user_follower_id)->first();
            });

            $following = $user_following->map(function ($follow) {
                return User::where('id', $follow->user_followed_id)->first();
            });

            // Return the message and user data
            return response()->json([
                'message' => 'Fetch successful',
                'user_follower' => $user_follower,
                'follower' => $followers,
                'user_following' => $user_following,
                'following' => $following,
            ], 200);


        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('User Follow fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'User Follow fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update_follow(Request $request) {
        try {
            // Validate input
            $validatedData = $request->validate([
                'user_followed_id' => 'required|integer|exists:users,id',
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();  

            // Check if the user exists
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }            

            if(isset($validatedData['user_id'])) {
                $user = User::where('id', $validatedData['user_id'])->first();
            }

            $user_follow = UserFollow::where('user_follower_id', $user->id)
                ->where('user_followed_id', $validatedData['user_followed_id'])
                ->where('status', 1)
                ->first();

            if($user_follow) {
                $user_follow->status = 0;
                $user_follow->updated_at = now();
                $user_follow->save();

                $user_followed = User::where('id', $validatedData['user_followed_id'])->first();

                $user_followed->total_follower = $user_followed->total_follower - 1;
                $user_followed->save();

                return response()->json([
                    'message' => 'User unfollowed successfully',
                ], 200);

                
            } else {
                $user_follow = UserFollow::create([
                    'user_follower_id' => $user->id,
                    'user_followed_id' => $validatedData['user_followed_id'],
                    'created_at' => now(),
                    // 'status' => 1,
                ]);

                $user_followed = User::where('id', $validatedData['user_followed_id'])->first();

                $user_followed->total_follower = $user_followed->total_follower + 1;
                $user_followed->save();

                return response()->json([
                    'message' => 'User followed successfully',
                    'user_follow' => $user_follow,
                ], 200);
            }

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('User Follow create failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'User Follow create failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index_user_badge(Request $request) {
        try {
            // Validate input
            $validatedData = $request->validate([
                'user_id' => 'nullable|integer|exists:users,id',
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            if(isset($validatedData['user_id'])) {
                $user = User::where('id', $validatedData['user_id'])->first();
            }

            $user_badges = UserBadge::where('user_id', $user->id)
                ->where('status', 1)
                ->get();

            $badges = $user_badges->map(function ($badge) {
                return Badge::where('id', $badge->badge_id)->first();
            });

            \Log::error('User Badge create failed', [
                'message' => 'Fetch successful',
                'user_badges' => $user_badges,
                'badges' => $badges
            ]);

            // Return the message and user data
            return response()->json([
                'message' => 'Fetch successful',
                'user_badges' => $user_badges,
                'badges' => $badges
            ], 200);


        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('User Badge fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'User Badge fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
