package com.example.tests.exp.example.tests;

import com.example.tests.UserLogin;
import com.example.tests.utils.AppCatFileReadUtils;
import com.example.tests.utils.ExpFileReadUtils;
import org.junit.*;
import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.support.ui.Select;

import java.util.concurrent.TimeUnit;

import static org.junit.Assert.*;

/*
 **********Executing Amber Application on Stampede**********
 * Created by Eroma on 9/15/14.
 * The script generates Amber application execution on Stampede
 * experiment-name and experiment-description are read from the exp.properties file
 * Modified by Eroma on 10/23/14. Base URL & Sub URL to be read from the exp.properties file
*/

public class AmberStampede extends UserLogin {
  private WebDriver driver;
  private String subUrl;
  private String baseUrl;
  private boolean acceptNextAlert = true;
  private StringBuffer verificationErrors = new StringBuffer();
  private String path = null;
  private String expName = null;

  @Before
  public void setUp() throws Exception {
    driver = new FirefoxDriver();
    baseUrl = ExpFileReadUtils.readProperty("base.url");
    subUrl = ExpFileReadUtils.readProperty("sub.url");
    path = ExpFileReadUtils.readProperty("local.path");
    expName = ExpFileReadUtils.readProperty("experiment.name");
    driver.manage().timeouts().implicitlyWait(30, TimeUnit.SECONDS);

  }

  @Test
  public void testAmberStampede() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("Project")).click();
    driver.findElement(By.linkText("Experiment")).click();
    driver.findElement(By.id("create-experiment")).click();
    driver.findElement(By.id("experiment-name")).clear();
    driver.findElement(By.id("experiment-name")).sendKeys(expName + "Amber-Stampede");
      waitTime (500);
    driver.findElement(By.id("experiment-description")).clear();
    driver.findElement(By.id("experiment-description")).sendKeys("Test Experiment");
      waitTime (500);
    new Select(driver.findElement(By.id("project"))).selectByVisibleText(ExpFileReadUtils.readProperty("project.name"));
      waitTime (500);
    new Select(driver.findElement(By.id("application"))).selectByVisibleText("Amber");
      waitTime (500);
    driver.findElement(By.name("continue")).click();
      waitTime (500);
    driver.findElement(By.id("Heat_Restart_File")).sendKeys(ExpFileReadUtils.AMBER_INPUT1);
      waitTime (500);
    driver.findElement(By.id("Parameter_Topology_File")).sendKeys(ExpFileReadUtils.AMBER_INPUT2);
      waitTime (500);
    driver.findElement(By.id("Production_Control_File")).sendKeys(ExpFileReadUtils.AMBER_INPUT3);
      waitTime (500);
    new Select(driver.findElement(By.id("compute-resource"))).selectByVisibleText("stampede.tacc.xsede.org");
      waitTime (500);
    driver.findElement(By.id("node-count")).clear();
    driver.findElement(By.id("node-count")).sendKeys("1");
    driver.findElement(By.id("cpu-count")).clear();
    driver.findElement(By.id("cpu-count")).sendKeys("4");
    driver.findElement(By.id("wall-time")).clear();
    driver.findElement(By.id("wall-time")).sendKeys("30");
    driver.findElement(By.name("launch")).click();
      waitTime (500);
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
