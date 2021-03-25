<?php
    include_once("Classes/DBConnection.php");
    include_once("Classes/FormValidator.php");

    $conn = DBConnection::initialize("memSafe.ini");
    $username ="";
    $memNote = "";
    $secret = "";

    //There is no sanitization of the memory note or secret prior to being stored in the database because there shouldn't be a limit on what a user's memory note can contain. All characters should be allowed.
    //Escaping certain characters happens prior to displaying the message back  to the user
     
    /**
     * Determines if input value is non empty and is less than the character limit and checks a user name input against a regex
     * Memory-notes can be up to 65,535 characters long while secrets must remain under 256 characters.
     * 
     * @param {Array} $source - array containing data from user input field
     * 
     * @return {Array} returns an array of valid inputs if input    passes conditions.
     * otherwise exit the program
     * 
     */
    function validInput($source){
        $validInputs = [];
        if(isset($source)){
            foreach($source as $key => $value){
                if(empty($value)){
                    exit(json_encode("$key empty"));
                }
                $trimmedVal = trim($value);
                switch($key){
                    case "memory-note":
                        if(!(mb_strlen($trimmedVal, "utf-8") < 65535)){
                            exit(json_encode("$key length"));
                        }
                        $validInputs["memory-note"] = $trimmedVal;
                    break;
                    case "secret":
                        if(!(mb_strlen($trimmedVal, "utf-8") < 256)){
                            exit(json_encode("$key length"));
                        }
                        $validInputs["secret"] = $trimmedVal;
                    break;
                    case "username":
                        
                        if(!(mb_strlen($trimmedVal, "utf-8") <= 20)){
                            exit("$key length");
                        }else if(!preg_match("/^[a-z0-9]+$/i", $trimmedVal)){
                            exit(json_encode("$key invalid"));
                        }
                        $validInputs["username"] = $trimmedVal;   
                    break;
                }
            } 
            return $validInputs;
        }   
        exit(json_encode("No Data"));
    }

    /**
     * Encrypts a memory with a known nonce and key
     * 
     * @param {String} $note  - the memory to encrypt
     * @param {String} $nonce - the salt to encrypt the note
     * @param {String} $key - the key to encrpyt the note 
     */
    function encryptKnown($nonce, $key, $note){
        $encMemory = sodium_crypto_secretbox($note, $nonce, $key);
         //prepend nonce and the salt to the encrypted note as both will be needed to decrypt note later. The length of the  nonce is SODUIOM_CRYPTO_SECRETBOX_NONCEBYTES or 24bit long the key is 32 bits long.
        $encMemory = base64_encode($nonce . $key . $encMemory);
        return $encMemory;
    }

    /**
     * Generates nonce and key from given secret to then encrypt a memory note.
     * 
     * @param {String} $secret - the password user to construct the key 
     * @param {String} $note - the message to be encrypted
     * @param {Boolean} $give - if true the nonce, key, and encrypted memory are returned if null only the encrypted memory is returned
     * @return {String | Array } -the encrypted message | [nonce,  key, encrypted note]
     * 
     */
    function encryptMemory($secret, $note, $give = null){
        $alg = SODIUM_CRYPTO_PWHASH_ALG_DEFAULT;
        $opslimit = SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE;
        $memlimit = SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE;
        $salt = random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES);
        $key = sodium_crypto_pwhash(32,
        $secret, $salt, $opslimit, $memlimit, $alg);
        //Encrypting the memory note with the derived key and the secret
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        /*$cipherMemory = sodium_crypto_secretbox($note, $nonce, $key);
        //prepend nonce and the salt to the encrypted note as both will be needed to decrypt note later. The length of the  nonce is SODUIOM_CRYPTO_SECRETBOX_NONCEBYTES or 24bit long
        $finalNote = base64_encode($nonce . $key . $cipherMemory);*/
        $finalNote = encryptKnown($nonce, $key, $note);
       // return $finalNote;
        if($give){
            return ["nonce" => $nonce, "key" => $key, "memory-note" => $finalNote];
        }else{
            return $finalNote;
        }
    }

    /**
     * Extracts the key, nonce, and note from the memory-note
     * and decrypts memory note
     * 
     * @param {String} $encryptedMemory - the note 
     * 
     * @return {Array} $memories - an array of consisting of the key, nonce, and decrypted message
     */
     function decryptMemory($encryptedMemory){
        $encryptedNote = base64_decode($encryptedMemory);
        $nonce = mb_substr($encryptedNote, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, "8bit");
        $key = mb_substr($encryptedNote, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, 32, "8bit");
        $cypherMemory = mb_substr($encryptedNote, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + 32, null, "8bit");
        $cypherMemory = sodium_crypto_secretbox_open($cypherMemory, $nonce, $key);
        //prevent possible xss attack via the note after decrypting note
        $cypherMemory =  htmlspecialchars($cypherMemory, ENT_QUOTES, "UTF-8");
        return ["nonce" => $nonce, "key" => $key, "memory-note" =>  $cypherMemory];
     }

    /**
     * Deletes currently logged in user's memory  note
     * 
     * @param {DBConnection} $dbconn - the connection to the database
    */
    function forgetMemory($dbconn){
        session_start();
        if(!isset($_SESSION["authorized"])){
            //break out of function if session isn't active although this function should only be called during and active session.
            exit(json_encode("no session"));
        }
        //craft mysql statement to delete memory
        $query = "update memory set note = null where username = ?";
        $result = $dbconn->dbQuery($query, [$_SESSION["username"]]);
        
        if($result){
            exit(json_encode("deleted"));
        }
    }

    /**
     * Updates currently logged in user's memory note
     * 
     * @param {DBConnection} $dbconn - the connection to the database
     * @param {String} $note - the note to be encrypted
     * @param {String} $secret - the secret/password
     */
    function updateMemory($dbconn, $note){
        session_start();
        $cypherMemory = "";
        if(!isset($_SESSION["authorized"])){
            exit(json_encode("no session"));
        }
        if(isset($_SESSION["key"]) && isset($_SESSION["nonce"])){
            //key and nonce are available so encrypt updated note
            $cypherMemory = encryptKnown($_SESSION["nonce"], $_SESSION["key"], $note);
        }else if(isset($_SESSION["secret"])){
            //key and nonce do not exist, create key and nonce and encrypt memory then set the session variables nonce and key
            $memoryData = encryptMemory($_SESSION["secret"], $note, true);
            $_SESSION["nonce"] = $memoryData["nonce"];
            $_SESSION["key"] = $memoryData["key"];
            $_SESSION["memory-note"] = $memoryData["memory-note"];
            
            $cypherMemory = $memoryData["memory-note"];
        }else{
            exit(json_encode("No key, nonce or secret exists"));
        }
        $query = 'update memory set note  = ? where username = ?';
        $result = $dbconn->dbQuery($query, [$cypherMemory, $_SESSION["username"]]);
        if($result){
            exit(json_encode("remembered"));
        }else{
            exit(json_encode("remembered error"));
        }
    }

