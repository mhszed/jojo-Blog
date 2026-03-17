<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){

  $a=false;
    $tmp = $_FILES["up"]["tmp_name"];
    $name = $_FILES["up"]["name"];
    $ext = strtolower(pathinfo($name,PATHINFO_EXTENSION));
//mime类型
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $type = finfo_file($finfo,$tmp);
    $allowed2 = ['image/jpg', 'image/png', 'image/gif', 'image/jpeg'];
    if(!in_array($type,$allowed2)){
      $a=true;  

    } 
//允许的文件尾
    $allowed3 = ['jpg', 'png', 'gif', 'jpeg'];
    if(!in_array($ext,$allowed3)){
      $a=true; 

    } 
//头部检测
    $f=fopen($tmp,"rb");
    $head=bin2hex(fread($f,2));
    $allowed = ["ffd8","8950","4749"];
    if(!in_array($head,$allowed)){
      $a=true; 

    }
    $dangerous = ["<?php", "<?= ", "<?=", "eval(", "system(", "shell_exec", "base64_decode("];
    $content = strtolower(file_get_contents($tmp));
    foreach($dangerous as $bad){
    if(strpos($content,$bad)){
      $a =true;
      break;
    }}
    if($a==true){
        $uploadMessages='sb';
       
     
    }else{
    $newname = bin2hex(random_bytes(12)) . "." .$ext;
    $save = 'img/'.$newname;
    if($tmp == ""){

    }
    if(move_uploaded_file($tmp,$save)){
            $uploadMessages= "文件 '" . $save. "' 上传成功。";
    }}
   }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">

    <input type="file" id="up" name="up" >

    <input type="submit" value="上传">
    <?php print_r ($uploadMessages) ?>
    </form>
</body>
</html>
