<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\UserBadge;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index_all(Request $request)
    {
        try {
            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $courses = DB::table('courses as c')
            ->join('user_courses as uc', 'uc.course_id', '=', 'c.id')
            ->join('users as u', 'uc.user_id', '=', 'u.id')
            ->where('c.status', 1);

            $user_courses = $courses
            ->where(function ($query) {
                $query->where('uc.role_id', 1)
                      ->orWhere('uc.role_id', 2);
            })
            ->select('uc.*')
            ->get();

            $tutors = $courses
            ->where(function ($query) {
                $query->where('uc.role_id', 1)
                      ->orWhere('uc.role_id', 2);
            })
            ->select('u.*')
            ->get();

            $courses = $courses->distinct()->select('c.*')->get();
            

            //$courses = Course::where('status', 1)->get();
            // Return a JSON response
            return response()->json([
                'message' => 'Fetch successful',
                'courses' => $courses,
                'user_courses' => $user_courses,
                'tutors' => $tutors
            ], 200);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Course fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'Course fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index_course(Request $request)
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

            // $courses = DB::table('courses as c')
            // ->join('user_courses as uc', 'uc.course_id', '=', 'c.id')
            // ->join('users as u', 'uc.user_id', '=', 'u.id')
            // ->where(function ($query) {
            //     $query->where('c.status', 1)
            //           ->orWhere('c.status', 2);
            // })
            // ->where('uc.status', 1)
            // ->where('uc.user_id', $user->id);

            // $user_courses = $courses
            // ->select('uc.*')
            // ->get();

            // $tutors = $courses
            // ->select('u.*')
            // ->get();

            // $courses = $courses->distinct()->select('c.*')->get();

            $created_course = DB::table('courses as c')
            ->join('user_courses as uc', 'uc.course_id', '=', 'c.id')
            ->where('uc.user_id', $user->id)
            ->where(function ($query) {
                $query->where('uc.role_id', 1)
                      ->orWhere('uc.role_id', 2);
            })
            ->where(function ($query) {
                $query->where('c.status', 1)
                      ->orWhere('c.status', 2);
            })
            ->select('c.*')
            ->get();

            $user_courses_created = $created_course->map(function ($course) {
                return UserCourse::where('course_id', $course->id)
                    ->where('status', 1)
                    ->where(function ($query) {
                        $query->where('role_id', 1)
                              ->orWhere('role_id', 2);
                    })
                    ->first();
            });

            $tutors_created = $user_courses_created->map(function ($user_course) {
                return User::where('id', $user_course->user_id)
                ->first();
            });

            $joined_course = DB::table('courses as c')
            ->join('user_courses as uc', 'uc.course_id', '=', 'c.id')
            ->where('uc.user_id', $user->id)
            ->where('uc.role_id', 3)
            ->where('c.status', 1)
            ->where('uc.status', 1)
            ->select('c.*')
            ->get();

            $user_courses_joined = $joined_course->map(function ($course) {
                return UserCourse::where('course_id', $course->id)
                    ->where('status', 1)
                    ->where(function ($query) {
                        $query->where('role_id', 1)
                              ->orWhere('role_id', 2);
                    })
                    ->first();
            });

            $tutors_joined = $user_courses_joined->map(function ($user_course) {
                return User::where('id', $user_course->user_id)
                ->first();
            });

            // Return the token and user data
            return response()->json([
                'message' => 'Fetch courses successful',
                'created_course' => $created_course,
                'user_courses_created' => $user_courses_created,
                'tutors_created' => $tutors_created,
                'joined_course' => $joined_course,
                'user_courses_joined' => $user_courses_joined,
                'tutors_joined' => $tutors_joined
                // 'courses' => $courses,
                // 'tutors' => $tutors
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Courses fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'Courses fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index_course_courseid(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'course_id' => 'required|integer|exists:courses,id',
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $course = Course::where('id', $validatedData['course_id'])->first();

            // Return the token and user data
            return response()->json([
                'message' => 'Fetch successful',
                'course' => $course,
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Course fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'Course fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index_user_course(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'course_id' => 'required|integer|exists:courses,id',
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $is_user_tutor = UserCourse::where('user_id', $user->id)
            ->where('course_id', $validatedData['course_id'])
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('role_id', 1)
                      ->orWhere('role_id', 2);
            })
            ->first();

            $is_user_student = UserCourse::where('user_id', $user->id)
            ->where('course_id', $validatedData['course_id'])
            ->where('status', 1)
            ->where('role_id', 3)
            ->first();

            $user_course = UserCourse::where('user_id', $user->id)
            ->where('course_id', $validatedData['course_id'])
            ->where('status', 1)
            ->first();

            $tutor = DB::table('user_courses as uc')
            ->where('uc.course_id', $validatedData['course_id'])
            ->where('uc.role_id', 1)
            ->leftjoin('users as u', 'u.id', '=', 'uc.user_id')
            ->select('u.*')
            ->first();

            $user_course_list = UserCourse::where('course_id', $validatedData['course_id'])
            ->leftjoin('users as u', 'u.id', '=', 'user_courses.user_id');

            $user_course_list_detail = $user_course_list->select('u.*')
            ->get();

            $user_course_list = $user_course_list->select('user_courses.*')
            ->get();

            if($is_user_tutor) {
                $is_user_tutor = true;
                $is_user_student = false;  
            } else {
                if ($is_user_student) {
                    $is_user_tutor = false;
                    $is_user_student = true;
                } else {
                    $is_user_tutor = false;
                    $is_user_student = false;
                }
            }

            // Return the token and user data
            return response()->json([
                'message' => 'Fetch successful',
                'is_user_tutor' => $is_user_tutor,
                'is_user_student' => $is_user_student,
                'user_course' => $user_course,
                'tutor' => $tutor,
                'user_course_list' => $user_course_list,
                'user_course_list_detail' => $user_course_list_detail
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('User Course fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'User Course fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Create a new course
    public function store(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'desc' => 'nullable|string|max:500',

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
                    $imagePath = $request->file('image')->store('resources/storage/course_picture', 'public');
                } else {
                    \Log::error('No image uploaded');
                }
            }

            // Use database transaction to ensure both operations succeed or fail together
            return DB::transaction(function () use ($validatedData, $user, $imagePath) {
                // Debug log before course creation
                \Log::info('Attempting to create course', $validatedData);

                // Create course
                $course = Course::create([
                    'name' => $validatedData['name'],
                    'desc' => $validatedData['desc'] ?? null,
                    'image' => $imagePath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Debug log after course creation
                \Log::info('Course creation result', ['course' => $course]);

                // Check if course exists but don't throw exception yet
                if (!$course) {
                    \Log::error('Course creation returned null');
                    throw new \Exception('Course creation failed - returned null');
                }

                // Debug log course ID
                \Log::info('Course ID check', ['id' => $course->id]);

                // Create a record in the UserCourse table
                $userCourse = UserCourse::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'role_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Debug log user course creation
                \Log::info('UserCourse creation result', ['userCourse' => $userCourse]);

                return response()->json([
                    'message' => 'Course created successfully.',
                    'course' => $course,
                    'user_course' => $userCourse
                ], 201);
            });

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Course creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'Course creation failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'course_id' => 'required|integer|exists:courses,id', // Ensure course exists in the courses table
                'name' => 'required|string|max:255',
                'desc' => 'nullable|string|max:500',
                'status' => 'required|integer',

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
    
            // Find the course to update
            $course = Course::find($validatedData['course_id']);
    
            // Check if the course exists
            if (!$course) {
                return response()->json([
                    'message' => 'Course not found',
                ], 404); // Not Found
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
    
            // Update the course
            $course->name = $validatedData['name'];
            $course->desc = $validatedData['desc'] ?? null;
            $course->status = $validatedData['status'];
            $course->image = $imagePath ?? $course->image;
            $course->updated_at = now();
    
            // Save the changes
            $course->save();
    
            // Return a success response
            return response()->json([
                'message' => 'Course updated successfully',
                'course' => $course,
            ], 201);
    
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Course update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            // Return an error response
            return response()->json([
                'message' => 'Course update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update_user_course(Request $request)
    {
        //joining/leaving course

        try {
            // Validate input
            $validatedData = $request->validate([
                'course_id' => 'required|integer|exists:courses,id', // Ensure course exists in the courses table
                'status' => 'nullable|integer', //0 = leaving, 1 = joining
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

                //user leaving course
                if($validatedData['status'] == 0) {
                    $userCourse = UserCourse::where('user_id', $user->id)
                    ->where('course_id', $validatedData['course_id'])
                    ->where('status', 1)
                    ->first();
                    
                    if($userCourse) {
                        $userCourse->status = 0;
                        $userCourse->updated_at = now();
            
                        // Save the changes
                        $userCourse->save();   
                    }

                    $course = Course::where('id', $validatedData['course_id'])
                    ->first();

                    if($course) {
                        $course->total_joined = $course->total_joined - 1;
                        $course->save();
                    }

                    //user joining course
                } else if($validatedData['status'] == 1) {
                    $userCourse = UserCourse::create([
                        'user_id' => $user->id,
                        'course_id' => $validatedData['course_id'],
                        'role_id' => 3,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);    

                    $course = Course::where('id', $validatedData['course_id'])
                    ->first();

                    if($course) {
                        $course->total_joined = $course->total_joined + 1;
                        $course->save();
                    }

                    // transfer old (userCourse) review in the same course to new userCourse if available
                    $allUserCourse = UserCourse::where('user_id', $user->id)
                    ->where('course_id', $validatedData['course_id'])
                    ->get();

                    foreach($allUserCourse as $auc) {
                        if($auc->rating != null) {
                            $userCourse->rating = $auc->rating;
                            $userCourse->comment_review = $auc->comment_review ?? null;
                            $userCourse->save();

                            $auc->rating = null;
                            $auc->comment_review = null;
                            $auc->save();
                        }
                    }

                    //check if user has badge 1
                    $userBadge = UserBadge::where('user_id', $user->id)
                    ->where('status', 1)
                    ->where('badge_id', 1)
                    ->first();

                    if(!$userBadge) {
                        $userBadgeNewCreated = UserBadge::create([
                            'user_id' => $user->id,
                            'badge_id' => 1,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Debug log user course creation
                \Log::info('UserCourse update result', [
                    'userCourse' => $userCourse,
                    'userBadgeNewCreated' => $userBadgeNewCreated ?? null,
                ]);

                return response()->json([
                    'message' => 'UserCourse updated successfully.',
                    'user_course' => $userCourse,
                    'userBadgeNewCreated' => $userBadgeNewCreated ?? null,
                ], 201);
            });

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('UserCourse update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'UserCourse update failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index_review(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'course_id' => 'required|integer|exists:courses,id',
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $data = DB::table('user_courses as uc')
            ->join('users as u', 'u.id', '=', 'uc.user_id')
            ->where('uc.course_id', $validatedData['course_id'])
            // ->where('uc.status', 1)
            ->where('uc.rating' , '!=', null);

            $reviews = $data->select('uc.*')->get();
            $users = $data->select('u.*')->get();

            // Return the token and user data
            return response()->json([
                'message' => 'Fetch successful',
                'reviews' => $reviews,
                'users' => $users,
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Review fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'Review fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update_review(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                //'course_id' => 'required|integer|exists:courses,id', // Ensure course exists in the courses table
                'user_course_id' => 'required|integer|exists:user_courses,id',
                'rating' => 'required|numeric|min:1|max:5',
                'comment_review' => 'nullable|string|max:500',
                'is_delete' => 'required|integer', // 0: not delete, 1: delete
            ]);

            \Log::info('validatedData result', ['validatedData' => $validatedData]);

            $user = User::where('id', Auth::user()->id)
                    ->first();

            // Check if the user exists and the password is correct
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }

            $userCourse = UserCourse::where('id', $validatedData['user_course_id'])
            ->where('status', 1)
            ->first();

            if ($userCourse) {
                if($validatedData['is_delete'] == 1) {
                    $userCourse->rating = null;
                    $userCourse->comment_review = null;
                } else {
                    $userCourse->rating = $validatedData['rating'];
                    $userCourse->comment_review = $validatedData['comment_review'] ?? null;
                }
                
                // $userCourse->status = $validatedData['status'] ?? $userCourse->status;
                $userCourse->updated_at = now();
    
                // Save the changes
                $userCourse->save();

                // Calculate new average rating for the course
                $course = Course::find($userCourse->course_id);
            
                if ($course) {
                    $averageRating = UserCourse::where('course_id', $course->id)
                        ->where('status', 1)
                        ->whereNotNull('rating')
                        ->avg('rating');
    
                    $course->average_rating = $averageRating ? round($averageRating, 2) : null;
                    $course->save();
                }
            }

            return response()->json([
                'message' => 'UserCourse updated successfully.',
                'user_course' => $userCourse
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('UserCourse update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'UserCourse update failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index_user_course_join(Request $request) {
        try {
            // Validate input
            $validatedData = $request->validate([
                'course_id' => 'required|integer|exists:courses,id',
            ]);

            $user = User::where('id', Auth::user()->id)
                    ->first();  

            // Check if the user exists
            if (!$user) {
                return response()->json([
                    'message' => 'User not exist',
                ], 401); // Unauthorized
            }
            
            $data = DB::table('user_courses as uc')
            ->join('users as u', 'uc.user_id', '=', 'u.id')
            ->where('uc.course_id', $validatedData['course_id'])
            ->where('uc.status', 1)
            ->where('uc.role_id', 3);

            $user_courses = $data->select('uc.*')->get();
            $users = $data->select('u.*')->get();

            return response()->json([
                'message' => 'Fetch successful',
                'user_courses' => $user_courses,
                'users' => $users
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('User joined course  fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            return response()->json([
                'message' => 'User joined course fetch failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
