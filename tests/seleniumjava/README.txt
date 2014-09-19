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
###########################################################################



###################Script Alterations & Execution Steps###################
1. Change the config file with values prefered for 
		i.	Project Name
		ii. Project Description
		iii.Experiment Name Extension
2. In the config file change the path of the input files for the applications. 
3. Execution order of the scripts
		i.	CreateUserLogin.java - If you don't have a user already
		ii.	CreateModifySearchProject.java - Create A Project, Modify and Search for the Project
		iii.Run Experiment creation scripts at your prefered sequence 
		iV.	At the end run the SearchProjectExp.java to view your experiments (For the ease you can create all your experiments under one Project)
		V.	UserLogout.java
4. UserLogin is not executable alone
###########################################################################