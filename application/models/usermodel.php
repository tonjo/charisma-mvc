<?php
/**
 * Class UserModel
 *
 * Derived from Panique's One File Login
 *
 * Uses very modern password hashing via the PHP 5.5 password hashing functions.
 * This project includes a compatibility file to make these functions available in PHP 5.3.7+ and PHP 5.4+.
 *
 * @author Panique
 * @author tonjo
 * @link https://github.com/tonjo/charisma-mvc
 * @license http://opensource.org/licenses/Apache-2.0 Apache License
 */

// Set the relative path of SQLite DB, from this directory
define ('DB_RELATIVE_PATH','./');
define ('DB_FULL_PATH',DB_RELATIVE_PATH . DB_NAME);

class UserModel
{
    /**
     * @var bool Login status of user
     */
    private $user_is_logged_in = false;

    /**
     * @var string System messages, likes errors, notices, etc.
     */
    public $feedback = "";

    /**
     * Even if the generic controller handles DB connection, UserModel needs its own
     * connection handling because we need it before calling any controller.
     */
    public function __construct()
    {
        if (DB_TYPE === 'sqlite') {
            try {
                $this->db = new PDO(DB_TYPE . ':' . DB_NAME, '','');
                $this->db->exec('PRAGMA foreign_keys = ON');
            } catch (PDOException $e) {
                die('ERRORE nell\'apertura del database');
            }
        } else die('Solo DB di tipo sqlite correntemente supportato (onefilephplogin)');

    }

    // public function test_users_table() {
    //     $sql = 'SELECT user_name FROM users';
    //     $Nusers = count($this->db->query($sql));
    //     return $Nusers;
    // }

    /**
     * Handles the flow of the login/logout process. According to the circumstances, a logout, a login with session
     * data or a login with post data will be performed
     */
    public function performUserLoginAction()
    {
        // Start session from here, it looks like a good place

        if (isset($_GET["action"]) && $_GET["action"] == "logout") {
            $this->doLogout();
        } elseif (!empty($_SESSION['user_name']) && ($_SESSION['user_is_logged_in'])) {
            $this->doLoginWithSessionData();
        } elseif (isset($_POST["login"])) {
            $this->doLoginWithPostData();
        }
    }

    /**
     * Set a marker (NOTE: is this method necessary ?)
     */
    private function doLoginWithSessionData()
    {
        $this->user_is_logged_in = true; // ?
    }

    /**
     * Process flow of login with POST data
     */
    private function doLoginWithPostData()
    {
        if ($this->checkLoginFormDataNotEmpty())
            $this->checkPasswordCorrectnessAndLogin();
    }

    /**
     * Logs the user out
     */
    public function doLogout()
    {
        if (ACCESS_LOG_ENABLED && isset($_SESSION['log_id']))
            $log_id = $_SESSION['log_id'];
        else $log_id = 0;

        $_SESSION = array();
        session_destroy();
        $this->user_is_logged_in = false;
        $this->feedback = "Logout effettuato.";

        if ($log_id) {
            $sql = "UPDATE log set last_logout = datetime('now') WHERE id = :log_id";
            $query = $this->db->prepare($sql);
            $query->bindValue(':log_id',$log_id);
            $query->execute(); // Not caring errors, not giving feedback
            unset($_SESSION['log_id']);
        }
    }

    /**
     * The registration flow
     * @return bool
     */
    public function doRegistration()
    {
        if ($this->checkRegistrationData()) {
            return $this->createUser_POST();
        }
        // default return
        return false;
    }

