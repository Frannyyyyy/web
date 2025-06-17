/*function name(fname,lname,age){
    var fname=prompt('Enter first name please');
    var lname=prompt('Enter last name please');
    alert('hey '+fname+' '+lname); 
    var age=prompt('Enter your age please');
    if(age < 18){
        alert('You are not allowed to enter this site');
    }
    else{
        alert('Welcome to witchcraft');
    }
}

name();

/*beginner basics*/

/*
var list=['red','blue','green'];
alert('hey '+list[0]);
var curr=list.shift();
alert(curr); 

for(var i=0; i<list.length; i++){
    alert('I am '+list[i]);
}
*/


 document.getElementById("Topbot").onclick = function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };



var one=document.getElementById("num1");
var two=document.getElementById("num2");
var sum=document.getElementById("sum");

one.addEventListener("input", press);
two.addEventListener("input", press);

function press(){
    sum.innerHTML=(parseFloat(one.value)||0)+(parseFloat(two.value)||0);
}

function check(){
    if(one.value < 100){
        alert('hey');
    }
    else{
        alert('good');
    }
}

function check2(){
    if(two.value < 100){
        alert('hey');
    }
    else{
        alert('good');
    }
}
one.addEventListener("blur",check);
two.addEventListener("blur",check2);


var sigil=document.getElementById("sigil");

/*var spic=document.getElementById("spic");
sigil.addEventListener("click",function(){
    if(spic.className=="hello"){
        spic.className="";
    }
    else{
        spic.className="hello";
    }
})*/


var voodoo=document.getElementById("voodoo");
var wicca=document.getElementById("wicca");
var tarot=document.getElementById("tarot");
var sigil=document.getElementById("sigil");

sigil.addEventListener("click",showPic);
voodoo.addEventListener("click",showPic);
tarot.addEventListener("click",showPic);
wicca.addEventListener("click",showPic);

function showPic(){
    var picId= this.getAttribute("data-img");
    var pic=document.getElementById(picId);
    if(pic.className==="hello"){
        alert("hide");
    }
    else{
        alert("no hide")
    }
}







