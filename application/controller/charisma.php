<?php

/**
 * Class Charisma
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 */
class Charisma extends Controller
{
    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/home/index (which is the default page btw)
     */
    public function index()
    {
        $this->render('charisma/dashboard');
    }

    public function ui()
    {
        $this->render('charisma/ui');
    }

    public function form()
    {
        $this->render('charisma/form');
    }

    public function chart()
    {
        $this->render('charisma/chart');
    }

    public function gallery()
    {
        $this->render('charisma/gallery');
    }

    public function table()
    {
        $this->render('charisma/table');
    }

    public function calendar()
    {
        $this->render('charisma/calendar');
    }

    public function grid()
    {
        $this->render('charisma/grid');
    }

    public function tour()
    {
        $this->render('charisma/tour');
    }

    public function icon()
    {
        $this->render('charisma/icon');
    }

    public function error()
    {
        $this->render('charisma/error');
    }

}
