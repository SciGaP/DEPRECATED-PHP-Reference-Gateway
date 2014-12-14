package com.example.tests.appcatalog.example.tests;

import java.util.concurrent.TimeUnit;

import com.example.tests.UserLogin;
import com.example.tests.utils.CurrentDateTime;
import com.example.tests.utils.ExpFileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;

import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;
/*
 **********Add an Application Module**********
 * Created by Eroma on 12/09/14.
 * The script creates an Application Module in Airavata Application Catalog
 * base URL, sub URL and Application Name are read from the exp.properties file
*/

    public class AddAppModule extends UserLogin {
    private WebDriver driver;
    private String subUrl;
    private String baseUrl;
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
  public void testAddAppModule() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("App Catalog")).click();
      waitTime (500);
    driver.findElement(By.id("module")).click();
      waitTime (500);
    driver.findElement(By.xpath("//body/div/div/button")).click();
      waitTime (500);
    driver.findElement(By.name("appModuleName")).sendKeys("TEST_APP" + CurrentDateTime.getTodayDate());
      waitTime (500);
    driver.findElement(By.name("appModuleDescription")).sendKeys("TEST APPLICATION MODULE");
      waitTime (500);
    driver.findElement(By.cssSelector("input.btn.btn-primary")).click();
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
