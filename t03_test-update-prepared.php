<?php

/**
 * Learnings:
 * - If doing an update prepared statement, you must buffer all prior queries (but we knew that anyway)
 * - Update prepared statements do not block the preparation of any following statements
 * - It doesn't hurt to do ->store_result on update statements
 */

require 'include.php';

$result1 = prepare_statement('SELECT "ID", "Title" FROM "SiteTree" WHERE ID > ?', array(0));
$result1->execute();

// Should be ok
$dbConn->query('UPDATE "SiteTree" SET "Title" = \'bob\' WHERE "ID" = 349997');

//
$result1->store_result(); // Will fail if moved to below the next prepare
$update1 = prepare_statement('UPDATE "SiteTree" SET "Title" = ? WHERE "ID" = ?', array('Thing', 12233));
$update1->execute();
$update1->store_result();



global $dbConn;
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