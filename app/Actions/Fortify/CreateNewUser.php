<?php

namespace App\Actions\Fortify;

use App\Exceptions\InviteException;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'token' => [
                'required', 'exists:invites,token',
                function ($attribute, $value, $fail) use ($input) {
                    $invite = Invite::where('token', $value)
                        ->where('email', $input['email'])
                        ->first();

                    if (!$invite) {
                        $fail("Le token n'existe pas ou ne correspond pas à l'adresse mail.");
                    } else {
                        $invite->delete();
                    }
                }
            ],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'name.required' => 'Le champ nom est requis.',
            'email.required' => 'Le champ email est requis.',
            'email.email' => 'Le champ email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le champ mot de passe est requis.',
            'password.string' => 'Le champ mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit comporter au moins :min caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'token.required' => 'Le champ token est requis.',
            'terms.accepted' => 'Vous devez accepter les termes et conditions.',
        ])->validate();

        // $invite = Invite::where('token', $input['token'])
        //     ->where('email', $input['email'])
        //     ->first();

        // if (!$invite) {
        //     throw new InviteException("Le token n'existe pas ou ne correspond pas à l'adresse mail.");
        // }

        // $invite->delete();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
