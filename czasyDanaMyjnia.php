<?php
	//Łączę się z bazą JR
	$db_name="jobrouter";
	$db_login="root";
	$db_password="Cdrtyj159polki!";
	$db_host="127.0.0.1";
	$JRDB = mysqli_connect($db_host, $db_login, $db_password, $db_name);
	
	//Lista możliwych urządzeń do wyboru
	$sqlHardware =
		"SELECT DISTINCT
			a.productID
		FROM
			st_timeemployee AS a
			INNER JOIN bkf_products AS b ON a.productID = b.productId
		ORDER BY
			a.productID ASC";
	$hardware = mysqli_query($JRDB, $sqlHardwareList);
	
	//Wybór montażu bądź spawalni na bazie listy (Montaz lub Spawalnia), użyjemy wbudowanej w język i damy prostą funkcję click z jQuery
	$actionType = NULL;
			
	//Rodzaj myjni lub innego urządzenia ktore user wybiera z $hardware, np. CW3M5
	$hwType = NULL;
	
	//Wyciaga numery seryjne do tabeli, na bazie wybranego urzadzenia
	$sqlSerials =
		"SELECT DISTINCT
			serialNumber
		FROM
			st_timeemployee
		WHERE
			productId = '".$hwType."'
		ORDER BY
			serialNumber ASC";
	$serials = mysqli_query($JRDB, $sqlSerials);
	
	//Numer seryjny który wybrał user na bazie listy $serials
	$serial = NULL;
	
	//Wyciaga liste czynnosci
	$sqlActivities =
		"SELECT DISTINCT
			a.Name
		FROM
			bkf_cwtimeaction AS a
			INNER JOIN bkf_standardtimes AS b ON b.action = a.Name
		WHERE
			a.Type = '".$actionType."'
			AND b.hwType = '".$hwType."'
		ORDER BY
			a.Name ASC";
	$activities = mysqli_query($JRDB, $sqlActivities);
	
	//Wybór zespołu który ma pokazać (z listy)
	$teamLeader = NULL
	
	//Pracownicy powiązani z urządzeniem i zespołem
	$sqlWorkers =
		"SELECT
			username
		FROM
			jrusers
		WHERE
			department = 'Produkcja'
			AND (blocked <> 1
			OR blocked IS NULL)
			AND supervisor = '".$teamLeader."'
		ORDER BY
			lastname ASC";
	$workers = mysqli_query($JRDB, $sqlWorkers);

	//ID kolumn (k) i wierszy (w)
	$w = 0;
	$k = 0;

	
	//Wpisuje nr seryjny urządzenia
	$result[0][0] = $serial;
	
	//Wpisuje czynnosci
	while ($row = mysqli_fetch_assoc($activities))
	{
		$result[$w+1][0] = $row['Name'];
		$w++;
	}
	
	//Wpisuje osoby do pierwszego (tj. zerowego) wiersza
	while ($row = mysqli_fetch_assoc($workers))
	{
		$result[0][$k+1] = $row['username'];
		$k++;
	}
	
	for ($i = 1; $i <= $w; $i++)
	{
		for ($j = 1; $j <= $k; $j++)
		{
				//Zapytanie sumujące czas poświęcony na daną czynność przy danej osobie i ustalonym jednym nr seryjnym
				$sqlCellValue =
					"SELECT
						SUM(time) AS CellSum
					FROM
						st_timeemployee
					WHERE
						employee = '".$result[0][$j]."'
						AND action = '".$result[$i][0]."'
						AND serialNumber = ".$serial."";
				$cellValue = mysqli_query($JRDB, $sqlCellValue);
				$row = mysqli_fetch_assoc($cellValue);
				$result[$i][$j] = $row['CellSum'];
		}
	}
?>