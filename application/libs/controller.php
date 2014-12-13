<?php

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 */
class Controller
{
    /**
     * @var null Database Connection
     */
    public $db = null;

    private $user_data;

    /**
     * Whenever a controller is created, open a database connection too. The idea behind is to have ONE connection
     * that can be used by multiple models (there are frameworks that open one connection per model).
     */
    function __construct()
    {
        $this->openDatabaseConnection();
        $this->setUserData();
    }

    /**
     *  Check whether users is an admin, or die.
     */
    protected function check_admin() {
        if (! $this->isAdmin()) {
            $this->setNotify('Permission denied','danger');
            $this->redirect('error');
            die();
        }
    }

    /**
     * Open the database connection with the credentials from application/config/config.php
     */
    private function openDatabaseConnection()
    {
        // set the (optional) options of the PDO connection. in this case, we set the fetch mode to
        // "objects", which means all results will be objects, like this: $result->user_name !
        // For example, fetch mode FETCH_ASSOC would return results like this: $result["user_name] !
        // @see http://www.php.net/manual/en/pdostatement.fetch.php
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);

        // generate a database connection, using the PDO connector
        // @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
        if (DB_TYPE === 'sqlite') {
            try {
                $this->db = new PDO(DB_TYPE . ':' . DB_NAME, '','');
                $this->db->exec('PRAGMA foreign_keys = ON');
            } catch (PDOException $e) {
                die('ERROR opening database');
            }
        }
        else
            $this->db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, $options);
    }

    /**
     * Load the model with the given name.
     * loadModel("SongModel") would include models/songmodel.php and create the object in the controller, like this:
     * $songs_model = $this->loadModel('SongsModel');
     * Note that the model class name is written in "CamelCase", the model's filename is the same in lowercase letters
     * @param string $model_name The name of the model
     * @return object model
     */
    public function loadModel($model_name)
    {
        require 'application/models/' . strtolower($model_name) . '.php';
        // return new model (and pass the database connection to the model)
        return new $model_name($this->db);
    }

    public function render($view, $data_array = array())
    {
        // load Twig, the template engine
        // @see http://twig.sensiolabs.org
        $twig_loader = new Twig_Loader_Filesystem(PATH_VIEWS);
        $twig = new Twig_Environment($twig_loader);
        $twig->getExtension('core')->setTimezone('Europe/Rome');
        // $twig->addExtension(new Twig_Extensions_Extension_Intl());
        // $twig->addExtension(new Twig_Extensions_Extension_I18n());
        // render a view while passing the to-be-rendered data
        echo $twig->render($view . PATH_VIEW_FILE_TYPE, $data_array);
    }

    /**
     * Set user data into internal class variable (name, email, admin status)
     */
    private function setUserData() {
        $user_data = array();
        if (!empty($_SESSION)) {
            $user_data['user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id']:NULL;
            $user_data['user_name'] = isset($_SESSION['user_name']) ? $_SESSION['user_name']:NULL;
            $user_data['user_email'] = isset($_SESSION['user_email']) ? $_SESSION['user_email']:NULL;
            if (isset($_SESSION['user_rank'])) {
                $user_data['user_rank'] =  $_SESSION['user_rank'];
                $user_data['is_admin'] = $_SESSION['user_rank'] == ADMIN_RANK;
            }
            else {
                $user_data['user_rank'] =  NULL;
                $user_data['is_admin'] = false;
            }
        }
        // myprint($user_data,1);
        $this->user_data = $user_data;
    }

    /**
     * @return user data (name, email, admin status)
     */
    public function getUserData() {
        return $this->user_data;
    }

    /**
     * Check admin user status.
     * @return true if user is admin, false if not, NULL if is_admin session var is not present
     */
    public function isAdmin() {
        if (isset($this->user_data['is_admin']))
            return $this->user_data['is_admin'];
        else
            return false;
    }

    /**
     * Uses session vars for setting notifications
     * @param string $notify Message to show
     * @param string $notify_type Bootstrap's alert type: can be 'info','warning','danger'
     */
    public function setNotify($notify,$notify_type) {
        $_SESSION['notify'] = $notify;
        $_SESSION['notify_type'] = $notify_type;
    }

    /**
     * Get notifications and clear them eventually
     * @return array with notifications (currently a string and its type)
     */
    public function getNotify($clear) {
        $notify_array = array();
        $notify_array['notify'] = isset($_SESSION['notify']) ? $_SESSION['notify'] : NULL;
        $notify_array['notify_type'] = isset($_SESSION['notify_type']) ? $_SESSION['notify_type'] : NULL;
        if ($clear)
            $this->clearNotify();
        return $notify_array;
    }

    /**
     * Self-explaining
     */
    public function clearNotify() {
        unset($_SESSION['notify'],$_SESSION['notify_type']);
    }

    /**
     * A simple helper function
     */
    public function redirect($relative_url) {
        header('Location: '.URL.$relative_url);
    }
}
