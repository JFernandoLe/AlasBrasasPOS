<?php 
require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';
$id = $_GET['id'] ?? null;
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id){
    header('Location: ver.php');
    exit;
}


$db = conectarDB();

/* ===== OBTENER INSUMO ===== */
$consulta = "SELECT * FROM insumos WHERE id_insumo = $id";
$resultado = mysqli_query($db, $consulta);
$insumo = mysqli_fetch_assoc($resultado);

if(!$insumo){
    header('Location: ver.php');
    exit;
}

/* ===== VARIABLES ===== */
$errores = [];

$nombre         = $insumo['nombre'];
$categoria      = $insumo['categoria'];
$unidad         = $insumo['unidad'];
$stock          = $insumo['stock_actual'];
$stockMinimo    = $insumo['stock_minimo'];
$diasCobertura  = $insumo['dias_cobertura'];
$activo         = $insumo['activo'];

/* ===== POST ===== */
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre         = mysqli_real_escape_string($db, $_POST['nombre']);
    $categoria      = mysqli_real_escape_string($db, $_POST['categoria']);
    $unidad         = mysqli_real_escape_string($db, $_POST['unidad']);
    $stock          = (float) $_POST['stock'];
    $stockMinimo    = (float) $_POST['stock_minimo'];
    $diasCobertura  = (int) $_POST['dias_cobertura'];
    $activo         = mysqli_real_escape_string($db, $_POST['activo']);

    /* ===== VALIDACIONES ===== */
    if(!$nombre){
        $errores[] = 'El nombre es obligatorio';
    }

    if(!$categoria){
        $errores[] = 'La categoría es obligatoria';
    }

    if(!$unidad){
        $errores[] = 'La unidad es obligatoria';
    }

    if($stock < 0){
        $errores[] = 'El stock no puede ser negativo';
    }

    if($stockMinimo <= 0){
        $errores[] = 'El stock mínimo debe ser mayor a 0';
    }

    if($diasCobertura <= 0){
        $errores[] = 'Los días de cobertura deben ser mayores a 0';
    }

    /* ===== UPDATE ===== */
    if(empty($errores)) {

        $query = "
            UPDATE insumos SET
                nombre = '$nombre',
                categoria = '$categoria',
                unidad = '$unidad',
                stock_actual = $stock,
                stock_minimo = $stockMinimo,
                dias_cobertura = $diasCobertura,
                activo = $activo
            WHERE id_insumo = $id
        ";

        $resultado = mysqli_query($db, $query);

        if($resultado){
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
                <legend class="formulario--titulo">Editar Insumo</legend>

                <div class="formulario--campo">
                    <label class="formulario--label">Nombre</label>
                    <input 
                        type="text"
                        class="formulario--input"
                        name="nombre"
                        value="<?= $nombre; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Categoría</label>
                    <input 
                        type="text"
                        class="formulario--input"
                        name="categoria"
                        value="<?= $categoria; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Unidad</label>
                    <input 
                        type="text"
                        class="formulario--input"
                        name="unidad"
                        placeholder="kg, litros, piezas"
                        value="<?= $unidad; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Stock actual</label>
                    <input 
                        type="number"
                        step="0.01"
                        min="0"
                        class="formulario--input"
                        name="stock"
                        value="<?= $stock; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Stock mínimo</label>
                    <input 
                        type="number"
                        step="0.01"
                        min="0"
                        class="formulario--input"
                        name="stock_minimo"
                        value="<?= $stockMinimo; ?>"
                    >
                </div>

                <div class="formulario--campo">
                    <label class="formulario--label">Días de cobertura</label>
                    <input 
                        type="number"
                        min="1"
                        class="formulario--input"
                        name="dias_cobertura"
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

            <input type="submit" value="Actualizar Insumo" class="boton--verde">
        </form>

    </section>
</main>

<?php 
incluirTemplate('footer', $inicio);
?>
