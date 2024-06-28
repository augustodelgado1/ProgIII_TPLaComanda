

<?php

require_once './db/AccesoDatos.php';
require_once './Clases/Rol.php';
require_once './Clases/Sector.php';
require_once './Clases/Cargo.php';

class Usuario 
{
    public const ESTADO_ACTIVO = "activo";
    public const ESTADO_SUSPENDIDO = "suspendido";
    public const ESTADO_BORRADO = "borrado";
    private $id;
    private $mail;
    private $clave;
    private $idDeRol;
    private $nombre;
    private $apellido;
    private $fechaDeRegistro;
    private $dni;
    private $cargo;
    private $estado;

   
    public function __construct($mail,$clave,$nombre,$apellido,$dni,$cargo = null,$idDeRol = null) {
        $this->SetEmail($mail);
        $this->SetClave($clave);
        $this->SetNombre($nombre);
        $this->SetApellido($apellido);
        $this->SetRol($idDeRol);
        $this->SetIdCargo($cargo);
        $this->SetDni($dni);
        $this->fechaDeRegistro = new DateTime('now') ;
        $this->estado = Usuario::ESTADO_ACTIVO;
    }
    private static function CrearUno($dataUsuario)
    {
        $unUsuario = null;
      
        if(isset($dataUsuario) )
        {
            $unUsuario= new Usuario($dataUsuario['email'],$dataUsuario['clave'],$dataUsuario['nombre'],
            $dataUsuario['apellido'],$dataUsuario['dni'],$dataUsuario['idDeCargo'],$dataUsuario['idDeRol']);
            $unUsuario->SetId($dataUsuario['id']);
            $unUsuario->SetFechaDeRegistro(new DateTime($dataUsuario['fechaDeRegistro']));
            $unUsuario->SetEstado($dataUsuario['estado']);
        }
        
        return $unUsuario ;
    }
    public function ObtenerListaDeOperaciones()
    {
        return LogDeAuditoria::FiltrarPorIdDeUsuarioBD($this->id);
    }
    public function ObtenerCantidadDeOperaciones()
    {
        return  LogDeAuditoria::ObternerCantidadDeAccionesDeUnUsuarioBD($this->id);
    }

    private static function CrearLista($data)
    {
        $listaDeUsuarios = null;
        if(isset($data))
        {
            $listaDeUsuarios = [];

            foreach($data as $unArray)
            {
                $unUsuario = Usuario::CrearUno($unArray);
               
                if(isset($unUsuario))
                {
                    array_push($listaDeUsuarios,$unUsuario);
                }
            }
        }

        return   $listaDeUsuarios;
    }
    public function AgregarBD()
    {
        $idDeUsuario = null;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Usuario 
            (email,clave,fechaDeRegistro,nombre,apellido,dni,idDeRol,idDeCargo,estado) 
            values (:email,:clave,:fechaDeRegistro,:nombre,:apellido,:dni,:rol,:cargo,:estado)");
            $consulta->bindValue(':email',$this->mail,PDO::PARAM_STR);
            $consulta->bindValue(':clave',$this->clave,PDO::PARAM_STR);
            $consulta->bindValue(':rol',$this->idDeRol,PDO::PARAM_INT);
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(':apellido',$this->apellido,PDO::PARAM_STR);
            $consulta->bindValue(':fechaDeRegistro',$this->fechaDeRegistro->format('y-m-d H:i:s'),PDO::PARAM_STR);
            $consulta->bindValue(':dni',$this->dni,PDO::PARAM_STR);
            $consulta->bindValue(':cargo',$this->cargo,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $consulta->execute();
            $idDeUsuario =  $objAccesoDatos->ObtenerUltimoID();
        }
        

        return $idDeUsuario;
    }

