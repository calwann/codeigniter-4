<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\ExceptionService;
use App\Services\UserService;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Model;
use Throwable;

class UserController extends BaseController
{
    public function __construct(
        private Model $userModel = new UserModel()
    ) {
    }

    /**
     * Render list of users
     *
     * @return string
     */
    public function list(): string
    {
        return view('users/list', [
            'title' => 'Users',
            'users' => $this->userModel->paginate(10),
            'pager' => $this->userModel->pager,
        ]);
    }

    /**
     * Render create new user
     *
     * @return string
     */
    public function create(): string
    {
        return view('users/store', [
            'title' => 'Users - Create',
        ]);
    }

    /**
     * Render edit user by id
     *
     * @param integer $id
     * @return string
     */
    public function edit(int $id): string
    {
        return view('users/store', [
            'title' => 'Users - Edit',
            'user' => $this->userModel->find($id),
        ]);
    }

    /**
     * Store new user or update user by id
     *
     * @param integer|null $id
     * @return Response
     */
    public function store(?int $id = null): Response
    {
        try {
            if ($id) {
                $return = UserService::updateUser($id, $this->request->getPost());
            } else {
                $return = UserService::insertUser($this->request->getPost());
            }
        } catch (Throwable $e) {
            return ExceptionService::reponseJson($e);
        }

        $statusCode = 200;
        $data = [
            'success' => true,
            'message' => 'User stored successfully',
            'user' => $return,
        ];

        return $this->response->setJSON($data)->setStatusCode($statusCode);
    }

    /**
     * Delete user by id
     *
     * @param integer $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        try {
            UserService::deleteUser($id);
        } catch (Throwable $e) {
            return ExceptionService::reponseJson($e);
        }

        $statusCode = 200;
        $data = [
            'success' => true,
            'message' => 'User deleted successfully',
        ];

        return $this->response->setJSON($data)->setStatusCode($statusCode);
    }
}
