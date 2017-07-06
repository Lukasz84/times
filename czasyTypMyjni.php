<?php
	//Łączę się z bazą JR
	$db_name = "jobrouter";
	$db_login = "root";
	$db_password = "Cdrtyj159polki!";
	$db_host = "127.0.0.1";
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
	$hardware = mysqli_query($JRDB, $sqlHardware);
	
	//Wybór montażu bądź spawalni na bazie listy (Montaz lub Spawalnia), użyjemy wbudowanej w język i damy prostą funkcję click z jQuery
	$actionType = NULL;
			
	//Rodzaj myjni lub innego urządzenia ktore user wybiera z $Hardware, np. CW3M5
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

	//ID kolumn (k) i wierszy (w)
	$w = 0;
	$k = 1;

	
	//Wpisuje rodzaj urządzenia
	$result[0][0] = $hwType;
	
	//Wpisuje czynnosci
	while ($row = mysqli_fetch_assoc($activities))
	{
		$result[$w+1][0] = $row['Name'];
		$w++;
	}
	
	//Wpisuje numery seryjne
	while ($row = mysqli_fetch_assoc($serials))
	{
		$result[0][1] = "Standard";
		$result[0][$k+1] = $row['serialNumber'];
		$k++;
	}
	
	/*Wpisuje rekordy do $result, dzialam od 1 i 2 ponieważ
	jest to minimalny wiersz oraz kolumna jeżeli istnieje jakaś czynność i jakieś urządzenie
	wpisane do macierzy*/
	for ($i = 1; $i <= $w; $i++)
	{
		//Zapytanie podające czas standardowy na daną czynność przy danym urządzeniu
		$sqlStandardValue =
			"SELECT
				standardTime
			FROM
				bkf_standardtimes
			WHERE
				hwType = '".$hwType."'
				AND action = '".$result[$i][0]."'";
		$standardValue = mysqli_query($JRDB, $sqlStandardValue);
		$row = mysqli_fetch_assoc($standardValue);
		$result[$i][1] = $row['standardTime'];

		for ($j = 2; $j <= $k; $j++)
		{
				//Zapytanie sumujące czas poświęcony na daną czynność przy danym urządzeniu
				$sqlCellValue =
					"SELECT
						SUM(time) AS CellSum
					FROM
						st_timeemployee
					WHERE
						serialNumber = ".$result[0][$j]."
						AND action = '".$result[$i][0]."'";
				$cellValue = mysqli_query($JRDB, $sqlCellValue);
				$row = mysqli_fetch_assoc($cellValue);
				$result[$i][$j] = $row['CellSum'];
		}
	}
?>