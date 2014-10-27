package com.example.tests;

import java.util.concurrent.TimeUnit;
import com.example.tests.utils.FileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;
import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;

/*
 **********Create, Modify & Search Project**********
 * Created by Airavata on 9/12/14.
 * The script creates, modifies and searches for the created Project
 * project-name and project-description are read from the config.properties file
 * Modified by Eroma on 10/23/14. Base URL & Sub URL to be read from the config.properties file
*/


public class CreateModifySearchProject extends UserLogin {
  private WebDriver driver;
  private String subUrl;
  private String baseUrl;
  private boolean acceptNextAlert = true;
  private StringBuffer verificationErrors = new StringBuffer();

  @Before
  public void setUp() throws Exception {
    driver = new FirefoxDriver();
    baseUrl = FileReadUtils.readProperty("base.url");
    subUrl = FileReadUtils.readProperty("sub.url");
    driver.manage().timeouts().implicitlyWait(30, TimeUnit.SECONDS);
  }

  @Test
  public void testCreateModifySearchProject() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("Project")).click();
    driver.findElement(By.id("create-project")).click();
    driver.findElement(By.id("project-name")).clear();
    driver.findElement(By.id("project-name")).sendKeys(FileReadUtils.readProperty("project.name"));
      waitTime (500);
    driver.findElement(By.id("project-description")).clear();
    driver.findElement(By.id("project-description")).sendKeys("Test Project");
      waitTime (500);
    driver.findElement(By.name("save")).click();
      waitTime(750);
    driver.findElement(By.cssSelector("span.glyphicon.glyphicon-pencil")).click();
    driver.findElement(By.id("project-description")).clear();
    driver.findElement(By.id("project-description")).sendKeys(FileReadUtils.readProperty("project.description.mod"));
      waitTime (500);
    driver.findElement(By.name("save")).click();
      waitTime(500);
    driver.findElement(By.linkText("Project")).click();
    driver.findElement(By.id("search-projects")).click();
      waitTime(500);
    driver.findElement(By.id("search-value")).clear();
    driver.findElement(By.id("search-value")).sendKeys(FileReadUtils.readProperty("project.name"));
      waitTime(500);
    driver.findElement(By.name("search")).click();
    driver.findElement(By.linkText("View")).click();
      waitTime(500);
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
