<?php
// http://www.simonbattersby.com/blog/drag-and-drop-with-jquery-ui/
include ("connection.php");
include ("functions.php");

$connection = new connection ();
$conn = $connection->con ();

$functions = new functions ();

if (isset ( $_GET ["action"] )) {
	if ($_GET ["action"] == "AddImage") {
		if (isset ( $_POST )) {
			
			$Display_Order_No = $functions->New_Display_Order_No ( $conn );
			// echo $Display_Order_No." This is insert";
			$title_name = $_POST ["title_name"];
			$image_url_name = basename ( $_FILES ["url"] ["name"] );
			
			$target_dir = "../upload/slider/";
			if (! is_dir ( $target_dir )) {
				mkdir ( $target_dir );
			}
			
			$target_file = $target_dir . basename ( $_FILES ["url"] ["name"] );
			$check = getimagesize ( $_FILES ["url"] ["tmp_name"] );
			$uploadOk = $functions->check_image_is_ok ( $target_file, $check );
			
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
			} else {
				if (move_uploaded_file ( $_FILES ["url"] ["tmp_name"], $target_file )) {
					
					echo "The file " . basename ( $_FILES ["url"] ["name"] ) . " has been uploaded.";
					
					$sql = "INSERT INTO slider(title, url, display_order) VALUES('$title_name','$image_url_name',$Display_Order_No)";
					
					$functions->executeQuery($conn, $sql);
					
				} else {
					echo "Sorry, there was an error uploading your file.";
				}
			}
			$conn->close ();
		}
	} elseif ($_GET ["action"] == "Images") {

		$sql = "SELECT * FROM slider ORDER BY Display_Order ASC";
		$result = $conn->query ( $sql );
		
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
					
		
		?>

<div class="row" id='<?php echo "ID_".$row["Id"]; ?>'>

	<div class="column">
		<div class="wrap">
			<img class="fake" src="<?php echo "upload/slider/".$row["url"]; ?>" />
			<div class="img_wrap">
				<img class="normal" src="<?php echo "upload/slider/".$row["url"]; ?>" />
			</div>
		</div>
	</div>


	<div class="column top">
		<label><h3><?php echo $row["title"]; ?></h3></label> 
		<input type="hidden" name="id" value="<?php echo $row["Id"]; ?>">
		<input type="hidden" name="order" value="<?php echo $row["display_order"]; ?>">  
		<input type="submit" name="delete_image" value="Delete" />
	</div>
</div>

<?php
			}
		}
	}
	elseif ($_GET ["action"] == "save") {
		$newOrder = $_POST['ID'];
		//print_r($newOrder);
		
		$counter = 1;
		
		
		foreach ($newOrder as $recordIDValue) {
			$query = "UPDATE slider SET display_order = " . $counter;
			$query .= " WHERE Id = " . (int)$recordIDValue;
						
			$functions->executeQuery($conn, $query);	
			$counter ++;
		}
		
	
	}
}

?>