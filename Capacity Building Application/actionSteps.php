<?php 
session_start();

//hyperlink function
function hyperlink($input) {
    if (strpos($input, "http") !== false) {
        $words = preg_split('/\s+/', $input);
        foreach ($words as &$word) {
            if (strpos($word, "http") !== false) {
                $word = "<a href='$word'>$word</a>";
            }
        }
        return implode(" ",$words);
    }
    return ($input);
}

$show = "";
$collapse = $_SESSION['collapse'];
$collapse2 = $_SESSION['collapse2'];
$val = 1;
$val2 = 1;
// Populate page with responses and recommendations
$npName = $_SESSION['nonprofit'];
$surveyFile = $npName . ".csv";
$saveFile = $npName . ".txt";
$lineNum = 1;
//Get survey responses
if ($handle = opendir('surveys/')) {
    while (false !== ($file = readdir($handle))) {
        if ('.' === $file) continue;
        if ('..' === $file) continue;
        //do something with file
        $fn = fopen("surveys/$file","r");
        //iterate through every line
        while(! feof($fn))  {
          $line = str_replace('"', "", fgets($fn));
          $list = preg_split ("/\,/", $line); 
          if ($lineNum == 3) {
            $emailAdd = $list[11];
            $fname = $list[9];
            $lname = $list[10];
            //Parse through responses
            $responseNum = 1;
            for ($i =13; $i<count($list); $i++) {
                $wordsInLine = explode(' ', $list[$i]);
                $lastWord = array_pop($wordsInLine);
                $secondToLastWord = array_pop($wordsInLine);
                //Add a space after Build, Thrive, and Sustain
                $lineChars = str_split($list[$i]);
                $word ="";
                for ($x=0; $x<7; $x++) {
                    $word = $word.$lineChars[$x];
                    if ($word == "Build") {
                        array_splice($lineChars, $x+1, 0, array("— "));
                        $list[$i] = implode($lineChars);
                    }
                    elseif ($word == "Thrive") {
                        array_splice($lineChars, $x+1, 0, array("— "));
                        $list[$i] = implode($lineChars);
                    }
                    elseif ($word == "Sustain") {
                        array_splice($lineChars, $x+1, 0, array("— "));
                        $list[$i] = implode($lineChars);
                    }
                }

                //Concatinate responses with commas in them
                if ($secondToLastWord.$lastWord == "majorgiving") {
                    ${"response" . $responseNum} = $list[$i].$list[$i+1];
                    $i = $i+1;
                }
                elseif ($secondToLastWord.$lastWord == "offiling") {
                    ${"response" . $responseNum} = $list[$i].$list[$i+1].$list[$i+2]; 
                    $i=$i+2;
                }
                elseif ($lastWord == "sharing" || $lastWord == "time" || $lastWord == "policies" || $lastWord == "equity" || $lastWord == "bookkeeping" || $lastWord == "identify" || $lastWord == "giving" || $lastWord == "plan" || $lastWord == "compliance" || $lastWord == "strategies" || $lastWord == "members") {
                    ${"response" . $responseNum} = $list[$i].$list[$i+1].$list[$i+2]; 
                    $i=$i+2;
                }
                elseif($lastWord == "remain") {
                    ${"response" . $responseNum} = $list[$i].$list[$i+1];
                    $i = $i+1;
                }
                elseif($lastWord == "active" || $lastWord == "reports" || $lastWord == "filing") {
                    ${"response" . $responseNum} = $list[$i].$list[$i+1].$list[$i+2]. $list[$i+3]. $list[$i+4];
                    $i = $i+4;
                }
                else {
                    ${"response" . $responseNum} = $list[$i];
                }
                $responseNum++;
            }
          }
          $lineNum = $lineNum+1;
        }
        fclose($fn);
    }
}

