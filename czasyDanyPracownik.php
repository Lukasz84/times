<?php
	//Łączę się z bazą JR
	$db_name="jobrouter";
	$db_login="root";
	$db_password="Cdrtyj159polki!";
	$db_host="127.0.0.1";
	$JRDB = mysqli_connect($db_host, $db_login, $db_password, $db_name);
	
	//Lista możliwych osób do wyboru
	$sqlWorkers =
		"SELECT
			username
		FROM
			jrusers
		WHERE
			department = 'Produkcja'
			AND (blocked <> 1
			OR blocked IS NULL)
		ORDER BY
			lastname ASC";
	$workers = mysqli_query($JRDB, $sqlWorkers);
	
	//Wybór pracownika z listy SQL, użyjemy wbudowanej w język i damy prostą funkcję click z jQuery
	$worker = NULL;

	//Zapytanie generujące raport nt. pracownika
	$sqlWorkersJob =
		"SELECT
			workDate AS 'Data',
			serialNumber AS 'Numer seryjny',
			productID AS 'Typ',
			action AS 'Czynność',
			time AS 'Czas',
			comments AS 'Uwagi'
		FROM
			st_timeemployee
		WHERE
			employee = '".$worker."'
		ORDER BY
			workDate DESC, productID ASC, serialNumber DESC, action ASC, time DESC";
	$workersJob = mysqli_query($JRDB, $sqlWorkersJob);
	
	//Ustawiam na sztywno pierwszy wiersz
	$result[0]['Data'] = "Data";
	$result[0]['Numer seryjny'] = "Numer seryjny";
	$result[0]['Typ'] = "Typ";
	$result[0]['Czynność'] = "Czynność";
	$result[0]['Czas'] = "Czas";
	$result[0]['Uwagi'] = "Uwagi";
	
	//Wpisuję dane do tablicy
	$i = 1;
	while ($row = mysqli_fetch_assoc($workersJob))
	{
		$result[$i]['Data'] = $row['Data'];
		$result[$i]['Numer seryjny'] = $row['Numer seryjny'];
		$result[$i]['Typ'] = $row['Typ'];
		$result[$i]['Czynność'] = $row['Czynność'];
		$result[$i]['Czas'] = $row['Czas'];
		$result[$i]['Uwagi'] = $row['Uwagi'];
		$i++;
	}
?>