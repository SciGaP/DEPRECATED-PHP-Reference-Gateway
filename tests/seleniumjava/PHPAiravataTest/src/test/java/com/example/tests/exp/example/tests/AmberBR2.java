package com.example.tests.exp.example.tests;

import java.util.concurrent.TimeUnit;

import com.example.tests.UserLogin;
import com.example.tests.utils.AppCatFileReadUtils;
import com.example.tests.utils.ExpFileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;
import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.support.ui.Select;

/*
 **********Executing Amber Application on BR2**********
 * Created by Eroma on 9/12/14.
 * The script generates Amber application execution on BR2
 * experiment-name and experiment-description are read from the exp.properties file
 * Modified by Eroma on 10/23/14. Base URL & Sub URL to be read from the exp.properties file
*/

public class AmberBR2 extends UserLogin {
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
  public void testAmberBR2() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("Experiment")).click();
    driver.findElement(By.id("create-experiment")).click();
    driver.findElement(By.id("experiment-name")).clear();
      waitTime (500);
    driver.findElement(By.id("experiment-name")).sendKeys(expName +"Amber-BR2");
    driver.findElement(By.id("experiment-description")).clear();
      waitTime (500);
    driver.findElement(By.id("experiment-description")).sendKeys("Test Experiment");
    new Select(driver.findElement(By.id("project"))).selectByVisibleText(ExpFileReadUtils.readProperty("project.name"));
      waitTime (500);
    new Select(driver.findElement(By.id("application"))).selectByVisibleText("Amber");
      waitTime (500);
    driver.findElement(By.name("continue")).click();
    driver.findElement(By.id("Heat_Restart_File")).sendKeys(ExpFileReadUtils.AMBER_INPUT1);
      waitTime (500);
    driver.findElement(By.id("Parameter_Topology_File")).sendKeys(ExpFileReadUtils.AMBER_INPUT2);
      waitTime (500);
    driver.findElement(By.id("Production_Control_File")).sendKeys(ExpFileReadUtils.AMBER_INPUT3);
      waitTime (500);
    new Select(driver.findElement(By.id("compute-resource"))).selectByVisibleText("bigred2.uits.iu.edu");
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
