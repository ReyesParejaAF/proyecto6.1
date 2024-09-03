<?php
require_once __DIR__ . '/../models/producto.php';
require_once __DIR__ . '/../models/database.php';
class ProductoController {
    private $conn; 

    public function __construct() {
        $database = new Database(); 
        $this->conn = $database->conn; 
    }
    public function guardarProducto() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $price = floatval($_POST['price']);
            $quantity = intval($_POST['quantity']);

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = 'C:\xampp\htdocs\proyecto5.0\assets\camilo\Pictures\nuevas\\'; 
                $showDir = '..\assets\camilo\Pictures\nuevas\\';

                $uploadFile = $uploadDir . basename($_FILES['image']['name']);
                $showFile = $showDir . basename($_FILES['image']['name']);

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {

                    $producto = new Producto($this->conn, $name, $description, $price, $showFile, $quantity);
                    
                    if ($producto->guardar()) {
                        session_start();
                        $_SESSION['suit3'] = true;
                        header('Location: views/AdminProducto.php');
                        exit; 
                    } else {
                        echo "Error al guardar el producto.";
                    }
                } else {
                    echo "Error al subir la imagen.";
                }
            } else {
                echo "Por favor, sube una imagen válida.";
            }
        }
    }    

    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            
            $database = new Database();
            $dbConnection = $database->conn;
            
            Producto::eliminarproduc($dbConnection, $id);
            
            session_start(); 
            $_SESSION['suit2'] = true ;
            header('Location: views/AdminProducto.php');
            exit; 
        }
 
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener la conexión a la base de datos
            $database = new Database();
            $dbConnection = $database->conn;

            $id = $_POST['id'];
            $name = $_POST['nombre'];
            $description = $_POST['descripcion'];
            $price = $_POST['precio'];
            $quantity = $_POST['cantidad'];

            // Verificar si se subió una nueva imagen
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = 'C:\xampp\htdocs\proyecto5.0\assets\camilo\Pictures\nuevas\\'; 
                $showDir = '..\assets\camilo\Pictures\nuevas\\';

                $uploadFile = $uploadDir . basename($_FILES['image']['name']);
                $showFile = $showDir . basename($_FILES['image']['name']);

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    // Si la imagen se subió con éxito, actualiza el producto con la nueva imagen
                    $producto = new Producto($dbConnection, $name, $description, $price, $showFile, $quantity);
                } else {
                    echo "Error al subir la nueva imagen.";
                    return;
                }
            } else {
                // Si no se subió una nueva imagen, solo actualiza los otros campos -- ERROR ARREGLAR
                $producto = new Producto($dbConnection, $name, $description, $price, null, $quantity);
            }

            if ($producto->actualizar($id)) {
                session_start();
                $_SESSION['suit4'] = true;
                header('Location: views/AdminProducto.php');
                exit; 
            } else {
                echo "Error al actualizar el producto.";
            }
        }
    }    
}
?>
