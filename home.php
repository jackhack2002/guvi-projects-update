<?php 
require './vendor/autoload.php';

require('redis-session-php/redis-session.php');
RedisSession::start(); // overrides PHP's default session_save_handler and calls session_start()

// use sessions as normal
$_SESSION['barbara'] = 'streisand';

$redis = new Predis\Client();
$redis->connect('127.0.0.1', 6379);


$cache_key = md5($sql);

            if ($redis->exists($cache_key)) {

                $data_source = "Data from Redis Server";
                $data = unserialize($redis->get($cache_key));

            } else {

                $data_source = 'Data from MySQL Database';

                $db_name     = 'guvi';
                $unmae  = "root";
                $db_password = '';
                $db_host     = 'localhost:3306';


                $pdo = new PDO('mysql:host=' . $db_host . '; dbname=' . $db_name, $unmae, $db_password);

                $pdo = new PDO('mysql:host=' . $db_host . '; dbname=' . $db_name, $unmae, $db_password);

                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $data = []; 

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {          
                   $data[] = $row;  
                }  

                $redis->set($cache_key, serialize($data)); 
                $redis->expire($cache_key, 86,400);        

           } 

session_start();

if (isset($_SESSION['id']) && isset($_SESSION['unmae'])) {

 ?>
<!DOCTYPE html>
<html>
<head>
	<title>HOME</title>
     <link rel="icon" href="img/websitelogo.png">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
     <h1>Hello, <?php echo $_SESSION['name']; ?></h1>
     <h1>Your Email is, <?php echo $_SESSION['email']; ?></h1>
     <h1>Your DOB is, <?php echo $_SESSION['dob']; ?></h1>
     <a href="logout.php">Logout</a>
</body>
</html>

<?php 
}else{
     header("Location: index.php");
     exit();
}
 ?>
