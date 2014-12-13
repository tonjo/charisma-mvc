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
            if (isset($_SESSION['auth_error'])) {
                $auth_error = $_SESSION['auth_error'];
                unset ($_SESSION['auth_error']);
            }

        }
        $this->render('login/login',array('feedback'=>$feedback,'auth_error'=>$auth_error));
    }

    public function authenticate()
    {
        //*** tnj FROM HERE ***//
        /* this code is never executed because in application
           I already checked if authenticated */
        $users = new UserModel();
        // Uses POST DATA
        $users->performUserLoginAction();
        $login_feedback = $users->feedback;
        if ($users->isAuthenticated()) {
            if ($login_feedback)
                // Never seen because of... read above
                $this->setNotify($login_feedback,'warning');
            $this->redirect('home');
        }
        //*** TO HERE ***//

        else {
            $_SESSION['auth_error'] = $users->feedback;
            $this->redirect('login');
        }
    }

    public function logout() {
        $users = new UserModel();
        $users->doLogout();
        $this->redirect('home');
    }

}