//============ Entrance to the script ===================//
/*
    There are Four different actions within this application.
    1. Create -User creates a new note with a new username and secret - this means the $_POST array contains three items
    2. Read - logs in in order to retrieve note - $_Post array only contains two items, username and secret
    3. Update - logged in user updates and submits new note - this means $_POST array only contains one item, the updated note
    4. Delete - logged in user deletes note
*/
    if(!isset($_POST["action"])){
        //either a create, read, or update action is taking place
        //inputs must be validated regardless
        $valid = validInput($_POST);
        if($valid && count($valid) == 1){
            //an update of memory is occuring because the valid array is one item long. 
            updateMemory($conn, $valid["memory-note"]);
        }else if($valid && count($valid) == 2){
            //retrieval of memory is occuring because only two indices are present in the valid array.
            //retrieve info for given username, only use info if secrets match
            $query = "select uptime, secret, note from memory where username = ?";
            $stmt = $conn->dbQuery($query, [$valid["username"]],1);
            $result = $stmt->fetch();
            //echo json_encode("\nthis is the secret: " . $result["note"]);
            
            if(!$result){
                exit(json_encode("username not found"));
            }else if(password_verify($valid["secret"], $result["secret"])){
                //username valid and secret is correct
                //decode note if it exists
                if(isset($result["note"])){
                    //Note exits, get decrypted data
                    $plainMemory = decryptMemory($result["note"]);
                    //Morph the upload time and date into a more readable format 
                    $time = preg_split("/\s/", $result["uptime"]);
                    $date = preg_split("/-/", $time[0]);
                    $time1 = preg_split("/:/", $time[1]);
                    $ampm;
                    if((int)$time1[0] > 12){
                        (int)$time1[0] -= 12;
                        $ampm = "PM";
                    }else{
                        $ampm = "AM";
                    }
                    $trueUpTime = [
                        "$date[1]/$date[2]/$date[0]",
                        "$time1[0]:$time1[1] $ampm"
                    ];
                    //Set session variables                   
                    session_start();
                    $_SESSION["authorized"] = true;
                    $_SESSION["username"] = $valid["username"];
                    $_SESSION["uptime"] = $trueUpTime;
                    $_SESSION["nonce"] = $plainMemory["nonce"];
                    $_SESSION["key"] = $plainMemory["key"];
                    $_SESSION["memory-note"] = $plainMemory["memory-note"];
                    echo json_encode("valid secret");
                }else{
                    //note doesn't exist and therefore neither the nonce nor the key since both are prepended to the note prior to database storage..send user to homepage 
                    session_start();
                    $_SESSION["authorized"] = true;
                    $_SESSION["username"] = $valid["username"];
                    $_SESSION["uptime"] = ["No memories stored.", "Type whenever ready."];
                    $_SESSION["secret"] = $valid["secret"];
                    exit(json_encode("valid secret"));
                }
            }else{
                exit(json_encode("invalid secret"));
            }
        }else if($valid){
        //the valid array length is more than two meaning a memory needs to be created and stored in the database.
        //check that username is not taken
        $userQuery = "select username from memory where username = ?";
        $userQueryRes = $conn->dbQuery($userQuery, [$valid["username"]], 1);

        if($userQueryRes->fetch()){
            //user name exists already..exit program
            exit(json_encode("username exists"));
        }

        //generate a hash from secret input
        $hash = password_hash($valid["secret"], PASSWORD_DEFAULT);
        //encrypt the note
        $finalNote = encryptMemory($valid["secret"], $valid["memory-note"]);
        //store contents into database
        $query = "insert into memory(mem_id, uptime, username ,secret, note) values (?, now(), ?, ?, ?)";
        $params = [0, $valid["username"], $hash, $finalNote];
        $result =  $conn->dbQuery($query, $params);
        exit(json_encode("$result"));
        }else{
            exit(json_encode("Error"));
        }
    }else{
        //Delete memory
        forgetMemory($conn);
        //echo json_encode("deleted");
    }
    

?>