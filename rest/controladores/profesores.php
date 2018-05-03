<?php
//profesores.php

/**
 * Acceder al recurso profesor
 * GET
 * http://localhost/PHP/proyectoTareas/profesores
 * 
 * Registro de profesor
 * POST
 * http://localhost/PHP/proyectoTareas/profesores/registro
 * 
 * Obtener profesor por id
 * GET
 * http://localhost/PHP/proyectoTareas/profesores/[1]
 * 
 * Modificar profesor
 * PUT
 * http://localhost/PHP/proyectoTareas/profesores/[1]
 * 
 * Eliminar profesor
 * DELETE
 * http://localhost/PHP/proyectoTareas/profesores/[1]
 * 
 * 
 */


class profesores {
    const NOMBRE_TABLA = "profesores";
    const CLAVE = "idProfesor";
    const NOMBRE = "nombre";
    const APELLIDO ="apellido";
    const CORREO = "correo";
    const ID_ALUMNO = "idAlumno";
  
    public static function get($solicitud)
    {
      $idAlumno = alumnos::autorizar();
      if (empty($solicitud)) {
        return self::obtenerProfesores($idAlumno);
      } else {
        return self::obtenerProfesores($idAlumno, $solicitud[0]);
      }
    }
    public static function post()
    {
      $idAlumno=alumnos::autorizar();
  
      $cuerpo = file_get_contents('php://input');
      $profesor = json_decode($cuerpo);
  
      $claveProfesor = self::crearProfesor($idAlumno,$profesor);
  
      http_response_code(201);
  
      return [
        "estado"=>utf8_encode("!!!Registro exitoso"),
        "mensaje"=>"Profesor creado",
        "Clave"=>$claveProfesor
      ];
    }
  
    public static function put($solicitud)
    {
      $idAlumno = alumnos::autorizar();
        if(!empty($solicitud)){
        $cuerpo = file_get_contents('php://input');
        $profesor = json_decode($cuerpo);
  
          if (self::actualizarprofesor($idAlumno,$profesor,$solicitud)>0) {
            http_response_code(200);
            return [
              "estado" => "OK",
              "mensaje" => "Registro actualizado correctamente"
            ];
  
          }else{
            throw new ExceptionApi("Profesor no actualizad0",
                  "No se actualizo el profesor solicitada",404);
          }
  
      }else{
        throw new ExceptionApi("Profesor no actualizada",
        "Faltan parametros para la consulta",422);
      }
    }
  
    public static function delete($solicitud)
    {
      $idAlumno = alumnos::autorizar();
      if(!empty($solicitud)){
      
  
        if (self::eliminarprofesor($idAlumno,$solicitud)>0) {
          http_response_code(200);
          return [
            "estado" => "OK",
            "mensaje" => "Registro Eliminado correctamente"
          ];
  
        }else{
          throw new ExceptionApi("Profesor no eliminado",
                "No se elimino la pro$profesor solicitada",404);
        }
  
    }else{
      throw new ExceptionApi("Profesor no eliminado",
      "Faltan parametros para la consulta",422);
    }
    }
  
  
    private function obtenerProfesores($idAlumno, $claveProfesor = NULL)
    {
      try {
        if (!$claveProfesor) {
          $sql = "SELECT * FROM " . self::NOMBRE_TABLA .
                 " WHERE " . self::ID_ALUMNO . "=?";
          $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
          $query = $pdo->prepare($sql);
          $query->bindParam(1,$idAlumno,PDO::PARAM_INT);
        } else {
          $sql = "SELECT * FROM " . self::NOMBRE_TABLA .
                 " WHERE " . self::ID_ALUMNO . "=? AND " .
                 self::CLAVE . "=?";
          $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
          $query = $pdo->prepare($sql);
          $query->bindParam(1,$idAlumno,PDO::PARAM_INT);
          $query->bindParam(2,$claveProfesor,PDO::PARAM_STR);
        }
        if ($query->execute()) {
          http_response_code(200);
          return [
            "estado" => "OK",
            "mensaje" => $query->fetchAll(PDO::FETCH_ASSOC)
          ];
        } else {
          throw new ExceptionApi("Error en consulta",
                  "Se ha producido un error al ejecutar la consulta");
        }
      } catch (PDOException $e) {
        throw new ExceptionApi("Error de PDO",
                $e->getMessage());
      }
  
    }
    private function crearProfesor($idAlumno, $profesor){
      if ($profesor) {
        try {
          $sql = "INSERT INTO " . self::NOMBRE_TABLA . " (" .
            self::CLAVE . "," .
            self::NOMBRE . "," .
            self::APELLIDO . "," .
            self::CORREO . "," .
            self::ID_ALUMNO . ")" .
            " VALUES(?,?,?,?,?)";
  
          $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
          $query = $pdo->prepare($sql);
  
          $query->bindParam(1,$profesor->idProfesor);
          $query->bindParam(2,$profesor->nombre);
          $query->bindParam(3,$profesor->apellido);
          $query->bindParam(4,$profesor->correo);
          $query->bindParam(5,$idAlumno);
  
          $query->execute();
  
          return $profesor->idProfesor;
  
        } catch (PDOException $e) {
          throw new ExceptionApi("Error de BD",
                  $e->getMessage());
        }
  
      } else {
        throw new ExceptionApi("Error de parametros",
                "Error al pasar la pro$profesor");
      }
    }
  
    private function actualizarprofesor($idAlumno, $profesor, $claveProfesor)
    {
      try {
          
        $sql = "UPDATE " . self::NOMBRE_TABLA .
               " SET " . self::NOMBRE . " = ? ," . 
               self::APELLIDO . " = ? ," .  
               self::CORREO . " = ? " .         
               " WHERE " . self::CLAVE . " = ? AND " .
               self::ID_ALUMNO . " = ?";
  
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
  
        $query = $pdo->prepare($sql);
        $query->bindParam(1,$profesor->nombre);
        $query->bindParam(2,$profesor->apellido);
        $query->bindParam(3,$profesor->correo);  
        $query->bindParam(4,$claveProfesor[0]);  
        $query->bindParam(5,$idAlumno);  
             
      
      $query->execute();
      return $query->rowCount();
      
    } catch (PDOException $e) {
      throw new ExceptionApi("Error en la consulta",
              $e->getMessage());
    }
    }
    private function eliminarprofesor($idAlumno, $claveProfesor)
    {
      try {
          
        $sql = "DELETE " . "  FROM " . self::NOMBRE_TABLA .                 
               " WHERE " . self::CLAVE . " = ? AND " .
               self::ID_ALUMNO . " = ? ";
               
  
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
  
        $query = $pdo->prepare($sql);
        $query->bindParam(1,$claveProfesor[0]);  
        $query->bindParam(2,$idAlumno);       
      
      $query->execute();
      return $query->rowCount();
      
    } catch (PDOException $e) {
      throw new ExceptionApi("Error en la consulta",
              $e->getMessage());
    }
    }
  }


?>
