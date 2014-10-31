<?php

/**
 * Class Home
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 */
class Home extends Controller
{
    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/home/index (which is the default page btw)
     */
    public function index()
    {
        $this->render('home/dashboard');
    }

    public function ui()
    {
        $this->render('home/ui');
    }

    public function form()
    {
        $this->render('home/form');
    }

    public function chart()
    {
        $this->render('home/chart');
    }

    public function gallery()
    {
        $this->render('home/gallery');
    }

    public function table()
    {
        $this->render('home/table');
    }

    public function calendar()
    {
        $this->render('home/calendar');
    }

    public function grid()
    {
        $this->render('home/grid');
    }

    public function tour()
    {
        $this->render('home/tour');
    }

    public function icon()
    {
        $this->render('home/icon');
    }

    public function error()
    {
        $this->render('home/error');
    }

    public function login()
    {
        $this->render('home/login');
    }

}
