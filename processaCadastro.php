<?php

$dbHost = 'localhost';
$dbUsername = 'root'; // Colocar entre aspas Nome do Local do banco de dados (Normalmente o padrão é root)
$dbPassword = ''; // Colocar entre aspas a senha que você configurou para guardar o banco de dados (caso não colocou seixar vazio)
$dbName = 'formulario-teste';

// Criar conexão
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Checar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);

    // Processo de upload de imagem
    $image = $_FILES['image']['name'];
    $target_dir = "img/";
    $target_file = $target_dir . basename($image);

    // Verificar se o diretório existe, se não, criar
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Usar prepared statement para evitar SQL injection
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $description, $price, $image);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Produto cadastrado com sucesso!');
                    window.location.href = 'equipamentos.php';
                  </script>";
        } else {
            echo "Erro: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Falha ao fazer upload da imagem.";
    }
}

$conn->close();
?>
