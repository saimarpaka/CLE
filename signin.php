<?php
//signin.php
include 'connection.php';
include 'header.php';

echo '<h3>Sign in</h3><br />';

//first, check if the user is already signed in. If that is the case, there is no need to display this page
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
{
	echo 'You are already signed in, you can <a href="signout.php">sign out</a> if you want.';
}
else
{
	if($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		/*the form hasn't been posted yet, display it
		  note that the action="" will cause the form to post to the same page it is on */
		echo '<form method="post" action="">
			Username: <input type="text" name="user_name" required/><br />
				<br>
			Password: <input type="password" name="user_pass" required><br />
			<br><input type="submit" value="Sign in" />
		 </form>';
	}
	else
	{
		/* so, the form has been posted, we'll process the data in three steps:
			1.	Check the data
			2.	Let the user refill the wrong fields (if necessary)
			3.	Varify if the data is correct and return the correct response
		*/
		$errors = array(); /* declare the array for later use */
		
		if(!isset($_POST['user_name']))
		{
			$errors[] = 'The username field must not be empty.';
		}
		
		if(!isset($_POST['user_pass']))
		{
			$errors[] = 'The password field must not be empty.';
		}
		
		if(!empty($errors)) /*check for an empty array, if there are errors, they're in this array (note the ! operator)*/
		{
			echo 'Uh-oh.. a couple of fields are not filled in correctly..<br /><br />';
			echo '<ul>';
			foreach($errors as $key => $value) /* walk through the array so all the errors get displayed */
			{
				echo '<li>' . $value . '</li>'; /* this generates a nice error list */
			}
			echo '</ul>';
		}
		else
		{
			//the form has been posted without errors, so save it
			//notice the use of mysql_real_escape_string, keep everything safe!
			//also notice the sha1 function which hashes the password
			$sql = "SELECT 
						user_id,
						user_name,
						user_level,
						user_pass,
						user_email,
						is_reported
					FROM
						users
					WHERE
						user_name = '" . mysql_real_escape_string($_POST['user_name']) . "'
							
					AND
						user_pass = '" . sha1($_POST['user_pass']) . "'";
						
						
			$result = mysql_query($sql);
			if(!$result)
			{
				//something went wrong, display the error
				echo 'Invalid UserName / Password';
				//echo mysql_error(); //debugging purposes, uncomment when needed
			}
			else
			{
				//the query was successfully executed, there are 2 possibilities
				//1. the query returned data, the user can be signed in
				//2. the query returned an empty result set, the credentials were wrong
				if(mysql_num_rows($result) == 0 )
				{
					echo 'You have supplied a wrong user/password combination. Please try again.';
				}
				else
				{
					//set the $_SESSION['signed_in'] variable to TRUE
					$_SESSION['signed_in'] = true;
					
					//we also put the user_id and user_name values in the $_SESSION, so we can use it at various pages
					while($row = mysql_fetch_assoc($result))
					{
						$_SESSION['user_id'] 	= $row['user_id'];
						$_SESSION['user_name'] 	= $row['user_name'];
						$_SESSION['user_level'] = $row['user_level'];
						$_SESSION['user_pass']	= $row['user_pass'];
						$_SESSION['user_email'] = $row['user_email'];
						$_SESSION['is_reported'] = $row['is_reported'];
					}
					if($_SESSION['user_level'] == 0  || $_SESSION['user_level'] == 2 || $_SESSION['user_level'] == 5)
					{
					echo 'Welcome, ' . $_SESSION['user_name'] .'  <br />';
					echo '<script>location.href="index.php"</script> ';
					}
					else 
					if($_SESSION['user_level'] == 6 || $_SESSION['user_level'] == 1)
					{
						echo 'Welcome, ' . $_SESSION['user_name'] .'  <br />';
						echo '<script>location.href="index1.php"</script> ';
					}
					else
					{
						if($_SESSION['user_level'] == 3)
						{
						echo 'This is a Deleted Account';
						$_SESSION['signed_in'] = NULL;
						echo '<script>location.href="index.php"</script> ';
						}
						else 
						{
							if($_SESSION['user_level'] == 4)
							{
						//echo '<div style="float:left;width:75%;color:black;">';
								echo '<script>location.href="reactivate.php?id=0"</script> ';
					
				}
				
					}
					
					}
					
						
					
						}
						
					}
				}
			}
		}
	


include 'footer.php';
?>