<?php

/**
 * Class Admin
 *
 * Gestione Applicazione: riservata agli amministratori.
 *
 */
class admin extends Controller
{

    private $MAIL_greetings;

    public function __construct()
    {
        parent::__construct();
        $this->MAIL_greetings = '<p>Mail da <a href="'.URL.'">'.URL.'</a></p>';
    }

    public function index()
    {
    }

    public function users()
    {
        $this->check_admin();

        $context = $this->getUserData();
        $users = new UserModel();
        $context['users'] = $users->get_users();
        $context['breadcrumbs'] = ['Gestione','Gestione Utenti'];
        $notify = $this->getNotify(true);
        if ($notify['notify']) {
            $context['notify'] = $notify['notify'];
            $context['notify_type'] = $notify['notify_type'];
        }

        // Preparing mail
        $context['greetings'] =  $this->MAIL_greetings;

        $this->render('admin/users', $context);
    }

    public function add_user()
    {
        $this->check_admin();
        $users = new UserModel();
        if ($users->doRegistration()) {
            $notify_type = 'info';
        } else {
            $notify_type = 'danger';
        }
        // Give to UserModel registration's feedback
        $this->setNotify($users->feedback, $notify_type);
        $this->redirect('admin/users');
    }

    public function delete_user()
    {
        $this->check_admin();
        extract($_POST);
        if (isset($deleteID)) {
            $users = new UserModel();
            if ($users->delete_user($deleteID))
                $this->setNotify('Utente rimosso', 'info');
            else
                $this->setNotify('Errore nella rimozione dell\'utente. '.$users->get_last_error(), 'danger');
        }
        $this->redirect('admin/users');
    }

    public function change_psw()
    {
        // Uncomment if you want to disable change password for users.
        // $this->check_admin();
        if (!empty($_POST)) {
            extract($_POST);
            $users = new UserModel();
            if ($users->checkPassword($user_name,$old_password)) {
                if ($new_password === $repeat_password) {
                    if ($users->set_psw($user_name,$new_password))
                        $this->setNotify('Password cambiata','info');
                    else
                        $this->setNotify($users->feedback,'danger');
                } else $this->setNotify('Le due password non coincidono','danger');
            } else $this->setNotify($users->feedback,'danger');
            $this->redirect('admin/change_psw');
        } else {
            $context = $this->getUserData();
            $notify = $this->getNotify(true);
            if ($notify['notify']) {
                $context['notify'] = $notify['notify'];
                $context['notify_type'] = $notify['notify_type'];
            }
            $this->render('admin/change_psw',$context);
        }

    }

    // ONLY generate clear psw for given array
    // ARRAY IS PER-REFERENCE !
    public function generate_psw($userlist,$include_admins=true)
    {
        foreach ($userlist as $u) {
            if (($u->user_rank != ADMIN_RANK) || $include_admins)
                $rs = random_str(8);
            else $rs="";
        }
        $u->clear_psw=$rs;

        return $userlist;
    }

