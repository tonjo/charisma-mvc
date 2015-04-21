<?php

class LogModel extends GenericModel
{
    public function get_last_access($Nlogs) {
        $sql = "SELECT user_email,from_ip,last_login,last_logout FROM log ORDER BY last_login DESC LIMIT :Nlogs";
        $q = $this->db->prepare($sql);
        if (!$q) {
            $this->set_last_error(_('Log table not present or corrupted'));
            return false;
        }
        $q->bindValue(':Nlogs',$Nlogs);
        $res = $q->execute();
        if (!$res) {
            $this->set_last_error($q->errorInfo()[2]);
            return false;
        }
        $logs = $q->fetchAll(PDO::FETCH_OBJ);
        return $logs;
    }
}