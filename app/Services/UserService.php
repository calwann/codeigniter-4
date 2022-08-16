<?php

namespace App\Services;

use App\Models\PasswordModel;
use App\Models\UserModel;
use CodeIgniter\Config\BaseService;
use Config\Database;
use Exception;

class UserService extends BaseService
{
    /**
     * Store new user
     *
     * @param mixed $data
     * @return array
     * @throws Exception
     */
    public static function insertUser(mixed $data): array
    {
        $validatedDataUser = self::_validateDataUser($data);
        $validatedDataPassword = self::_validateDataPassword($data);

        $db = Database::connect();
        $db->transStart();

        $userModel = new UserModel();
        $userModel->insert($validatedDataUser);

        $validatedDataPassword['user_id'] = $userModel->getInsertID();

        $passwordModel = new PasswordModel();
        $passwordModel->insert($validatedDataPassword);

        $db->transComplete();
        if (!$db->transStatus()) {
            throw new Exception('Insertion failed.', 500);
        }

        $query = $userModel->find($userModel->getInsertID());

        if (!$query) {
            return [];
        }

        return $query;
    }

    /**
     * Update user by id
     *
     * @param int $id
     * @param mixed $data
     * @return array
     * @throws Exception
     */
    public static function updateUser(int $id, mixed $data): array
    {
        $validatedDataUser = self::_validateDataUser($data);
        $validatedDataPassword = self::_validateDataPassword($data);

        $db = Database::connect();
        $db->transStart();

        $userModel = new UserModel();
        $userModel->update($id, $validatedDataUser);

        $passwordModel = new PasswordModel();
        $passwordModel->where('user_id', $id)->update(null, $validatedDataPassword);

        $db->transComplete();
        if (!$db->transStatus()) {
            throw new Exception('Update failed.', 500);
        }

        $query = $userModel->find($id);

        if (!$query) {
            return [];
        }

        return $query;
    }

    /**
     * Delete user by id
     *
     * @param integer $id
     * @return void
     * @throws Exception
     */
    public static function deleteUser(int $id): void
    {
        $db = Database::connect();
        $db->transStart();

        $userModel = new UserModel();
        $userModel->delete($id);

        $passwordModel = new PasswordModel();
        $passwordModel->where('user_id', $id)->delete();

        $db->transComplete();
        if (!$db->transStatus()) {
            throw new Exception('Deletion failed.', 500);
        }
    }

    /**
     * Validate data for UserModel
     *
     * @param mixed $data
     * @return array
     * @throws Exception
     */
    private static function _validateDataUser(mixed $data): array
    {
        $return = [];

        if (
            empty((string) $data['username'])
            || !preg_match("/^[A-Za-z]+$/", $data['username'])
        ) {
            throw new Exception('Invalid username.', 400);
        }
        $return['username'] = (string) $data['username'];

        if (
            empty((string) $data['email'])
            || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
        ) {
            throw new Exception('Invalid email.', 400);
        }
        $return['email'] = (string) $data['email'];

        if (
            empty((string) $data['name'])
            || !preg_match("/^[a-zA-Z-' ]*$/", $data['name'])
        ) {
            throw new Exception('Invalid name.', 400);
        }
        $return['name'] = (string) $data['name'];

        if (
            empty((int) $data['age'])
            || !filter_var(
                $data['age'],
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 0, 'max_range' => 200]]
            )
        ) {
            throw new Exception('Invalid age.', 400);
        }
        $return['age'] = (int) $data['age'];

        $return['status'] = 'active';

        return $return;
    }

    /**
     * Validate data for PasswordModel
     *
     * @param mixed $data
     * @return array
     * @throws Exception
     */
    private static function _validateDataPassword(mixed $data): array
    {
        $return = [];

        if (
            empty((string) $data['password'])
            || !preg_match("/^\S*$/", $data['password'])
            || strlen($data['password']) < 8
            || strlen($data['password']) > 30
        ) {
            throw new Exception('Invalid password.', 400);
        }
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $return['password'] = $passwordHash;

        return $return;
    }
}
