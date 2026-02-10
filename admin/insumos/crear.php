<?php 
require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';

$db = conectarDB();
$errores = [];

// Variables
$nombre = '';
$categoria = '';
$unidad = '';
$stock = 0;
$stockMinimo = 0;
$diasCobertura = 0;
$activo = 1;

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre         = mysqli_real_escape_string($db, $_POST['nombre']);
    $categoria      = mysqli_real_escape_string($db, $_POST['categoria']);
    $unidad         = mysqli_real_escape_string($db, $_POST['unidad']);
    $stock          = (float) $_POST['stock'];
    $stockMinimo    = (float) $_POST['stock_minimo'];
    $diasCobertura  = (int) $_POST['dias_cobertura'];
    $activo         = isset($_POST['activo']) ? (int) $_POST['activo'] : 1;

    /* ===== VALIDACIONES ===== */
    if(!$nombre) {
        $errores[] = 'El nombre es obligatorio';
    }

    if(!$categoria) {
        $errores[] = 'La categoría es obligatoria';
    }

    if(!$unidad) {
        $errores[] = 'La unidad es obligatoria (kg, litros, piezas, etc.)';
    }

    if($stock < 0) {
        $errores[] = 'El stock no puede ser negativo';
    }

    if($stockMinimo <= 0) {
        $errores[] = 'El stock mínimo debe ser mayor a 0';
    }

    if($diasCobertura <= 0) {
        $errores[] = 'Los días de cobertura deben ser mayores a 0';
    }

    /* ===== INSERT ===== */
    if(empty($errores)) {

        $query = "INSERT INTO insumos 
            (nombre, categoria, unidad, stock_actual, stock_minimo, dias_cobertura, activo)
            VALUES 
            ('$nombre', '$categoria', '$unidad', $stock, $stockMinimo, $diasCobertura, $activo)
        ";

        $resultado = mysqli_query($db, $query);

        if($resultado) {
            header('Location:'. BASE_URL . '/admin/avisos.php?resultado=1');
            exit;
        }
    }
}

$inicio = true;
incluirTemplate('header', $inicio);
?>

<main class="bg-menu">
    <section class="contenedor formulario--seccion">

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?= $error; ?>
            </div>
        <?php endforeach; ?>

        <form class="formulario" method="POST">
            <fieldset class="formulario--fieldset">
                <legend class="formulario--titulo">Agregar Insumo</legend>

                <div class="formulario--campo">
                    <label class="formulario--label">Nombre</label>
                    <input 
                        type="text" 
                        name="nombre" 
                        placeholder="Ej. Alitas, Carbón, Salsa BBQ"
                        class="formulario--input"
                        value="<?= $nombre; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Categoría</label>
                    <input 
                        type="text" 
                        name="categoria" 
                        placeholder="Producción, Insumos, Desechables"
                        class="formulario--input"
                        value="<?= $categoria; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Unidad</label>
                    <input 
                        type="text" 
                        name="unidad" 
                        placeholder="kg, litros, piezas, bolsas"
                        class="formulario--input"
                        value="<?= $unidad; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Stock actual</label>
                    <input 
                        type="number" 
                        name="stock" 
                        step="0.01" 
                        min="0"
                        class="formulario--input"
                        value="<?= $stock; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Stock mínimo</label>
                    <input 
                        type="number" 
                        name="stock_minimo" 
                        step="0.01" 
                        min="0"
                        class="formulario--input"
                        value="<?= $stockMinimo; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Días de cobertura</label>
                    <input 
                        type="number" 
                        name="dias_cobertura" 
                        min="1"
                        class="formulario--input"
                        value="<?= $diasCobertura; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Estado</label>
                                        <div class="formulario--radio">
                        <p>Activo</p>
                        <label>

                            <input 
                                type="radio" 
                                name="activo" 
                                value="1"
                                <?= $activo ? 'checked' : '' ?>
                            >
                        </label>
                    </div>

                    <div class="formulario--radio">
                        <p>Inactivo<p>
                        <label>
                            <input 
                                type="radio" 
                                name="activo" 
                                value="0"
                                <?= !$activo ? 'checked' : '' ?>
                            >
                        </label>
                    </div>
                </div>

            </fieldset>

            <input type="submit" value="Agregar Insumo" class="boton--verde">
        </form>

    </section>
</main>

<?php 
incluirTemplate('footer', $inicio);
?>