    /**
     * Validates the login form data, checks if username and password are provided
     * @return bool Login form data check success state
     */
    private function checkLoginFormDataNotEmpty()
    {
        if (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {
            return true;
        } elseif (empty($_POST['user_name'])) {
            $this->feedback = "Username field was empty.";
        } elseif (empty($_POST['user_password'])) {
            $this->feedback = "Password field was empty.";
        }
        // default return
        return false;
    }


    /**
     * Checks if user exits, if so: check if provided password matches the one in the database
     * Provide an array with data, used for fill SESSION vars
     * @param string $user_name
     * @param string $user_password
     * @param array (optional) $result_row
     * @return bool User login success status
     */
    public function checkPassword($user_name,$user_password,& $result_row =NULL)
    {
        // remember: the user can log in with username or email address
        $sql = 'SELECT user_id, user_name, user_email, user_password_hash, user_rank
                FROM users
                WHERE user_name = :user_name OR user_email = :user_name
                LIMIT 1';

        $query = $this->db->prepare($sql);

        if (! $query) {
            $this->feedback = "Tabella utenti inesistente";
            return false;
        }
        $query->bindValue(':user_name', $user_name);
        $query->execute();

        // Btw that's the weird way to get num_rows in PDO with SQLite:
        // if (count($query->fetchAll(PDO::FETCH_NUM)) == 1) {
        // Holy! But that's how it is. $result->numRows() works with SQLite pure, but not with SQLite PDO.
        // This is so crappy, but that's how PDO works.
        // As there is no numRows() in SQLite/PDO (!!) we have to do it this way:
        // If you meet the inventor of PDO, punch him. Seriously.
        $result_row = $query->fetchObject();
        if ($result_row) {
            // using PHP 5.5's password_verify() function to check password
            if (password_verify($user_password, $result_row->user_password_hash)) {
                return true;
            } else {
                $this->feedback = "Wrong password.";
            }
        } else {
            $this->feedback = "This user does not exist.";
        }
        // default return
        return false;
    }

    /**
     * Uses checkPassword above passing POST vars.
     */
    private function checkPasswordCorrectnessAndLogin()
    {
        if ($this->checkPassword($_POST['user_name'],$_POST['user_password'],$result_row)) {
            // write user data into PHP SESSION [a file on your server]
            $_SESSION['user_id'] = $result_row->user_id;
            $_SESSION['user_name'] = $result_row->user_name;
            $_SESSION['user_email'] = $result_row->user_email;
            $_SESSION['user_is_logged_in'] = true;
            $_SESSION['user_rank'] = $result_row->user_rank;
            $this->user_is_logged_in = true;
            if (ACCESS_LOG_ENABLED) {
                $sql = "INSERT INTO log (user_email,from_ip) VALUES (:user_email,:from_ip)";
                $query = $this->db->prepare($sql);
                $query->bindValue(':user_email',$_SESSION['user_email']);
                if (isset($_SERVER['REMOTE_ADDR']))
                    $remote = $_SERVER['REMOTE_ADDR'];
                else $remote = '';
                $query->bindValue(':from_ip',$remote);
                $query->execute();
                $_SESSION['log_id'] = $this->db->lastInsertId();
            }
            return true;
        } else
            return false;
    }

    /**
     * Validates the user's registration input
     * @return bool Success status of user's registration data validation
     */
    private function checkRegistrationData()
    {
        // if no registration form submitted: exit the method
        if (!isset($_POST["register"])) {
            return false;
        }

        // validating the input
        if (!empty($_POST['user_name'])
            && strlen($_POST['user_name']) <= 64
            && strlen($_POST['user_name']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])
            && !empty($_POST['user_email'])
            && strlen($_POST['user_email']) <= 64
            && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
            && !empty($_POST['user_password_new'])
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
        ) {
            // only this case return true, only this case is valid
            return true;
        } elseif (empty($_POST['user_name'])) {
            $this->feedback = "Empty Username";
        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $this->feedback = "Empty Password";
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $this->feedback = "Password and password repeat are not the same";
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $this->feedback = "Password has a minimum length of 6 characters";
        } elseif (strlen($_POST['user_name']) > 64 || strlen($_POST['user_name']) < 2) {
            $this->feedback = "Username cannot be shorter than 2 or longer than 64 characters";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            $this->feedback = "Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters";
        } elseif (empty($_POST['user_email'])) {
            $this->feedback = "Email cannot be empty";
        } elseif (strlen($_POST['user_email']) > 64) {
            $this->feedback = "Email cannot be longer than 64 characters";
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $this->feedback = "Your email address is not in a valid email format";
        } else {
            $this->feedback = "An unknown error occurred.";
        }

        // default return
        return false;
    }

    /**
     * Creates a new user.
     * @param string $user_name
     * @param string $user_email
     * @param string $user_password
     * @param integer $user_rank
     * @return bool Success status of user registration
     */
    public function createUser($user_name,$user_email,$user_password,$user_rank) {
        // remove html code etc. from username and email
        $user_name = htmlentities($user_name, ENT_QUOTES);
        $user_email = htmlentities($user_email, ENT_QUOTES);
        $user_password = $user_password;

        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 char hash string.
        // the constant PASSWORD_DEFAULT comes from PHP 5.5 or the password_compatibility_library
        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);

