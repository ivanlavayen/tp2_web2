<?php

class LibrosApiModel {

    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;'.'dbname=db_libreria;charset=utf8', 'root', '');
    }

    /**
     * Devuelve la lista de tareas completa.
     */
    public function getAll() {
        // 1. abro conexión a la DB
        // ya esta abierta por el constructor de la clase

        // 2. ejecuto la sentencia (2 subpasos)
        $query = $this->db->prepare("SELECT * FROM titulos");
        $query->execute();

        // 3. obtengo los resultados
        return $query->fetchAll(PDO::FETCH_OBJ); // devuelve un arreglo de objetos
    }
    
    public function get($id) {
        $query = $this->db->prepare("SELECT * FROM titulos WHERE id = ?");
        $query->execute([$id]);
        $titulo = $query->fetch(PDO::FETCH_OBJ);
        
        return $titulo;
    }

    /**
     * Inserta una tarea en la base de datos.
     */
    public function insert($obra, $autor, $precio, $id) {
        $query = $this->db->prepare("INSERT INTO titulos (obra, autor, precio, id_genero) VALUES (?, ?, ?, ?)");
        $query->execute([$obra, $autor, $precio,$id]);

        return $this->db->lastInsertId();
    }

    /**
     * Elimina una tarea dado su id.
     */
    function delete($id) {
        $query = $this->db->prepare('DELETE FROM titulos WHERE id = ?');
        $query->execute([$id]);
    } 






    public function getBooksOrder($attribute, $order){
        $query = $this->db->prepare("SELECT * FROM titulos ORDER BY $attribute $order");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Filtra los insumos segun el tipo de insumo que se pase por parametro
     */
    public function getBooksForGenero($genero){
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $query = $this->db->prepare("SELECT id, genero FROM genero WHERE id LIKE ?");
        $query->execute(["%$genero%"]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Filtra los insumos segun el insumo (nombre) que se pase por parametro
     */
    public function getBooksForName($name) {
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $query = $this->db->prepare("SELECT id, obra, autor, precio, id_genero FROM titulos WHERE obra LIKE ?");
        $query->execute(["%$name%"]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Filtra los insumos segun la unidad de medida que se pase por parametro
     */
    public function getBooksForAuthor($autores){
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $query = $this->db->prepare("SELECT id_insumo, insumo, unidad_medida, id_tipo_insumo FROM insumo WHERE unidad_medida LIKE ?");
        $query->execute(["%$autores%"]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Muestra una pagina especifica de XX registros
     */
    public function getPagination($start, $records){
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $query = $this->db->prepare("SELECT id_insumo, insumo, unidad_medida, id_tipo_insumo FROM insumo LIMIT ?, ?");
        $query->execute([$start, $records]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}
