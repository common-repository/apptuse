<html>
	<head>
		<style>
			.btn {
				    background: #0073b7;
				    color: white!important;
				    font-size: 15px;
				    line-height: 50px;
				    text-transform: none;
				    margin-top: 20px;
				    font-weight: 600;
				    padding: 20px;
				    letter-spacing: 1px;
				    font-family: "Brandon Text W01 Regular",Helvetica,sans-serif;
				    text-decoration: none;
				    border-color: transparent!important;
				    border-radius: 4px;
			}
			.img-logo{
				margin-top: 60px;
			    height: 90px;
			    margin-bottom: 60px;
			}
		</style>
	</head>
	<body>

		<center>
		 	<img class="img-logo" src ="<?php echo plugin_dir_url( __FILE__ ); ?>assets/logo-dark.png"/>
			<br/>
			<a class="btn" target="_blank" href="https://app.apptuse.com/plugin-login-url?token=<?php echo $token;?>&email=<?php echo $email;?>"> Manage Apptuse </a>
		</center>
	</body>
</html>