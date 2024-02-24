<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteStoreRequest;
use App\Models\Invite;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    public function index(Request $request)
    {
        $invites = Invite::get();
        $message = $request->session()->get('success');
        return Inertia::render('Invites/Index', [
            'invites' => $invites,
            'message' => $message,
        ]);
    }

    public function store(InviteStoreRequest $request)
    {
        $invite = Invite::make();
        $invite->email = $request->validated()['email'];
        $invite->token = Str::random(8);
        $invite->save();
        return redirect()->route('invites.index')->with('success', 'Invitation créée avec succès');
    }
}
