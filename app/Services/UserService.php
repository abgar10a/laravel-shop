<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\Upload;
use App\Models\User;

class UserService
{

    public function updateUser($id, $userData)
    {

        $user = User::find($id);

        if (is_null($user)) {
            return ResponseHelper::build(error: 'User not found');
        }

        $user->update($userData);

        return ResponseHelper::build('User updated successfully', ['user' => $user]);

    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return ResponseHelper::build(error: 'User not found');
        }

        if ($user->avatar) {
            Upload::find($user->avatar)->delete();
        }
        $user->delete();

        return ResponseHelper::build('User deleted successfully');
    }

}