    // Send mail to array of users, providing userlist with clear psw
    public function mail($userlist,$subject,$extra_body)
    {
        // Sending mails
        if (!defined(SMTP_HOST))
            return false;
        $mail = new PHPMailer();
        $mail->isSMTP();                    // Set mailer to use SMTP
        $mail->Host = SMTP_HOST;            // Specify main and backup SMTP servers
        $mail->Port = SMTP_PORT;
        $mail->SMTPAuth = SMTP_AUTH;        // Set SMTP authentication
        if (SMTP_USERNAME !== '')
            $mail->Username = SMTP_USERNAME;    // SMTP username
        if (SMTP_PASSWORD !== '')
            $mail->Password = SMTP_PASSWORD;    // SMTP password
        if (SMTP_SECURE !== '')
            $mail->SMTPSecure = SMTP_SECURE;    // Encryption, `tls` or `ssl` accepted

        $mail->From = SMTP_FROM;
        $mail->FromName = SMTP_FROM_NAME;
        $mail->WordWrap = 80;
        $mail->isHTML(true);                    // Set email format to HTML
        $mail->CharSet = 'UTF-8';

        $mail->Subject = $subject;
        $all_mail_OK = true;
        foreach ($userlist as $u) {
            $mail->clearAddresses();
            $mail->addAddress($u->user_email,$u->user_name);
            $body = nl2br($extra_body)."<br>";
            $body .= $this->MAIL_greetings;
            $body .= '<br>Il tuo nome utente: <b>'.$u->user_name.'</b> oppure <b>'.$u->user_email.'</b><br>';
            $body .= 'La tua password: <b>'.$u->clear_psw.'</b><br>';
            $body .= "<hr>Non rispondere a questa mail, viene generata automaticamente.";
            $mail->Body = $body;
            $is_mail_OK = $mail->send();
            $all_mail_OK = $all_mail_OK && $is_mail_OK;

            // If mail errors set in property (adding it)
            if (!$is_mail_OK)
                $u->mail_error = $mail->ErrorInfo;
        }

        return $all_mail_OK;
    }

    /**
     *  Generate and set password for all users, send mail to everyone with the meeting's info
     *  In case of POST errors, SILENTLY redirect
     *  NOTE: Can be used also with one single destination
     */
    public function massive_mail()
    {
        if (!empty($_POST)) {
            extract($_POST);
            $subject = "Nuova password";
            $users = new UserModel();
            // A single user is given
            if ($regenerateID !== "") {
                $single_user = $users->get_user($regenerateID);
                $userlist = array($single_user);
            } else {
            // Take users from POST var
                $userlist = [];
                foreach ($userlistID as $uid)
                    array_push($userlist,$users->get_user($uid));
            }
            if (!empty($userlist)) {
                if (count($userlist) == 1)
                    $include_admins = true;
                else $include_admins = false;  // Let change password for a single user even if is admin
                // GENERATE ALL PASSWORDS RANDOMLY and create new array with users and psw
                // IF it's a bulk operations (many users) NOT including admins (second param=false)
                //    -> clear_psw will remain EMPTY
                $userlist = $this->generate_psw($userlist,$include_admins);
                $is_psw_update = $users->set_psw_bulk($userlist);
                // myprint($userlist,1);
                $all_mail_OK = $this->mail($userlist,$subject,$extra_body);
                $updatemsg = '';
                if ($all_mail_OK === true) {
                    $mailmsg = "Invio mail avvenuto con successo";
                    if (!$is_psw_update) {
                        $mailmsg .= ', ma problemi nell\'impostazione delle nuove password, potrebbe essere necessario rigenerarle.';
                        $notify_type = 'warning';
                    } else
                        $notify_type = 'info';
                    $this->setNotify($mailmsg,$notify_type);
                } else {
                    $notify = 'Errore nell\'invio delle mail agli indirizzi: ';
                    foreach ($userlist as $u)
                        if (isset($u->mail_error))
                            $notify .= $u->user_email.' ('.$u->mail_error.'), ';
                    $this->setNotify(rtrim($notify,', '),'danger');
                }
            } else {
                $mailmsg = "Elenco vuoto. Nessuna mail inviata e nessuna password rigenerata";
                $notify_type = 'warning';
                $this->setNotify($mailmsg,$notify_type);
            }
        }
        $this->redirect('admin/users');
    }

    public function access_log()
    {
        $this->check_admin();
        $Nlogs = 100;
        $log = $this->loadModel('LogModel');
        $context = $this->getUserData();
        $context['logs'] = $log->get_last_access($Nlogs);
        if ($context['logs'] === false) {
            $context['notify'] = 'Errore nella ricerca dei log. '.$log->get_last_error();
            $context['notify_type'] = 'danger';
        }
        $context['Nlogs'] = $Nlogs;
        $this->render('admin/access_log',$context);
    }
}
