var express=require('express')
var app=express();
app.use(express.static('js'));

var http=require("http").createServer(app);

var io=require("socket.io")(http);

var bodyParser=require("body-parser");
app.use(bodyParser.urlencoded())

var mysql=require("mysql");
var connection=mysql.createConnection({
    "host":"localhost",
    "user":"root",
    "password":"Sildim",
    "database":"messages"
})

connection.connect(function(error){

});

app.use(function(request,result,next){
    result.setHeader("Access-Control-Allow-Origin","*");
    next();
});

app.post("/get_messages",function(request,result){
    connection.query("SELECT * FROM messages WHERE (sender ='"+request.body.sender+"'AND receiver ='"+request.body.receiver+"') OR (sender ='"+request.body.receiver+"'AND receiver ='"+request.body.sender+"')",function(error,messages){
        result.end(JSON.stringify(messages))
    })
})


var users=[];
///////////////////////////

/////////////////////////////////


io.on("connection",function(socket){
    console.log("Kullanici Baglandi",socket.id);

    socket.on("user_connected",function(username){
        users[username]=socket.id;

        io.emit("user_connected",username);
    });

    socket.on("send_message",function(data){
        var socketId=users[data.receiver];
        io.to(socketId).emit("new_message",data);

        connection.query("INSERT INTO messages (sender,receiver,message) VALUES ('"+data.sender+"','"+data.receiver+"','"+data.message+"')",function(error,result){
        
        })
});

socket.on("toplu",function(data){
    io.emit("yenitoplu",data);
    connection.query("INSERT INTO messages (sender,message) VALUES ('"+data.sender+"'','"+data.message+"')",function(error,result){
    })

})

});

http.listen(3000,function(){
    console.log("Server Basladi");
});