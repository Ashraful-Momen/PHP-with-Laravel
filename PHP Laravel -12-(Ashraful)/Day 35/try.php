var frist = document.querySelector("#fname");
var last = document.querySelector("#lname");
var mail = document.querySelector("#email");
var adresses = document.querySelector("#adress");
var password = document.querySelector("#pass");
var submitbtn = document.querySelector("#submit");
submitbtn.addEventListener('click',function(){
    if(frist.value=='' || last.value=='' || mail.value=='' || adresses.value=='' || password.value==''){
        alert("Fill All the box to continue!");
    }
    else{
        // addtodo();
        if (localStorage.getItem('reg-user') == null) {
            localStorage.setItem('reg-user', '[]');
            var data = JSON.parse(localStorage.getItem('reg-user'));
            data.push(user = {
                'fname' : frist.value,
                'lname' : last.value,
                'mail' : mail.value,
                'adress' : adresses.value,
                'pass' : password.value

            })
            localStorage.setItem('reg-user', JSON.stringify(data));
        }
        else{
            var data = JSON.parse(localStorage.getItem('reg-user'));
            data.push(user = {
                'fname' : frist.value,
                'lname' : last.value,
                'mail' : mail.value,
                'adress' : adresses.value,
                'pass' : password.value
            })
            localStorage.setItem('reg-user', JSON.stringify(data));
        }
    }
});