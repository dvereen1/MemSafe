
// Setting up references to the navigation buttons and inputs 
const form = document.getElementById('memory-inputs');
const memNote = document.querySelector('.memory-note');
const memKey = document.querySelector('.key-in');
const username = document.querySelector('.username');
const remUsername = document.querySelector('.rem-username');
const remKey = document.querySelector('.rem-key');
const subBtn = document.getElementById('sub-btn');
const smalls = document.getElementsByTagName('small');
const menu = document.querySelector('nav .bars-cont');
const menuClose = document.querySelector('nav .bars-cont .fa-times');
const navMenu = document.getElementById('nav-menu');
const smallsArr = Array.from(smalls);
const formArr = [memNote, username, memKey];
const rememBtn = document.querySelector(".remem-btn");
const rememDiv = document.querySelector(".remem-inputs");
const recallBtn = document.getElementById("recall-btn");
const rememFormArr = [document.forms.rememberInputs.username, document.forms.rememberInputs.secret];
//Register event listener for the nav menu button
//Trigger active class which displays navigation when button is clicked

//Refer to JSClasses/formValidator.js
const validator =  new Validator();

menu.addEventListener('click', event => {
    //console.log("Menu button clicked");
    navMenu.classList.toggle('active');
    menuClose.classList.toggle('active');
    menuClose.previousElementSibling.classList.toggle('active');
});

//an array the holding true or false values resulting from the form validation of the retrieve memory inputs

//=================Login to View Note=====================/
//register event listener to the remember button
rememBtn.addEventListener("click", function(){
    //console.log("expand the remember form");
    this.classList.toggle("hide");
    rememDiv.classList.toggle("show");
    ////console.log("the remember form elements: ", rememFormArr);
    recallBtn.addEventListener("click", function(event){
        //clear the array if page hasn't been refreshed and it still holds fals values
        const rvalid = [];
        event.preventDefault();
        //console.log(" recall button clicked");
        rememFormArr.forEach((input, index) =>{
            if(index == 0 ){
                //if the first array element is being validated, i.e. username 
                if(validator.notEmpty(input)){
                    rvalid.push(validator.checkText(input));
                }else{
                    rvalid.push(false);
                }
    
            }else{
                rvalid.push(validator.notEmpty(input));
            }
        });
        if(!rvalid.includes(false)){
            const rFormData = new FormData(document.forms.rememberInputs);
            fetch("memSafeLogic.php", {
                method: 'post',
                body: rFormData
            }).then(
                //response => response.text()
               response => response.json()
            ).then(data => {
                //in this instance we don't need to display any data but only display error messages if any exist. If the correct username and secret are provided 
                
                //console.log(data);
                if(typeof data === 'string'){
                    //if the expected error message is a string
                    switch(data){
                        case 'username empty':
                            alert("Cannot leave username blank");
                            break;
                        case 'username length':
                            alert('username cannot exceed 20 characters.');
                            validator.clearFields(remUsername, 1);
                            break;
                        case 'username invalid':
                            alert('Username may contain only alphanumeric characters.');
                            validator.clearFields(remUsername, 1);
                            break;
                        case 'username not found':
                            alert("Username not found");
                            validator.clearFields(remUsername, 1);
                            break;
                        case 'invalid secret':
                            alert('Invalid secret');
                            validator.clearFields(remKey, 1);
                            break;
                        case 'secret empty':
                            alert('Cannot leave secret blank');
                            break;
                        case 'secret length':
                            alert("Secret cannot exceed 256 characters");
                            validator.clearFields(remKey, 1);
                            break;
                        case 'No Data':
                            alert("No data submitted");
                            break;
                        case 'valid secret':
                           // alert("Memory stored successfully");
                           location.replace("memories/memories.php");
                            break;
                    }
                    //no matter the error, clear the form after
                    validator.clearFields(rememFormArr);
                }else if(data.dbConnError || data.queryError || data.prepStatError){// values defined in a php database connection class
                    alert('Encountered database error.\nPlease try again later.');
                 }
            }).catch(error => {
                alert('Encountered application error.\nPleaser try again later');
                //console.log('Error: ', error);
            });
        }
    });
});


//===================Create NOTE and Account=================/

//register event listener for the submit button
subBtn.addEventListener( 'click', event =>{
    event.preventDefault();
    const valid = [];
    
    formArr.forEach((item,index) => {
        if(index == 1){
        //if the index is that of the username
          if(validator.notEmpty(item)){
            valid.push(validator.checkText(item));
            ////console.log("this is the username: ", item.value);
          }else{
            valid.push(false);
          }
        }else{
            valid.push(validator.notEmpty(item))
        }
    });
    
    ////console.log("Are there empty input elements? " + valid);
    const formData = new FormData(form);
    if(!valid.includes(false)){
        fetch('memSafeLogic.php', {
            method: 'post',
            body: formData
        }).then(
            //response => response.text()
            response => response.json()
        )
        .then(data => {
            //in this instance we don't need to display any data but only display error messages if any exist.
            //console.log(data);
            if(typeof data === 'string'){
                switch(data){
                    case 'username empty':
                        alert("Cannot leave username blank");
                        break;
                    case 'username length':
                        alert('username cannot exceed 20 characters.');
                        validator.clearFields(username, 1);
                        break;
                    case 'username invalid':
                        alert('username must only contain alphanumeric characters and an underscore.');
                        validator.clearFields(username, 1);
                    case 'username exists':
                        alert('Choose another username');
                        validator.clearFields(username, 1);
                        break;
                    case 'Password':
                        alert('Invalid secret');
                        validator.clearFields(remKey, 1);
                        break;
                    case 'memory-note empty':
                        alert('Cannot leave memory note blank');
                        break;
                    case 'memory-note length':
                        alert('Memory note cannot exceed 65,535 characters');
                        break;
                    case 'secret empty':
                        alert('Cannot leave secret blank');
                        break;
                    case 'secret length':
                        alert("Secret cannot exceed 256 characters");
                        validator.clearFields(memkey, 1);
                        break;
                    case 'No Data':
                        alert("No data submitted");
                        break;
                    case "1":
                        alert("We'll remember that for you!");
                        location.reload();
                        break;
                }
                //validator.clearFields(formArr);
            }else if(data.dbConnError || data.queryError || data.prepStatError){// values defined in a php database connection class
                alert('Encountered database error.\nPlease try again later.');
             }
        })
        .catch(error => {
            alert('Encountered application error.\nPleaser try again later');
            //console.log('Error: ', error);
        })
    }
   
});