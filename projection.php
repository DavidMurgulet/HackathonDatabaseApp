<!-- CODE REFERENCES: 
  Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22) -->



  <?php
    if (connectToDB()) {
        global $db_conn;
        session_start();
        $tables = executePlainSQL("SELECT table_name FROM user_tables");
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

    <title>View Tables</title>
    </head>

    <body class = "base">

    <div >
        <button class = "back-button"> <a href="index.php">Back to Homepage</a></button> 
    </div>
        
    <div class = "title">
            <h1>View Tables</h1>
    </div>
    
    <div class = "content">
        
        <hr />

        <h2>Select a Table and its Attributes</h2>
        <form method="POST" action="projection.php">
            <input type="hidden" id="projectionRequest" name="projectionRequest">
            <label for="tableName">Select a Table:</label>
            <input type="text" name='tableName' list='tableName'>
            <datalist id='tableName'>
                <?php 
                    while ($row = OCI_Fetch_Array($tables, OCI_BOTH)) {
                        echo "<option value='" . $row['TABLE_NAME'] . "' </option>";
                    }
                ?>
            </datalist>
            <p><input type="submit" value="Select" name="getTables"></p>

            <?php
             if (connectToDB()) {
                global $db_conn;
                $tableName = $_POST['tableName'];
                $table = $tableName;
                $attributes = executePlainSQL("SELECT column_name FROM USER_TAB_COLUMNS WHERE table_name = '$tableName'");
                }
            ?>

            <input type="hidden" id="attRequest" name="attRequest">
            <label for="attNames">Select Attributes:</label>
            <select name="attNames[]" multiple>
                <?php 
                    while ($row = OCI_Fetch_Array($attributes, OCI_BOTH)) {
                        echo "<option value='" . $row['COLUMN_NAME'] . "'>" . $row['COLUMN_NAME'] . "</option>";
                   }
                ?>
            </select>
            <p><input type="submit" value="View Table" name="projectedTable"></p>
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


        // projection code
        function handleProjectedTable() {
            global $db_conn;

            $attributes = $_POST['attNames'];
            $table = $_SESSION['tableName'];

            $selectQ = "SELECT ";
            $fromQ = " FROM $table";
            $inc = 0;
            $len = count($attributes);

            foreach ($attributes as $attribute) {
                if ($inc == ($len - 1)) {
                    $selectQ .= $attribute;
                } else {
                    $selectQ .= $attribute . ", ";
                    $inc++;
                }
            }


            $query = $selectQ . $fromQ;
            $finalTuples = executePlainSQL($query);


            echo '</tr>';
            echo '</tr>';

            echo '<br>' . $table . ' ' . 'Table:<br><br>';
            echo '<table class="dbresults">';
            echo '<tr>';
            foreach($attributes as $attribute) {
                echo '<th>' . $attribute . '</th>';
            }
            echo '</tr>';

            while ($row = OCI_Fetch_Array($finalTuples, OCI_BOTH)) {
                // table input from query result 
                echo '<tr>';
                foreach($attributes as $attribute) {
                    echo '<td>' . $row[$attribute] . '</td>';
                }
                echo '</tr>';
            }
 
            echo "</table>";
        }

        
        function handleGetTable() {
            global $db_conn;

            $tableName = $_POST['tableName'];
            $_SESSION['tableName'] = $tableName;

            echo "$tableName table selected! please select the attributes you want to view 
            (hold ctrl/cmd to select multiple)";

        }



        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('getTables', $_POST)) {
                    handleGetTable();
                } else if (array_key_exists('projectedTable', $_POST)) {
                    handleProjectedTable();
                }
            
                disconnectFromDB();
            }
    
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                disconnectFromDB();
            }
        }

		if (isset($_POST['getTables']) || isset($_POST['projectedTable'])) {
            handlePOSTRequest();
        } 
		?>
        </div>
	</body>
</html>