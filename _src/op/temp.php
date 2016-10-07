<?php
//die;
$host = "localhost";
$user = "admin";
$pass = "#JB_ProJecT\\\\746-341>?stop%567";
$dbname = "university_network";

$mysql = mysqli_connect($host,$user,$pass,$dbname);

if (mysqli_connect_errno()){
    http_response_code(500);
    die("MySQL connection failed.");
}
if (!isset($_GET['psw'])) die('empty');
$psw = password_hash($_GET['psw'], PASSWORD_DEFAULT);

$initial = array(
    'seasons' => array(
        'name' => 'desc',
        'data' =>  array('autumn', 'spring')
    ),
    'rooms' => array(
        'name' => 'location',
        'data' => array('Б 214', 'Б 204','213','Б 209')
    ),
    'classtypes' => array(
        'name' => 'type',
        'data' => array('lection', 'lab', 'activity')
    ),
    'examtypes' => array(
        'name' => 'type',
        'data' => array('exam', 'pass')
    ),
    'subjects' => array(
        'name' => 'name',
        'data' => array('Теория автоматов', 'Правоведение', 'Базы данных', 'Основы теории управления', 'Основы логического программирования', 'Элективные курсы по физической культуре', 'Прикладная статистика', 'Электронные цепи ЭВМ', 'Теория принятия решений')
    )
);

$query = "
START TRANSACTION;
INSERT INTO `Accounts`(`Login`, `PswHash`, `AccountType`) VALUES ('disentless', '$psw','admin');";

foreach($initial as $c => $e){
    $table = $c;
    $field = $e['name'];
    foreach ($e['data'] as $value){
        $query .= "INSERT INTO `$table` (`$field`) VALUES ('$value');";
    }
}

$query .= "COMMIT;";

if ($mysql->multi_query($query)){
    do {
        $mysql->store_result();
    } while($mysql->next_result());
    if ($mysql->errno != 0) {
        $mysql->query("ROLLBACK");
        die($mysql->error);
    }
} else die($mysql->error);

?>