        $sql = 'SELECT * FROM users WHERE user_name = :user_name OR user_email = :user_email';
        $query = $this->db->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->bindValue(':user_email', $user_email);
        $query->execute();

        // As there is no numRows() in SQLite/PDO (!!) we have to do it this way:
        // If you meet the inventor of PDO, punch him. Seriously.
        $result_row = $query->fetchObject();
        if ($result_row) {
            $this->feedback = "Sorry, that username / email is already taken. Please choose another one.";
        } else {
            $sql = 'INSERT INTO users (user_name, user_password_hash, user_email, user_rank)
                    VALUES(:user_name, :user_password_hash, :user_email, :user_rank)';
            $query = $this->db->prepare($sql);
            $query->bindValue(':user_name', $user_name);
            $query->bindValue(':user_password_hash', $user_password_hash);
            $query->bindValue(':user_email', $user_email);
            $query->bindValue(':user_rank', $user_rank);
            // PDO's execute() gives back TRUE when successful, FALSE when not
            // @link http://stackoverflow.com/q/1661863/1114320
            $registration_success_state = $query->execute();

            if ($registration_success_state) {
                $this->feedback = "Your account has been created successfully. You can now log in.";
                return true;
            } else {
                $this->feedback = "Sorry, your registration failed. Please go back and try again.";
            }
        }
        // default return
        return false;
    }

    /**
     * Creates a new user calling createUser above and POST vars
     * @return bool Success status of user registration
     */
    private function createUser_POST()
    {
        $user_name = $_POST['user_name'];
        $user_email = $_POST['user_email'];
        $user_password = $_POST['user_password_new'];
        $user_rank = DEFAULT_RANK; // default user rank.
        return $this->createUser($user_name,$user_email,$user_password,$user_rank);
    }

    /**
     * Simply returns the current status of the user's login
     * @return bool User's login status
     */
    public function isAuthenticated()
    {
        return $this->user_is_logged_in;
    }

    /**
     *  Normal users (no admins)
     *  @return array of object
     **/
    public function get_users() {
        $res = $this->db->query("SELECT * FROM users WHERE user_rank>0");
        return $res->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     *  Get single user
     *  @return object
     **/
    public function get_user($userID) {
        $sql = "SELECT * FROM users WHERE user_id = :user_id";
        $q = $this->db->prepare($sql);
        $q->bindValue(':user_id',$userID);
        $res = $q->execute();
        if ($res)
            return $q->fetch(PDO::FETCH_OBJ);
        else
            return false;
    }

    /**
     *  Delete user
     *  @param integer $userID
     *  @return boolean whether deletion was successful
     **/
    public function delete_user($userID) {
        $sql = "DELETE FROM users WHERE user_id = :user_id";
        $q = $this->db->prepare($sql);
        $q->bindValue(':user_id',$userID);
        $res = $q->execute();
        $this->feedback = $q->errorInfo()[2];
        return $res;
    }

    /**
     *  Set user's password
     *  @param string $user_name
     *  @param string $new_password
     *  @return boolean whether successful
     **/
    public function set_psw($user_name,$new_password) {
        if (strlen($new_password) < 6) {
            $this->feedback = "La password deve avere almeno 6 caratteri";
            return false;
        }

        $sql = "UPDATE users SET user_password_hash = :password_hash WHERE user_name = :user_name";
        $q = $this->db->prepare($sql);
        $q->bindValue(':user_name',$user_name);
        $q->bindValue(':password_hash',password_hash($new_password,PASSWORD_DEFAULT));
        $res = $q->execute();
        $this->feedback = $q->errorInfo()[2];
        return $res;
    }

    /**
     *  Set password for a list of users, using given passwords in clear_psw property
     *  @param array of objects $userlist
     *  @return boolean whether successful
     **/
    public function set_psw_bulk($userlist) {
        $this->db->beginTransaction();
        foreach ($userlist as $u) {
            $res = $this->set_psw($u->user_name,$u->clear_psw);
            if (!$res) break;
        }
        $this->db->commit();
        return $res;
    }

}

?>
