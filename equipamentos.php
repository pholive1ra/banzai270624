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

// Obter o tipo de usuário
include_once('config.php');
$sql = "SELECT tipo_usuario FROM usuarios WHERE email = '$logado'";
$result = $conexao->query($sql);
$user = $result->fetch_assoc();
$tipo_usuario = $user['tipo_usuario'];

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
  <script src="script.js"></script>
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
          <a href="../php/index.php">Início</a>
        </li>
        <li>
          <a href="../php/index.php">Motos</a>
        </li>
        <li>
          <a href="#" class="active">Equipamentos</a>
        </li>
        <?php if($tipo_usuario === 'usuario master'): ?>
        <li>
          <a href="cadastroProdutoPg.php">Cadastrar produto</a>
        </li>
        <?php endif; ?>

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

  <main>

    <div class="background-banzai"></div>
    <div class="backgroound-banzai"></div>

    <section class="home">
      <div class="img-equipamentos">
        <a href="#capacete"> <img id="equipamento-item" src="img/capacete.jpg"> </a>
        <a href="#luva"> <img id="equipamento-item" src="img/luva.png"> </a>
        <a href="#jaqueta"> <img id="equipamento-item" src="img/jaqueta.png"> </a>
        <a href="#bota"> <img id="equipamento-item" src="img/bota.png"> </a>
      </div>
    </section>

    <div class="cards" id="cards">
                <?php
                $dbHost = 'localhost';
                $dbUsername = 'root'; // Colocar entre aspas Nome do Local do banco de dados (Normalmente o padrão é root)
                $dbPassword = ''; // Colocar entre aspas a senha que você configurou para guardar o banco de dados (caso não colocou seixar vazio)
                $dbName = 'formulario-teste';
                
                // Criar conexão
                $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
                

                // Verificar conexão
                if ($conn->connect_error) {
                    die("Falha na conexão: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM products";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "
                        <div class='product-card'>
                            <img src='img/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'>
                            <h3 class='modelo-equipamento'>" . htmlspecialchars($row['name']) . "</h3>
                            <p class='preco-avista'>" . htmlspecialchars($row['description']) . "</p>
                            <p class='preco-pix'>R$ " . htmlspecialchars($row['price']) . "</p>
                            <button class='btn buy-btn'>Comprar</button>
                        </div>";
                    }
                } else {
                    echo "<p>Sem produtos disponíveis.</p>";
                }

                $conn->close();
                ?>
            </div>
        </div>

    <!--Capacete-->

    <div class="capacete" id="capacete">

      <div class="equipamento-container">
        <img class="capacete-ls2" src="img/capacete-ls2.png"> <!--Class moto-img-->
        <h3 class="modelo-equipamento">Capacete LS2 FF358 Tribal - Rosa/Cinza</h3> <!--Class moto-modelo-->
        <h4 class="preco-pix">R$ 664,90 à vista</h3>
          <h4 class="preco-avista"> ou 12x de R$ 58,32 <br> sem juros </h3> <!--Preço parcelas-->
            <button class="btn-compra">Comprar</button>
      </div>

      <div class="equipamento-container">
        <img class="capacete-shark" src="img/capacete-shark.png">
        <h3 class="modelo-equipamento">Capacete Shark D-Skwal 2 Noxxys KBG Preto/Azul</h3>
        <h4 class="preco-pix">R$ 2.089,05 à vista</h3>
          <h4 class="preco-avista"> ou 12x de R$ 183,25<br> Sem juros </h3>
            <button class="btn-compra">Comprar</button>
      </div>

      <div class="equipamento-container">
        <img class="capacete-norisk" src="img/capacete-norisk.png">
        <h3 class="modelo-equipamento">Capacete Norisk Motion Articulado Monocolor - Preto Fosco</h3>
        <h4 class="preco-pix">R$ 712,40 à vista</h3>
          <h4 class="preco-avista"> ou 12x de R$ 62,49 <br> sem juros</h3>
            <button class="btn-compra">Comprar</button>
      </div>
    </div> <!--Fecha div capacetes-->


    <!--Luva-->

    <div class="luva" id="luva">

      <div class="equipamento-container">
        <img class="luva-x11-preto" src="img/luva-x11-preto.png"> <!--Class moto-img-->
        <h3 class="modelo-equipamento">Luva X11 Racer 2 Cano Longo (Couro) - Preta/Cinza/Vermelha</h3>
        <!--Class moto-modelo-->
        <h4 class="preco-pix">R$ 408,40 à vista</h3>
          <h4 class="preco-avista"> ou 12x de R$ 35,82 <br> sem juros </h3> <!--Preço parcelas-->
            <button class="btn-compra" onclick="redirecionarParaErro()">Comprar</button>
      </div>

      <div class="equipamento-container">
        <img class="luva-x11-azul" src="img/luva-x11-azul.png">
        <h3 class="modelo-equipamento">Luva X11 Racer 2 Cano Longo (Couro) - Azul/Vermelha/Branca</h3>
        <h4 class="preco-pix">R$ 408,40 à vista</h3>
          <h4 class="preco-avista"> ou 2x de R$ 35,82<br> Sem juros </h3>
            <button class="btn-compra">Comprar</button>
      </div>

      <div class="equipamento-container">
        <img class="luva-x11-blackout" src="img/luva-x11-blackout.png">
        <h3 class="modelo-equipamento">Luva X11 Blackout 2 Meio Dedo Preta</h3>
        <h4 class="preco-pix">R$ 85,40 à vista</h3>
          <h4 class="preco-avista"> ou 3x de R$ 29,97 <br> sem juros</h3>
            <button class="btn-compra">Comprar</button>
      </div>
    </div> <!--Fecha div luvas-->


    <!--Jaqueta-->

    <div class="jaqueta" id="jaqueta">

      <div class="equipamento-container">
        <img class="jaqueta-texx-falcon" src="img/jaqueta-texx-falcon.png"> <!--Class moto-img-->
        <h3 class="modelo-equipamento">Jaqueta Texx Falcon V2 Preta/Verde (Couro)</h3> <!--Class moto-modelo-->
        <h4 class="preco-pix">R$ 1.044,05 à vista</h3>
          <h4 class="preco-avista"> ou 12x de R$ 91,58 <br> sem juros </h3> <!--Preço parcelas-->
            <button class="btn-compra">Comprar</button>
      </div>

      <div class="equipamento-container">
        <img class="jaqueta-x11" src="img/jaqueta-x11.png">
        <h3 class="modelo-equipamento">Jaqueta X11 Super Air Preta (Ventilada)</h3>
        <h4 class="preco-pix">R$ 426,55 à vista</h3>
          <h4 class="preco-avista"> ou 12x de R$ 37,42<br> Sem juros </h3>
            <button class="btn-compra">Comprar</button>
      </div>

      <div class="equipamento-container">
        <img class="jaqueta-texx-sniper" src="img/jaqueta-texx-sniper.png">
        <h3 class="modelo-equipamento">Luva X11 Blackout 2 Meio Dedo Preta</h3>
        <h4 class="preco-pix">R$ 85,40 à vista</h3>
          <h4 class="preco-avista"> ou 3x de R$ 29,97 <br> sem juros</h3>
            <button class="btn-compra">Comprar</button>
      </div>
    </div> <!--Fecha div jaqueta-->


    <!--Bota-->

    <div class="bota" id="bota">

      <div class="equipamento-container">
        <img class="bota-alphinestars" src="img/bota-alphinestars.png"> <!--Class moto-img-->
        <h3 class="modelo-equipamento">Bota Alpinestars Radon Drystar Preta</h3> <!--Class moto-modelo-->
        <h4 class="preco-pix">R$ 1.614,05 à vista</h3>
          <h4 class="preco-avista"> ou 12x de R$ 141,58 <br> sem juros </h3> <!--Preço parcelas-->
            <button class="btn-compra">Comprar</button>
      </div>

      <div class="equipamento-container">
        <img class="bota-texx" src="img/bota-texx.png">
        <h3 class="modelo-equipamento">Bota Texx Super Tech V2 Preta</h3>
        <h4 class="preco-pix">R$ 759,05 à vista</h3>
          <h4 class="preco-avista"> ou 12x de R$ 66,58<br> Sem juros </h3>
            <button class="btn-compra">Comprar</button>
      </div>

      <div class="equipamento-container">
        <img class="bota-x11" src="img/bota-x11.png">
        <h3 class="modelo-equipamento">Bota X11 Fuse 2 Preta</h3>
        <h4 class="preco-pix">R$ 616,55 à vista</h3>
          <h4 class="preco-avista"> ou 12x de R$ 54,08 <br> sem juros</h3>
            <button class="btn-compra">Comprar</button>
      </div>
    </div> <!--Fecha div jaqueta-->


  </main>

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