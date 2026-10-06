const password=document.getElementById("password");

if(password){

password.addEventListener("keyup",()=>{

if(password.value.length<8){

password.style.borderColor="red";

}else{

password.style.borderColor="green";

}

});

}