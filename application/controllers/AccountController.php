<?php

namespace application\controllers;

use application\components\native\core\base\BaseController;
use application\components\native\core\db\MySqlDBO;
use application\models\Users;

class AccountController extends BaseController
{
    public function actionLogin(int $id = 5, string $last_login = 'shiner'): string
    {
        if (!empty($_POST)) {
            die(json_encode(['status' => 'success', 'message' => 123]));
        }
        return $this->render('login', '', ['age' => 37, 'id' => $id, 'last' => $last_login]);
    }

    public function actionLogout(): string
    {
        return 'Logout page!';
    }

    public function actionUsers()
    {
        $users = (new Users())->find()->get();

        return $this->render('users', 'Users', ['users' => $users]);
    }
}

?>