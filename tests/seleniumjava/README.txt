###########################################################################
################JAVA TEST SCRIPTS FOR PHP REFERENCE GATEWAY################
###########################################################################



###############Prerequisites To Run The Selenium Java Scripts##############
1. Firefox V 31.0 or less is required for the scripts. 
https://support.mozilla.org/en-US/kb/install-older-version-of-firefox
2. Once Firefox is installed install Selenium IDE 
http://www.seleniumhq.org/download/
3. Copy all PHP-Reference-Gateway application input files from 
https://cwiki.apache.org/confluence/display/AIRAVATA/XSEDE14+Gateway+Tutorial+Application+Input+Files+and+Parameters 
4. Download the Selenium Java test classes from folder 'tests' in 
https://github.com/SciGaP/PHP-Reference-Gateway
###########################################################################


###############Environments available for Running the Scripts##############
Selenium test are executed through PHP-Reference-Gateway by giving the URL
Current Production URLs
Base URL: http://test-drive.airavata.org/PHP-Reference-Gateway
Sub URL: index.php
###########################################################################


###################Script Alterations & Execution Steps###################
1. Change the config file with values prefered for 
		i.	Project Name
		ii. Project Description
		iii.Experiment Name Extension
2. In the config.properties file enter 
	The correct base URL and sub URL of your working PPHP-Reference-Gateway.
	Change the path of the input files (Where the files exists in your local machine) for the applications. 
	The static part of the path is in the config file and the varying part is in each test class.
4. Execution Steps
		i.	CreateUserLogin.java - If you don't have a user already
		ii.	In UserLogin class change the username and password to your own username and password
		iii.CreateModifySearchProject.java - Create A Project, Modify and Search for the Project
		iV.	Run Experiment creation scripts at your prefered sequence 
		V.	At the end run the SearchProjectExp.java to view your experiments (For the ease you can create all your experiments under one Project)
		VI.	UserLogout.java
5. UserLogin is not executable alone
###########################################################################