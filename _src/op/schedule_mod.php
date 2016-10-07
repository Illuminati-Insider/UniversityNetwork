<?php

switch($data['type']){
    case 'list':
        if (isset($data['groupID'])){
            $groupID = checkInt($data['groupID']);
        }
        $output = array(
            'rules' => array(),
            'rooms' => array(),
            'profs' => array(),
            'groups' => array()
        );
        if ($accountType == 'manager'){
            // Rules
            $query = "SELECT DISTINCT `id` FROM `rulesList`";
            if (isset($groupID)){
                $condition = " WHERE `Groups_ID` = $groupID";
            } else if (isset($data['subjectID'])){
                $condition =" WHERE `Subjects_ID` = {$data['subjectID']}";
            }
            $query .= $condition;
            $result = $mysql->query($query);
            if (!$result) throw403();
            
            $rules_id = array();
            while($row = $result->fetch_row()){
                $rules_id[] = $row[0];
            }
            
            
            $output = array();
            foreach($rules_id as $ruleID){
                $container = array();
                $query = "
                    SELECT DISTINCT `id`, `weekDay`, `weekType`, `classType`, `SubgroupIndex` as `subIndex`, `Subjects_ID` as `subjectID`, `order` 
                    FROM `rulesList`
                    WHERE `id` = $ruleID";
                if ($result = $mysql->query($query)){
                    if ($row = $result->fetch_assoc()){
                        $container = $row;
                    }
                } else throw403();
                
                // Dependencies.
                $fields = array('rooms' => 'Rooms_id','groups' => 'Groups_id', 'profs' => 'Profs_id');

                foreach($fields as $c => $f){
                    $query = "
                        SELECT DISTINCT `$f` as `id`, `$c`.`name` as `name`
                        FROM `rulesList` INNER JOIN `$c` ON `$c`.`id` = `rulesList`.`$f`
                        WHERE `rulesList`.`id`=$ruleID";
                    $container[$c] = array();
                    if ($result = $mysql->query($query)){
                        while($row = $result->fetch_assoc()){
                            $container[$c][] = $row;
                        }
                    } else throw403();
                }
                $output[] = $container;
            }
        } else {
            // Rules
            if ($result = $mysql->query("
                SELECT `ClassType`, `SubgroupIndex`, `Subjects_ID` 
                FROM `rulesList` 
                WHERE `Groups_ID` = $groupID
                ")){
                while($row = $result->fetch_row()){
                    $output['rules'][] = array(
                        'classType' => $row[0],
                        'subIndex' => $row[1],
                        'subjectID' => $row[2]
                    );
                }
            } else throw403();
            
            // Rooms
            if ($result = $mysql->query("
                SELECT `ID`, `Location`
                FROM `Rooms`
                WHERE `ID` IN (
                    SELECT DISTINCT `Rooms_ID`
                    FROM `rulesList` 
                    WHERE `Groups_ID` = $groupID
                )
                ")){
                while($row = $result->fetch_row()){
                    $output['rooms'][] = array(
                        'id' => $row[0],
                        'location' => $row[1]
                    );
                }
            } else throw403();
            
            // Groups
            if ($result = $mysql->query("
                SELECT `ID`, `Name`
                FROM `Groups`
                WHERE `ID` IN (
                    SELECT DISTINCT `Groups_ID`
                    FROM `rulesList` 
                    WHERE `Groups_ID` = $groupID
                )
                ")){
                while($row = $result->fetch_row()){
                    $output['groups'][] = array(
                        'id' => $row[0],
                        'name' => $row[1]
                    );
                }
            } else throw403();
            
            // Profs
            if ($result = $mysql->query("
                SELECT `ID`, `Surname`, `Name`, `Lastname`
                FROM `Profs`
                WHERE `ID` IN (
                    SELECT DISTINCT `Profs_ID`
                    FROM `rulesList` 
                    WHERE `Groups_ID` = $groupID
                )
                ")){
                while($row = $result->fetch_row()){
                    $output['profs'][] = array(
                        'id' => $row[0],
                        'surname' => $row[1],
                        'name' => $row[2],
                        'lastname' => $row[3]
                    );
                }
            } else throw403();
            
            // Classes
            if ($result = $mysql->query("
                SELECT `ID`, `StartTime` 
                FROM `Classes` 
                WHERE `ClassRules_ID` IN (
                    SELECT `ID` FROM `rulesList` WHERE `Groups_ID` = $groupID
                )
            ")){
                $output['classes'] = array();
                while($row = $result->fetch_row()){
                    $output['classes'][] = array(
                        'id' => $row[0],
                        'startTime' => $row[1]
                    );
                }
            } else throw403();
        }
        break;
    case 'modify':
        $ruleID = checkInt($data['ruleID']);
        $query = "DELETE FROM `ClassRules` WHERE `ID` = $ruleID; ";
    case 'add':
        $roomsID = $data['rooms_id'];
        $profsID = $data['profs_id'];
        $groupsID = $data['groups_id'];
        $dates = $data['dates'];
        $weekDay = checkInt($data['weekDay']);
        $weekType = checkInt($data['weekType']);
        $classType = checkString($data['classType']);
        $subgroup = $data['subgroup'];
        $order = checkInt($data['order']);
        $subjectID = checkInt($data['subjectID']);
        
        $query .= "
            INSERT INTO `ClassRules`(`weekDay`, `weektype`, `classtype`, `subgroupIndex`, `order`, `subjects_id`)
            VALUES ($weekDay, $weekType, '$classType', ".($subgroup ? $subgroup : 'NULL').", $order, $subjectID);

            SET @RuleID = @@IDENTITY;";
        
        foreach($roomsID as $roomID){
            $query .= "INSERT INTO `ClassRoom` VALUES (@RuleID, $roomID);";
        }
        foreach($profsID as $profID){
            $query .= "INSERT INTO `ClassProf` VALUES (@RuleID, $profID);";
        }
        foreach($groupsID as $groupID){
            $query .= "INSERT INTO `ClassGroup` VALUES (@RuleID, $groupID);";
        }
        foreach($dates as $date){
            $query .= "INSERT INTO `Classes`(`classRules_id`, `startTime`) VALUES (@RuleID, '$date');";
        }
        runMultiQuery($query);
        $output = array(
            'ruleID' => $ruleID ? $ruleID : $mysql->query("SELECT @RuleID")->fetch_row()[0],
            'rooms' => $roomsID,
            'profs' => $profsID,
            'groups' => $groupsID,
            'dates' => $dates,
            'weekDay' => $weekDay,
            'weekType' => $weekType,
            'classType' => $classType,
            'subgroup' => $subgroup,
            'order' => $order
        );
        break;
    case 'delete':
        $ruleID = checkInt($data['ruleID']);
        
        if (!$mysql->query("DELETE FROM `ClassRules` WHERE `ID` = $ruleID")) throw403();
        $output = $ruleID;
        break;
}

?>