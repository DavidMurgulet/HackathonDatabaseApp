<html>
    <head> 
    <style>
      /* style tag denotes CSS*/
.header { 
  /* background-color: #F1F1F1; */
  margin-top:-15px;
  margin-left:-15px;
  margin-right:-15px;
  text-align: center;
  top: 40%;
  font-size: 40px;
  padding: 30px;
  font-family: monospace;
  padding-top: 200px;
  background-color: #163987;
  color: white;
  background-image: url("https://rare-gallery.com/mocahbig/1361456-Abstract-Blue-8k-Ultra-HD-Wallpaper.jpg");
  background-size: cover; 
  height: 100%
}
.column {
    top: 50%;
    text-align: center;
  width: 80%%;
}
.button {
  background-color: #1412a4;
  padding: 2px 12px;
  text-align: center;
  border-radius: 12px;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 20px;
  font-family: monospace;
  box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
}
    .center { /*buttons placed in center of column*/
        margin: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        }
a {
  color: white;
  text-decoration: none !important;
}
a:hover {
    color:#8142d2; 
    text-decoration:none; 
    cursor:pointer;  
}
</style>
        <title>HackBase</title>
    </head>
    <div class = "header">
      <lable>Welcome to the HackBase Management System!</lable>
            <br>
            <br>
            <button class="button"> <a href="hackathon.php"><h3>Hackathon Information</h3></button> <!-- href specifies link's destination, a defines a hyperlink--> 
            <br>
            <button class="button"> <a href="hacker.php"><h3>Hacker Information</h3></button>
            <br>
            <button class="button"> <a href="volunteer.php"><h3>Volunteer Information</h3></button>
            <br>
            <button class="button"> <a href="projection.php"><h3>View Tables</h3></button>
            <br>
    </div>
</html>
