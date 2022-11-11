<?php

class LibrosApiModel {

    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;'.'dbname=db_libreria;charset=utf8', 'root', '');
    }

    /**
     * Devuelve la lista completa.
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
     * Filtra por el genero  que se pase por parametro
     */
    public function getBooksForGenero($genero){
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $query = $this->db->prepare("SELECT id, genero FROM genero WHERE id LIKE ?");
        $query->execute(["%$genero%"]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Filtra los libros por nombre de obra que se pase por parametro
     */
    public function getBooksForName($name) {
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $query = $this->db->prepare("SELECT id, obra, autor, precio, id_genero FROM titulos WHERE obra LIKE ?");
        $query->execute(["%$name%"]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Filtra los libros segun autor que se pase por parametro
     */
    public function getBooksForAuthor($autor){
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $query = $this->db->prepare("SELECT id,obra, autor, precio, id_genero FROM titulos WHERE autor LIKE ?");
        $query->execute(["%$autor%"]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
     //filtra por precio
    public function getBooksForPrice($precio){
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $query = $this->db->prepare("SELECT id,obra, autor, precio, id_genero FROM titulos WHERE precio LIKE ?");
        $query->execute(["%$precio%"]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Muestra una pagina especifica de XX registros
     */
    public function getPagination($start, $records){
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $query = $this->db->prepare("SELECT id,obra, autor, precio, id_genero FROM titulos LIMIT ?, ?");
        $query->execute([$start, $records]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}
//para subir
