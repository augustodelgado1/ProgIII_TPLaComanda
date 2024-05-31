<?php

require_once '../vendor/autoload.php';
require_once './Controller/UsuarioController.php';
require_once './Controller/EmpleadoController.php';
require_once './Controller/SectorController.php';
require_once './Controller/MesaController.php';
require_once './Controller/ProductoController.php';
require_once './Controller/TipoDeProductoController.php';
require_once './Clases/Mesa.php';

Use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

$app = AppFactory::create();


$app->group('/prueva', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',function($request, $response, array $args) 
	{
		$data = $request->getParsedBody();
		$data = new DateTime('10:00:60');
		echo "es ".$data->format("H:i:s");
		return $response;
	}
);
});


$app->group('/Mesa', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->get('[/]',\MesaController::class.':Listar');

	$grupoDeRutas->post('[/]',\MesaController::class.':CargarUno');

});

$app->group('/sector', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->get('[/]',\SectorController::class.':Listar');

});

$app->group('/empleados', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\EmpleadoController::class.':CargarUno');
	$grupoDeRutas->get('/{rol}',\EmpleadoController::class.':ListarPorRolDeTrabajo');
});


$app->group('/producto', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\ProductoController::class.':CargarUno');
	$grupoDeRutas->get('[/]',\ProductoController::class.':Listar');
	$grupoDeRutas->get('/{tipo}',\ProductoController::class.':ListarPorTipoDeProducto');
});

$app->group('/pedido', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\EmpleadoController::class.':CargarUno');
	$grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
});

$app->group('/tipoDeProducto', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->post('[/]',\TipoDeProductoController::class.':CargarUno');
	$grupoDeRutas->get('[/]',\TipoDeProductoController::class.':Listar');
});


$app->group('/orden', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\EmpleadoController::class.':CargarUno');
	$grupoDeRutas->get('/{rol}',\EmpleadoController::class.':ListarPorRolDeTrabajo');
});



$app->run();
?>