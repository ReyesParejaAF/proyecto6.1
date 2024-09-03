<?php
require_once 'database.php';

class Catalogo {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function guardarPedido($pedido, $valorTotal) {
        $sql = "INSERT INTO pedidos (productos, valor_total) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $this->conn->error);
        }

        $stmt->bind_param('sd', $pedido, $valorTotal);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

?>
