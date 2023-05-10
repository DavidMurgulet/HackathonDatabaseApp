<!-- CODE REFERENCES: 
  Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22) -->

  <?php
    if (connectToDB()) {
        global $db_conn;
        session_start();
        $vids = executePlainSQL("SELECT vid FROM Mentor UNION SELECT vid FROM Judge");
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

    <title>Volunteer Info</title>
    </head>

    <body class = "base">

    <div >
        <button class = "back-button"> <a href="index.php">Back to Homepage</a></button> 
    </div>
        
    <div class = "title">
            <h1>Volunteer Information</h1>
    </div>
    
    <div class = "content">
        <h2>Volunteer Hackathons</h2>
        <p>Find information about volunteers</p>
        <form method="GET" action="volunteer.php">
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <label for="vids">VID:</label>
            <input type="text" name='vid' list='vid'>
            <datalist id='vid'>
                <?php 
                    while ($row = OCI_Fetch_Array($vids, OCI_BOTH)) {
                        echo "<option value={$row[0]}> </option>";
                    }
                ?>
            </datalist>
            <label for="hackathon">Hackathon:</label>
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
            <label for="year">Year:</label>
            <?php 
                $query = "SELECT DISTINCT year FROM Hackathon ORDER BY year";
                $res = executePlainSQL($query);
                echo "<select name = 'year'>";
                while (($row = OCI_Fetch_Array($res, OCI_BOTH))) {
                    echo "<option value = {$row[0]}>{$row[0]}</option>";
                }
                echo "<option value = 'all'>All Years</option>";
                echo "</select>";
            ?>
            <p><input type="submit" value="View" name="hackathons"></p>
        </form>

        <hr />

        <h2>Average Scores</h2>
        <form method="POST" action="volunteer.php">
        <p>View the average score given by Judge volunteers who have ranked at least 
            <select id="numProjects" name = "numProjects">
                <option value=1>1</option>
                <option value=2>2</option>
                <option value=3>3</option>
                <option value=4>4</option>
                <option value=5>5</option>
                <option value=6>6</option>
                <option value=7>7</option>
            </select> 
            project(s)</p>

            <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <p><input type="submit" value="View" name="avgScore"></p>
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

        function handleAvgScore() {
            global $db_conn;


            // $result = executePlainSQL("SELECT Count(*) FROM demoTable");

            // if (($row = oci_fetch_row($result)) != false) {
            //     echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
            // }
            $numProj = $_POST['numProjects'];
            $avgs = executePlainSQL("SELECT vid, avg(score) FROM Rank Group by vid HAVING count(*) >= $numProj");
            
            //$numRows = oci_fetch_all($avgs, $rows, null, null, OCI_FETCHSTATEMENT_BY_ROW);
            //echo "Number of rows returned by query: ".$numRows."<br>";

            echo "<br>Average Score For Judge Volunteers Who Have Ranked at Least $numProj Project(s):<br><br>";

            // Table headers: 
            echo '<table class="dbresults">';
            echo '<tr>';
            echo '<th>VID</th>';
            echo '<th>AVERAGE SCORE</th>';
            echo '</tr>';

            while ($row = OCI_Fetch_Array($avgs, OCI_BOTH)) {
                // table input from query result 
                echo '<tr>';
                echo '<td>' . $row["VID"] . '</td>';
                echo '<td>' . $row["AVG(SCORE)"] . '</td>';
                echo '</tr>'; //or just use "echo $row[0]"
            }
 
            echo "</table>";
        }

        function handleVolunteerHackathons() {
            global $db_conn;

            $vid = $_GET['vid'];
            $hackathon = $_GET['hackathon'];
            $year = $_GET['year'];

            if (!is_numeric($vid) && $vid) {
                echo "<br>VID $vid is not a number!<br>";
                return;
            };

            $whereJObj = constructJudgeWhere($vid, $hackathon, $year);
            $whereMObj = constructMentorWhere($vid, $hackathon, $year);

            $hackathons = executePlainSQL("SELECT  j.vid as VID, hackathon_name, Hackathon_yr 
                                     FROM J_Volunteers jv, Judge j
                                     WHERE jv.vid = j.vid $whereJObj
                                     UNION
                                     SELECT m.vid as VID, hackathon_name, Hackathon_yr
                                     FROM M_Volunteers mv, Mentor m
                                     WHERE mv.vid = m.vid $whereMObj");

            $numRows = oci_fetch_all($hackathons, $rows, null, null, OCI_FETCHSTATEMENT_BY_ROW);
            
            if (!$numRows) {
                echo "<br>No Volunteers matched those filters<br>";
                return;
            }


            //I have no idea why I have to do this twice, all I know is it breaks if the query isn't run again
            $hackathons = executePlainSQL("SELECT j.vid as VID, j.name as NAME, hackathon_name, Hackathon_yr 
                                     FROM J_Volunteers jv, Judge j
                                     WHERE jv.vid = j.vid $whereJObj
                                     UNION
                                     SELECT m.vid as VID, m.name as NAME, hackathon_name, Hackathon_yr
                                     FROM M_Volunteers mv, Mentor m
                                     WHERE mv.vid = m.vid $whereMObj");
            


        
            echo "<br>Hackathons that volunteer $vid has participated in:<br>";


            // Table headers: 
            echo '<table class="dbresults">';
            echo '<tr>';
            echo '<th>VID</th>';
            echo '<th>NAME</th>';
            echo '<th>HACKATHON NAME</th>';
            echo '<th>HACKATHON YEAR</th>';
            echo '</tr>';

            while ($row = OCI_Fetch_Array($hackathons, OCI_BOTH)) {
                // table input from query result 
                echo '<tr>';
                echo '<td>' . $row["VID"] . '</td>';
                echo '<td>' . $row["NAME"] . '</td>';
                echo '<td>' . $row["HACKATHON_NAME"] . '</td>';
                echo '<td>' . $row["HACKATHON_YR"] . '</td>';
                echo '</tr>'; //or just use "echo $row[0]"
            }
    
            echo "</table>";
        }

        function constructJudgeWhere($vid, $hackathon, $year) {
            $finalString = '';
            $hackathon = trim($hackathon);

            if ($vid || $vid == 0) {
                $finalString .= " AND jv.vid = $vid";
            } 
            if ($hackathon != 'all') {
                $finalString .= " AND hackathon_name = '$hackathon'";
            } 
            if ($year != 'all') {
                $finalString .= " AND hackathon_yr = $year";
            }
            return $finalString;
        }

        function constructMentorWhere($vid, $hackathon, $year) {
            $finalString = '';
            $hackathon = trim($hackathon);

            if ($vid || $vid == 0) {
                $finalString .= " AND mv.vid = $vid";
            } 
            if ($hackathon != 'all') {
                $finalString .= " AND hackathon_name = '$hackathon'";
            } 
            if ($year != 'all') {
                $finalString .= " AND hackathon_yr = $year";
            }
            return $finalString;
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                } else if (array_key_exists('avgScore', $_POST)) {
                    handleAvgScore();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('hackathons', $_GET)) {
                    handleVolunteerHackathons();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['avgScore'])) {
            handlePOSTRequest();
        } else if (isset($_GET['hackathons'])) {
            handleGETRequest();
        }

		?>
        </div>
	</body>
</html>