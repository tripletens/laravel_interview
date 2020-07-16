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
                'message' => "Token Generated",
                'data' => ['token' => compact('token')],
                'error' => []
            ];

            return response()->json();
        }

        public function register(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($validator->fails()){
                return response()->json($validator->errors(), 400);
            }

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);

            $token = JWTAuth::fromUser($user);
        
            // create the event 
            event( new UserRegistrationEvent($user));
            
            return response()->json(compact('user','token'),201);
        }

        public function getAuthenticatedUser()
            {
                try {

                        if (! $user = JWTAuth::parseToken()->authenticate()) {
                                return response()->json(['user_not_found'], 404);
                        }

                    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                        return response()->json(['token_expired'], $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                        return response()->json(['token_invalid'], $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                        return response()->json(['token_absent'], $e->getStatusCode());

                    }

                    return response()->json(compact('user'));
        }
    }