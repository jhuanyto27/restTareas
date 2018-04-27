<?php

class tareas {
    const NOMBRE_TABLA = "tareas";
    const CLAVE = "idTarea";
    const DESCRIPCION = "descripcion";
    const TITULO ="titulo";
    const FECHA = "fecha_entrega";
    const UNIDAD = "unidad";
    const ID_ALUMNO = "idAlumno";
  
    public static function get($solicitud)
    {
      $idAlumno = alumnos::autorizar();
      if (empty($solicitud)) {
        return self::obtenerTareas($idAlumno);
      } else {
        return self::obtenerTareas($idAlumno, $solicitud[0]);
      }
    }

    public static function post()
    {
      $idAlumno=alumnos::autorizar();
  
      $cuerpo = file_get_contents('php://input');
      $tarea = json_decode($cuerpo);
  
      $claveTarea = self::CrearTareas($idAlumno,$tarea);
  
      http_response_code(201);
  
      return [
        "estado"=>utf8_encode("!!!Registro exitoso"),
        "mensaje"=>"tarea creada",
        "Clave"=>$claveTarea
      ];
    }
  
    public static function put($solicitud)
    {
      $idAlumno = alumnos::autorizar();
        if(!empty($solicitud)){
        $cuerpo = file_get_contents('php://input');
        $tarea = json_decode($cuerpo);
  
          if (self::actualizartarea($idAlumno,$tarea,$solicitud)>0) {
            http_response_code(200);
            return [
              "estado" => "OK",
              "mensaje" => "Registro actualizado correctamente"
            ];
  
          }else{
            throw new ExceptionApi("tarea no actualizada",
                  "No se actualizo la tarea solicitada",404);
          }
  
      }else{
        throw new ExceptionApi("tarea no actualizada",
        "Faltan parametros para la consulta",422);
      }
    }
  
    public static function delete($solicitud)
    {
      $idAlumno = alumnos::autorizar();
      if(!empty($solicitud)){
      
  
        if (self::eliminartarea($idAlumno,$solicitud)>0) {
          http_response_code(200);
          return [
            "estado" => "OK",
            "mensaje" => "Registro Eliminado correctamente"
          ];
  
        }else{
          throw new ExceptionApi("tarea no eliminada",
                "No se elimino la tarea solicitada",404);
        }
  
    }else{
      throw new ExceptionApi("tarea no eliminada",
      "Faltan parametros para la consulta",422);
    }
    }
  
  
    private function obtenerTareas($idAlumno, $clavetarea = NULL)
    {
      try {
        if (!$clavetarea) {
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
          $query->bindParam(2,$clavetarea,PDO::PARAM_STR);
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
    private function crearTareas($idAlumno, $tarea){
      if ($tarea) {
        try {
          $sql = "INSERT INTO " . self::NOMBRE_TABLA . " (" .
            self::CLAVE . "," .
            self::TITULO . "," .
            self::DESCRIPCION . "," .
            self::FECHA . "," .
            self::UNIDAD . "," .
            self::ID_ALUMNO . ")" .
            " VALUES(?,?,?,?,?,?)";
  
          $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
          $query = $pdo->prepare($sql);
  
          $query->bindParam(1,$tarea->idTarea);
          $query->bindParam(2,$tarea->titulo);
          $query->bindParam(3,$tarea->descripcion);
          $query->bindParam(4,$tarea->fecha);
          $query->bindParam(5,$tarea->unidad);
          $query->bindParam(6,$tarea->idAlumno);

  
          $query->execute();
  
          return $tarea->idTarea;
  
        } catch (PDOException $e) {
          throw new ExceptionApi("Error de BD",
                  $e->getMessage());
        }
  
      } else {
        throw new ExceptionApi("Error de parametros",
                "Error al pasar la tarea");
      }
    }
  
    private function actualizartarea($idAlumno, $tarea, $clavetarea)
    {
      try {
          
        $sql = "UPDATE " . self::NOMBRE_TABLA .
               " SET " . self::TITULO . " = ?, " . 
               self::DESCRIPCION . " = ?, " .
                 self::FECHA . " = ?, " . 
                 self::UNIDAD . " =? " .        
               " WHERE " . self::CLAVE . " = ? AND " .
               self::ID_ALUMNO . " = ?";
  
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
  
        $query = $pdo->prepare($sql);
        $query->bindParam(1,$tarea->titulo);
        $query->bindParam(2,$tarea->descripcion);
        $query->bindParam(3,$tarea->fecha);  
        $query->bindParam(4,$tarea->unidad);   
        $query->bindParam(5,$clavetarea[0]);  
        $query->bindParam(6,$idAlumno);       
      
      $query->execute();
      return $query->rowCount();
      
    } catch (PDOException $e) {
      throw new ExceptionApi("Error en la consulta",
              $e->getMessage());
    }
    }
    private function eliminartarea($idAlumno, $clavetarea)
    {
      try {
          
        $sql = "DELETE " . "  FROM " . self::NOMBRE_TABLA .                 
               " WHERE " . self::CLAVE . " = ? AND " .
               self::ID_ALUMNO . " = ? ";
               
  
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
  
        $query = $pdo->prepare($sql);
        $query->bindParam(1,$clavetarea[0]);  
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
