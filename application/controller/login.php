<?php

/**
 * Class Login
 *
 * Handle Login stuff, obviously
 *
 */

class login extends Controller
{
    public function index()
    {
        $feedback = '';
        $auth_error = '';
        if (! empty($_SESSION)) {
            // $feedback = $_SESSION['feedback'];
            if (isset($_SESSION['auth_error'])) {
                $auth_error = $_SESSION['auth_error'];
                unset ($_SESSION['auth_error']);
            }

        }
        $this->render('login/login',array('feedback'=>$feedback,'auth_error'=>$auth_error));
    }

    public function authenticate()
    {
        $auth = new OneFileLogin();
        // Uses POST DATA
        $auth->performUserLoginAction();
        $_SESSION['feedback'] = $auth->feedback;
        if ($auth->isAuthenticated()) {
            header('Location: '.URL.'home');
        }
        else {
            // $_SESSION['feedback'] = $auth->feedback;
            $_SESSION['auth_error'] = $auth->feedback;
            header('Location: '.URL.'login');
            // $this->index();
        }
    }

    public function logout() {
        $auth = new OneFileLogin();
        $auth->doLogout();
        header('Location: '.URL.'home');
    }

}
