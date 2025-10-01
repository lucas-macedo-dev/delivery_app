<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserApproved;
use App\Mail\UserRejected;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserApprovalController extends Controller
{
    public function approve(
        Request $request,
        User $user
    ) {
        try {
            $decrypted = decrypt($request->token);
            [$userId, $timestamp] = explode('|', $decrypted);

            // Verificar se o token tem menos de 72 horas
            if ($userId != $user->id || (now()->timestamp - $timestamp) > 259200) {
                return response()->view('errors.invalid-link');
            }
        } catch (\Exception $e) {
            return response()->view('errors.invalid-link');
        }

        if ($user->is_approved) {
            return redirect()->route('login')
                ->with('status', 'Acesso aprovado! Por favor, faça login.');
        }

        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id() ?? 1, // Se estiver logado, usa o ID, senão usa 1
        ]);

        Mail::to($user->email)->send(new UserApproved($user));

        return view('admin.user-approved', ['user' => $user]);
    }

    public function reject(
        Request $request,
        User $user
    ){
        try {
            $decrypted = decrypt($request->token);
            [$userId, $timestamp] = explode('|', $decrypted);

            if ($userId != $user->id || (now()->timestamp - $timestamp) > 259200) {
                return response()->view('errors.invalid-link');
            }
        } catch (\Exception $e) {
            return response()->view('errors.invalid-link');
        }

        if ($user->is_approved) {
            $this->revokeAccess($user);
            return redirect()->route('login')
                ->with('status', 'Acesso negado! Seu acesso anterior foi revogado. Por favor, entre em contato com o administrador.');
        }

        $userName  = $user->name;
        $userEmail = $user->email;

        Mail::to($userEmail)->send(new UserRejected($userName));
        $user->delete();

        return view('admin.user-rejected', ['userName' => $userName]);
    }

    public function index()
    {
        $pendingUsers  = User::where('is_approved', false)->get();
        $approvedUsers = User::where('is_approved', true)->get();

        return view('admin.users.index', compact('pendingUsers', 'approvedUsers'));
    }

    // Aprovar via painel admin (POST)
    public function approveAction(User $user)
    {
        if ($user->is_approved) {
            return back()->with('warning', 'Usuário já está aprovado.');
        }

        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
        Mail::to($user->email)->send(new UserApproved($user));

        return back()->with('success', 'Usuário aprovado com sucesso!');
    }

    // Rejeitar via painel admin (DELETE)
    public function rejectAction(User $user)
    {
        if ($user->is_approved) {
            return back()->with('warning', 'Não é possível rejeitar um usuário já aprovado.');
        }

        $userName  = $user->name;
        $userEmail = $user->email;

        Mail::to($userEmail)->send(new UserRejected($userName));

        $user->delete();

        return back()->with('success', 'Usuário rejeitado e removido do sistema.');
    }

    public function revokeAccess(User $user)
    {
        $user->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('success', 'Acesso do usuário foi revogado.');
    }
}
