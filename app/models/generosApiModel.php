<?php

class GenerosModel {

    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;'.'dbname=db_libreria;charset=utf8', 'root', '');
    }

    /**
     * Devuelve la lista de GENEROS.
     */
    function getAll() {
        // 1. abro conexión a la DB
        // ya esta abierta por el constructor de la clase

        // 2. ejecuto la sentencia (2 subpasos)
        $query = $this->db->prepare("SELECT * FROM genero");
        $query->execute();

        // 3. obtengo los resultados
        return $query->fetchAll(PDO::FETCH_OBJ); // devuelve un arreglo de objetos
    }

    
}