    public static function ModificarUnoBD($id,$mail,$clave,$nombre,$apellido,$dni,$cargo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE `usuario` as u
            SET `email`= :mail,
            `clave`= :clave,
            `nombre`= :nombre,
            `apellido`= :apellido,
            `dni`= :dni 
            Where u.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':mail',$mail,PDO::PARAM_STR);
            $consulta->bindValue(':clave',$clave,PDO::PARAM_STR);
            $consulta->bindValue(':nombre',$nombre,PDO::PARAM_STR);
            $consulta->bindValue(':apellido',$apellido,PDO::PARAM_STR);
            $consulta->bindValue(':dni',$dni,PDO::PARAM_STR);
            $estado = $consulta->execute() === true 
            && Usuario::ModificarCargoBD($id,$cargo) === true;
        }

        return  $estado;
    }
    public static function ModificarCargoBD($id,$cargo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($cargo))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE `Usuario` 
            SET idDeCargo = :cargo Where id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':cargo',$cargo,PDO::PARAM_INT);
            $estado= $consulta->execute();
        }

        return  $estado;
    }

    private static function ModificarEstadoBD($id,$estadoDeUsuario)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estadoDeLaFuncion = false;

        if(Usuario::ValidarEstado($estadoDeUsuario))
        {
            // var_dump($estado);
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE `Usuario` 
            SET estado = :estado Where id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$estadoDeUsuario,PDO::PARAM_STR);
            $estadoDeLaFuncion= $consulta->execute();
        }

        return  $estadoDeLaFuncion;
    }

    public static function SuspenderUnoPorIdBD($id)
    {
        $estado = false;

        if(isset($id))
        {
            $estado =  Usuario::ModificarEstadoBD($id,Usuario::ESTADO_SUSPENDIDO);
        }

        return  $estado;
    }
    public static function ObtenerUnoCompletoBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data= false;

        if(isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT 
            u.id, u.nombre, u.dni, r.descripcion AS rol, c.descripcion AS cargo
            FROM Usuario AS u
            JOIN rol AS r ON r.id = u.idDeRol
            JOIN Cargo AS c ON c.id = u.idDeCargo
            WHERE u.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $data;
    }

    public static function BorrarUnoPorIdBD($id)
    {
        $estado = false;

        if(isset($id))
        {
            $estado =  Usuario::ModificarEstadoBD($id,Usuario::ESTADO_BORRADO);
        }

        return  $estado;
    }

    public static function ListarBD()
    {
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeUsuario = null;

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Select * From Usuario");
            $consulta->execute();
            $listaDeUsuario = $consulta->fetchAll(Pdo::FETCH_ASSOC);
        }

        return $listaDeUsuario;
    }
    private static function BuscarPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data= null;
        
        if(isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u where u.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
         
        }

        return  $data;
    }
    public static function ObtenerUnoPorIdBD($id)
    {
        return  Usuario::CrearUno(Usuario::BuscarPorIdBD($id));;
    }

    public static function FiltrarPorCargoBD($idDeCargo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeTipos= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Empleado 
            as e where e.idDeCargo = :idDeCargo");
            $consulta->bindValue(':idDeCargo',$idDeCargo,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeTipos= Usuario::CrearLista($data);
        }

        return $listaDeTipos;
    }
    public static function FiltrarPorRolBD($idDeRol)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeTipos= null;

        if(isset($idDeRol))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario 
            as u where u.idDeRol = :idDeRol");
            $consulta->bindValue(':idDeRol',$idDeRol,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
           
            $listaDeTipos= Usuario::CrearLista($data);
        }

        return $listaDeTipos;
    }
    public static function FiltrarPorFechaDeRegistroBD($fechaDeRegistro)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeTipos= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Empleado 
            JOIN Usuario u ON e.idDeUsuario = u.id
            as e where u.fechaDeRegistro = :fechaDeRegistro");
            $consulta->bindValue(':fechaDeRegistro',$fechaDeRegistro->format('d-m-y'),PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeTipos= Usuario::CrearLista($data);
        }

        return $listaDeTipos;
    }

    public static function ObternerListaDeEmpledosPorSectorBD($idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeUsuario= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT e.id,e.nombre,e.apellido,e.dni,e.idDeRol,e.idDeCargo,e.fechaDeRegistro 
            FROM usuario e 
            JOIN cargo c on c.id = e.idDeCargo 
            JOIN sector s on s.id = c.idDeSector 
            JOIN Rol r ON r.id = e.idDeRol 
            WHERE r.descripcion = 'Empleado' and s.id = :id");
            $consulta->bindValue(':descripcion',$idDeSector,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            
            $listaDeUsuario = Usuario::CrearLista($data);
        }

        return  $listaDeUsuario;
    }
  

    public static function ToStringList($listaDeSocioes)
    {
        $strLista = null; 

        if(isset($listaDeSocioes) )
        {
            $strLista = "";
            foreach($listaDeSocioes as $unSocio)
            {
                $strLista .= $unSocio->ToString().'<br>';
            }
        }

        return   $strLista;
    }
    public static function MostarCantidadDeOperaciones($listaDeUsuarios)
    {
        $strLista = null; 

        if(isset($listaDeUsuarios))
        {
            $strLista = "";
            foreach($listaDeUsuarios as $unUsuario)
            {
                $cantidad = $unUsuario->ObtenerCantidadDeOperaciones();

                if( $cantidad > 0)
                {
                    $strLista .= $unUsuario->ToString().'<br>'. 
                    "Cantidad De Operaciones: ".$cantidad;
                }
                
            }
        }

        return   $strLista;
    }
    public static function CantidadDeOperacionesDeUnaLista($listaDeUsuarios)
    {
        $acumulador = null; 

        if(isset($listaDeUsuarios))
        {
            $acumulador = 0;
            foreach($listaDeUsuarios as $unUsuario)
            {
                $cantidad = $unUsuario->ObtenerCantidadDeOperaciones();

                if( $cantidad > 0)
                {
                    $acumulador += $cantidad;
                }
                
            }
        }

        return   $acumulador;
    }
    public static function FiltrarPorEstado($listaDeUsuarios,$estado)
    {
        $listaFiltrada = null; 

        if(isset($listaDeUsuarios) && isset($estado))
        {
            $listaFiltrada = [];
            foreach($listaDeUsuarios as $unUsuario)
            {
                if(strcasecmp($unUsuario->estado,$estado) === 0)
                {
                    
                    array_push($listaFiltrada,$unUsuario);
                }
            }
        
        }

        return   $listaFiltrada;
    }
    public static function FiltrarPorCargo($listaDeUsuarios,$cargo)
    {
        $listaFiltrada = null; 

        if(isset($listaDeUsuarios) && isset($cargo))
        {
            $listaFiltrada = [];
            foreach($listaDeUsuarios as $unUsuario)
            {
                if(strcasecmp($unUsuario->cargo,$cargo) == 0)
                {
                    array_push($listaFiltrada,$unUsuario);
                }
            }
        }

        return   $listaFiltrada;
    }
    
    public function ToString()
    {
        return      "Email: ".$this->mail.'<br>'.
            "Nombre Completo: ".$this->GetNombreCompleto().'<br>'.
            "fecha De Registro: ".$this->fechaDeRegistro->format('y-m-d H:i:s').'<br>'.
            $this->GetStrCargo();
    }

    public function Equals($unUsuario)
    {
        $estado = false;
 
        if(isset($unUsuario))
        {
            $estado =  strcasecmp($unUsuario->mail,$this->mail) == 0
                       && $unUsuario->clave === $this->clave;
        }
        return  $estado ;
    }

    //Setters
    private function SetId($id)
    {
        $estado = false;
        if(isset($id))
        {
            $this->id = $id;
            $estado = true;
        }

        return  $estado ;
    }

    protected function SetFechaDeRegistro($fechaDeRegistro)
    {
        $estado = false;
        if(isset($fechaDeRegistro))
        {
            $this->fechaDeRegistro = $fechaDeRegistro;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetEmail($email)
    {
        $estado = false;
        if(Usuario::ValidadorEmail($email))
        {
            $this->mail = $email;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetClave($clave)
    {
        $estado = false;
        if(Usuario::ValidadorClave($clave))
        {
            $this->clave = $clave;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetNombre($nombre)
    {
        $estado = false;
        if(Util::ValidadorDeNombre($nombre))
        {
            $this->nombre = $nombre;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetApellido($apellido)
    {
        $estado = false;
        if(Util::ValidadorDeNombre($apellido))
        {
            $this->apellido = $apellido;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetDni($dni)
    {
        $estado = false;
        if(Usuario::ValidadorDni($dni))
        {
            $this->dni = $dni;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetRol($rol)
    {
        $estado  = false;
        if(isset( $rol) )
        {
            $this->idDeRol = $rol;
        }

        return $estado;
    }

    private function SetIdCargo($idDecargo)
    {
        $estado  = false;
        if(isset($idDecargo))
        {
            $this->cargo = $idDecargo;
        }

        return $estado;
    }

    protected function SetEstado($estadoDeUsuario)
    {
        $estado = false;

        if(Usuario::ValidarEstado($estadoDeUsuario))
        {
            $this->estado = $estadoDeUsuario;
            $estado = true;
        }

        return  $estado ;
    }

    private static function ValidarEstado($estadoDeUsuario)
    {
        $array = array(Usuario::ESTADO_ACTIVO,Usuario::ESTADO_BORRADO,Usuario::ESTADO_SUSPENDIDO);

        return isset($estadoDeUsuario) && in_array($estadoDeUsuario,$array);
    }
    

    //Getters
    public function GetMail()
    {
        return  $this->mail;
    }
    public function GetId()
    {
        return  $this->id;
    }
    public function GetFechaDeRegistro()
    {
        return  $this->fechaDeRegistro;
    }
    //Getters
    public function GetNombre()
    {
        return  $this->nombre;
    }

    public function GetApellido()
    {
        return  $this->apellido;
    }
    public function GetSector()
    {
        return  $this->GetCargo()->GetSector();
    }
    public function GetCargo()
    {
        return  Cargo::BuscarCargoPorIdBD($this->cargo) ;
    }
    private function GetStrCargo()
    {
        $mensaje = "";
        $unCargo = $this->GetCargo();
        if(isset($unCargo) && $unCargo !== false)
        {
            $mensaje = "Cargo: ".$unCargo->GetDescripcion();
        }
        return   $mensaje  ;
    }

    public function GetRolDeUsuario()
    {
        return  Rol::BuscarRolPorIdBD($this->idDeRol);
    }
    public function GetNombreCompleto()
    {
       
        return  $this->nombre." ".$this->apellido;
    }
    public static function BuscarPorLoggin($email,$clave)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data = null;

        if(isset($email) && isset($clave))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u 
            where LOWER(u.email) = LOWER(:email) and u.clave = :clave");
            $consulta->bindValue(':email',$email,PDO::PARAM_STR);
            $consulta->bindValue(':clave',$clave,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $data;
    }
    public static function Validador($data)
    {
        return  isset($data) && Usuario::ValidadorEmail($data['email']) 
                             && Usuario::ValidadorClave($data['clave'])
                             && Util::ValidadorDeNombre($data['nombre'])
                             && Util::ValidadorDeNombre($data['apellido'])
                             && Usuario::ValidadorDni($data['dni'])
                             && Usuario::ValidadorDeCargo($data['cargo']);
    }

    private static function ValidadorDeCargo($desripcion)
    {
        try 
        {
            $estado = Cargo::VerificarUnoPorDescripcionBD($desripcion);
        } catch (Exception $th) {
            $estado = false;
        }

        return $estado;
    }
    
    public static function ValidarLoggin($data)
    {
        return Usuario::ValidadorEmail($data['email']) && 
        Usuario::ValidadorClave($data['clave']) &&
        ($unUsuario = Usuario::BuscarPorLoggin($data['email'],$data['clave'])) !== false
        && $unUsuario['estado'] === Usuario::ESTADO_ACTIVO;
    }

    public static function VerificarUno($data)
    {
        return Usuario::BuscarPorIdBD($data['id']) !== null;
    }

    public static function ValidarRolSocio($data)
    {
        $estado = false;
        $unUsuario = Usuario::ObtenerUnoCompletoBD($data['id']);

        
        if(isset($unUsuario) && $unUsuario !== false)
        {
            $estado = $unUsuario['rol'] === 'Socio';
        }

        return $estado;
    }
    public static function ValidarRolEmpleado($data)
    {
        $estado = false;
        $unUsuario = Usuario::ObtenerUnoCompletoBD($data['id']);

        if(isset($unUsuario) && $unUsuario !== false)
        {

            $estado = strcasecmp($unUsuario['rol'],'Empleado') === 0;
        }

    

        return $estado;
    }

    
    private static function ValidadorEmail($email)
    {
        $estado = false; 

        if(isset($email) && isset($email) && strlen($email) >= 8)
        {

            $estado = true; 
        }

        
        return $estado;
    }

    private static function ValidadorClave($clave)
    {
        $estado = false; 
  
        if(isset($clave) && isset($clave)
        && strlen($clave) >= 8)
        {
            $estado = true; 
        }

       
        return $estado;
    }
    private static function ValidadorDni($dni)
    {
        $estado = false; 
        
        if(isset($dni) && strlen($dni) === 8 && Util::VerificarQueContengaSoloNumeros($dni))
        {
            $estado = true; 
        }

        
        return $estado;
    }
   
}


?>