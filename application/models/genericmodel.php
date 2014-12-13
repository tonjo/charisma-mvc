<?php

class GenericModel
{

    private $last_error;

    /**
     * Every model needs a database connection, passed to the model.
     * @param object $db A PDO database connection
     */
    function __construct($db) {
        try {
            $this->db = $db;
        } catch (PDOException $e) {
            $this->set_last_error('Impossible to connect to DB');
        }
    }

    /**
     *   Retrieve the last DB error.
     *   @return string describing error
     */
    public function get_last_error() {
        $error = $this->last_error;
        $this->last_error = false;
        return $error;
    }

    /**
     *   Set DB error for controllers.
     *   @param string $error String describing error.
     */
    protected function set_last_error($error) {
        $this->last_error = $error;
    }

}

?>