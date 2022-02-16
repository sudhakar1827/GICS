<?php
$conn=new PDO('mysql:host=localhost; dbname=demo', 'root', '') or die(mysql_error());
if(isset($_POST['submit'])!=""){
  $name=$_FILES['photo']['name'];
  $size=$_FILES['photo']['size'];
  $type=$_FILES['photo']['type'];
  $temp=$_FILES['photo']['tmp_name'];
  $caption1=$_POST['caption'];
  $link=$_POST['link'];
  move_uploaded_file($temp,"files/".$name);
$query=$conn->query("insert into upload(name)values('$name')");
if($query){
header("location:index.php");
}
else{
die(mysql_error());
}
}
?>

<!-- Creates PDF file-->
<?php
	$error = "";		//error holder
	if(isset($_POST['createpdf'])){
		$post = $_POST;		
		$file_folder = "files/";	// folder to load files
		if(extension_loaded('zip')){	// Checking ZIP extension is available
			if(isset($post['files']) and count($post['files']) > 0){	// Checking files are selected
				$zip = new ZipArchive();			// Load zip library	
				$zip_name = time().".zip";			// Zip name
				if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE){		// Opening zip file to load files
					$error .=  "* Sorry ZIP creation failed at this time<br/>";
				}
				foreach($post['files'] as $file){				
					$zip->addFile($file_folder.$file);			// Adding files into zip
				}
				$zip->close();
				if(file_exists($zip_name)){
					// push to download the zip
					header('Content-type: application/zip');
					header('Content-Disposition: attachment; filename="'.$zip_name.'"');
					readfile($zip_name);
					// remove zip file is exists in temp path
					unlink($zip_name);
				}
				
			}else
				$error .= "* Please select file to zip <br/>";
		}else
			$error .= "* You dont have ZIP extension<br/>";
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
		<title>Download As Zip</title>
	
</head>	
<body>
<center>
	<h1>Create ZIP file</h1>
		
		<div style="border:1px solid #aaa; width:371px; padding:10px;">
			<form enctype="multipart/form-data" action="" name="form" method="post">
				Select File
					<input type="file" name="photo" id="photo" /></td>
					<input type="submit" name="submit" id="submit" value="Submit" />
			</form>
		</div>
		<br>
	<form name="zips" method="post">
		<?php if(!empty($error)) { ?>
			<p style=" border:#C10000 1px solid; background-color:#FFA8A8; color:#B00000;padding:8px; width:588px; margin:0 auto 10px;"><?php echo $error; ?></p>
		<?php } ?>
				<table cellpadding="8" cellspacing="1" border="1" width="500">
						<thead>
							<tr>
								<th width="4%" align="center">*</th>
								<th width="80%">File Name</th>
								<th width="11%">Download</th>
							</tr>
						</thead>
						</tbody>
							<?php
							$query=$conn->query("select * from upload order by id desc");
							while($row=$query->fetch()){
								$name=$row['name'];
							?>
							
							<tr>
								<td align="center">
									<input type="checkbox" name="files[]" value="<?php echo $name; ?>" />
								</td>
				
								<td>
									&nbsp;<?php echo $name ;?>
								</td>
								<td>
									<button style="background:green; border-radius:2px; padding:8px; font-weight:bold; color:#fff;><a style="text-decoration:none;" href="download.php?filename=<?php echo $name;?>">Download</a></button>
								</td>
							</tr>
							<?php }?>
							<tr>
								<td colspan="3" align="center">
									<input type="submit" name="createpdf" value="Download as ZIP file" style="background:#A70F58; border-radius:10px; padding:15px; font-weight:bold; color:#fff;" />
								</td>
							</tr>
							
						</tbody>
				</table>
	
	</form>
</center>
</body>
</html>
