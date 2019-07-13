<?php

namespace Hanabi\Core;

class Controller
{
    public function __construct()
    {
    }

    public static function fire()
    {
        $smarty = new Smarty();
        $smarty->template_dir = 'public';
        $smarty->compile_dir = 'cache';
        $smarty->display('index.html');
    }
}
