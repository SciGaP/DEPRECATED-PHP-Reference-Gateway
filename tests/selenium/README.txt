README: Useful Info on Selenium
NOTE: The scripts are work-in-progress but can be used. 
I will upload as and when i improve them. Please feel free to improve the existing and also to add any new scripts. 

Selenium is a Firefox plugin which is to record and playback. 
This is useful to run the daily application tests on compute resources. 

1. Download the plugin from http://www.seleniumhq.org/download/ 
2. Then its just a matter of recording the tests on PHP gateway.
   Tip: same set of scripts can be executed in different environments; just need to change the base URL in selenium.
   Currently there is a user support section in the selenium site at; 
   http://www.seleniumhq.org/support/ 
3. To run existing scripts;
	i.Take a copy to your local machine and load all the scripts in to Selenium UI.
	ii. Open script ‘Create Login & Logon’ give your own user information in the script before executing the script.
	iii. Open script ‘Create, Modify, Search Project’ and change the Project name to your own
	iv. In all other scripts give your created project name as the project label.
	v. In scripts where file uploading is required please give your own file location (where you have input files in your machine)
	vi. You can execute all scripts together as a test suite OR can select and run individuals.
4. My wishlist
	i. The existing scripts needs to be updated every time we need to create a new project and experiments under the newly created project. if we could change the script to create the projects giving the sysdate and the experiment to use sysdate as the project name we don't need to change the scripts every time we run them
	ii. Have set of scripts to do load testing through the gateway. If we could link the scripts with a excel sheet which has input data and the script to pick and create experiments; we can have the load testing automated
	iii. Test script to send out notification emails when they fail, so we don't need to keep looking at th scripts.
	iv. Scripts to read input parameters from a excel file/external file 
	v. Scripts to monitor experiment status and fail if status is other than COMPLETE
