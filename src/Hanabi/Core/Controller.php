<?php

namespace Hanabi\Core;

use ReflectionClass;
use Hanabi\Core\AppSmarty;
use Zend\Http\PhpEnvironment\Request;

class Controller
{
    private $file;
    private $ext = 'html';
    private $dir;
    private $action;
    private $func;
    
    public function __construct($action)
    {
        $this->action = $action;
    }

    // URL解析
    private function parseUrl()
    {
        if(isset($_SERVER['PATH_INFO'])) {
            $uri = $_SERVER['PATH_INFO'];
            if(preg_match('/(.*)\/$/', $uri)) {
                $dir = $uri;
                $file = 'index.html';
            } else {
                $dir = dirname($uri);
                $file = basename($uri);
            }
        } else {
            $dir = '/';
            $file = 'index.html';
        }
        $dirs = explode('/', $dir);
        $this->dir = [];
        foreach($dirs as $dir) {
            if($dir !== "")
                $this->dir[] = $dir;
        }

        $arr = explode('.', $file);
        $this->ext = $arr[1];
        $arr = explode('_', $arr[0]);

        $this->file = $arr[0];
        if(isset($arr[1]))
            $this->func = $arr[1];
        else
            $this->func = 'index';
    }

    // Actionクラス実行
    private function internalAction()
    {
        $dir = "";
        foreach($this->dir as $d) {
            $dir .= '\\' . ucfirst($d);
        }
        $className = $this->action . $dir . '\\' . ucfirst($this->file);
        if(!class_exists($className))
            return;

        $class = new $className;
        $req = new Request();
        if($req->isPost()) {
            foreach($class as $k => $v) {
                $class->$k = $req->getPost($k);
            }
        } else {
            foreach($class as $k => $v)
                $class->$k = $req->getQuery($k);
        }
        
        if(method_exists($class, $this->func)) {
            $ref = new ReflectionClass($class);
            $method = $ref->getMethod($this->func);
            $params = $method->getParameters();
            $funcParam = [];

            $ret = $method->invokeArgs($class, $funcParam);
        }
    }

    public static function fire($action)
    {
        $self = new static($action);
        $self->parseUrl();
        $self->internalAction();
        
        $smarty = new AppSmarty();
        $smarty->template_dir = 'public' . '/' . implode('/', $self->dir);
        $smarty->compile_dir = 'cache';

        // TODO: assign処理
        
        $smarty->display($self->file . '.' . $self->ext);
    }
}
