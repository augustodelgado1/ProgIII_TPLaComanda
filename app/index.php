<?php

require_once '../vendor/autoload.php';
require_once './Controller/UsuarioController.php';
require_once './Controller/EmpleadoController.php';

Use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

$app = AppFactory::create();


$app->group('/usuarios', function (RouteCollectorProxy $grupoDeRutas) 
{

	$grupoDeRutas->get('[/]',\UsuarioController::class.':Listar');
});

$app->group('/empleados', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\EmpleadoController::class.':CargarUno');
	$grupoDeRutas->get('/{sector}',\EmpleadoController::class.':ListarPorSector');
});

$app->run();
?>