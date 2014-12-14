package com.example.tests.exp.example.tests;

import java.util.concurrent.TimeUnit;

import com.example.tests.UserLogin;
import com.example.tests.utils.ExpFileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;

import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;

/*
 **********Search for Project which has Experiments executed**********
 * Created by Eroma on 9/16/14.
 * The script will search for a project which will list all experiments created; can view the experiment status
 * project-name is read from exp.properties file
 * Changed by Eroma to read the base URL and sub URL from exp.properties file
*/

public class SearchProjectExp extends UserLogin {
  private WebDriver driver;
  private String baseUrl;
  private String subUrl;
  private boolean acceptNextAlert = true;
  private StringBuffer verificationErrors = new StringBuffer();

  @Before
  public void setUp() throws Exception {
    driver = new FirefoxDriver();
    baseUrl = ExpFileReadUtils.readProperty("base.url");
    subUrl = ExpFileReadUtils.readProperty("sub.url");
    driver.manage().timeouts().implicitlyWait(30, TimeUnit.SECONDS);
  }

  @Test
  public void testSearchProjectExp() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("Project")).click();
    driver.findElement(By.id("search-projects")).click();
    driver.findElement(By.id("search-value")).clear();
    driver.findElement(By.id("search-value")).sendKeys(ExpFileReadUtils.readProperty("project.name"));
    driver.findElement(By.name("search")).click();
    driver.findElement(By.linkText("View")).click();
      waitTime (10000);
  }

    private void waitTime(int i) {
        try {
            Thread.sleep(i);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
    }

  @After
  public void tearDown() throws Exception {
    driver.quit();
    String verificationErrorString = verificationErrors.toString();
    if (!"".equals(verificationErrorString)) {
      fail(verificationErrorString);
    }
  }

  private boolean isElementPresent(By by) {
    try {
      driver.findElement(by);
      return true;
    } catch (NoSuchElementException e) {
      return false;
    }
  }

  private boolean isAlertPresent() {
    try {
      driver.switchTo().alert();
      return true;
    } catch (NoAlertPresentException e) {
      return false;
    }
  }

  private String closeAlertAndGetItsText() {
    try {
      Alert alert = driver.switchTo().alert();
      String alertText = alert.getText();
      if (acceptNextAlert) {
        alert.accept();
      } else {
        alert.dismiss();
      }
      return alertText;
    } finally {
      acceptNextAlert = true;
    }
  }
}
