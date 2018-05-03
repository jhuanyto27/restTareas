<?php
//materias.php
/**
 *
 Acceder al recurso materias
 * GET
 * localhost/~instructor/restDocente/materias/
 *
 Registro de materias
 * POST
 * localhost/~instructor/restDocente/materia/registro
 *
 Obtener materia por id
 * GET
 * localhost/~instructor/restDocente/materias/[id]
 Modificar materias
 * PUT
 * localhost/~instructor/restDocente/materias/[id]
 Eliminar materias
 * DELETE
 * localhost/~instructor/restDocente/materias/[id]
 */
/**
 *
 */
class materias {
  const NOMBRE_TABLA = "materias";
  const CLAVE = "idMateria";
  const NOMBRE = "nombre";
  const ID_ALUMNO = "idAlumno";

  public static function get($solicitud)
  {
    $idAlumno = alumnos::autorizar();
    if (empty($solicitud)) {
      return self::obtenerMaterias($idAlumno);
    } else {
      return self::obtenerMaterias($idAlumno, $solicitud[0]);
    }
  }
  public static function post()
  {
    $idAlumno=alumnos::autorizar();

    $cuerpo = file_get_contents('php://input');
    $materia = json_decode($cuerpo);

    $claveMateria = self::crearMateria($idAlumno,$materia);

    http_response_code(201);

    return [
      "estado"=>utf8_encode("!!!Registro exitoso"),
      "mensaje"=>"Materia creada",
      "Clave"=>$claveMateria
    ];
  }

  public static function put($solicitud)
  {
    $idAlumno = alumnos::autorizar();
      if(!empty($solicitud)){
      $cuerpo = file_get_contents('php://input');
      $materia = json_decode($cuerpo);

        if (self::actualizarMateria($idAlumno,$materia,$solicitud)>0) {
          http_response_code(200);
          return [
            "estado" => "OK",
            "mensaje" => "Registro actualizado correctamente"
          ];

        }else{
          throw new ExceptionApi("Materia no actualizada",
                "No se actualizo la materia solicitada",404);
        }

    }else{
      throw new ExceptionApi("Materia no actualizada",
      "Faltan parametros para la consulta",422);
    }
  }

  public static function delete($solicitud)
  {
    $idAlumno = alumnos::autorizar();
    if(!empty($solicitud)){
    

      if (self::eliminarMateria($idAlumno,$solicitud)>0) {
        http_response_code(200);
        return [
          "estado" => "OK",
          "mensaje" => "Registro Eliminado correctamente"
        ];

      }else{
        throw new ExceptionApi("Materia no eliminada",
              "No se elimino la materia solicitada",404);
      }

  }else{
    throw new ExceptionApi("Materia no eliminada",
    "Faltan parametros para la consulta",422);
  }
  }


  private function obtenerMaterias($idAlumno, $claveMateria = NULL)
  {
    try {
      if (!$claveMateria) {
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
        $query->bindParam(2,$claveMateria,PDO::PARAM_STR);
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
  private function crearMateria($idAlumno, $materia){
    if ($materia) {
      try {
        $sql = "INSERT INTO " . self::NOMBRE_TABLA . " (" .
          self::CLAVE . "," .
          self::NOMBRE . "," .
          self::ID_ALUMNO . ")" .
          " VALUES(?,?,?)";

        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
        $query = $pdo->prepare($sql);

        $query->bindParam(1,$materia->clave);
        $query->bindParam(2,$materia->nombre);
        $query->bindParam(3,$idAlumno);

        $query->execute();

        return $materia->clave;

      } catch (PDOException $e) {
        throw new ExceptionApi("Error de BD",
                $e->getMessage());
      }

    } else {
      throw new ExceptionApi("Error de parametros",
              "Error al pasar la Materia");
    }
  }

  private function actualizarMateria($idAlumno, $materia, $claveMateria)
  {
    try {
        
      $sql = "UPDATE " . self::NOMBRE_TABLA .
             " SET " . self::NOMBRE . " = ? " .         
             " WHERE " . self::CLAVE . " = ? AND " .
             self::ID_ALUMNO . " = ?";

      $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

      $query = $pdo->prepare($sql);
      $query->bindParam(1,$materia->nombre);
      $query->bindParam(2,$claveMateria[0]);  
      $query->bindParam(3,$idAlumno);       
    
    $query->execute();
    return $query->rowCount();
    
  } catch (PDOException $e) {
    throw new ExceptionApi("Error en la consulta",
            $e->getMessage());
  }
  }
  private function eliminarMateria($idAlumno, $claveMateria)
  {
    try {
        
      $sql = "DELETE " . "  FROM " . self::NOMBRE_TABLA .                 
             " WHERE " . self::CLAVE . " = ? AND " .
             self::ID_ALUMNO . " = ? ";
             

      $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

      $query = $pdo->prepare($sql);
      $query->bindParam(1,$claveMateria[0]);  
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
