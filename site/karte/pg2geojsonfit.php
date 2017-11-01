<?php
	// DB-Verbindungsdaten-Datei einlesen
	include 'config.inc';
	
	# Verbindung herstellen
	$dbconn = pg_connect($conn_string);

	if ( !$dbconn ) {
		echo "<p>Konnte keine DB-Verbindung herstellen.</p>\n";
		exit;
	}

	# Tabellennamen aus Parameter übernehmen
	if ( isset ($_GET['table']) && $_GET['table'] !== '' ) {
		$table = $_GET['table'];
	} else {
		echo "Bitte geben Sie einen Tabellennamen an!";
		exit;
	}
	
	# SRID aus Parameter übernehmen
	if ( isset ($_GET['srid']) && $_GET['srid'] !== '' ) {
		$srid = $_GET['srid'];
	} else {
		$srid = "3857";
	}

	# Geometry-Spalte aus Parameter übernehmen
	if ( isset ($_GET['geocol']) && $_GET['geocol'] !== '' ) {
		$geocol = $_GET['geocol'];
	} else {
		$geocol = "geometry";
	}

	# Query-Parameter übernehmen
	if ( isset ($_GET['query']) && $_GET['query'] !== '' ) {
		$query = ' WHERE ' . $_GET['query'];
	} else {
		$query = '';
	}

	# Abfrage ausführen
	$sql = 'SELECT *,ST_AsGeoJSON(ST_Transform(' . $geocol . ',' . 
						$srid . ')) AS coord FROM ' . $table;


	# Debug
	#if ( isset ($_GET['debug']) && $_GET['debug'] !== '' ) {
	if ( isset ($_GET['debug']) ) {
		echo "<h1>pg2geojson</h1>";
		echo "<p>DB-Verbindung hergestellt.";
		echo "<p>Tabelle: $table</p>";
		echo "<p>SRID: $srid</p>";
		echo "<p>Geometry-Spalte: $geocol</p>";
		echo "<p>Query: $query</p>";
		echo "<p>SQL: $sql</p>";
		exit;
	}

	$result = pg_query( $dbconn, $sql );
	if ( !$result ) {
		echo "<p>Ein Fehler ist aufgetreten!</p>";
		echo "<h2 style='color: red;'>$sql</h2>";
		exit;
	}

	# GeoJSON mit den Daten aus der Datenbankabfrage ausgeben
	header('Content-Type: application/json; charset=utf-8');
	echo "{ \"type\": \"FeatureCollection\",\n";
	echo "\"features\": [\n";

	$rowcnt = 0;
	while ( $row = pg_fetch_assoc($result) ) {
		$properties = "";
		$colcnt = 0;
		if ( $rowcnt++ > 0 ) { echo ",\n"; }
		foreach ( $row as $fieldname => $fieldvalue ) {
			$fieldtype = pg_field_type($result, $colcnt);
			if ( $fieldtype !== "geometry" ) {
				if ( $fieldname == "coord" ) { 
					$geometry = "\"geometry\": \n";
					$geometry .= $fieldvalue . "\n";
					$geometry .= ",\n";
				} else {
					$properties .= "\"$fieldname\": \"$fieldvalue\",\n";
				}
			}
			$colcnt++;
		}
		echo "{\n";
		echo "\"type\": \"Feature\",\n";
		echo $geometry;
		echo "\"properties\": {\n";
		echo substr($properties, 0, -2);
		echo "}\n";
		echo "}";
	}
	echo "]\n";
	echo "}\n";
?>
