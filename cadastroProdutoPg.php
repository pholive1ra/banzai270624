<?php
session_start();

if ((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true))
{
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('Location: login.php');
}
$logado = $_SESSION['email'];

// Conexão com o banco de dados para buscar o nome do usuário
include 'config.php';

// Função para registrar log de atividades
function registrar_log($conexao, $usuario_id, $nome_usuario, $acao, $cpf, $detalhes_adicionais = '') {
  $sql = "INSERT INTO log_atividades (usuario_id, nome_usuario, acao, cpf, detalhes_adicionais) VALUES (?, ?, ?, ?, ?)";
  $stmt = $conexao->prepare($sql);
  $stmt->bind_param('issss', $usuario_id, $nome_usuario, $acao, $cpf, $detalhes_adicionais);
  $stmt->execute();
}

// Verifica se o usuário está logado
if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];
  $sql = "SELECT * FROM usuarios WHERE email = ?";
  $stmt = $conexao->prepare($sql);
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();

      // Registra o log de acesso à página inicial
      registrar_log($conexao, $user['id'], $user['nome'], 'Equipamentos', $user['cpf']);
  }
} else {
  // Se o usuário não está logado, redireciona para a página de login
  header('Location: login.php');
  exit;
}

$sql = "SELECT nome FROM usuarios WHERE email = '$logado'";
$result = $conexao->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $nomeUsuario = $user['nome'];
} else {
    $nomeUsuario = 'Usuário';
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
    integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="equipamentos.css">
  <script src="compra.js"></script>
  <title>Equipamentos - Banzai Motos</title>
</head>

<body>

  <header>

    <div class="logo">
      <img class="logo-img" src="../imagens/logobanzai.png">
    </div>
    <input type="checkbox" id="nav_check" hidden>
    <nav>
      <ul>
        <li>
          <a href="index.php">Início</a>
        </li>
        <li>
          <a href="index.php">Motos</a>
        </li>
        <li>
          <a href="equipamentos.php" class="active">Equipamentos</a>
        </li>
      
        <li class="separa">
          |
        </li>
        <?php if (isset($_SESSION['email'])): ?>
        <li class="area-usuario">
          <a class="btn-usuario" href="profile.php">Olá, <?php echo htmlspecialchars($nomeUsuario); ?></a>
        </li>
        <li class="area-logout">
          <a class="btn-logout" href="../php/logout.php">Logout</a>
        </li>
        <?php else: ?>
        <li class="area-login">
          <a class="btn-login" href="../php/login.php">Login</a>
        </li>
        <li class="area-registro">
          <a class="btn-registrar" href="../php/register.php">Registrar</a>
        </li>
        <?php endif; ?>
      </ul>
    </nav>
    <label for="nav_check" class="hamburger">
      <div></div>
      <div></div>
      <div></div>
    </label>
  </header>

  <div class="container" id="container">

<form action="processaCadastro.php" method="POST" enctype="multipart/form-data">
    <h2>Cadastrar Novo Produto</h2>
    <label for="name">Nome do Produto:</label>
    <input type="text" id="name" name="name" required>

    <label for="description">Descrição:</label>
    <textarea id="description" name="description" required></textarea>

    <label for="price">Preço:</label>
    <input type="text" id="price" name="price" pattern="^\d+(\.\d{1,2})?$" required title="Insira um valor válido em números">

    <label for="image">Imagem:</label>
    <input type="file" id="image" name="image" required>

    <input class="btn-cadastrar" type="submit" value="Cadastrar Produto">
</form>
</div>



  <footer>
    <div id="footer_content">
      <div id="footer_contacts">
        <div class="logo">
          <img class="logo-img" src="img/logobanzai.png">
        </div>

        <div id="footer_social_media">
          <a href="https://www.instagram.com/banzai_motos/" target="_blank" class="footer-link" id="instagram">
            <i class="fa-brands fa-instagram"></i>
          </a>

          <a href="https://www.facebook.com/banzaimotos" target="_blank" class="footer-link" id="facebook">
            <i class="fa-brands fa-facebook-f"></i>
          </a>

          <a href="#" class="footer-link" id="whatsapp">
            <i class="fa-brands fa-whatsapp"></i>
          </a>
        </div>
      </div>

      <ul class="footer-list">
        <li>
          <h3>Saiba Mais</h3>
        </li>
        <li>
          <a href="#" class="footer-link">Modelos</a>
        </li>
        <li>
          <a href="#" class="footer-link">Contratos</a>
        </li>
        <li>
          <a href="#" class="footer-link">Contato</a>
        </li>
      </ul>

      <ul class="footer-list">
        <li>
          <h3>Produtos</h3>
        </li>
        <li>
          <a href="equipamentos.html" class="footer-link">equipamentos</a>
        </li>

      </ul>

      <div id="footer_subscribe">
        <h3>Entre em contato</h3>

        <p>
          Digite seu email para receber nossas novidades
        </p>

        <div id="input_group">
          <input type="email" id="email">
          <button>
            <i class="fa-regular fa-envelope"></i>
          </button>
        </div>
      </div>
    </div>

    <div id="footer_copyright">
      &#169
      2024 all rights reserved
    </div>
    </div>
  </footer>

  <script>
  function redirecionarParaErro() {
    // Redireciona para a tela de erro
    window.location.href = '../telaerror/telaerror.html';

    // Após 3 segundos, redireciona de volta para a página inicial
    setTimeout(function() {
      window.location.href = '../php/index.php';  // Substitua 'index.php' pelo seu arquivo inicial
    }, 3000);  // Tempo em milissegundos (3 segundos)
  }
</script>

  
 
</body>



</html>