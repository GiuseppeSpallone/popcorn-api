<?php
// Routes

use Slim\Http\Request;
use Slim\Http\Response;

require_once 'controllers/DbController.php';
require_once 'controllers/Route.php';
require_once 'controllers/UserRoutes.php';

UserRoutes::register_routes($app);