$rlines = array();
//Get saved recommendation data 
if (file_exists("recommendationSaveData/$saveFile")) {
    $fn = fopen("recommendationSaveData/$saveFile","r");
    $recNum = 1;
    //iterate through every line
    $rGroup = "";
    while(! feof($fn))  {
        $line = strtok(fgets($fn), ';');
        $line = hyperlink($line);
        //This line keeps the hyperlink
        $rName = preg_split ("/\:/", $line, 2); 
        //keep the lines separate to write to the file later
        $rlines[] = $line;
        //If the line is the date line
        if(strpos($line, "date=") !== false ) {

        }
        //If the line is a data line
        elseif ($line !== "next") {
            $offset="";
            if(isset($rName[1])) {
                $offset = $rName[1];
            }
            $rGroup = $rGroup."<strong>". $rName[0]. ": </strong>" . $offset ."<br> ";
        }
        //if the line is a next line
        elseif ($line == "next") {
            ${"recom".$recNum} = $rGroup;
            $recNum = $recNum +1;
            $rGroup = "";
        }
    }
}
else {
    $recom1 = "";
    $recom2 = "";
    $recom3 = "";
    $recom4 = "";
    $recom5 = "";
    $recom6 = "";
    $recom7 = "";
    $recom8 = "";
    $recom9 = "";
    $recom10 = "";
    $recom11 = "";
    $recom12 = "";
}

$glines = array();
//Get saved goals data 
if (file_exists("goalsSaveData/$saveFile")) {
    $fn = fopen("goalsSaveData/$saveFile","r");
    $recNum = 1;
    //iterate through every line
    $rGroup = "";
    while(! feof($fn))  {
        $line = strtok(fgets($fn), ';');
        $line = hyperlink($line);
        //This line keeps the hyperlink
        $rName = preg_split ("/\:/", $line, 2); 
        //keep the lines separate to write to the file later
        $glines[] = $line;
        //If the line is the date line
        if(strpos($line, "date=") !== false ) {

        }
        //If the line is a data line
        elseif ($line !== "next") {
            $offset="";
            if(isset($rName[1])) {
                $offset = $rName[1];
            }
            $rGroup = $rGroup."<strong>". $rName[0]. ": </strong>" . $offset ."<br> ";
        }
        //if the line is a next line
        elseif ($line == "next") {
            ${"goal".$recNum} = $rGroup;
            $recNum = $recNum +1;
            $rGroup = "";
        }
    }
}
else {
    $goal1 = "";
    $goal2 = "";
    $goal3 = "";
    $goal4 = "";
    $goal5 = "";
    $goal6 = "";
    $goal7 = "";
    $goal8 = "";
    $goal9 = "";
    $goal10 = "";
    $goal11 = "";
    $goal12 = "";
}


$lines = array();
//Get saved action step data 
if (file_exists("actionStepsSaveData/$saveFile")) {
    $fn = fopen("actionStepsSaveData/$saveFile","r");
    $recNum = 1;
    //iterate through every line
    $rGroup = "";
    while(! feof($fn))  {
        $line = strtok(fgets($fn), ';');
        $line = hyperlink($line);
        //This line keeps the hyperlink
        $rName = preg_split ("/\:/", $line, 2); 
        //keep the lines separate to write to the file later
        $lines[] = $line;
        //If the line is the date line
        if(strpos($line, "date=") !== false ) {

        }
        //If the line is a data line
        elseif ($line !== "next") {
            $offset="";
            if(isset($rName[1])) {
                $offset = $rName[1];
            }
            //hyperlink goes away here
            $rGroup = $rGroup."<strong>". $rName[0]. ": </strong>" . $offset ."<br> ";
        }
        //if the line is a next line
        elseif ($line == "next") {
            ${"rec".$recNum} = $rGroup;
            $recNum = $recNum +1;
            $rGroup = "";
        }
    }
}

