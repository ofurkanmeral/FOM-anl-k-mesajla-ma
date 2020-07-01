
<script src="js/jquery.js"></script>
<script src="js/socket.io.js"></script>

<link rel="stylesheet" type="text/css" href="js/style.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">


<div class="container">
<div class="row">
<div class="col-md-4">
<h2>FOM Mesajlaşma</h2>
<form class="form" onsubmit="return enterName();">
    <input id="name" placeholder="isminizi girin">
    <input class="button" type="submit">
</form>

<ul class="liste"id=users></ul>
</div>
<div class="col-md-8"> 
<div  class="mesaj">
<ul class="output"id="messages"></ul>
</div>
<form onsubmit="return sendMessage();">
<input id="message" placeholder="Enter message">
<input class="button" type="submit">
</form>
<form onsubmit="return toplumesaj();">
<input class="button" type="submit">
</form>

</div>
</div>
</div>
<script>

var io=io('http://localhost:3000');
var receiver="";
var sender="";

function enterName(){
    //get username
    var name=document.getElementById("name").value;
    //send it server
    io.emit("user_connected",name);
    sender=name;
    return false;
}

io.on("user_connected",function(username){
   var html="";
   html+="<li><button onclick='onUserSelected(this.innerHTML);'>"+ username +"</button></li>";
   document.getElementById("users").innerHTML += html;

});

function onUserSelected(username){
    receiver=username;
}

$.ajax({
    url:"http://localhost:3000/get_messages",
    method:"POST",
    data:{
        sender:sender,
        receiver:receiver
    },
    success:function(response){
        console.log(response)
    }
})

function sendMessage(){
    var message=document.getElementById("message").value;

    io.emit("send_message",{
        sender:sender,
        receiver:receiver,
        message:message
    });
    return false;
}

io.on("new_message",function(data){
     var html="";
     html+="<li>"+data.sender+" :"+data.message+"</li>"
     document.getElementById("messages").innerHTML+=html;   
})

function toplumesaj(){
    var message=document.getElementById("message").value;
    io.emit("toplu",{
        sender:sender,
        message:message
    });
    return false;
}
io.on("yenitoplu",function(data){
    var html="";
     html+="<li>"+data.sender+"(Toplu)"+" :"+data.message+"</li>"
     document.getElementById("messages").innerHTML+=html;  
});


</script>