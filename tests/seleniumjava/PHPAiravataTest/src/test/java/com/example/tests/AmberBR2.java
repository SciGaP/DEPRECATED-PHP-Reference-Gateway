package com.example.tests;

import java.util.concurrent.TimeUnit;
import com.example.tests.utils.FileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;
import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.support.ui.Select;

/*
 **********Executing Amber Application on BR2**********
 * Created by Eroma on 9/12/14.
 * The script generates Amber application execution on BR2
 * experiment-name and experiment-description are read from the config.properties file
 * Modified by Eroma on 10/23/14. Base URL & Sub URL to be read from the config.properties file
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
      baseUrl = FileReadUtils.readProperty("base.url");
      subUrl = FileReadUtils.readProperty("sub.url");
      path = FileReadUtils.readProperty("local.path");
      expName = FileReadUtils.readProperty("experiment.name");
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
    new Select(driver.findElement(By.id("project"))).selectByVisibleText(FileReadUtils.readProperty("project.name"));
      waitTime (500);
    new Select(driver.findElement(By.id("application"))).selectByVisibleText("Amber");
      waitTime (500);
    driver.findElement(By.name("continue")).click();
//    driver.findElement(By.id("Heat_Restart_File")).clear();
    driver.findElement(By.id("Heat_Restart_File")).sendKeys(path + "/BigRed2/Amber/02_Heat.rst");
      waitTime (500);
//    driver.findElement(By.id("Parameter_Topology_File")).clear();
    driver.findElement(By.id("Parameter_Topology_File")).sendKeys(path + "/BigRed2/Amber/prmtop");
      waitTime (500);
//    driver.findElement(By.id("Production_Control_File")).clear();
    driver.findElement(By.id("Production_Control_File")).sendKeys(path + "/BigRed2/Amber/03_Prod.in");
      waitTime (500);
    new Select(driver.findElement(By.id("compute-resource"))).selectByVisibleText("bigred2.uits.iu.edu");
      waitTime (500);
    driver.findElement(By.name("save")).click();
      waitTime (500);
    driver.findElement(By.name("launch")).click();
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
