<?php

namespace App\Actions\Fortify;

use App\Models\AdminProfile;
use App\Models\CustomerProfile;
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
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {

        $type = $input['type'] ?? '';
        if ($type == 'user') {
            $attr =  Validator::make($input, CustomerProfile::$createRules)->validate();
        } else {
            $attr = $input;
        }

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();


        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
        $this->addToProfile($user, $type, $attr);

        return $user;
    }

    /*
    there is two types of users in this project 
    1- users of the flutter app 
    and there data exists in customer_profiles table in database 
    2- Admins and there data exists in admin_profiles in the database
        admins have 3 type of roles admin - editor - superAdmin
    */
    
    private function addToProfile($user, $type, $attr)
    {
        if ($user != null && $user->exists()) {
            if ($type == 'user') {
                $userProfile = CustomerProfile::create($attr);
                if ($userProfile->exists()) {
                    $userProfile->user()->save($user);
                } else {
                    $user->delete();
                }
            } else {
                $admin = AdminProfile::create([
                    'email' => $attr['email'],
                ]);
                if ($admin->exists()) {
                    $admin->user()->save($user);
                } else {
                    $user->delete();
                }
            }
        }
    }
}
