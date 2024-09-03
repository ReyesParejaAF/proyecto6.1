<?php
require_once 'database.php';

class Producto {
    private $conn;
    public string $name;
    public string $description;
    public int $price;
    public string $image;
    public int $quantity;


    public function __construct($dbConnection, $name, $description , $price, $image, $quantity ) {
        $this->conn = $dbConnection;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->image = $image;
        $this->quantity = $quantity;
    }

    public function guardar() {

        $stmt = $this->conn->prepare("INSERT INTO productos (Nombre_P, Precio_p, Cantidad, descripcion, image) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("siiss", $this->name, $this->price, $this->quantity, $this->description, $this->image);
            return $stmt->execute(); 
        } else {
            return false; 
        }
    }

    public static function obtenerProductos($dbConnection) {
        $sql = "SELECT * FROM productos";
        $stmt = $dbConnection->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $productos = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row; // Añadir cada fila al array
            }
        }

        return $productos; 
    }

    public static function eliminarproduc($dbConnection, $id) {
        $stmt = $dbConnection->prepare("DELETE FROM productos WHERE ID_Producto = ?");
        $stmt->bind_param("i", $id); 
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            return true; 
        } else {
            return false; 
        }
    
        $stmt->close();
    }

    public function actualizar($id) {
        $sql = "UPDATE productos SET Nombre_P = ?, descripcion = ?, Precio_P = ?, Cantidad = ?";
        
        if ($this->image) {
            $sql .= ", image = ?";
            $stmt = $this->conn->prepare($sql . " WHERE ID_Producto = ?");
            $stmt->bind_param("ssdisi", $this->name, $this->description, $this->price, $this->quantity, $this->image, $id);
        } else {
            $stmt = $this->conn->prepare($sql . " WHERE ID_Producto = ?");
            $stmt->bind_param("ssdi", $this->name, $this->description, $this->price, $this->quantity, $id);
        }
    
        return $stmt->execute();
    }
}

?>
