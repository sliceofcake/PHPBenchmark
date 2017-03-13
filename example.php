<?
header("Content-Type: text/plain"); // if displaying the result in a web browser
require_once("PHPBenchmark.php");

$a = new stdClass;
$a->hello    = "world";
$a->mountain = TRUE;
$a->backpack = [0,1,2,3];
echo "Output of testing when printF flag set high\n";
echo "-------------------------------------------\n";
$resE = PHPB_test(
	function()use($a){isset($a->hello);},
	function()use($a){property_exists($a,"hello");},
	function()use($a){},
	TRUE);
echo "\n\n";
echo "Returned result variable\n";
echo "------------------------\n";
var_export($resE);

?>