

<?php


use Firebase\JWT\JWT;


class AutentificadorJWT
{
    private static $tipoEncriptacion = 'HS256';
    public static function CrearUnToken($datos)
    {
        
        $payload = array(
            'iat' => time(),
            'data' => $datos,
            'app' => 'Api Restaurante'
            
        );
        
        
        return JWT::encode($payload,$_ENV['CLAVE_SECRETA'],self::$tipoEncriptacion);
    }

    public static function VerificarToken($token )
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
       
        try 
        {
            JWT::decode(
                $token,
                $_ENV['CLAVE_SECRETA'],
                new stdClass(self::$tipoEncriptacion) ,
            );
        
        } catch (\Exception $th) 
        {
            throw $th;
        }
        
    }

    

    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            $_ENV['CLAVE_SECRETA'],
            new stdClass(self::$tipoEncriptacion)
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            $_ENV['CLAVE_SECRETA'],
            new stdClass(self::$tipoEncriptacion)
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}


?>