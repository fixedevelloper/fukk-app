<?php


namespace App\Http\Controllers\Auth;


use App\Http\Helpers\Helpers;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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
    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Cherche l'utilisateur
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Toujours renvoyer succès pour éviter de révéler si l'email existe
            return Helpers::success( 'Si cet email existe, un lien de réinitialisation a été envoyé.');
        }

        // Générer un token aléatoire
        $token = Str::random(64);

        // Stocker le token dans la table password_resets
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => bcrypt($token),
                'created_at' => now()
            ]
        );

        // Envoyer la notification
        $user->notify(new ResetPasswordNotification($token, $user->email));

        return response()->json([
            'message' => 'Si cet email existe, un lien de réinitialisation a été envoyé.'
        ]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Cherche l'entrée token dans la table password_resets
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return Helpers::error('Token invalide ou email inconnu.');
        }

        // Vérifie la correspondance du token
        if (!Hash::check($request->token, $record->token)) {
            return Helpers::error('Token invalide.');
        }

        // Vérifie expiration (60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return Helpers::error('Token expiré.');
        }

        // Récupère l’utilisateur
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return Helpers::error('Utilisateur introuvable.');
        }

        // Met à jour le mot de passe
        $user->password = Hash::make($request->password);
        $user->remember_token = Str::random(60);
        $user->save();

        // Supprime l’entrée token après usage
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return Helpers::success('Mot de passe réinitialisé avec succès.');
    }
}

