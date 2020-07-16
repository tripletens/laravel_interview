<?php

    namespace App\Http\Controllers;

    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;
    use Illuminate\Support\Facades\Mail;
    use App\Mail\RegistrationSuccess;
    use App\Events\UserRegistrationEvent;

    class UserController extends Controller
    {
        public function authenticate(Request $request)
        {
            $credentials = $request->only('email', 'password');

            try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    $data = [
                        'message' => "An Error Occured",
                        'data' => [],
                        'error' => ['invalid credentials']
                    ];
                    return response()->json($data, 400);
                }
            } catch (JWTException $e) {
                $data = [
                    'message' => "An Error Occured",
                    'data' => [],
                    'error' => ['could_not_create_token']
                ];
                return response()->json($data, 500);
            }

            $data = [
                'message' => "Login Successful",
                'data' => [compact('token')],
                'error' => []
            ];

            return response()->json($data);
        }

        public function register(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($validator->fails()){
                $data = [
                    'message' => "Token Generated",
                    'data' => [],
                    'error' => $validator->errors()
                ];
                return response()->json($data, 400);
            }

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);

            $token = JWTAuth::fromUser($user);
        
            // create the event 
            event( new UserRegistrationEvent($user));
            
            $data = [
                'message' => "User Successfully Registered",
                'data' => [compact('user','token')],
                'error' => $validator->errors()
            ];

            return response()->json($data,201);
        }

        public function getAuthenticatedUser()
            {
                try {

                        if (! $user = JWTAuth::parseToken()->authenticate()) {
                            $data = [
                                'message' => "User not Found",
                                'data' => [],
                                'error' => ['user_not_found']
                            ];
                            return response()->json($data, 404);
                        }

                    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                        $data = [
                            'message' => "Token Expired",
                            'data' => [],
                            'error' => ['token_expired']
                        ];
                        return response()->json($data, $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                        $data = [
                            'message' => "Token Invalid",
                            'data' => [],
                            'error' => ['token_invalid']
                        ];
                        
                        return response()->json(['token_invalid'], $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                        $data = [
                            'message' => "Token Absent",
                            'data' => [],
                            'error' => ['token_absent']
                        ];

                        return response()->json($data, $e->getStatusCode());

                    }

                    $data = [
                        'message' => "User Details",
                        'data' => [compact('user')],
                        'error' => []
                    ];

                    return response()->json($data);
        }
    }