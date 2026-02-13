<?php


namespace App\Http\Controllers\Auth;


use App\Http\Helpers\Helpers;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * ✅ Enregistrement d'un nouvel utilisateur
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {

        $request->validate([
            'user_type'     => 'nullable|max:255',
            'first_name'     => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        $user = User::create([
            'first_name'     => $request->first_name,
            'last_name'     => $request->last_name,
            'phone'     => $request->phone,
            'email'    => $request->email,
            'user_type'=>$request->user_type,
            'role'=>$request->user_type==2?'customer':'vendor',
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('pos-app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->email,
                'role' => $user->role ?? 'customer',
                'token' => $token,
                'expires_in' => 3600,
            ]
        ], 201);
    }

    /**
     * ✅ Connexion
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Identifiants invalides',
                ], 401);
            }

            $user = Auth::user();

            // Optionnel : une seule session
            $user->tokens()->delete();

            $token = $user->createToken('web-app')->plainTextToken;

            logger($token);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $user->id,
                    'name' => trim($user->first_name . ' ' . $user->last_name),
                    'email' => $user->email,
                    'role' => $user->role ?? 'customer',
                    'token' => $token,
                    'expires_in' => 3600,
                ]
            ]);
        }catch (\Exception $exception){
            logger($exception);
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 401);
        }

    }

    /**
     * ✅ Déconnexion
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Supprime tous les tokens
        return Helpers::success( 'Déconnecté avec succès');
    }

    /**
     * ✅ Profil de l'utilisateur connecté
     */
    public function profile(Request $request)
    {
        return Helpers::success($request->user());
    }
}

