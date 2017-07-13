<?php
class times
{

//Przetrzymywanie łącza do DB
private $handler;
private $typMyjni=array();

function __construct()
{
  	$db_name = "jobrouter";
	$db_login = "root";
	$db_password = "Cdrtyj159polki!";
	$db_host = "127.0.0.1";
	$JRDB = mysqli_connect($db_host, $db_login, $db_password, $db_name) or die('Cannot connect to DB::mysqli_connect');
	$this->handler=$JRDB;
	
}
public
function czasyTypMyjni()
{
    $sqlHardware =
		"SELECT DISTINCT
			a.productID
		FROM
			st_timeemployee AS a
			INNER JOIN bkf_products AS b ON a.productID = b.productId
		ORDER BY
			a.productID ASC";
	$hardware = mysqli_query($this->handler, $sqlHardware);
	
	//Wybór montażu bądź spawalni na bazie listy (Montaz lub Spawalnia), użyjemy wbudowanej w język i damy prostą funkcję click z jQuery
	$actionType = 'Montaz';
			
	//Rodzaj myjni lub innego urządzenia ktore user wybiera z $Hardware, np. CW3M5
	$hwType = 'CW3T5';
	
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
	$serials = mysqli_query($this->handler, $sqlSerials);
	
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
	$activities = mysqli_query($this->handler, $sqlActivities);

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
		$standardValue = mysqli_query($this->handler, $sqlStandardValue);
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
				$cellValue = mysqli_query($this->handler, $sqlCellValue);
				$row = mysqli_fetch_assoc($cellValue);
				$result[$i][$j] = $row['CellSum'];
		}
	}
		$this->typMyjni=$result;
		return($result);
	}

public
function czasyDanaMyjnia()
	{
		//Lista możliwych urządzeń do wyboru
	$sqlHardware =
		"SELECT DISTINCT
			a.productID
		FROM
			st_timeemployee AS a
			INNER JOIN bkf_products AS b ON a.productID = b.productId
		ORDER BY
			a.productID ASC";
	$hardware = mysqli_query($this->handler, $sqlHardwareList);
	
	//Wybór montażu bądź spawalni na bazie listy (Montaz lub Spawalnia), użyjemy wbudowanej w język i damy prostą funkcję click z jQuery
	$actionType = 'Montaz';
			
	//Rodzaj myjni lub innego urządzenia ktore user wybiera z $hardware, np. CW3M5
	$hwType = 'CW6T5';
	
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
	$serials = mysqli_query($this->handler, $sqlSerials);
	
	//Numer seryjny który wybrał user na bazie listy $serials
	$serial = 20109;
	
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
	$activities = mysqli_query($this->handler, $sqlActivities);
	
	//Wybór zespołu który ma pokazać (z listy)
	$teamLeader = 'l.kot';
	
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
	$workers = mysqli_query($this->handler, $sqlWorkers);

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
				$cellValue = mysqli_query($this->handler, $sqlCellValue);
				$row = mysqli_fetch_assoc($cellValue);
				$result[$i][$j] = $row['CellSum'];
			}
		}
		return($result);
	}

public
function czasyDanyPracownik()
{
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
	$workers = mysqli_query($this->handler, $sqlWorkers);
	
	//Wybór pracownika z listy SQL, użyjemy wbudowanej w język i damy prostą funkcję click z jQuery
	$worker = 'k.szmagara';

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
	$workersJob = mysqli_query($this->handler, $sqlWorkersJob);
	
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
return($result);
print_r($result);

}

public
function getView($result)
	{
		$dane=$result;
	//print_r($dane);
		foreach($dane as $key => $value)
			//foreach($value as $key2 => $value2)
				{
					$a=$value;
				}
					$cnt=count($a);
					echo '<table class="table  table-curved">';
					echo '<thead>';
					echo '<tr>';
					for($i=0;$i<$cnt;$i++)
						{
							echo '<th>'.$dane[0][$i].'</th>';
						}
					echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					$k=1;
					
					for($i=1;$i<$cnt;$i++)
						{
							echo '<tr>';
						for($j=0;$j<$cnt;$j++)
							{
								echo '<td>'.$dane[$i][$j].'</td>';
								$k++;
							}

							echo '</tr>';
						}
					echo '<tbody>';
					echo '</tbody>';
					echo '</table>';

	
	
	
	}	
							

}
    

?>