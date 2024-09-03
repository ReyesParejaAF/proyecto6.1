<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        body {
            background-color: beige; 
        }
    </style>
</head>
<body>
    <div class="container mt-5">
    <h1 class="text-center my-4 bg-primary text-white border border-dark p-3 " style="border-radius: 30px;">Productos en catálogo</h1>
    <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Imagen</th>
                    <th>Actualizar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                
<!-- con este codigo muestra el sweetalert de que se elimino el producto -->
        <?php
                
            session_start();
 
            if (isset($_SESSION['suit3']) && $_SESSION['suit3'] === true){
                echo '<script>
                swal({
                    title: "¡Producto creado con exito!",
                    text: "Tu producto se ha creado en el catalogo.",
                    icon: "success",
                    button: "Genial"
                    });
                </script>';
            unset($_SESSION['suit3']);         
            }

            if (isset($_SESSION['suit2']) && $_SESSION['suit2'] === true){
                    echo '<script>
                    swal({
                      title: "¡Producto Eliminado con exito!",
                      text: "Tu producto se ha eliminado del catalogo.",
                      icon: "warning",
                      button: "Super"
                    });
                  </script>';
                unset($_SESSION['suit2']);         
                }
                ?>
                <?php
                require_once __DIR__ . '/../models/producto.php';
                require_once __DIR__ . '/../models/database.php';
                $database = new Database();
                $dbConnection = $database->conn;
                
                $productos = Producto::obtenerProductos($dbConnection);
                foreach ($productos as $producto) {
                    echo '<tr>
                        <td class="text-center align-middle">' . htmlspecialchars($producto['Nombre_P']) . '</td>
                        <td class="text-center align-middle">' . htmlspecialchars($producto['descripcion']) . '</td>
                        <td class="text-center align-middle">' . htmlspecialchars($producto['Precio_P']) . '</td>
                        <td class="text-center align-middle">' . htmlspecialchars($producto['Cantidad']) . '</td>
                        <td class="text-center align-middle"><img src="' . htmlspecialchars($producto['image']) . '" alt="Imagen Producto 1" class="img-thumbnail" style="width: 100px;"></td>
                        <td class="text-center align-middle"><button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal" data-id="'. htmlspecialchars ($producto['ID_Producto']).'" data-nombre="' . htmlspecialchars($producto['Nombre_P']) . '" data-descripcion="' . htmlspecialchars($producto['descripcion']) . '" data-precio="' . htmlspecialchars($producto['Precio_P']) . '" data-cantidad="' . htmlspecialchars($producto['Cantidad']) . '">Actualizar</button></td>
                        <td class="d-flex justify-content-center align-items-center" style="height: 100px;"><form action="..\index.php?controller=producto&action=eliminar" method="POST"><input type="hidden" name="id" value="' . htmlspecialchars($producto['ID_Producto']) . '"><button type="submit" class="btn btn-danger">Eliminar</button></form></td>
                    </tr>';
                }
                
                ?>
            </tbody>
        </table>
        <br>
          <!-- Modal -->
          <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Actualizar Producto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateForm" action="../index.php?controller=producto&action=actualizar" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="productId">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Nueva Imagen (opcional)</label>
                        <input type="file" class="form-control" id="image" name="image" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assets/camilo/JS/modal.js"></script>    
        <div class="text-center mt-4">
           <a href="AdminCrearP.php" class="btn btn-success btn-lg">Crear producto</a>
        </div>
 </div>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+9oM29Xo8BD7a36l5aOgfS7N1xLVU" crossorigin="anonymous"></script> -->
</body>
</html>
