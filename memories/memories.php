<?php 
    session_start();
    if(!isset($_SESSION["authorized"])){
        header("location: /memSafe/memSafe");
        exit;
    }
    include_once("headNoNav.php");
?>
    <title>MemSafe</title>
    <base href = "/portfolio/memSafe/memories/">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;500;900&display=swap" rel="stylesheet">
    <link rel = "stylesheet" href = "memories.css">

    </head>
    <body>
        <header>
            <nav>
                <h4 id="logo"><a href = "/memSafe/memories">MemSafe</a></h4>
                <ul id = "nav-menu">
                    <li><a href = "#">Code</a></li>
                    <li><a href="/#portfolio-">Portfolio</a></li>
                    <li><a href = "leaveMemories.php">Log Out</a></li>
                </ul>
                <div class = "bars-cont">
                    <i class = "fas fa-bars"></i>
                    <i class = "fas fa-times"></i>
                </div> 
            </nav>
        </header>
        <main class = "container">
            
            <section class="container-content">
                <!--<div class="edit-note">-->
                    <div class="heading">
                        <h1>
                            Welcome
                            <?php echo $_SESSION["username"]; ?> 
                        </h1>
                        <h2>
                            Find Your Memories Here
                        </h2>
                        <h3>Don't hesistate to add more</h3>
                    </div>
                    <form class = "mem-form" name = "mem-form" id = "mem-form" enctype ="multipart/form-data" method = "post" accept-charset = "utf-8">
                        <div class="input-ctrl">
                            <div class="ta-header">
                                <h2><?php echo $_SESSION["uptime"][0]?></h2>
                                <h3><?php echo $_SESSION["uptime"][1]?></h3>
                                <i class="far fa-trash-alt"></i>
                            </div>
                            <textarea name="memory-note" class="memory-note" type = "text" onfocusout = "validator.notEmpty(memNote, 'Enter memory')">
                                <?php 
                                    if(isset($_SESSION["memory-note"])){
                                        echo trim($_SESSION["memory-note"]);
                                    }
                                ?>
                            </textarea>
                            <small id = "err-msg"></small>
                        </div>
                        <div class = "opt-btns">
                            <button type = "submit" name = "sub-btn" id = "sub-btn">Secure Memory</button>
                        </div>
                    <form>
              
            </section>
        </main>  
      
        <script type = "text/javascript" src ="../../JSClasses/formValidator.js"></script>
        <script type = "text/javascript" src = "memories.js"></script>
    </body>
</html>