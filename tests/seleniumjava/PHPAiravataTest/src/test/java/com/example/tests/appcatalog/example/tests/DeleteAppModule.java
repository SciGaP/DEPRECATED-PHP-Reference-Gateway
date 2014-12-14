package com.example.tests.appcatalog.example.tests;

import java.util.concurrent.TimeUnit;

import com.example.tests.UserLogin;
import com.example.tests.utils.ExpFileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;

import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;
/*
 **********Delete an Application Module**********
 * Created by Eroma on 12/09/14.
 * The script deletes an Application Module in Airavata Application Catalog
 * base URL, sub URL and Application Name are read from the exp.properties file
*/

  public class DeleteAppModule extends UserLogin {
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
  public void testDeleteAppModule() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("App Catalog")).click();
      waitTime (500);
    driver.findElement(By.id("module")).click();
      waitTime (500);
      String id_ = ExpFileReadUtils.readProperty("appModuleName");
      waitTime (500);
      String clickId = null;
      for(int h = 1; h < 100; ++h) {
          String findPath = "(//div[@id='accordion']/div[" + h + "]/div/h4/a)";
          waitTime (500);
          String foundid = driver.findElement(By.xpath(findPath)).getText().trim();
          waitTime (500);
          System.out.println(foundid);
          waitTime (500);
          if (foundid.equals(id_)) {
              clickId = "(//div[@id='accordion']/div[" + h + "]/div/h4/div/span[2])";
              waitTime (500);
              System.out.println(clickId);
              waitTime (500);
              driver.findElement(By.xpath(clickId)).click();
              waitTime (500);
              break;
          }
      }
      if (clickId == null) {
          System.out.println("Searched Application Module Does Not Exists in the System !");
          return;
      }
      driver.findElement(By.cssSelector("input.btn.btn-danger")).click();
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
