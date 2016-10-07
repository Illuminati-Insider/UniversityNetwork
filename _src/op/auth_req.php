<?php

switch($data['type']){
    case 'login':
        $login = $_POST['login'];
        $psw = $_POST['psw'];

        $query = "
            -- Получение хэша и ID пользователя из БД (с последующей валидацией в PHP)
            SELECT `ID`, `PswHash`
            FROM `Accounts`
            WHERE `Login` LIKE '$login';

            -- Создание/обновление сессии
            INSERT INTO `Sessions` (`Token`, `Accounts_ID`)
            VALUES ('$token', $accountID) 
            ON DUPLICATE KEY UPDATE `LastRequestTimestamp` = CURRENT_TIMESTAMP;";

        if ($result = $mysql->query("
            -- Получение хэша и ID пользователя из БД (с последующей валидацией в PHP)
            SELECT `ID`, `PswHash`
            FROM `Accounts`
            WHERE `Login` LIKE '$login'")){
            if ($row = $result->fetch_row()){
                $accountID = $row[0];
                $hash = $row[1];
                $result->free();
            } else throw403();
        } else throw403();

        if (!password_verify($psw, $hash)) throw403();

        # generating token for new session.
        $token = generateToken();

        if ($result = $mysql->query("
            -- Создание/обновление сессии
            INSERT INTO `Sessions` (`Token`, `Accounts_ID`)
            VALUES ('$token', $accountID) 
            ON DUPLICATE KEY 
                UPDATE `LastRequestTimestamp` = CURRENT_TIMESTAMP, 
                    `Token` = '$token', 
                    `ID` = LAST_INSERT_ID(`ID`)")){
            $sessionID = $mysql->insert_id;
            
        } else throw403();

        $accountType = $mysql->query("SELECT `accountType` FROM `Accounts` WHERE `ID` = (SELECT `Accounts_ID` FROM `Sessions` WHERE `ID` = $sessionID)")->fetch_row()[0];
        
        $_SESSION['id'] = $sessionID;
        $_SESSION['token'] = $token;
        $_SESSION['userID'] = $accountID;
        $_SESSION['accountType'] = $accountType;
        
        $output = array(
            'accountType' => $accountType
        );
        break;
    case 'invite_check':
        $hash = $data['hash'];
        $row = $mysql->query("SELECT `AccountType`, `ID`
            FROM `Accounts`
            WHERE `Accounts`.`ID` = (
                SELECT `Accounts_ID` FROM `Invites` WHERE `Hash` LIKE '$hash' AND `Used` = FALSE
            )")->fetch_row();
        switch($row[0]){
            default: throw403(); break;
            case 'manager':
                $output = array(
                    'type' => 'manager',
                    'name' => $mysql->query("SELECT `Name` FROM `Managers` WHERE `Accounts_ID` = {$row[1]}")->fetch_row()[0]
                );
                break;
            case 'student':
                $result = $mysql->query("SELECT `Surname`, `Name`, `Lastname`, `Groups_ID` FROM `Students` WHERE `Accounts_ID` = {$row[1]}");
                if (!$result) throw403();
                $studentRow = $result->fetch_row();
                $groupRow = $mysql->query("SELECT `Name` FROM `Groups` WHERE `ID` = {$studentRow[3]}")->fetch_row();
                $output = array(
                    'type' => 'student',
                    'surname' => $studentRow[0],
                    'name' => $studentRow[1],
                    'lastname' => $studentRow[2],
                    'groupName' => $groupRow[0]
                );
                break;
        }
        break;
    case 'signout':
        $_SESSION = array();
        break;
}
?>