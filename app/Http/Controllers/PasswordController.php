<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EncryptionController;
use Illuminate\Http\Request;
use App\Models\Password;

class PasswordController extends Controller
{
    public function create() {
        return view('passwords.create');
    }

    public function store() {


        $password = new Password($this->validateForm());

        $password->user_id = auth()->id();
        $password->email = EncryptionController::encrypt($password->email);
        $password->web = EncryptionController::encrypt($password->web);
        $password->url_web = EncryptionController::encrypt($password->url_web);
        $password->password = EncryptionController::encrypt($password->password);

        $password->save();

        return redirect(route('home'));

    }

    public function edit(Password $password) {

        $password->web = EncryptionController::decrypt($password->web);
        $password->url_web = EncryptionController::decrypt($password->url_web);
        $password->email = EncryptionController::decrypt($password->email);
        $password->password = EncryptionController::decrypt($password->password);

        return view('passwords.edit', [
            'password' => $password
        ]);

    }

    public function update(Password $password) {

        if ($this->validateForm()) {

            request()->merge([
                'web' => EncryptionController::encrypt(request()->input('web')),
                'url_web' => EncryptionController::encrypt(request()->input('url_web')),
                'email' => EncryptionController::encrypt(request()->input('email')),
                'password' => EncryptionController::encrypt(request()->input('password'))

            ]);

            Password::where('id', $password->id)->update([
                'web' => request()->input('web'),
                'url_web' => request()->input('url_web'),
                'email' => request()->input('email'),
                'password' => request()->input('password')
            ]);

            return redirect(route('home'));
        }


    }

    public function delete($id) {

        $password = Password::findOrFail($id);

        $password->delete();

        return redirect(route('home'));

    }

    protected function validateForm(): array
    {

        return request()->validate([
            'web' => 'required',
            'url_web' =>'url',
            'email' => 'required|email',
            'password' => 'required'
        ]);
    }
}
