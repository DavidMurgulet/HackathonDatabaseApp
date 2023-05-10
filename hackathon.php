<!-- CODE REFERENCES: 
  Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22) -->
  <?php
    if (connectToDB()) {
        global $db_conn;

        $prize_names = executePlainSQL("SELECT DISTINCT P_NAME FROM PRIZE_WIN");
    }
  ?>


  <html>
    <head>
        <style>
            .base {
                background-image: url("https://rare-gallery.com/mocahbig/1361456-Abstract-Blue-8k-Ultra-HD-Wallpaper.jpg");
                background-size: cover; 
                height: 100%
            }

            .content {
                border-radius: 25px;
                background: white;
                padding: 20px;
                opacity: 0.9;
                font-family: monospace;
                margin: 20px;
            }

            .title {
                border-radius: 25px;
                background: white;
                padding: 5px;
                opacity: 0.9;
                font-family: monospace;
                margin: 20px;
                text-align: center; 
            }

            .dbresults, .dbresults td, .dbresults th {
                border: 1px solid black;
                border-collapse: collapse;
                padding: 8px; 
            }

            .back-button {
                background-color: #1412a4;
                padding: 2px 12px;
                text-align: center;
                border-radius: 12px;
                text-decoration: none;
                display: inline-block;
                font-size: 14px;
                margin: 10px;
                font-family: monospace;
                top: 20px;
                box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
            }

            a {
                color: white;
                text-decoration: none !important;
            }

            a:hover {
                color:#8142d2; 
                text-decoration:none !important; 
                cursor:pointer;  
            }

        </style>
        <title>Hackathon Info</title>
    </head>
    <body class = "base">

    <div >
        <button class = "back-button"> <a href="index.php">Back to Homepage</a></button> 
    </div>

    <div class = "title">
            <h1>Hackathon Information</h1>
    </div>

    <div class = "content">
        <h2>Winning Projects</h2>
        
        <form method="POST" action="hackathon.php">
            <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
            <!-- <input type="hidden" id="resetTablesRequest" name="resetTablesRequest"> -->
            
            <input type="hidden" id ="prizeRequest" name='prizeRequest'>
            <label for="pname">Select a Prize:</label><br><br>
            <input type="text" name='pname' list='pname'>
            <datalist id='pname'>
                <?php 
                    while ($row = OCI_Fetch_Array($prize_names, OCI_BOTH)) {
                        echo "<option value='" . $row['P_NAME'] . "' </option>";
                    }
                ?>
            </datalist>
            <br><br>
            <p><input type="submit" value="View Winner" name="winningProjects"></p>
        </form>

        <hr />

        <h2>Remove a submission</h2>
        <form method="POST" action="hackathon.php">
            <input type="hidden" id="invalidSubmissionRequest" name="invalidSubmissionRequest">
            TNUM: <input type="text" name="tnum"> <br /><br />
            Hackathon: <input type="text" name="hName"> <br /><br />
            Year: <input type="text" name="hYear"> <br /><br /> 
            <p><input type="submit" value="Remove" name="removeInvalid"></p>
        </form>

        <hr />

        <h2>Hackathons with multiple hostings</h2>
        <form method="POST" action="hackathon.php">
            <p>View hackathons with 
                <select id="hostAmount" name = "hostAmount">
                    <option value =1>1</option>
                    <option value =2>2</option>
                    <option value =3>3</option>
                    <option value =4>4</option>
                </select>
                hostings</p>

            <input type="hidden" id="multipleHackathonRequest" name="multipleHackathonRequest">
            <p><input type="submit" value="View" name="multipleHackathonNames"></p>
        </form>

        <hr />

        <h2>Hackers in All Hackathons</h2>
        <p>Find names of hackers that have participated in every iteration of the selected hackathon</p>
        <form method="GET" action="hackathon.php">
            <input type="hidden" id="allHackathonsRequest" name="allHackathonsRequest">
            <?php
                $query = "SELECT DISTINCT name FROM Hackathon";
                $res = executePlainSQL($query);
                echo "<select name = 'hackathon'>";
                while (($row = OCI_Fetch_Array($res, OCI_BOTH))) {
                    echo "<option value = '{$row[0]}'>{$row[0]}</option>";
                }
                echo "<option value = 'all'>All Hackathons</option>";
                echo "</select>";
            ?>
            <br>
            <br>
            <p><input type="submit" value="View" name="hackerAll"></p>
        </form>

        <hr />

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_jnguyen9", "a20629010", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }


        function handleWinningProjects() {
            global $db_conn;

            $pname = $_POST['pname'];
            // echo $pname;
            

            $winner = executePlainSQL("SELECT t.project_name, p.p_name, 
                    p.amount FROM prize_win p, Team_Submits_project t WHERE p.tnum = t.tnum AND p.p_name = '". $pname ."'");
        
            // $numRows = oci_fetch_all($winner, $rows, null, null, OCI_FETCHSTATEMENT_BY_ROW);
            // echo "Number of rows returned by query: ".$numRows."<br>";

            
            echo "<br>Winner of $pname Prize:<br><br>";

            // Table headers: 
            echo '<table class="dbresults">';
            echo '<tr>';
            echo '<th>PROJECT</th>';
            echo '<th>PRIZE</th>';
            echo '<th>AMOUNT</th>';
            echo '</tr>';

            while ($row = OCI_Fetch_Array($winner, OCI_BOTH)) {
                // table input from query result 
                echo '<tr>';
                echo '<td>' . $row["PROJECT_NAME"] . '</td>';
                echo '<td>' . $row["P_NAME"] . '</td>';
                echo '<td>' . $row["AMOUNT"] . '</td>';
                echo '</tr>'; //or just use "echo $row[0]"
            }
 
            echo "</table>";
            
        }


        function handleInvalidSubmission() {
            global $db_conn;

            $tnum = $_POST['tnum'];
            $name = $_POST['hName'];
            $year = $_POST['hYear'];

            $checkFields = executePlainSQL("SELECT * FROM team_submits_project WHERE tnum = ' ". $tnum ." ' AND hackathon_name = '$name' AND hackathon_yr = ' ". $year ." ' ");

            if ($row = OCI_Fetch_Array($checkFields, OCI_BOTH)) {
                executePlainSQL("DELETE FROM team_submits_project WHERE tnum = ' ". $tnum ." ' AND hackathon_name = '$name' AND hackathon_yr = ' ". $year ." '");
                OCICommit($db_conn);

                $tableNoRemoved = executePlainSQL("SELECT tnum, hackathon_name, hackathon_yr, submission_time, project_name FROM team_submits_project");                
                echo "Submission with TNUM = $tnum succesfully removed from Hackathon with Name = $name and Year = $year.";
              
                echo '</tr>';
                echo '</tr>';

                echo "<br>Project Submissions:<br><br>";

                echo '<table class="dbresults">';
                echo '<tr>';
                echo '<th>TNUM</th>';
                echo '<th>HACKATHON NAME</th>';
                echo '<th>YEAR</th>';
                echo '<th>SUBMISSION TIME</th>';
                echo '<th>PROJECT NAME</th>';
                echo '</tr>';
    
                while ($row = OCI_Fetch_Array($tableNoRemoved, OCI_BOTH)) {
                    echo '<tr>';
                    echo '<td>' . $row["TNUM"] . '</td>';
                    echo '<td>' . $row["HACKATHON_NAME"] . '</td>';
                    echo '<td>' . $row["HACKATHON_YR"] . '</td>';
                    echo '<td>' . $row["SUBMISSION_TIME"] . '</td>';
                    echo '<td>' . $row["PROJECT_NAME"] . '</td>';
                    echo '</tr>'; 
                }
     
                echo "</table>";
            } else {
                echo "No submission found with TNUM = $tnum under the hackathon: $name, in $year.";
            }
        }

        function handleMultipleHackathons() {
            global $db_conn;

            $numHostings = $_POST['hostAmount'];

            $multipleHackathons = executePlainSQL("SELECT DISTINCT h.name, count(*) FROM hackathon h GROUP BY h.name HAVING count(*) >= $numHostings");
       	    echo "<br>Hackathons with $numHostings or more hostings: <br>";

            echo '<table class="dbresults">';
            echo '<tr>';
            echo '<th>HACKATHON NAME</th>';
            echo '<th>COUNT</th>';
            echo '</tr>';
 
            while ($row = OCI_Fetch_Array($multipleHackathons, OCI_BOTH)) {
                echo '<tr>';
                echo '<td>' . $row["NAME"] . '</td>';
                echo '<td>' . $row["COUNT(*)"] . '</td>';
                echo '</tr>';
            }

            echo "</table>";
        }

        function handleAllHackathons() {
            global $db_conn;

            $hackathon = $_GET['hackathon'];

            if ($hackathon == 'all') {
            
                echo "<br>Hackers who have participated in every hackathon:<br><br>";

                $hackers = executePlainSQL("SELECT h.hid, h.name 
                FROM Hacker h
                WHERE NOT EXISTS 
                    ((SELECT name, year 
                    FROM Hackathon ) 
                    MINUS 
                    (SELECT hackathon_name, hackathon_yr 
                    FROM Participates p 
                    WHERE p.hid = h.hid))");
            } else {

                echo "<br>Hackers who have participated in every iteration of $hackathon:<br>";

                $hackers = executePlainSQL("SELECT h.hid, h.name 
                FROM Hacker h
                WHERE NOT EXISTS 
                    ((SELECT name, year 
                    FROM Hackathon 
                    WHERE name = '$hackathon') 
                    MINUS 
                    (SELECT hackathon_name, hackathon_yr 
                    FROM Participates p 
                    WHERE p.hid = h.hid))");
            }

                            
            echo '<table class="dbresults">';
            echo '<tr>';
            echo '<th>HID</th>';
            echo '<th>NAME</th>';
            echo '</tr>';

            while ($row = OCI_Fetch_Array($hackers, OCI_BOTH)) {
                // table input from query result 
                echo '<tr>';
                echo '<td>' . $row[0] . '</td>';
                echo '<td>' . $row[1] . '</td>';
                echo '</tr>'; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('removeInvalid', $_POST)) {
                    handleInvalidSubmission();
                } else if (array_key_exists('winningProjects', $_POST)) {
                    handleWinningProjects();
                } else if (array_key_exists('multipleHackathonNames', $_POST)) {
                    handleMultipleHackathons();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            //print_r($_REQUEST); // to see what is executed 
            if (connectToDB()) {
                if (array_key_exists('hackerAll', $_GET)) {
                    handleAllHackathons();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['removeInvalid'])||isset($_POST['winningProjects']) || isset($_POST['multipleHackathonNames'])) {
            handlePOSTRequest();
        } else if (isset($_GET['hackerAll'])) {
            handleGETRequest();
        }


		?>
        </div>
	</body>
</html>
