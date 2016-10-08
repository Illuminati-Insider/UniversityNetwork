switch ($data['type']){
			case 'add':
				$name = check_str($data['name']);
				$surname = check_str($data['surname']);
				$lastname = check_str($data['lastname']);
				$depID = $data['depID'];
				$query = "-- Добавление преподавателя
						INSERT INTO `Profs` (`Surname`, `Name`, `Lastname`, `Departments_ID`,`Modified`)
						VALUES ('$surname', '$name', '$lastname', '$depID',CURRENT_TIMESTAMP)";
					if($mysql->query($query)) 	{
						$output = array('id' => $mysql->insert_id, 'name' => $name,'surname'=>$surname,'lastname'=>$lastname,'depId'=>$depID);
					} 
							
					else 	{
						throw403();
					};
				break;	
			case 'list':
			if (count($data) == 1) {
						$query = "-- Список сотрудников кафедры
							SELECT `ID`, `Surname`, `Name`, `Lastname`
							FROM `Profs`;";
			}
			else {
			$depID = $data['depID'];
			$query = "-- Список сотрудников кафедры
				SELECT `ID`, `Surname`, `Name`, `Lastname`
				FROM `Profs`
				WHERE `Departments_ID` = '$depID';";
			};
				if ($result = $mysql->query($query)) {	
					$output = array();
					 while ($row = $result->fetch_row())  
					 {
						$output[] = array(
						'id' => $row[0],
						'surname' => $row[1], 
						'name' =>$row[2], 
						'lastname' =>$row[3], 
						);
						
					 };					
					 /* очищаем результирующий набор */
					$result->close();
				}
					else {
						throw403();
					} 	
				break;	
			case 'modify':
				$new_name=check_str($data['name']);
				$new_surname = check_str($data['surname']);
				$new_lastname = check_str($data['lastname']);
				$profID = $data['profID'];
				$query = "-- Изменение данных преподавателя
						UPDATE `profs` 
						SET `name` = '$new_name', `surname` = '$new_surname', `lastname` = '$new_lastname',`Modified` = CURRENT_TIMESTAMP 
						WHERE `profs`.`ID` = $profID";
				
				
						if(!($mysql->query($query))) {
							throw403();
						} 
					
								else 	{
									$output = array('id' => $profID, 'name' => $new_name, 'lastname' => $new_lastname, 'surname' => $new_surname);
								};
						
				break;	
				case 'delete':
				$profID = $data['profID'];			
				$query = "DELETE FROM `Profs` WHERE `ID` = $profID;
				INSERT INTO `dellog` (`Text`, `ID`) VALUES ('prof', $profID);";
				(runmultiquery($query));	
				$output = array('id' =>$profID);										
				break;
			 
		}
