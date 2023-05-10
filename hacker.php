<!-- CODE REFERENCES: 
  Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22) -->


  <?php
    if (connectToDB()) {
        global $db_conn;
        session_start();
        $hackers = executePlainSQL("SELECT HID, name as hackerName FROM Hacker");
        $hackathons = executePlainSQL("SELECT DISTINCT name, year FROM Hackathon");
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
        <title>Hacker Page</title>
    </head>

    <body class = "base">

    <div >
        <button class = "back-button"> <a href="index.php">Back to Homepage</a></button> 
    </div>

    <div class = "title">
            <h1>Hacker Information</h1>
    </div>

    <div class = "content">

        <h2>Count Hackers</h2>
            <p>Find the number of hackers that participated in hackathons, either by organization, year, or specific hackathon.</p>
            <form method="GET" action="hacker.php">
                <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
                <label for="groupBy">Group on:</label>
                <select id="groupBy" name = "group">
                    <option value='hackathon_name'>Hackathon Name</option>
                    <option value='Hackathon_yr'>Hackathon Year</option>
                    <option value="hackathon_name, Hackathon_yr">Hackathon Name and Year</option>
                </select>
                <br>
                <br>
                <p><input type="submit" value="View" name="countHackers"></p>
            </form>

        <hr />

        <h2>Submit a new project</h2>
        <form method="POST" action="hacker.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            <div>
                <h3>Specify up to 4 team members: </h3>
                <input type="text" id="teamMember1" name="teamMember1" list = 'hackers'>
                    <datalist id='hackers'>
                        <?php 
                            while ($row = OCI_Fetch_Array($hackers, OCI_BOTH)) {
                                echo "<option value=" . $row['HID'] . ">{$row[0]}, {$row[1]}</option>";
                            }
                        ?>
                    </datalist>
                <input type="text" id="teamMember2" name="teamMember2", list='hackers'>
                <input type="text" id="teamMember3" name="teamMember3", list='hackers'>
                <input type="text" id="teamMember4" name="teamMember4", list='hackers'>
            </div>
            <h3>Name of project: </h3>
                <input type="text" id="projName" name="projName">
            <br>
            <br>
            <h3>Hackathon submitted to: </h3>
            <?php
                echo "<select name = 'hackathonSubmit'>";
                while (($row = OCI_Fetch_Array($hackathons, OCI_BOTH))) {
                    echo "<option value ='" .trim($row[0]) . ', '.$row[1]."'>{$row[0]} {$row[1]}</option>";
                }
                echo "</select>";
            ?>
            <input type="submit" value="Insert" name="insertSubmit"></p>
        </form>

        <hr />

        <h2>Update Hacker Skill Level</h2>

        <form method="POST" action="hacker.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateSkillQueryRequest" name="updateSkillQueryRequest">
            HID: <input type="text" name="hid"> <br /><br />
            <label for="skillLevel">Skill Level:</label>
            <select id="skillLevel" name = "skill">
                <option value='beginner'>Beginner</option>
                <option value='intermediate'>Intermediate</option>
                <option value="professional">Professional</option>
            </select>
             <!-- Skill Level: <input type="text" name="newName"> <br /><br /> -->
            <br>
            <br>
            <input type="submit" value="Update" name="updateSkill"></p>
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

        function handleUpdateSkillRequest() {
            global $db_conn;

            $hid = $_POST['hid'];
            $skill = $_POST['skill'];

            // you need the wrap the old name and new name values with single quotations
            // executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
           
            $checkHid = executePlainSQL("SELECT * FROM hacker WHERE hid = ' ". $hid ." ' ");
           
            if ($row = OCI_Fetch_Array($checkHid, OCI_BOTH)) { // exits hacker with given hid
                executePlainSQL("UPDATE hacker SET skill_level = '". $skill ."' WHERE hid = '". $hid . "' ");
                OCICommit($db_conn);
                echo "Successfully updated hacker with HID = $hid to have $skill skill level!";

                $hackers = executePlainSQL("SELECT hid, skill_level FROM Hacker");

                echo '<table class="dbresults">';
                echo '<tr>';
                echo '<th>Hacker ID</th>';
                echo '<th>Skill Level</th>';
                echo '</tr>';
    
                while ($row = OCI_Fetch_Array($hackers, OCI_BOTH)) {
                    // table input from query result 
                    echo '<tr>';
                    echo '<td>' . $row["HID"] . '</td>';
                    echo '<td>' . $row["SKILL_LEVEL"] . '</td>';
                    echo '</tr>'; //or just use "echo $row[0]"
                }
    
                echo "</table>";
    
            
            } else {
                echo "There is no Hacker with the given HID = $hid";
            }
        }

        function handleInsertRequest() {
            global $db_conn;

            $teamMember1 = $_POST['teamMember1'];
            $teamMember2 = $_POST['teamMember2'];
            $teamMember3 = $_POST['teamMember3'];
            $teamMember4 = $_POST['teamMember4'];
            $projName = $_POST['projName'];
            $hackathon = $_POST['hackathonSubmit'];
            $hackathonName = explode(",", $hackathon)[0];
            $hackathonYear = explode(",", $hackathon)[1];



            if (!ctype_alpha($projName)) {
                echo "<br>Project name must only consist of alphabetical characters<br>";
                return;
            }
            if($teamMember1) {
                $isHacker = executePlainSQL("SELECT * from Hacker where hid = $teamMember1");
                $numRows = oci_fetch_all($isHacker, $rows, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                if (!$numRows) {
                    echo "<br>No Hacker with HID $teamMember1<br>";
                    return;
                }
            }

            if ($teamMember2) {
                $isHacker = executePlainSQL("SELECT * from Hacker where hid = $teamMember2");
                $numRows = oci_fetch_all($isHacker, $rows, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                if (!$numRows) {
                    echo "<br>No Hacker with HID $teamMember2<br>";
                    return;
                }
            }


            if ($teamMember3) {
                $isHacker = executePlainSQL("SELECT * from Hacker where hid = $teamMember3");
                $numRows = oci_fetch_all($isHacker, $rows, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                if (!$numRows) {
                    echo "<br>No Hacker with HID $teamMember3<br>";
                    return;
                }
            }

            if ($teamMember4) {
                $isHacker = executePlainSQL("SELECT * from Hacker where hid = $teamMember4");
                $numRows = oci_fetch_all($isHacker, $rows, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                if (!$numRows) {
                    echo "<br>No Hacker with HID $teamMember4<br>";
                    return;
                }
            }


            $tnum = rand(115, 999);
            //$time = construct_timestamp(date('Y-m-d h:i:s', time()));
            $gmTime = strtoupper(gmdate('d-M-y H.i.s.u A', time()));



            executePlainSQL("insert into Team_Submits_project values ($tnum, '$hackathonName', $hackathonYear, '$gmTime', '$projName')");
            OCICommit($db_conn);
            if (($teamMember1 && is_numeric($teamMember1)) || $teamMember1 == 0) {

                executePlainSQL("insert into Belong_to values ($teamMember1, $tnum)");
                OCICommit($db_conn);
            }
            if (($teamMember2 && is_numeric($teamMember2)) || $teamMember2 == 0) {

                executePlainSQL("insert into Belong_to values ($teamMember2, $tnum)");
                OCICommit($db_conn);
            }
            if (($teamMember3 && is_numeric($teamMember3)) || $teamMember3 == 0) {

                executePlainSQL("insert into Belong_to values ($teamMember3, $tnum)");
                OCICommit($db_conn);
            }
            if (($teamMember4 && is_numeric($teamMember4)) || $teamMember4 == 0) {

                executePlainSQL("insert into Belong_to values ($teamMember4, $tnum)");
                OCICommit($db_conn);
            }

            echo "<br>Successfully submitted project $projName by $teamMember1, $teamMember2, $teamMember3, $teamMember4 to $hackathon<br>";
            $teams = executePlainSQL("SELECT tnum, hackathon_name, hackathon_yr, project_name, submission_time FROM Team_Submits_project");

            echo '<table class="dbresults">';
            echo '<tr>';
            echo '<th>TEAM NUM</th>';
            echo '<th>HACKATHON</th>' ;
            echo '<th>YEAR</th>';
            echo '<th>PROJECT</th>';
            echo '<th>SUBMISSION TIME</th>';
            echo '</tr>';

            while ($row = OCI_Fetch_Array($teams, OCI_BOTH)) {
                // table input from query result 
                echo '<tr>';
                echo '<td>' . $row["TNUM"] . '</td>';
                echo '<td>' . $row["HACKATHON_NAME"] . '</td>';
                echo '<td>' . $row["HACKATHON_YR"] . '</td>';
                echo '<td>' . $row["PROJECT_NAME"] . '</td>';
                echo '<td>' . $row["SUBMISSION_TIME"] . '</td>';
                echo '</tr>'; //or just use "echo $row[0]"
            }

            echo "</table>";

        }

        function handleCountRequest() {
            global $db_conn;

            $groupBy = $_GET['group'];

            $hackers = executePlainSQL("SELECT $groupBy, count(*) as num_hackers FROM Participates p GROUP BY $groupBy");

            if ($groupBy == 'hackathon_name') {
                echo "<br>Total number of hackers each hackathon organization has hosted:<br>";
                            
                echo '<table class="dbresults">';
                echo '<tr>';
                echo '<th>HACKATHON NAME</th>';
                echo '<th>NUMBER OF HACKERS</th>';
                echo '</tr>';

                while ($row = OCI_Fetch_Array($hackers, OCI_BOTH)) {
                    // table input from query result 
                    echo '<tr>';
                    echo '<td>' . $row["HACKATHON_NAME"] . '</td>';
                    echo '<td>' . $row["NUM_HACKERS"] . '</td>';
                    echo '</tr>'; //or just use "echo $row[0]"
                }
            } else if ($groupBy == 'Hackathon_yr') {
                echo "<br>Number of hackers that participated in a hackathon by year:<br>";

                echo '<table class="dbresults">';
                echo '<tr>';
                echo '<th>HACKATHON YEAR</th>';
                echo '<th>NUMBER OF HACKERS</th>';
                echo '</tr>';

                while ($row = OCI_Fetch_Array($hackers, OCI_BOTH)) {
                    // table input from query result 
                    echo '<tr>';
                    echo '<td>' . $row["HACKATHON_YR"] . '</td>';
                    echo '<td>' . $row["NUM_HACKERS"] . '</td>';
                    echo '</tr>'; //or just use "echo $row[0]"
                }
            } else if ($groupBy == 'hackathon_name, Hackathon_yr') {
                echo "<br>Number of hackers that participated in each hackathon:<br>";

                echo '<table class="dbresults">';
                echo '<tr>';
                echo '<th>HACKATHON NAME</th>';
                echo '<th>HACKATHON YEAR</th>';
                echo '<th>NUMBER OF HACKERS</th>';
                echo '</tr>';

                while ($row = OCI_Fetch_Array($hackers, OCI_BOTH)) {
                    // table input from query result 
                    echo '<tr>';
                    echo '<td>' . $row["HACKATHON_NAME"] . '</td>';
                    echo '<td>' . $row["HACKATHON_YR"] . '</td>';
                    echo '<td>' . $row["NUM_HACKERS"] . '</td>';
                    echo '</tr>'; //or just use "echo $row[0]"
                }
            }

            echo "</table>";
        }




        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                } else if (array_key_exists('updateSkillQueryRequest', $_POST)) {
                    handleUpdateSkillRequest();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countHackers', $_GET)) {
                    handleCountRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['updateSkill']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest'])) {
            handleGETRequest();
        } else if (isset($_GET['countHackers'])) {
            handleGETRequest();
        }
		?>
        </div>
	</body>
</html>
