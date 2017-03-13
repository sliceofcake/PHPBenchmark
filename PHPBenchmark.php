<?
// This function tests two functions for the purpose of figuring out which completes in less time and by how much.
// Modern personal computers won't run a code block in the same amount of time, all the time.
// This function is primarily intended to find large (≥2x) speed differences between two functions.
// When this function is used to test comparable and/or very fast functions, the results don't have much meaning.
// Remember to test your functions on the actual system that you're running your project on, results depend on the system.
function PHPB_test($PHPB_1Fxn,$PHPB_2Fxn,$PHPB_emptyFxn,$PHPB_printF=FALSE){
	// Input
	// $PHPB_1Fxn     - the first  function with a testing code block inside
	// $PHPB_2Fxn     - the second function with a testing code block inside
	// $PHPB_EmptyFxn - an empty function (though possibly with "global" or "use" clauses)
	//                  used as a reference to identify testing overhead
	
	// Output
	// [
	//  "fxn1MicrosecondN" => number - roughly how long [microseconds] one run of $PHPB_1Fxn takes
	//  "fxn2MicrosecondN" => number - roughly how long [microseconds] one run of $PHPB_2Fxn takes
	// ]
	
	// [!] PROGRAMMER CONFIGURATION VARIABLES --------------------------------------
	// tl;dr - start with 100, lower it if testing takes too long
	// We switch back and forth between the code blocks.
	// This hopefully irons out ~some~ temporal biases [caching/garbage collection/warm-up].
	$PHPB_overallN = 100; // iteration count
	
	// tl;dr - this should probably be 100 for most purposes
	// Our timer measures in whole microseconds.
	// Our worst-case error is therefore 1 microsecond.
	// We need to run large batches of individual runs, enough to make up for this error.
	// We'll then divide batch time by batch size to get approximated individual run time.
	$PHPB_batchMinT = 100; // microseconds
	// However, larger batches have a higher risk of incorporating CPU hiccups.
	// Strike a balance.
	
	
	
	
	// INTERNAL VARIABLES ----------------------------------------------------------
	$PHPB_sample1A            = [];
	$PHPB_sample2A            = [];
	$PHPB_sampleEmpty1A       = [];
	$PHPB_sampleEmpty2A       = [];
	$PHPB_display             = function($headerS,$microsecondPerRunT)use($PHPB_printF){
		if (!$PHPB_printF){return;}
		if ($microsecondPerRunT <= 0){
			echo $headerS." : took no time per run";
			return;}
		$thousandShiftN = 0;
		$perRunT = $microsecondPerRunT;
		while ($perRunT <     1){$perRunT *= 1000;$thousandShiftN--;}
		while ($perRunT >= 1000){$perRunT /= 1000;$thousandShiftN++;}
		switch ($thousandShiftN){default:$unitS = "?s(ERROR)";
			break;case -6:$unitS = "ys";
			break;case -5:$unitS = "zs";
			break;case -4:$unitS = "as";
			break;case -3:$unitS = "fs";
			break;case -2:$unitS = "ps";
			break;case -1:$unitS = "ns";
			break;case  0:$unitS = "µs";
			break;case  1:$unitS = "ms";
			break;case  2:$unitS = "s";}
		
		$sigFigGoalN = 4;
		$digitLeftN = floor(log10($perRunT)+1);
		$perRunFormattedT = round(($perRunT / pow(10,$digitLeftN)) * pow(10,$sigFigGoalN)) / pow(10,$sigFigGoalN) * pow(10,$digitLeftN);
		echo $headerS." : ".str_repeat(" ",4-$digitLeftN).number_format($perRunFormattedT,3).$unitS." per run";
		flush();};
	$PHPB_echo = function($m)use($PHPB_printF){
		if (!$PHPB_printF){return;}
		echo $m;
		flush();};
	
	
	
	// EMPTY/CALIBRATION TEST ------------------------------------------------------
	
	// select a good $PHPB_perN
	$PHPB_perN_practicalLimit = pow(2,16);
	
	// calculate how large a 1Fxn batch needs to be
	$PHPB_echo("\nfinding a good size for batch #1 ...\n");
	for ($PHPB_per1N = 1; $PHPB_per1N < $PHPB_perN_practicalLimit; $PHPB_per1N*=2){
		$PHPB_1T = microtime(TRUE);
		for ($PHPB_perI = 0; $PHPB_perI < $PHPB_per1N; $PHPB_perI++){
			$PHPB_emptyFxn();}
		$PHPB_2T = microtime(TRUE);
		$PHPB_emptyBatchT = $PHPB_2T*1000000 - $PHPB_1T*1000000;
		
		$PHPB_1T = microtime(TRUE);
		for ($PHPB_perI = 0; $PHPB_perI < $PHPB_per1N; $PHPB_perI++){
			$PHPB_1Fxn();}
		$PHPB_2T = microtime(TRUE);
		$PHPB_1BatchT = $PHPB_2T*1000000 - $PHPB_1T*1000000;
		
		$PHPB_echo("w/ batch #1 size:".$PHPB_per1N." | fxn #1 batch time:".$PHPB_1BatchT." - empty fxn batch time:".$PHPB_emptyBatchT." (=".($PHPB_1BatchT-$PHPB_emptyBatchT).") ≥? minimum batch tolerance time:".$PHPB_batchMinT."\n");
		if ($PHPB_1BatchT - $PHPB_emptyBatchT >= $PHPB_batchMinT){break;}}
	$PHPB_echo("batch #1 size : ".$PHPB_per1N."\n");
	
	// calculate how large a 2Fxn batch needs to be
	$PHPB_echo("\nfinding a good size for batch #2 ...\n");
	for ($PHPB_per2N = 1; $PHPB_per2N < $PHPB_perN_practicalLimit; $PHPB_per2N*=2){
		$PHPB_1T = microtime(TRUE);
		for ($PHPB_perI = 0; $PHPB_perI < $PHPB_per2N; $PHPB_perI++){
			$PHPB_emptyFxn();}
		$PHPB_2T = microtime(TRUE);
		$PHPB_emptyBatchT = $PHPB_2T*1000000 - $PHPB_1T*1000000;
		
		$PHPB_1T = microtime(TRUE);
		for ($PHPB_perI = 0; $PHPB_perI < $PHPB_per2N; $PHPB_perI++){
			$PHPB_2Fxn();}
		$PHPB_2T = microtime(TRUE);
		$PHPB_2BatchT = $PHPB_2T*1000000 - $PHPB_1T*1000000;
		
		$PHPB_echo("w/ batch #2 size:".$PHPB_per2N." | fxn #2 batch time:".$PHPB_2BatchT." - empty fxn batch time:".$PHPB_emptyBatchT." (=".($PHPB_2BatchT-$PHPB_emptyBatchT).") ≥? minimum batch tolerance time:".$PHPB_batchMinT."\n");
		if ($PHPB_2BatchT - $PHPB_emptyBatchT >= $PHPB_batchMinT){break;}}
	$PHPB_echo("batch #2 size : ".$PHPB_per2N."\n");
	
	// test empty execution times, filling $PHPB_sampleEmpty1A and $PHPB_sampleEmpty2A
	$PHPB_echo("\ntesting empty functions in order to discover overhead time ...\n");
	for ($PHPB_overallI = 0; $PHPB_overallI < $PHPB_overallN; $PHPB_overallI++){
		$PHPB_1T = microtime(TRUE);
		for ($PHPB_perI = 0; $PHPB_perI < $PHPB_per1N; $PHPB_perI++){
			$PHPB_emptyFxn();}
		$PHPB_2T = microtime(TRUE);
		$PHPB_sampleEmpty1A[] = ($PHPB_2T*1000000 - $PHPB_1T*1000000);
		
		$PHPB_1T = microtime(TRUE);
		for ($PHPB_perI = 0; $PHPB_perI < $PHPB_per2N; $PHPB_perI++){
			$PHPB_emptyFxn();}
		$PHPB_2T = microtime(TRUE);
		$PHPB_sampleEmpty2A[] = ($PHPB_2T*1000000 - $PHPB_1T*1000000);}
	$PHPB_echo("slot #1 overhead time:".number_format(min($PHPB_sampleEmpty1A)/$PHPB_per1N,3)."µs\n");
	$PHPB_echo("slot #2 overhead time:".number_format(min($PHPB_sampleEmpty2A)/$PHPB_per2N,3)."µs\n");
	
	
	
	
	// TEST RUNNING ----------------------------------------------------------------
	
	// intertwine calls, run many times
	$PHPB_echo("\ntesting functions ...\n");
	for ($PHPB_overallI = 0; $PHPB_overallI < $PHPB_overallN; $PHPB_overallI+=0.5){
		if (floor($PHPB_overallI) === $PHPB_overallI){
			$PHPB_1T = microtime(TRUE);
			for ($PHPB_perI = 0; $PHPB_perI < $PHPB_per1N; $PHPB_perI++){
				$PHPB_1Fxn();}
			$PHPB_2T = microtime(TRUE);
			$PHPB_sample1A[] = ($PHPB_2T*1000000 - $PHPB_1T*1000000);}
		else{
			$PHPB_1T = microtime(TRUE);
			for ($PHPB_perI = 0; $PHPB_perI < $PHPB_per2N; $PHPB_perI++){
				$PHPB_2Fxn();}
			$PHPB_2T = microtime(TRUE);
			$PHPB_sample2A[] = ($PHPB_2T*1000000 - $PHPB_1T*1000000);}}
	
	// reported batch time = fastest batch time - fastest empty batch time ; result must be ≥ 0
	$PHPB_minBatch1T = max(0,min($PHPB_sample1A) - min($PHPB_sampleEmpty1A)); // empty function could run in less time than empty
	$PHPB_minBatch2T = max(0,min($PHPB_sample2A) - min($PHPB_sampleEmpty2A)); // empty function could run in less time than empty
	
	// reported individual run time = reported batch time / batch size
	$PHPB_minRun1T = $PHPB_minBatch1T / $PHPB_per1N;
	$PHPB_minRun2T = $PHPB_minBatch2T / $PHPB_per2N;
	
	// print some information about the results
	$PHPB_display("FXN #1",$PHPB_minRun1T);$PHPB_echo(", adjusted to eliminate testing overhead\n");
	$PHPB_display("FXN #2",$PHPB_minRun2T);$PHPB_echo(", adjusted to eliminate testing overhead\n");
	
	$PHPB_echo("\nRESULT\n");
	if ($PHPB_minRun1T === 0 || $PHPB_minRun2T === 0){
		$PHPB_echo("FXN #1 and FXN #2 are incomparable (at least one was instantaneous).\n");}
	else if (round($PHPB_minRun2T / $PHPB_minRun1T,1) == 1){
		$PHPB_echo("FXN #1 and FXN #2 are about the same speed.\n");}
	else if ($PHPB_minRun2T > $PHPB_minRun1T){
		$PHPB_echo("FXN #1 is roughly ".round($PHPB_minRun2T / $PHPB_minRun1T,1)."x faster.\n");}
	else{
		$PHPB_echo("FXN #2 is roughly ".round($PHPB_minRun1T / $PHPB_minRun2T,1)."x faster.\n");}
	
	
	
	
	return [
		"fxn1MicrosecondN"=>$PHPB_minRun1T,
		"fxn2MicrosecondN"=>$PHPB_minRun2T,];
}
?>