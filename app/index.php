<?php

require_once '../vendor/autoload.php';


require_once './Controller/UsuarioController.php';
require_once './Controller/EmpleadoController.php';
require_once './Controller/SectorController.php';
require_once './Controller/MesaController.php';
require_once './Controller/ProductoController.php';
require_once './Controller/TipoDeProductoController.php';
require_once './Controller/OrdenController.php';
require_once './Controller/PedidoController.php';
require_once './Controller/EncuestaController.php';
require_once './Controller/SocioController.php';
require_once './Controller/CargoController.php';
require_once './Controller/PuntuacionController.php';


require_once './middlewares/ValidadorMiddleware.php';
require_once './middlewares/ValidarUsuarioMiddleware.php';

Use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;


// 2do Sprint ( Entrega 10 de Junio)

// ❖ Usar MW de usuarios/perfiles
// ❖ Verificar usuarios para las tareas de ABM
// ❖ Manejo del estado del pedido

// 3er Sprint ( Entrega 24 de Junio)

// ❖ Carga de datos desde un archivo .CSV
// ❖ Descarga de archivos .CSV

// 4to Sprint ( Entrega 01 de Julio)

// ❖ Hacer todo el circuito de un pedido.
// ❖ Manejo del estado del pedido + estadísticas 30 días
// ❖ Descarga de archivos PDF
// ❖ Seguimiento de las acciones de los empleados.
// ❖ Manejo del estado de los empleado



$app = AppFactory::create();

date_default_timezone_set('America/Argentina/Buenos_Aires');

$app->group('/prueva', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',function($request, $response, array $args) 
	{
		
		return $response;
	}
);
});

$app->group('/usuario', function (RouteCollectorProxy $grupoDeRutas) 
{
	//Post
	
	$grupoDeRutas->post('/{login}',\UsuarioController::class.':Login')
	->add(new ValidadorMiddleware(array(Usuario::class,'ValidadorClave'),"Debe ingresar una clave valida"))
	->add(new ValidadorMiddleware(array(Usuario::class,'ValidadorEmail'),"Debe ingresar un email valido"));
	
	//Get
	$grupoDeRutas->get('[/]',\UsuarioController::class.':Listar');
	
});

// 2do Sprint ( Entrega 10 de Junio)

// ❖ Usar MW de usuarios/perfiles
// ❖ Verificar usuarios para las tareas de ABM
// ❖ Manejo del estado del pedido


$app->group('/empleado', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');

	//ABM
	$grupoDeRutas->post('[/]',\EmpleadoController::class.':CargarUno')
	;
	$grupoDeRutas->put('[/]',\EmpleadoController::class.':ModificarUno');
	$grupoDeRutas->put('/{suspender}',\EmpleadoController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Usuario::class.'ValidadorDni'),"Debe ingresar un dni valido"));
	// ->add(new ValidarUsuarioMiddleware($unUsuario,array(Usuario::class.'ValidadorRolSocio'),"Debe ser socio para ingresar"));

	$grupoDeRutas->delete('[/]',\EmpleadoController::class.':EliminarUno');

	$grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	//LISTADOS
	
});

$app->group('/socio', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->post('[/]',\SocioController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'ValidadorClave'),"Debe ingresar una clave valida"))
	->add(new ValidadorMiddleware(array(Usuario::class,'ValidadorEmail'),"Debe ingresar un email valido"));

	$grupoDeRutas->put('[/]',\SocioController::class.':ModificarUno');
	$grupoDeRutas->delete('[/]',\SocioController::class.':EliminarUno');
	$grupoDeRutas->get('[/]',\SocioController::class.':Listar');
});



$app->group('/producto', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\ProductoController::class.':CargarUno');
	$grupoDeRutas->get('[/]',\ProductoController::class.':Listar');
	$grupoDeRutas->get('/{tipo}',\ProductoController::class.':ListarPorTipoDeProducto');

	$grupoDeRutas->put('[/]',\ProductoController::class.':ModificarUno');
	$grupoDeRutas->delete('[/]',\ProductoController::class.':BorrarUno');
	
});

