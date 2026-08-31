<?php

namespace Modules\Frontend\System\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use App\Libraries\UserPermissionLib;


class Users extends BaseController
{

    public function addUser($userId = 0)
    {
        $user = (int)getKey($userId, "userMaster");
 
        if (!UserPermissionLib::userCanDo("userMaster", 'add') && $this->user->userId != $user) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'Add User';
        if ($userId) {
            $data['pageTitle'] = 'Edit User';
        }

        $data['userId'] = $userId;

        $loggedUserId = $this->user->userId;
        if ($user == $loggedUserId) {
            $data['loginUser'] = '1';
        } else {
            $data['loginUser'] = '0';
        }
 
        AssetManager::loadLibrary('ImageUpload');
        AssetManager::loadLibrary('InternationalNumber');
        $data['profile_pic'] = userProfilePicUrl($userId);

        $data["view"] =  'Modules\Frontend\System\Views\addUser';

        return view('viewLoader', $data);
    }

    public function editUser($userId)
    {
        return $this->addUser($userId);
    }

    public function manageUsers()
    {
        if (!UserPermissionLib::userCanDo("userMaster", 'viewAll') && !UserPermissionLib::userCanDo("userMaster", 'viewOwn')) {
            return redirect()->to('');
        }

        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        // AssetManager::loadLibrary('Flatpickr');

        $data['pageTitle'] = 'Manage Users';
        $data["view"] =  'Modules\Frontend\System\Views\manageUsers';

        return view('viewLoader', $data);
    }

    //Trusted Devices screen
    public function manageTrustedDevice()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');

        $data['pageTitle'] = 'Manage Users';
        $data["view"] =  'Modules\Frontend\System\Views\manageTrustedDevice';

        return view('viewLoader', $data);
    }
}
