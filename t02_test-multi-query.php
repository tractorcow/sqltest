<?php

/**
 * Learnings:
 * - Update ->queries do not interfere with any prior or following prepared statements
 * - Select ->queries do not interfere with any following prepared statements, but any prior statements
 * must be buffered
 */

require 'include.php';

$result1 = prepare_statement('SELECT "ID", "Title" FROM "SiteTree" WHERE ID > ?', array(0));
$result1->execute();

// does not interfere with anything and is always safe to call
$dbConn->query('UPDATE "SiteTree" SET "Title" = \'bob\' WHERE "ID" = 349997');

// select ->queries will break if unbuffered statements exist
$result1->store_result(); // Causes error if after the following query
$result3 = $dbConn->query('SELECT "ID", "Title" FROM "SiteTree" WHERE ID > 0');

echo "<p>Result3</p>";
foreach($result3 as $row) {
	var_dump($row);
}


$result2 = prepare_statement('SELECT "ID", "Title" FROM "SiteTree" WHERE ID > ?', array(0));
$result2->execute();

var_dump($result1);
var_dump($result2);

echo "<p>Result1</p>";
$result1->bind_result($id, $title);

while($result1->fetch()) {
	var_dump($id.": ".$title);
}


echo "<p>Result2</p>";
$result2->bind_result($id, $title);
while($result2->fetch()) {
	var_dump($id.": ".$title);
}