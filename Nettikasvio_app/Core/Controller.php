<?php

namespace app\Core;

use app\{
    Core\View,
    Models\Model,
    Core\Sessions
};


class Controller {

    protected View $view;
    protected Sessions $sessions;
    protected Model $model;

    public function __construct() {

        $this->view = new View();
        $this->sessions = new Sessions();
        $this->model = new Model();
    }
}
