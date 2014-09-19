package com.example.tests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

/*
 **********User Login to PHP-Reference-Gateway**********
 * Created by Airavata on 9/12/14.
 * User Login in to PHP-Reference-Gateway. This class is called by all other test classes to login into the gateway.
 * Enter your Username & Pwd in this script
*/

public abstract class UserLogin {

    public void     authenticate(WebDriver driver){
        driver.findElement(By.linkText("Log in")).click();
          waitTime (500);
        driver.findElement(By.name("username")).clear();
          waitTime (500);
        driver.findElement(By.name("username")).sendKeys("Daniel");
          waitTime (500);
        driver.findElement(By.name("password")).sendKeys("dan123");
          waitTime (500);
        driver.findElement(By.name("Submit")).click();

    }

    private void waitTime(int i) {
        try {
            Thread.sleep(i);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }

    }
}
