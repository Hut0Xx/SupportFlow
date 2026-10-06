<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse { $data=$request->validate(['name'=>'required|string|max:120','email'=>'required|email:rfc,dns|unique:users','password'=>'required|string|min:10|confirmed']); $data['organization_id']=1; $user=User::create($data); $user->roles()->attach(1); return response()->json(['user'=>$user,'token'=>$user->createToken('web')->plainTextToken],201); }
    public function login(Request $request): JsonResponse { $credentials=$request->validate(['email'=>'required|email','password'=>'required|string']); $user=User::where('email',$credentials['email'])->where('active',true)->first(); abort_unless($user && Hash::check($credentials['password'],$user->password),422,'Las credenciales no son válidas.'); $user->tokens()->where('name','web')->delete(); return response()->json(['user'=>$user->load('roles'),'token'=>$user->createToken('web')->plainTextToken]); }
    public function logout(Request $request): JsonResponse { $request->user()->currentAccessToken()?->delete(); return response()->json(['message'=>'Sesión cerrada.']); }
    public function me(Request $request): JsonResponse { return response()->json($request->user()->load('roles','teams')); }
    public function forgotPassword(Request $request): JsonResponse { $data=$request->validate(['email'=>'required|email']); Password::sendResetLink($data); return response()->json(['message'=>'Si la cuenta existe, recibirás un enlace de recuperación.']); }
}

