<?php
session_start();

$dsn = 'mysql:dbname=tb270440db;host=localhost;charset=utf8';
$user = 'tb-270440';
$password = 'kyVw5r2w3n';

$dsn = 'mysql:dbname=tb270440db;host=localhost';
$user = 'tb-270440';
$password = 'kyVw5r2w3n';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$message = "";

if (!empty($_POST['name']) && !empty($_POST['password']) && isset($_POST['register'])) {
    $reg_name = trim($_POST['name']);
    $reg_pass = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM mission6_1 WHERE name=:name");
    $stmt->execute([':name' => $reg_name]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $message = "この名前は既に使われています。<br>";
    } else {
        $hash = password_hash($reg_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO mission6_1 (name, password) VALUES (:name, :password)");
        $stmt->execute([':name' => $reg_name, ':password' => $hash]);
        $message = "新規登録が完了しました！ログインしてください。<br>";
    }
}


if (!empty($_POST['name']) && !empty($_POST['password']) && isset($_POST['login'])) {
    $login_name = trim($_POST['name']);
    $login_pass = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM mission6_1 WHERE name=:name");
    $stmt->execute([':name' => $login_name]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($login_pass, $user['password'])) {
        $_SESSION['user'] = $user['name'];
        header("Location: trainig_menu.php");
        exit;
    } else {
        $message = "名前またはパスワードが間違っています。<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ログイン・新規登録</title>
<link rel="stylesheet" href="mission6.2.css">
</head>
<body>
<h2>ログイン / 新規登録</h2>

<form method="post" >
    <input type="text" name="name" placeholder="名前"><br>
    <input type="password" name="password" placeholder="パスワード"><br><br>
    <input type="submit" name="login" value="ログイン" class="submit-button">
    <input type="submit" name="register" value="新規登録" class="submit-button">
 

</form>

<p style="color:red;"><?php echo $message; ?></p>
</body>
</html>
