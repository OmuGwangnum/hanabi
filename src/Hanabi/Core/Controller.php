<?php

namespace Hanabi\Core;

use Hanabi\Core\AppSmarty;

class Controller
{
    public function __construct()
    {
    }

    public static function fire()
    {
        $smarty = new AppSmarty();
        $smarty->template_dir = 'public';
        $smarty->compile_dir = 'cache';
        $smarty->display('index.html');
    }
}
