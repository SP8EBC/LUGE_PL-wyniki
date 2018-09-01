<!doctype html>
<html lang="pl">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <body>
  
  <div class="container">
  
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

	<h5>UWAGA! Strona nie odświeża się automatycznie!</h5>

<?php
include_once 'secret.php';

//$rankSort;

if ($_GET['sort'] == "rank") {
    $rankSort = true;
}
else {
    $rankSort = false;
}

$competitionId = $_GET['competitionId'];
if (is_numeric($competitionId)) {
    $q = "SELECT
    competitor_name,
    competitor_start_number,
    competitor_rank,
    competitor_partial_rank,
    club_name,
    total_scored_time_str,
    birth_year,
    array_to_json(training_runs_times_str) AS training_runs_times_str,
    array_to_json(scored_runs_times_str) AS scored_runs_times_str
    FROM public.competition_data WHERE competition_serial_number = " . $_GET['competitionId'];
    
}
else exit();

if ($rankSort) {
    $q = $q . " ORDER BY competitor_rank DESC";
}

echo "<p>";
echo "Sortuj według: <a href=\"?competitionId=" . $competitionId .  "\" class=\"btn btn-primary btn-sm\" role=\"button\">Numerów startowych</a>";
echo "   <a href=\"?competitionId=" . $competitionId .  "&sort=rank\" class=\"btn btn-primary btn-sm\" role=\"button\">Lokat</a>";
echo "</p>";

$db = pg_connect( $db_string  );

$ret = pg_query($db, $q);
if(!$ret) {
    echo pg_last_error($db);
    exit;
} 

    //echo "<h2>" . $ . "</h2>";
    echo " <table class=\"table table-striped text-center\" style=\"font-size: 14px;\">
    <thead>
      <tr>
        <th>Nr Startowy</th>
        <th>Lokata po ostatniej konkurencji</th>
        <th>Lokata z uwzgędnieniem aktualne rozgrywanej</th>
        <th>Imię i Nazwisko</th>
        <th>Klub</th>
        <th>Rok urodzenia</th>
        <th>Czasy w ślizgach treningowych</th>
        <th>Czasy w śizgach punktowanych</th>
        <th>Łączny czas w ślizgach punktowanych</th>


      </tr>
    </thead>
    <tbody>";

while ($row = pg_fetch_assoc($ret)) {
    
    $training_json = $row['training_runs_times_str'];
    $scored_json = $row['scored_runs_times_str'];
    
    $training = json_decode($training_json, true);
    $scored = json_decode($scored_json, true);
    
    echo "<tr>";
    echo "<td> " . $row['competitor_start_number'] . " </td>";
    echo "<td> " . $row['competitor_rank'] . " </td>";
    echo "<td> " . $row['competitor_partial_rank'] . " </td>";
    echo "<td> " . $row['competitor_name'] . " </td>";
    echo "<td> " . $row['club_name'] . " </td>";
    echo "<td> " . $row['birth_year'] . " </td>";
    
    
    echo "<td>";
    foreach($training as $k=>$v) {
        if ($v != '00:00'){
            echo $v;
            echo " ; ";
        }
    }
    echo "</td>";

    echo "<td>";
    foreach($scored as $k=>$v) {
        if ($v != '00:00'){
            echo $v;
            echo " ; ";
        }
    }
    echo "</td>";
    echo "<td> " . $row['total_scored_time_str'] . " </td>";
    
    
    echo "</tr>";
}

echo "</tbody></table>";
?>

<h5>Wyjaśnenia</h5>
<dl>
	<dt>Lokata po ostatniej konkurencji</dt>
	<dd> - Lokata obliczana przez przogram MKS_JG po ostatnim zawodniku w ślizgu bądź zjeździe. 
		Mówi ona jakie miejsce zajmował dany zawodnik po ostatnim w pełni ukończonym ślizgu</dd>
	<dt>Lokata z uwzgędnieniem aktualne rozgrywanej</dt>
	<dd> - Lokata obliczana na bierząco po każdym zawodniku. Pokazuje ona które miejsce zajmuje zawodnik
		   po uwzględnieniu jego wszystkich czasów, z poprzednich i aktualnie dokonanego ślizgu</dd>
		
</dl>

</div>
</body>
</html>
