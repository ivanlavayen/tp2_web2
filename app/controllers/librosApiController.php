<?php
require_once './app/models/librosApiModel.php';
require_once './app/views/apiView.php';


class librosApiController {
    private $model;
    private $view;
    private $data;

    public function __construct() {
        $this->model = new LibrosApiModel();
        $this->view = new ApiView();
    ;
        
        // lee el body del request
        $this->data = file_get_contents("php://input");
    }

    private function getData() {
        
        return json_decode($this->data);
    }

    public function getLibro($params = null) {
        // obtengo el id del arreglo de params
        $id = $params[':ID'];
        $titulo = $this->model->get($id);

        // si no existe devuelvo 404
        if (!empty($titulo)){
            $this->view->response($titulo, 200);
           }
        else{ 
            $this->view->response("El libro con el id=$id no existe", 404);
            }
        }

    public function deleteLibro($params = null) {
        $id = $params[':ID'];
        $titulo= $this->model->get($id);

        if (!empty($titulo)) {
                $this->model->delete($id);
                $this->view->response("el libro con el id= $id fue eliminado", 200);  //si pongo 204 no aparece el mensaje en el postman!!!!
        } 
        else {
              $this->view->response("el libro con el id= $id no existe", 404);
        }
    }       

    public function insertLibro($params = null) {
        
        $titulo = $this->getData();
        if (
            (!array_key_exists("obra",$titulo)) || 
            (!array_key_exists("autor",$titulo)) || 
            (!array_key_exists("precio",$titulo)) || 
            (!array_key_exists("id_genero",$titulo)) ){
                $this->view->response("Complete los datos", 400);
        }
        

        else {
                $obra = $titulo->obra;
                $autor = $titulo->autor;
                $precio = $titulo->precio;
                $genero = $titulo->id_genero;
                if(
                (empty($obra))||
                (empty($autor))||
                (empty($precio))||
                (empty($genero)))
                {
                    $this->view->response("Datos incorrectos, revise", 400);

                }
                 else {
                $id = $this->model->insert($obra, $autor, $precio, $genero);
                 $this->view->response("el libro con el id= $id se inserto", 201);}

        }
    }

    public function getLibros($params = null) {
        if(isset($_GET['start']) && isset($_GET['records']) && is_numeric($_GET['start']) && is_numeric($_GET['records'])){
            $start = $_GET['start'] - 1;//pagina inicial
            $records = $_GET['records'];//cantidad de registros
            $this->getPaginationForCountRecords($start, $records);   
        } 
        
        elseif (isset($_GET['page']) && isset($_GET['records']) && is_numeric($_GET['page']) && is_numeric($_GET['records'])) {
            $page = $_GET['page'];//desde que pagina
            $records = $_GET['records'];//cantidad de registros
            $this->getPaginationForPage($page, $records);
        }

        elseif(isset($_GET['obra']) && count($_GET) == 2){ 
            $obra = $_GET['obra'];
            $this->getBooksForName($obra);
        }

        elseif(isset($_GET['autor']) && count($_GET) == 2){
            $autor = $_GET['autor'];
            $this->getBooksForAuthor($autor);   
        }

        elseif(isset($_GET['precio']) && count($_GET) == 2){ 
            $precio = $_GET['precio']; 
            $this->getBooksForPrice($precio);    
        }

        elseif(isset($_GET['genero']) && count($_GET) == 2){ 
            $genero = $_GET['genero']; 
            $this->getBooksForGenero($genero);    
        }

        elseif(isset($_GET['sortBy']) && isset($_GET['order']) && count($_GET) == 3){ 
            $sortBy = $_GET['sortBy'];
            $order = $_GET['order'];
            $this->getBooksSortByAndOrder($sortBy, $order);   
        } 

        elseif(count($_GET) == 1){
            $libros = $this->model->getAll();
            $this->view->response($libros);            
        }

        else{
            $this->view->response("El recurso no existe", 404);
        }
    }

  
  
    private function msgNotRegister(){
        $this->view->response("No hay registros para mostrar", 404);
    } 
    
    /**
     * Funcion que ordena por uno de los campos de la tabla Insumos y los ordena ascendente o descendentemente.
     */
    private function getBooksSortByAndOrder($sortBy, $order){
        if (($sortBy == 'obra' || $sortBy == 'autor' || $sortBy == 'id_genero' || $sortBy == 'precio') && ($order == 'asc' || $order == 'desc')) {
            $supplies = $this->model->getBooksOrder($sortBy, $order);
            if (count($supplies) > 0) {
                $this->view->response($supplies, 200);
            } else {
                $this->msgNotRegister();
            }
        } else {
            $this->view->response("El campo o la forma a ordenar, no existe", 404);
        }  
    }

    /**
     * Funcion que filtra los insumos por Tipo de Insumo.
     */
    private function getBooksForGenero($genero){
        $supplies = $this->model->getBooksForGenero($genero);
        if (count($supplies) > 0) {
            $this->view->response($supplies);
        } else {
            $this->msgNotRegister();
        } 
    }

    /**
     * Funcion que filtra los insumos por unidad de medida.
     */
    private function getBooksForAuthor ($autores){
        $libros = $this->model->getBooksForAuthor($autores);
        if (count($libros) > 0) {
            $this->view->response($libros);
        } else {
            $this->msgNotRegister();
        }    
    }

    /**
     * Funcion que filtra los libors por nombre de libro.
     */
    private function getBooksForName($libro){
        $libros = $this->model->getBooksForName($libro);
        if (count($libros) > 0) {
            $this->view->response($libros);
        } else {
            $this->msgNotRegister();
        }
    }

    private function getBooksForPrice($price){
        $libros = $this->model->getBooksForPrice($price);
        if (count($libros) > 0) {
            $this->view->response($libros, 200);
        } else {
            $this->msgNotRegister();
        }
    }
    

    /**
     * Funcion que permite la paginacion de los datos, pasando por parametro, desde que registro comenzar y la cantidad de registros.  
     */
    private function getPaginationForCountRecords($start, $records) {
        $libros = $this->model->getAll();
        if (count($libros) < $start || $start < 0) {
            $this->view->response("Error: ingreso un inicio que es superior al numero de registros o un valor de inicio negativo", 404);
        } else {
            $libros = $this->model->getPagination($start, $records);
            $this->view->response($libros);
        }
    }

    /**
     * Funcion que permite la paginacion de los datos, pasando por parametro, la pagina y la cantidad de registros.  
     */
    private function getPaginationForPage($page, $records){
        $libros = $this->model->getAll();
        $countLibros = count($libros);
        $pages = $countLibros / $records;
        $start = $page * $records;
        if($pages >= $page){
            $result = array();
            for ($i=$start-$records; $i < $start; $i++) { 
                array_push($result, $libros[$i]); 
            }
            $this->view->response($result);
        }
        else{
            $this->view->response("Error: no hay suficientes paginas para mostrar", 404); 
        }
    }

}