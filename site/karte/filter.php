<?php
	//  Datenbankverbindung aufbauen  ---------------------------------------------------
	include ('config.inc'); // in config.inc sind die Verbindungsparameter
	
	// Verbindung herstellen:
	// PHP-Funktion pg_connect() öffnet eine PostgreSQL-Verbindung;
	// $conn_string kommt aus Datei config.inc und enthält die Verbindungsparameter 
	$dbconn = pg_connect($conn_string);

	// Verbindungsfehler abfangen
	if ( !$dbconn )
	{
		echo "<p>Konnte keine DB-Verbindung herstellen</p>\n";
		exit;
	}
	
	

	

	
	$winkel = $_POST['winkel'];
	$entfernung = $_POST['entf'];


	// SQL-Statement (auf Variable $sql geschrieben)
	
	

	$sql = 
			"

WITH project as(
SELECT 
	baum.id as id,
	ST_Transform(ST_Project(ST_Transform(baum.geom, 4326)::geography, $entfernung, radians($winkel.0))::geometry, 3035) as geom
FROM 
	its_hackathon.baeume_50m_dwithin as baum
-- WHERE ??? = ???
)

, baumfall as (
SELECT 
	ST_MakeLine(project.geom::geometry,  baum.geom) as geom
FROM 
	project, 
	its_hackathon.baeume_50m_dwithin as baum
WHERE
	project.id = baum.id
)
,hamburg_streckennetz as(
    SELECT * FROM its_hackathon.db_streckennetz, its_hackathon.hamburg_bounds
    WHERE ST_Within (db_streckennetz.geom, hamburg_bounds.geom)
)
SELECT 
'{ \"type\": \"GeometryCollection\", \"geometries\": ' ||
    json_agg(ST_AsGeoJSON(ST_Transform(geom, 3857))::json)::text
    || '}' AS geojson 
FROM baumfall;
    
			;";
	
	
	
	//echo $sql;
	// SQL-Statement wird in Methode pg_query() übergeben und auf $result geschrieben
	$result = pg_query( $dbconn, $sql );
	//pg_fetch_all — Gibt alle Zeilen eines Abfrageergebnisses als Array zurück
	$resultArr = pg_fetch_row($result);
	//print_r ($resultArr);
	//exit;
	// Fehlermeldung
	if ( !$result )
	{
		echo "<p>Oops, ein  Fehler ist aufgetreten!</p>";
		echo $sql;
		exit;
	} else {
//		echo json_encode($resultArr); //array muss in json umgewandelt werden
		echo $resultArr[0];
	}
?>
