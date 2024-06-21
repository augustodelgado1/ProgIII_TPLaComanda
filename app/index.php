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
require_once './Herramientas/File.php';




require_once './middlewares/ValidadorMiddleware.php';
require_once './middlewares/VerificarRoles.php';
require_once './middlewares/ValidadorGetMiddleware.php';
require_once './middlewares/ValidadorTokenMiddleware.php';

Use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

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
$app->addBodyParsingMiddleware();

date_default_timezone_set('America/Argentina/Buenos_Aires');

$app->group('/prueva', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',function($request, $response, array $args) 
	{
		$data = $request->getParsedBody();
		

		var_dump(Util::ValidadorDeNombre($data['nombre']));
		return $response;
	});

});

$app->group('/usuario', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->post('[/]',\UsuarioController::class.':Login')
	->add(new ValidadorMiddleware(array(Usuario::class,'ValidarLoggin'),"Debe ingresar un email y una clave valida"));

	
});


$app->group('/empleado', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');

	//ABM
	$grupoDeRutas->post('[/]',\EmpleadoController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'Validador'),"Debe ingresar los datos completos del empleado"))
	->add(new VerificarRoles(array('Socio')));


	$grupoDeRutas->put('[/]',\EmpleadoController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'Validador'),"Debe ingresar los datos completos del empleado"));


	$grupoDeRutas->put('/{suspender}',\EmpleadoController::class.':SuspenderUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'VerificarUno'),"El empleado ingresado no existe"))
	->add(new VerificarRoles(array('Socio')));


	$grupoDeRutas->delete('[/]',\EmpleadoController::class.':EliminarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'VerificarUno'),"El empleado ingresado no existe"))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	
	//LISTADOS
	

});

$app->group('/empleado/{consulta}', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->get('[/]',\EmpleadoController::class.':ListarSuspendidos')
	->add(new VerificarRoles(array('Admin')));;

	$grupoDeRutas->get('/{pedidos}',\EmpleadoController::class.':ListarPedidosPendientes')
	->add(new VerificarRoles(array('Admin')));;

	$grupoDeRutas->post('[/]',\EmpleadoController::class.':ListarBorrados')
	->add(new VerificarRoles(array('Admin')));;
});

$app->group('/socio', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->post('[/]',\SocioController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'Validador'),"Debe ingresar los datos completos del Socio"));

	$grupoDeRutas->put('[/]',\SocioController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'Validador'),"Debe ingresar los datos completos del Socio"));

	$grupoDeRutas->delete('[/]',\SocioController::class.':BorrarUno')
	->add(new ValidadorMiddleware(array(Usuario::class.'VerificarUno'),"El Socio ingresado no existe"));
	
	$grupoDeRutas->get('[/]',\SocioController::class.':Listar');
});



$app->group('/producto', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\ProductoController::class.':CargarUno');;
	$grupoDeRutas->get('[/]',\ProductoController::class.':Listar');
	$grupoDeRutas->put('[/]',\ProductoController::class.':ModificarUno');
	
	
	$grupoDeRutas->delete('[/]',\ProductoController::class.':BorrarUno');

	$grupoDeRutas->post('/{csv}',\ProductoController::class.':EscribirListaEnCsv')
	->add(new ValidadorMiddleware(array(File::class,'ValidarNombreDelArchivo'),'Debe Ingresar Un Nombre para el archivo'));
	
	$grupoDeRutas->get('/{csv}',\ProductoController::class.':LeerListaEnCsv')
	->add(new ValidadorGetMiddleware(array(File::class,'ValidarExistenciaDelArchivo'),'El archivo no existe'))
	->add(new ValidadorGetMiddleware(array(File::class,'ValidarNombreDelArchivo'),'Debe Ingresar Un Nombre para el archivo'));
	
	
});



