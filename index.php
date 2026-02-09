<?php
function consumirAPI($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$baseUrl = "https://parallelum.com.br/fipe/api/v1/carros";
$marcas = consumirAPI("$baseUrl/marcas");

$modelos = [];
$anos = [];
$preco = null;

if (isset($_GET['marca'])) {
    $marca = $_GET['marca'];
    $dadosModelos = consumirAPI("$baseUrl/marcas/$marca/modelos");
    $modelos = $dadosModelos['modelos'];
}

if (isset($_GET['marca']) && isset($_GET['modelo'])) {
    $anos = consumirAPI("$baseUrl/marcas/{$_GET['marca']}/modelos/{$_GET['modelo']}/anos");
}

if (isset($_GET['marca']) && isset($_GET['modelo']) && isset($_GET['ano'])) {
    $preco = consumirAPI(
        "$baseUrl/marcas/{$_GET['marca']}/modelos/{$_GET['modelo']}/anos/{$_GET['ano']}"
    );
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consulta FIPE</title>
</head>
<body>

<h2>Consulta Tabela FIPE - Carros</h2>

<form method="GET">

    <label>Marca:</label><br>
    <select name="marca" onchange="this.form.submit()">
        <option value="">Selecione</option>
        <?php foreach ($marcas as $m): ?>
            <option value="<?= $m['codigo'] ?>" 
                <?= ($_GET['marca'] ?? '') == $m['codigo'] ? 'selected' : '' ?>>
                <?= $m['nome'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <?php if (!empty($modelos)): ?>
        <label>Modelo:</label><br>
        <select name="modelo" onchange="this.form.submit()">
            <option value="">Selecione</option>
            <?php foreach ($modelos as $mod): ?>
                <option value="<?= $mod['codigo'] ?>"
                    <?= ($_GET['modelo'] ?? '') == $mod['codigo'] ? 'selected' : '' ?>>
                    <?= $mod['nome'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
    <?php endif; ?>

    <?php if (!empty($anos)): ?>
        <label>Ano:</label><br>
        <select name="ano" onchange="this.form.submit()">
            <option value="">Selecione</option>
            <?php foreach ($anos as $a): ?>
                <option value="<?= $a['codigo'] ?>">
                    <?= $a['nome'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
    <?php endif; ?>

</form>

<?php if ($preco): ?>
    <h3>Resultado FIPE</h3>
    <p><strong>Marca:</strong> <?= $preco['Marca'] ?></p>
    <p><strong>Modelo:</strong> <?= $preco['Modelo'] ?></p>
    <p><strong>Ano:</strong> <?= $preco['AnoModelo'] ?></p>
    <p><strong>Valor:</strong> <?= $preco['Valor'] ?></p>
<?php endif; ?>

</body>
</html>