$app->group('/pedido', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\PedidoController::class.':CargarUno');
	$grupoDeRutas->put('[/]',\PedidoController::class.':PrepararUnPedido');
	$grupoDeRutas->put('/{finalizacion}',\PedidoController::class.':FinalizarPreparacionDeUnPedido');
	$grupoDeRutas->delete('[/]',\PedidoController::class.':CancelarUnPedido');
	$grupoDeRutas->get('[/]',\PedidoController::class.':Listar');

	$grupoDeRutas->group('/csv', function (RouteCollectorProxy $grupoDeRutas) 
	{
		$grupoDeRutas->post('[/]',\PedidoController::class.':EscribirListaEnCsv')
		->add(new ValidadorMiddleware(array(Pedido::class,'ValidarNombreDelArchivo'),"Debe ingresar el nombre de archivo"));
		
		$grupoDeRutas->get('[/]',\PedidoController::class.':LeerListaEnCsv')
		->add(new ValidadorMiddleware(array(Pedido::class,'ValidarExistenciaDelArchivo'),"el archvivo no existe"))
		->add(new ValidadorMiddleware(array(Pedido::class,'ValidarNombreDelArchivo'),"Debe ingresar nombre de un archivo"));
	});

	$grupoDeRutas->group('/listado', function (RouteCollectorProxy $grupoDeRutas) 
	{
		$grupoDeRutas->get('[/]',\PedidoController::class.':ListarNoEntregadoEnElTimpoEstipulado');
		$grupoDeRutas->get('/{cancelado}',\PedidoController::class.':ListarCancelados');

		$grupoDeRutas->group('/venta', function (RouteCollectorProxy $grupoDeRutas) 
		{
			$grupoDeRutas->get('[/]',\PedidoController::class.':ListarElPedidoMasVendido');
			$grupoDeRutas->get('/{menos}',\PedidoController::class.':ListarElPedidoMenosVendido');
		});
		
	});
});

$app->group('/orden', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\OrdenController::class.':CargarUno');
	$grupoDeRutas->get('/{obtener}',\OrdenController::class.':ListarUno');
	$grupoDeRutas->put('[/]',\OrdenController::class.':ModificarUno');
	$grupoDeRutas->delete('[/]',\OrdenController::class.':BorrarUno');
	$grupoDeRutas->get('[/]',\OrdenController::class.':Listar');
});


$app->group('/encuesta', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\EncuestaController::class.':CargarUno');
	$grupoDeRutas->put('[/]',\EncuestaController::class.':ModificarUno');
	$grupoDeRutas->delete('[/]',\EncuestaController::class.':BorrarUno');
	$grupoDeRutas->get('[/]',\EncuestaController::class.':Listar');
});

$app->group('/cargo', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->get('[/]',\CargoController::class.':Listar');
	$grupoDeRutas->post('[/]',\CargoController::class.':CargarUno');
	$grupoDeRutas->put('[/]',\CargoController::class.':ModificarUno');
	$grupoDeRutas->delete('[/]',\CargoController::class.':EliminarUno');
});

$app->group('/tipoDeProducto', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->get('[/]',\TipoDeProductoController::class.':Listar');
	$grupoDeRutas->post('[/]',\TipoDeProductoController::class.':CargarUno');
	$grupoDeRutas->put('[/]',\TipoDeProductoController::class.':ModificarUno');
	$grupoDeRutas->delete('[/]',\TipoDeProductoController::class.':EliminarUno');
});

$app->group('/puntuacion', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->get('[/]',\PuntuacionController::class.':Listar');
	$grupoDeRutas->post('[/]',\PuntuacionController::class.':CargarUno');
	$grupoDeRutas->put('[/]',\PuntuacionController::class.':ModificarUno');
	$grupoDeRutas->delete('[/]',\PuntuacionController::class.':EliminarUno');
});

$app->group('/sector', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->get('[/]',\SectorController::class.':Listar');
	$grupoDeRutas->post('[/]',\SectorController::class.':CargarUno');
	$grupoDeRutas->put('[/]',\SectorController::class.':ModificarUno');
	$grupoDeRutas->delete('[/]',\SectorController::class.':EliminarUno');
});

$app->group('/Mesa', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->get('[/]',\MesaController::class.':Listar');
	$grupoDeRutas->group('/comentarios',function (RouteCollectorProxy $grupoDeRutas)
	{
		$grupoDeRutas->get('[/]',\MesaController::class.':ListarComentariosPositivosDeLasMesas');
		$grupoDeRutas->get('/{negativo}',\MesaController::class.':ListarComentariosNegativosDeLasMesas');
	});

	$grupoDeRutas->group('/estado',function (RouteCollectorProxy $grupoDeRutas)
	{
		$grupoDeRutas->post('[/]',\MesaController::class.':SetEstadoInicial');
		$grupoDeRutas->put('[/]',\MesaController::class.':SetEstadoServirComida');
		$grupoDeRutas->put('/{pagar}',\MesaController::class.':SetEstadoPagarOrden');
		$grupoDeRutas->delete('[/]',\MesaController::class.':SetEstadoCerrarMesa');
	});


	$grupoDeRutas->post('[/]',\MesaController::class.':CargarUno');
	$grupoDeRutas->delete('[/]',\SectorController::class.':EliminarUno');

});


$app->run();
?>