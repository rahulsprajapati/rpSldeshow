<?php
class functions {
	function New_Display_Order_No($conn) {
		$Display_Order_No = 0;
		
		$sql = "SELECT * FROM slider";
		$result = $conn->query ( $sql );
		
		if ($result->num_rows > 0) {
			
			$sql1 = "SELECT * FROM slider ORDER BY Display_Order DESC";
			$result1 = $conn->query ( $sql1 );
			
			if ($result1->num_rows > 0) {
				$row = $result1->fetch_assoc ();
				$Display_Order_No = $row ["display_order"];
			}
		}
		$Display_Order_No ++;
		
		return $Display_Order_No;
	}
	
	function check_image_is_ok($target_file, $check) {
		$uploadOk = 1;
		
		$imageFileType = pathinfo ( $target_file, PATHINFO_EXTENSION );
		
		// $check = getimagesize($_FILES["url"]["tmp_name"]);
		if ($check !== false) {
			// echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
		
		if (file_exists ( $target_file )) {
			echo "Sorry, file already exists.";
			$uploadOk = 0;
		}
		
		$imageFileType = strtolower ( $imageFileType );
		if ($imageFileType != "jpeg" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
			echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}
		
		return $uploadOk;
	}
	
	function executeQuery($conn,$query){
		
		if ($conn->query ( $query ) === TRUE) {
			//echo "New record updated successfully";
		} else {
			echo "Error: " . $query . "<br>" . $conn->error;
		}
			
	}
	
}

?>