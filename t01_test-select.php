<?php

/**
 * Learnings:
 * - If you store_results on any prepared statement, you can store it for later traversal, even
 * after preparing other statements.
 * - You cannot prepare a second statement until you store_results on a prior one
 * - You can bind a statement before/after storing the result safely
 */

require 'include.php';

$result1 = prepare_statement('SELECT "ID", "Title" FROM "SiteTree" WHERE ID > ?', array(0));
$result1->execute();
$result1->store_result(); // If moved to below the next prepare_statement, it will cause an error


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