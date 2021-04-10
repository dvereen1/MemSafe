
        <?php include_once("headNoNav.php")?>
        <title>MemSafe</title>
        <base href = "/portfolio/memSafe/">
        <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;500;900&display=swap" rel="stylesheet">
        <link href = "memSafe.css" rel ="stylesheet">
        <link rel = "stylesheet" href = "/CSS/allProjectsModal.css">
    </head>
    <body>
        <?php include_once("Classes/projectInfoModal.php");
            createProjectModal("MemSafe", $projectModalArr);
        ?>
        <main class ="djv-container">
                <nav>
                    <h4 id="logo" ><a href = "/memSafe">MemSafe</a></h4>
                    <ul id = "nav-menu">
                        <li><a href = "https://github.com/dvereen1/MemSafe">View Code</a></li>
                        <li><a href="/#portfolio-">Portfolio</a></li>
                    </ul>
                    <div class = "bars-cont">
                        <i class="fas fa-bars"></i>
                        <i class="fas fa-times"></i>
                    </div> 
                </nav>
            <div class = "content">
                <section class = "hero mb-10vb">
                    <div class = "hero-img">
                        <img src = "imgs/undraw_Note_list_re_r4u9-2.svg"></img>
                    </div>
                    <div class = "tagline">
                        <h1>
                            MemSAFE
                        </h1>
                        <h2>
                            We remember in case you don't
                        </h2>
                    </div>
                </section>
                <section class = "form-container">
                    <div class = "guide">
                        <h2>
                           How It Works
                        </h2>
                        <div class ="instructions">
                           <p>
                            To get started, write a note that needs remembering below. Next, enter a unique username or nickname. Lastly, wrap things up with a secret that only you know and can easily recall. 
                           </p>
                          
                           <p> Finally, click "Secure Memory" to encrypt the note that only your secret can unlock. Aim to keep your note and secret less than 256 characters.</p>
                        </div>
                    </div>
                    <div class = "remember-form">
                        <div class = "get-memory">
                            <h3>
                                Just need to retrieve a memory?
                            </h3>
                            <button class = "remem-btn">
                                Remember
                            </button>
                        </div>
                        <div class="remem-inputs">
                            <form name = "rememberInputs" id = "remember-inputs" enctype = "multipart/form-data" method = "post" accept-charset="utf-8">
                                <div class = "input-ctrl">
                                    <label>
                                        Username 
                                    </label>
                                    <input class = "rem-username" type = "text" name = "username" placeholder = "Ex. iambatman840" onfocusout = "validator.notEmpty(remUsername, 'Enter username')">
                                    <small class = "error-msg0"></small>
                                </div>
                                <div class = "input-ctrl">
                                    <label>
                                        Secret
                                    </label>
                                    <input class = "rem-key" type = "password" name = "secret" onfocusout="validator.notEmpty(remKey, 'Enter secret')">
                                    <small></small>
                                </div>
                                <div class="opt-btns">
                                    <button type = "submit" name = "remem-btn" id = "recall-btn">
                                        Recall
                                    </button>
                                </div>
                        </form>
                    </div>
                        </div>
                    <form name = "memory-inputs" id="memory-inputs" enctype="multipart/form-data" method = "post" accept-charset="utf-8">
                        <div class = "input-container mb-10r">
                            <div class = "input-header">
                                <h2>What needs remembering?</h2>
                            </div>
                            <textarea class = "memory-note"  name = "memory-note" type = "text" placeholder = "Memory Goes Here" onfocusout="validator.notEmpty(memNote, 'Enter memory')"></textarea>
                            <small id = "error-msg2">Defacto erro message</small>
                        </div> 
                        <div class = "input-ctrl">
                            <label>You aren't you without a username</label>
                            <input type = "text" class = "username" name = "username"
                            placeholder = "&nbsp" onfocusout="validator.notEmpty(username, 'Enter username')"
                            >
                            <small class = "error-msg0"></small>
                            <p class = "place-hldr">
                                Username here
                            <p>
                        </div>
                        <div class = "input-ctrl">
                            <label>What's something only you know?</label>
                            <input type = "password" class = "key-in"  name = "secret" placeholder = "&nbsp" onfocusout = 
                            "validator.notEmpty(memKey, 'Enter secret')" >
                            <small id = "error-msg1">Defacto error message</small>
                            <p class = "place-hldr">Secret here</p>
                        </div>   
                        <div class = "opt-btns">
                            <button type = "submit" name = "sub-btn" id = "sub-btn">Secure Memory</button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
        <script src ="/JS/allProjectsModal.js"></script>
        <script type = "text/javascript" src ="../JSClasses/formValidator.js"></script>
        <script type = "text/javascript" src ="memSafe.js"></script>
    </body>
</html>
