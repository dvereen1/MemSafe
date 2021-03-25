//(function(){
    //reference the memory note
    const memForm = document.getElementById("mem-form");
    const memNote = document.querySelector(".memory-note");
    const menuBtn = document.querySelector(".bars-cont");
    const navMenu = document.getElementById("nav-menu");
    const menuIcons =  Array.from(menuBtn.childNodes)
                        .filter(function(node){
                            return node.nodeName == "I";
                        });
    const validator =  new Validator();

  //========= Menu Button ============ //
    menuBtn.addEventListener("click", function(){
        menuIcons.forEach(function(el){
            el.classList.toggle("active");
        });
        navMenu.classList.toggle("active");
    });

    //========= Trash Button ============//
    const trashBtn = document.querySelector(".fa-trash-alt");
    trashBtn.addEventListener("click", function(event){
        event.preventDefault();
        //create a form to notify php to delete the note
        const form = new FormData();
        form.append("action", "delete");
        //send data to memSafeLogic.php
        fetch("../memSafeLogic.php", {  
            method: "post",
            body: form
        }).then(
           //response => response.text()
           response => response.json()
        ).then( data => {
            //console.log(data);
            switch(data){
                case "deleted":
                    alert("Memory Forgotten");
                    validator.clearFields(memNote, 1);
                    break;
                default:
                    alert("Application error, try again later");
                    break;
            }
        }).catch(error => {
            alert('Encountered application error.\nPleaser try again later');
            //console.log('Error: ', error);
        });

    });


    //========= Secure Memory Button =====//
   
    const subBtn = document.getElementById("sub-btn");
    subBtn.addEventListener("click", function(event){
        event.preventDefault();
        //check if input is empty
        const valid = [];
        valid.push(validator.notEmpty(memNote));

        if(valid.indexOf(0) === -1){
            //create formdata object
            const form = new FormData(memForm);
          
            fetch("../memSafeLogic.php", {
                method: "post",
                body: form
            }).then(
               //response => response.text()
               response => response.json()
            ).then( data => {
                //console.log(data);
                switch(data){
                    case "remembered":
                        alert("We'll remember that for you!");
                        break;
                    case "remembered error":
                        alert("Database error. Please try again later.");
                }
            }).catch( error =>{
                alert('Encountered application error.\nPleaser try again later');
                //console.log('Error: ', error)
            });
            //console.log(form);

        }
        //trigger pop up to change secret or skip -- maybe
        
        //pass form data off to memSafeLogic.php
    });
    //return validator;
//})();
