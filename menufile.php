<?php
$dsn = 'mysql:dbname=tb270440db;host=localhost;charset=utf8';
$user = 'tb-270440';
$password = 'kyVw5r2w3n';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$edit_id = "";
$edit_temperature = "";
$edit_wind = "";
$edit_weather = "";
$edit_menu = "";

if(!empty($_POST['temperature']) && !empty($_POST['wind']) && !empty($_POST['weather']) && !empty($_POST['menu'])){
    $temperature = (int)$_POST['temperature'];
    $wind = (int)$_POST['wind'];
    $weather = $_POST['weather'];
    $menu = $_POST['menu'];
    $date = date('Y-m-d H:i:s');

    if(!empty($_POST['edit_id'])){
        $id = (int)$_POST['edit_id'];
        $sql = 'UPDATE training_log 
                   SET temperature=:temperature, wind=:wind, weather=:weather, menu=:menu, date=:date 
                 WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':temperature', $temperature, PDO::PARAM_INT);
        $stmt->bindValue(':wind', $wind, PDO::PARAM_INT);
        $stmt->bindValue(':weather', $weather, PDO::PARAM_STR);
        $stmt->bindValue(':menu', $menu, PDO::PARAM_STR);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }else{
        $sql = 'INSERT INTO training_log (temperature, wind, weather, menu, date) 
                VALUES (:temperature, :wind, :weather, :menu, :date)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':temperature', $temperature, PDO::PARAM_INT);
        $stmt->bindValue(':wind', $wind, PDO::PARAM_INT);
        $stmt->bindValue(':weather', $weather, PDO::PARAM_STR);
        $stmt->bindValue(':menu', $menu, PDO::PARAM_STR);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
    }
}

$suggest_results = [];
if(!empty($_POST['temperature']) && !empty($_POST['wind']) && !empty($_POST['weather'])){
    $temperature = (int)$_POST['temperature'];
    $wind = (int)$_POST['wind'];
    $weather = $_POST['weather'];

    $min_temp = $temperature - 5;
    $max_temp = $temperature + 5;
    $min_wind = $wind - 2;
    $max_wind = $wind + 2;

    if($weather == "雨"){  
        $sql = "SELECT * FROM training_log 
                  WHERE temperature BETWEEN :min_temp AND :max_temp
                    AND wind BETWEEN :min_wind AND :max_wind
                    AND weather = :weather
                  ORDER BY date DESC";
    }else{
        $sql = "SELECT * FROM training_log 
                  WHERE temperature BETWEEN :min_temp AND :max_temp
                    AND wind BETWEEN :min_wind AND :max_wind
                  ORDER BY date DESC";
    }
              
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':min_temp', $min_temp, PDO::PARAM_INT);
    $stmt->bindValue(':max_temp', $max_temp, PDO::PARAM_INT);
    $stmt->bindValue(':min_wind', $min_wind, PDO::PARAM_INT);
    $stmt->bindValue(':max_wind', $max_wind, PDO::PARAM_INT);
    if($weather === "雨"){ 
        $stmt->bindValue(':weather', $weather, PDO::PARAM_STR);
    }
    $stmt->execute();
    $suggest_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$sql = "SELECT * FROM training_log ORDER BY date DESC";
$stmt = $pdo->query($sql);
$all_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>練習メニュー管理</title>
<link rel="stylesheet" href="mission6.2.css">
</head>
<body>

<h2>練習メニュー記録</h2>

<form method="post" class="log-form">
    <input type="text" name="temperature" placeholder="気温(℃)" value="<?php echo htmlspecialchars($edit_temperature, ENT_QUOTES); ?>"><br>
    <input type="text" name="wind" placeholder="風速(m/s)" value="<?php echo htmlspecialchars($edit_menu, ENT_QUOTES); ?>"><br>
    <input type="text" name="weather" placeholder="天気(晴れ/曇り/雨)" value="<?php echo htmlspecialchars($edit_weather, ENT_QUOTES); ?>"><br>
    <input type="text" name="menu" placeholder="練習メニュー" value="<?php echo htmlspecialchars($edit_menu, ENT_QUOTES); ?>"><br>
    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
    <input type="submit" value="送信" class="submit-button">
</form>

<?php
if(!empty($suggest_results)){
    echo "<h3>過去の類似条件の練習メニュー:</h3>";
    foreach($suggest_results as $row){
        echo "<div class='log-card suggest'>";
        echo "<strong>日時: ".$row['date']."</strong><br>";
        echo "気温: ".$row['temperature']." / 風速: ".$row['wind']." / 天気: ".$row['weather']."<br>";
        echo "練習メニュー: ".$row['menu'];
        echo "</div>";
    }
}

if(!empty($all_logs)){
    echo "<h3>これまでの練習記録:</h3>";
    foreach($all_logs as $row){
        echo "<div class='log-card'>";
        echo "<strong>日時: ".$row['date']."</strong><br>";
        echo "気温: ".$row['temperature']." / 風速: ".$row['wind']." / 天気: ".$row['weather']."<br>";
        echo "練習メニュー: ".$row['menu'];
        echo "</div>";
    }
}
?> 

</body>
</html>