//When save is clicked
    if(isset($_POST['save'])) {
        if (empty($_POST['name'])) {
            $_SESSION['show'] ="**Please type your name in the box to save your recommendations";
            //save values if no name is there
            $r1= $_POST['r1'];
            $r2= $_POST['r2'];
            $r3= $_POST['r3'];
            $r4= $_POST['r4'];
            $r5= $_POST['r5'];
            $r6= $_POST['r6'];
            $r7= $_POST['r7'];
            $r8= $_POST['r8'];
            $r9= $_POST['r9'];
            $r10= $_POST['r10'];
            $r11= $_POST['r11'];
            $r12= $_POST['r12'];
        }
        //Append new rec to the static rec list on the right side
        else {
            $_SESSION['show'] ='';
            $name =$_POST['name'];
            //initialize recommendations if there are none
            if (!isset($rec1)) {
                $rec1='';
            }
            if (!isset($rec2)) {
                $rec2='';
            }
            if (!isset($rec3)) {
                $rec3='';
            }
            if (!isset($rec4)) {
                $rec4='';
            }
            if (!isset($rec5)) {
                $rec5='';
            }
            if (!isset($rec6)) {
                $rec6='';
            }
            if (!isset($rec7)) {
                $rec7='';
            }
            if (!isset($rec8)) {
                $rec8='';
            }
            if (!isset($rec9)) {
                $rec9='';
            }
            if (!isset($rec10)) {
                $rec10='';
            }
            if (!isset($rec11)) {
                $rec11='';
            }
            if (!isset($rec12)) {
                $rec12='';
            }


            if (!empty($_POST['r1'])) {
                $rec1 = $rec1 . "<strong> $name: </strong>". hyperlink($_POST['r1']);
            }
            if (!empty($_POST['r2'])) {
                $rec2 = $rec2 . "<strong> $name: </strong>". hyperlink($_POST['r2']);
            }
            if (!empty($_POST['r3'])) {
                $rec3 = $rec3 . "<strong> $name: </strong>". hyperlink( $_POST['r3']);
            }
            if (!empty($_POST['r4'])) {
                $rec4 = $rec4 . "<strong> $name: </strong>". hyperlink($_POST['r4']);
            }
            if (!empty($_POST['r5'])) {
                $rec5 = $rec5 . "<strong> $name: </strong>". hyperlink($_POST['r5']);
            }
            if (!empty($_POST['r6'])) {
                $rec6 = $rec6 . "<strong> $name: </strong>". hyperlink($_POST['r6']);
            }
            if (!empty($_POST['r7'])) {
                $rec7 = $rec7 . "<strong> $name: </strong>". hyperlink($_POST['r7']);
            }
            if (!empty($_POST['r8'])) {
                $rec8 = $rec8 . "<strong> $name: </strong>". hyperlink($_POST['r8']);
            }
            if (!empty($_POST['r9'])) {
                $rec9 = $rec9 . "<strong> $name: </strong>". hyperlink($_POST['r9']);
            }
            if (!empty($_POST['r10'])) {
                $rec10 = $rec10 . "<strong> $name: </strong>". hyperlink($_POST['r10']);
            }
            if (!empty($_POST['r11'])) {
                $rec11 = $rec11 . "<strong> $name: </strong>". hyperlink($_POST['r11']);
            }
            if (!empty($_POST['r12'])) {
                $rec12 = $rec12 . "<strong> $name: </strong>". hyperlink($_POST['r12']);
            }
        }


        //create or open save file when save is clicked only if Name is set
        if(isset($name)) {
            $contents="";
            $recNum = 1;
            if (file_exists("actionStepsSaveData/$npName.txt")) {
                $newFile = fopen("actionStepsSaveData/$npName.txt", "w");
                foreach($lines as $line) {
                    //update the date if we are on the first line
                    if($line == $lines[0]) {
                        $line = "date=".date('m/d/y') . ";";
                    }
                    if ($line == "next") {
                        $recId ="r" . $recNum;
                        if (!empty($_POST["$recId"])) {
                            $contents = $contents . $name . ": ". $_POST["$recId"] .";" .PHP_EOL ;
                        }
                        $recNum = $recNum+1;
                    }
                    $contents = $contents. $line . ";" .PHP_EOL;
                }
                //write or overwrite file
            }
            //If the file doesn't exist make a new one
            else {
                $newFile = fopen("actionStepsSaveData/$npName.txt", "w");
                $contents = "date=".date('m/d/y') . ";" . PHP_EOL;
                for ($x=1; $x<=12; $x++) {
                    $recId="r$x";
                    if (!empty($_POST["$recId"])) {
                        $contents= $contents . $name . ": ". $_POST["$recId"].";" .PHP_EOL . "next;" .PHP_EOL;
                    }
                    else {
                        $contents= $contents . "next;" .PHP_EOL;
                    }
                }
    
            }
            fwrite($newFile, $contents);
        }
        header('Location: actionSteps.php');
    }

    if(isset($_POST['collapseBtn']) && $_POST['collapseBtn'] == 1) {
        $collapse = TRUE;
        $_SESSION['collapse'] = TRUE;
        $val = 2;
        $r1= $_POST['r1'];
        $r2= $_POST['r2'];
        $r3= $_POST['r3'];
        $r4= $_POST['r4'];
        $r5= $_POST['r5'];
        $r6= $_POST['r6'];
        $r7= $_POST['r7'];
        $r8= $_POST['r8'];
        $r9= $_POST['r9'];
        $r10= $_POST['r10'];
        $r11= $_POST['r11'];
        $r12= $_POST['r12'];
    }
    elseif(isset($_POST['collapseBtn']) && $_POST['collapseBtn'] == 2) {
        $collapse= FALSE;
        $_SESSION['collapse'] = FALSE;
        $val =1;
        $r1= $_POST['r1'];
        $r2= $_POST['r2'];
        $r3= $_POST['r3'];
        $r4= $_POST['r4'];
        $r5= $_POST['r5'];
        $r6= $_POST['r6'];
        $r7= $_POST['r7'];
        $r8= $_POST['r8'];
        $r9= $_POST['r9'];
        $r10= $_POST['r10'];
        $r11= $_POST['r11'];
        $r12= $_POST['r12'];
    }
    
    if(isset($_POST['collapseBtn2']) && $_POST['collapseBtn2'] == 1) {
        $collapse2 = TRUE;
        $_SESSION['collapse2'] = TRUE;
        $val2 = 2;
        $r1= $_POST['r1'];
        $r2= $_POST['r2'];
        $r3= $_POST['r3'];
        $r4= $_POST['r4'];
        $r5= $_POST['r5'];
        $r6= $_POST['r6'];
        $r7= $_POST['r7'];
        $r8= $_POST['r8'];
        $r9= $_POST['r9'];
        $r10= $_POST['r10'];
        $r11= $_POST['r11'];
        $r12= $_POST['r12'];
    }
    elseif(isset($_POST['collapseBtn2']) && $_POST['collapseBtn2'] == 2) {
        $collapse2= FALSE;
        $_SESSION['collapse2'] = FALSE;
        $val2 =1;
        $r1= $_POST['r1'];
        $r2= $_POST['r2'];
        $r3= $_POST['r3'];
        $r4= $_POST['r4'];
        $r5= $_POST['r5'];
        $r6= $_POST['r6'];
        $r7= $_POST['r7'];
        $r8= $_POST['r8'];
        $r9= $_POST['r9'];
        $r10= $_POST['r10'];
        $r11= $_POST['r11'];
        $r12= $_POST['r12'];
    }
    


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="styles/edit.css">
</head>
<body>
    <img src="images/nccnpLogo.svg" alt="NCCNP Logo" id="nccnp"/>
    <div id="buttons">
    <form method='post' action='actionSteps.php' enctype="multipart/form-data">
    <input type="text" placeholder="*NCCNP Employee's Name" id="name" name = "name">
    <input type='submit' onclick='save();' value='Save' name='save' id='save'>
    <!-- <div id="save" onClick="save()"><p id="saveText">Save</p></div> -->
    <div id="export" onClick="window.print()"><p id="exportText">Export</p></div>
    <br>
    <a href="index.php" id="back">← Back to Surveys</a>
    <a href="goals.php" id="goalArrow">← Back to Goals</a>
    </div> <!-- end buttons div -->
    <?php 
        $show = $_SESSION['show'];
        echo "<h3 id = 'warn'>$show</h3> <br> <br>";
        echo "<h2 id='npName'>$npName </h2>";
        echo "<p id='npData'> Survey filled by: $fname $lname <br> $emailAdd </p>"
    ?>
    <section id="workspace">
        <table id="all">
            <tr>
                <td class="as">
                    <h3>1. Advocacy and Civil Engagement</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"><?= "$response1"?> </p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response2"?></p>
                </td>
                <button <?php if($collapse2 && $collapse) echo "id = 'bothCollapsed1'"; elseif($collapse) echo "id='collapsed1'"; elseif($collapse2) echo "id = 'otherCollapsed1'"; else echo "id='collapse1'";  ?> name = 'collapseBtn' <?php echo "value = '$val'";?>>
                        <?php if($collapse) echo ">"; else echo "<"; ?> </button>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom1</p>";
                    }
                    else {
                        echo "<p>$recom1 </p>"; 
                    }
                    ?>
                </td>
                <button <?php if($collapse2 && $collapse) echo "id = 'bothCollapsed2'";elseif($collapse2) echo "id='collapsed2'"; elseif($collapse) echo "id = 'otherCollapsed2'"; else echo "id='collapse2'";  ?> name = 'collapseBtn2' <?php echo "value = '$val2'";?>>
                        <?php if($collapse2) echo ">"; else echo "<"; ?> </button>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal1</p>";
                    }
                    else {
                        echo "<p>$goal1 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class="recommendations">
                        <?php
                        if (isset($rec1)) {
                            echo "<p> $rec1 </p>"; 
                        }
                         ?>
                    </div>
                    <textarea name = 'r1' placeholder="Type your action steps here..." class="type"> <?php if(isset($r1)) echo "$r1";?></textarea>
                </td>
            </tr>
            <tr>
                <td class="as">
                    <h3>2. Board Governance</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response3"?> </p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response4"?> </p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom2</p>";
                    }
                    else {
                        echo "<p>$recom2 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal2</p>";
                    }
                    else {
                        echo "<p>$goal2 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php
                        if (isset($rec2)) {
                            echo "<p>$rec2 </p>";
                        }    
                        ?>
                    </div>
                    <textarea name = 'r2' placeholder="Type your action steps here..." class="type"><?php if(isset($r2)) echo "$r2";?></textarea>
                </td>
            </tr>  
            <tr>
                <td class="as">
                    <h3>3. Equity, Diversity, and Inclusion</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response5"?></p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response6"?> </p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom3</p>";
                    }
                    else {
                        echo "<p>$recom3 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal3</p>";
                    }
                    else {
                        echo "<p>$goal3 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php
                        if (isset($rec3)) {
                        echo "<p>$rec3 </p>"; 
                        }
                        ?>
                    </div>
                    <textarea name = 'r3' placeholder="Type your action steps here..." class="type"><?php if(isset($r3)) echo "$r3";?></textarea>
                </td>
            </tr>
            <tr>    
                <td class="as">
                    <h3>4. Financial Management</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response7"?> </p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response8"?> </p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom4</p>";
                    }
                    else {
                        echo "<p>$recom4 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal4</p>";
                    }
                    else {
                        echo "<p>$goal4 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php
                        if (isset($rec4)) {
                        echo "<p>$rec4 </p>"; 
                        }
                        ?>
                    </div>
                    <textarea name = 'r4' placeholder="Type your action steps here..." class="type"><?php if(isset($r4)) echo "$r4";?></textarea>
                </td>
            </tr>
            <tr>  
                <td class="as">
                    <h3>5. Fundraising</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response9"?> </p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a">  <?= "$response10"?></p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom5</p>";
                    }
                    else {
                        echo "<p>$recom5 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal5</p>";
                    }
                    else {
                        echo "<p>$goal5 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php
                        if (isset($rec5)) {
                            echo "<p>$rec5 </p>"; 
                        }
                        ?>
                    </div>
                    <textarea name = 'r5' placeholder="Type your action steps here..." class="type"><?php if(isset($r5)) echo "$r5";?> </textarea>
                </td>
            </tr>
            <tr>   
                <td class="as">
                    <h3>6. Human Resources</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response11"?></p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response12"?></p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom6</p>";
                    }
                    else {
                        echo "<p>$recom6 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal6</p>";
                    }
                    else {
                        echo "<p>$goal6 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php
                        if (isset($rec6)) {
                            echo "<p>$rec6 </p>"; 
                        }
                        ?>
                    </div>
                    <textarea name = 'r6' placeholder="Type your action steps here..." class="type"> <?php if(isset($r6)) echo "$r6";?></textarea>
                </td>
            </tr>
            <tr>  
                <td class="as">
                    <h3>7. Information and Technology</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response13"?></p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response14"?></p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom7</p>";
                    }
                    else {
                        echo "<p>$recom7 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal7</p>";
                    }
                    else {
                        echo "<p>$goal7 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php 
                        if (isset($rec7)) {
                            echo "<p>$rec7 </p>"; 
                        }
                        ?>
                    </div>
                    <textarea name = 'r7' placeholder="Type your action steps here..." class="type" ><?php if(isset($r7)) echo "$r7";?></textarea>
                </td>
            </tr>
            <tr>  
                <td class="as">
                    <h3>8. Legal Compliance and Transparency</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response15"?></p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response16"?></p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom8</p>";
                    }
                    else {
                        echo "<p>$recom8 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal8</p>";
                    }
                    else {
                        echo "<p>$goal8 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php
                        if (isset($rec8)) {
                            echo "<p>$rec8 </p>"; 
                        }
                        ?>
                    </div>
                    <textarea name = 'r8' placeholder="Type your action steps here..." class="type"><?php if(isset($r8)) echo "$r8";?></textarea>
                </td>
            </tr>
            <tr>  
                <td class="as">
                    <h3>9. Partnerships and Collaboration</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response17"?></p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response18"?></p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom9</p>";
                    }
                    else {
                        echo "<p>$recom9 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal9</p>";
                    }
                    else {
                        echo "<p>$goal9 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php
                        if (isset($rec9)) {
                            echo "<p>$rec9 </p>"; 
                        }
                        ?>
                    </div>
                    <textarea name = 'r9' placeholder="Type your action steps here..." class="type"><?php if(isset($r9)) echo "$r9";?></textarea>
                </td>
            </tr>
            <tr>  
                <td class="as">
                    <h3>10. Program Design, Management, and Evaluation</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response19"?></p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response20"?></p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom10</p>";
                    }
                    else {
                        echo "<p>$recom10 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal10</p>";
                    }
                    else {
                        echo "<p>$goal10 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php
                        if (isset($rec10)) {
                            echo "<p>$rec10 </p>"; 
                        }
                        ?>
                    </div>
                    <textarea name = 'r10' placeholder="Type your action steps here..." class="type"><?php if(isset($r10)) echo "$r10";?></textarea>
                </td>
            </tr>
            <tr>  
                <td class="as">
                    <h3>11. Strategic Communication</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response21"?></p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response22"?></p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom11</p>";
                    }
                    else {
                        echo "<p>$recom11 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal11</p>";
                    }
                    else {
                        echo "<p>$goal11 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php
                        if (isset($rec11)) {
                            echo "<p>$rec11 </p>"; 
                        }
                        ?>
                    </div>
                    <textarea name = 'r11' placeholder="Type your action steps here..." class="type"><?php if(isset($r11)) echo "$r11";?></textarea>
                </td>
            </tr>
            <tr>
                <td class="as">
                    <h3>12. Strategic Planning</h3>
                    <p class="q">What is the current condition of your nonprofit?</p>
                    <p class="a"> <?= "$response23"?></p>
                    <p class="q">In what condition would you like your nonprofit to be a year from now?</p>
                    <p class="a"> <?= "$response24"?></p>
                </td>
                <td class="as <?php if($collapse) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse) {echo "hide";} ?>">NCCNP Recommendation</h3>
                    <?php 
                    if ($collapse) {
                        echo "<p class= 'hide'> $recom12</p>";
                    }
                    else {
                        echo "<p>$recom12 </p>"; 
                    }
                    ?>
                </td>
                <td class="as <?php if($collapse2) echo "collapsed"; ?>">
                    <h3 class="recTitle <?php if($collapse2) {echo "hide";} ?>">NCCNP Goals</h3>
                    <?php 
                    if ($collapse2) {
                        echo "<p class= 'hide'> $goal12</p>";
                    }
                    else {
                        echo "<p>$goal12 </p>"; 
                    }
                    ?>
                </td>
                <td class="as">
                    <h3>NCCNP Suggested Action Steps</h3>
                    <div class ="recommendations">
                        <?php 
                        if (isset($rec12)) {
                            echo "<p>$rec12 </p>"; 
                        }
                        ?>
                    </div>
                    <textarea name = 'r12' placeholder="Type your action steps here..." class="type"><?php if(isset($r12)) echo "$r12";?></textarea></textarea>
                </td>
            </tr>
        </table>
        </form>
    </section>
</body>
</html>
