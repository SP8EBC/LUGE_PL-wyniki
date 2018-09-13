<!doctype html>
<html lang="pl">
  <head>
    <title>In the name of luge we united</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <body>
  
  <div class="container">
  
	<h1>System wyników on-line <span class="badge badge-secondary">MKS_JG</span></h1>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

	<p>Strona którą aktualnie oglądasz umożliwia śledzenie w czasie rzeczywistym czasów ślizgów bądź zjazdów na zawodach i treningach sankowych, na 
	których wykorzystywany jest pakiet oprogramowania MKS_JG.</p>
	<p style="padding-bottom: 35px;">Prezentowane dane zostały albo automatycznie przechwycone
	z podłączonego chronometru, albo zostały wpisane ręcznie przez operatora.</p>
	
	
<?php 
include_once 'secret.php';

$db = pg_connect( $db_string  );
if(!$db) {
    //echo "Error : Unable to open database\n";
} else {
    //echo "Opened database successfully\n";
}

$competitions_query = "SELECT 
  competitions.id, 
  competitions.serial_num, 
  competitions.competitions_name, 
  competitions.date, 
  competitions.location, 
  competitions.track_name, 
  competitions.logo1, 
  competitions.logo2, 
  competitions.judge1, 
  competitions.judge2, 
  competitions.judge3, 
  competitions.organizer, 
  competitions.comp_count
FROM 
  public.competitions;
";

$ret = pg_query($db, $competitions_query);
if(!$ret) {
    echo pg_last_error($db);
    exit;
} 

while($row = pg_fetch_assoc($ret)) {
    $allCmpQuery = "SELECT
    competition_data.competition_serial_number,
    competitions_to_competition_mapping.competition_type_name,
    competition_data.competition_id
    FROM
    public.competitions_to_competition_mapping
    JOIN
    public.competition_data ON competitions_to_competition_mapping.competition_serial_number = competition_data.competition_serial_number
    WHERE
    competitions_to_competition_mapping.cmps_name = '" . $row['competitions_name'] . "'
    GROUP BY
    competitions_to_competition_mapping.competition_serial_number,
    competitions_to_competition_mapping.competition_type_name,
    competition_data.competition_serial_number,
    competition_data.competition_id";
    
    $allCmpResult = pg_query($db, $allCmpQuery);
    
    echo "<div class=\"jumbotron\">";
    
    echo "<div class=\"media\">";
           echo "<img class=\"align-self-center mr-3\" src=\"" .  $row['logo1'] . "\" alt=\"Generic placeholder image\">";
           echo "<div class=\"media-body\">";
           
               echo "<h3>";
               echo $row['competitions_name'];
               echo "</h3>";
               
               echo "<div class=\"row\">";
                echo "<div class=\"col-md-1\"></div>";
                echo "<div class=\"col-md-11\">";
                    echo "Kiedy? <div class=\"font-weight-bold\">" . $row['date'] . "</div>";
                echo "</div>";
               echo "</div>";
               
               echo "<div class=\"row\">";
                echo "<div class=\"col-md-1\"></div>";
                echo "<div class=\"col-md-11\">";
                    echo "Gdzie? <div class=\"font-weight-bold\">" . $row['location'] . "</div>";
                echo "</div>";
               echo "</div>";
               
               echo "<div class=\"row\"></div>";
               
               echo "<div class=\"row\">";
                   echo "<div class=\"col-md-1\"></div>";
                   echo "<div class=\"col-md-11\">";
                   echo "Tor: <button type=\"button\" class=\"btn btn-warning\">" . $row['track_name'] . "</button>";
                   
                   echo "</div>";
               echo "</div>";
           
           echo "</div>";
    echo "</div>";      // media
    
    echo "<div class=\"conatiner\" style=\"padding-left: 5%; padding-right: 5%; padding-top: 2%;\">";

        //echo "<div class=\"row\">Rozgrywane konkurencje</div>";
        
        echo "<table class=\"table table-hover\">
                                    <thead>
                                        <tr>
                                            <th>Kategoria</th>
                                            <th>Liczba zawodników</th>
                                        </tr>
                                    </thead>
                            <tbody>";
        
        while($crow = pg_fetch_assoc($allCmpResult)) {
            $lugersCntQuery = "SELECT 
                                COUNT(competition_data.competition_serial_number)
                                FROM 
                                  public.competition_data
                                WHERE
                                	competition_serial_number = " . $crow['competition_serial_number'];
            $countResult = pg_query($db, $lugersCntQuery);
            
            echo "<tr>";
                echo "<td>" . $crow['competition_type_name'] . "</td>";
                echo "<td>" . pg_fetch_result($countResult, 0, 'count') . "</td>";
                echo "<td><a href=\"showCompetition.php?competitionId=" . $crow['competition_serial_number'] . "\"  class=\"btn btn-outline-dark\" role=\"button\">Pokaż</a></td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
    
    echo "</div>";
    
    echo "</div>";      // jumbotron
}

    pg_close($db);

?>
		<p> </p>
		<p> </p>
		<p> </p>
		<p> </p>
		
		<p class="small">MKS_JG system obsługi pomiaru czasu w sporcie sankowym. Mateusz Lubecki, Bielsko-Biała 2018, tel: +48 660 43 44 46 </p>

	</div>
	</body>
</html>