$app->group('/pedido', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\PedidoController::class.':CargarUno');
	$grupoDeRutas->put('[/]',\PedidoController::class.':PrepararUnPedido');
	$grupoDeRutas->put('/{finalizacion}',\PedidoController::class.':FinalizarPreparacionDeUnPedido');
	$grupoDeRutas->delete('[/]',\PedidoController::class.':CancelarUnPedido');
	$grupoDeRutas->get('[/]',\PedidoController::class.':Listar');
	$grupoDeRutas->get('/{terminados}',\PedidoController::class.':ListarTerminados');
});

$app->group('/orden', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->get('[/]',\OrdenController::class.':Listar');
	$grupoDeRutas->get('/{obtener}',\OrdenController::class.':ListarUno');
	$grupoDeRutas->post('[/]',\OrdenController::class.':CargarUno');
	$grupoDeRutas->put('[/]',\OrdenController::class.':ModificarUno');
	$grupoDeRutas->delete('[/]',\OrdenController::class.':BorrarUno');
});


$app->group('/encuesta', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\EncuestaController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Encuesta::class,'ValidadorEncusta'),'Debe Ingresar un nombre y '));
	
	$grupoDeRutas->put('[/]',\EncuestaController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Encuesta::class,'ValidadorEncusta'),'Debe Ingresar todos los datos de la encuesta'));;
	
	
	// $grupoDeRutas->delete('[/]',\EncuestaController::class.':BorrarUno')
	// ->add(new ValidadorMiddleware(array(Encuesta::class,'VerificarUno'),'Debe Ingresar un nombre y '));;
	$grupoDeRutas->get('[/]',\EncuestaController::class.':Listar');
});


$app->group('/mesa', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->get('[/]',\MesaController::class.':Listar');
	$grupoDeRutas->post('[/]',\MesaController::class.':CargarUno');
	$grupoDeRutas->delete('[/]',\SectorController::class.':EliminarUno');

	$grupoDeRutas->group('/{estado}',function (RouteCollectorProxy $grupoDeRutasEstado)
	{
		$grupoDeRutasEstado->post('[/]',\MesaController::class.':SetEstadoInicial');
		$grupoDeRutasEstado->put('[/]',\MesaController::class.':SetEstadoServirComida');
		$grupoDeRutasEstado->put('/{pagar}',\MesaController::class.':SetEstadoPagarOrden');

		
		$grupoDeRutasEstado->delete('[/]',\MesaController::class.':SetEstadoCerrarMesa')
		->add(new VerificarRoles(array('Socio')));;;
	});

});

$app->group('/comentarios',function (RouteCollectorProxy $grupoDeRutas)
{
	$grupoDeRutas->get('[/]',\MesaController::class.':ListarComentariosPositivosDeLasMesas');
	$grupoDeRutas->get('/negativos',\MesaController::class.':ListarComentariosNegativosDeLasMesas');
});


$app->group('/consultaPedidos', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->get('[/]',\PedidoController::class.':ListarNoEntregadoEnElTimpoEstipulado');
	$grupoDeRutas->get('/{cancelados}',\PedidoController::class.':ListarCancelados');

	
	
});

$app->group('/venta', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->get('[/]',\PedidoController::class.':ListarElPedidoMasVendido');
	$grupoDeRutas->get('/{menos}',\PedidoController::class.':ListarElPedidoMenosVendido');
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


// $grupoDeRutas->group('/pedido/{csv}', function (RouteCollectorProxy $grupoDeRutas) 
// 	{
// 		$grupoDeRutas->post('[/]',\PedidoController::class.':EscribirListaEnCsv')
// 		->add(new ValidadorMiddleware(array(Pedido::class,'ValidarNombreDelArchivo'),"Debe ingresar el nombre de archivo"));
		
// 		$grupoDeRutas->get('[/]',\PedidoController::class.':LeerListaEnCsv')
// 		->add(new ValidadorMiddleware(array(Pedido::class,'ValidarExistenciaDelArchivo'),"el archvivo no existe"))
// 		->add(new ValidadorMiddleware(array(Pedido::class,'ValidarNombreDelArchivo'),"Debe ingresar nombre de un archivo"));
// 	});


$app->run();
?>