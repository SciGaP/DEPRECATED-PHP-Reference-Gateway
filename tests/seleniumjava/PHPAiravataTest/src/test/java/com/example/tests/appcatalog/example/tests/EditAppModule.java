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
 **********Modify an Application Module**********
 * Created by Eroma on 12/09/14.
 * The script modifies an existing Application Module in Airavata Application Catalog
 * base URL, sub URL, existing application Name and modified name are read from the exp.properties file
*/
  public class EditAppModule extends UserLogin {
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
  public void testModAppModule() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("App Catalog")).click();
    driver.findElement(By.id("module")).click();
      String id_ = ExpFileReadUtils.readProperty("appModuleName");
      String clickId = null;
      for(int h = 1; h < 100; ++h) {
          String findPath = "(//div[@id='accordion']/div[" + h + "]/div/h4/a)";
          String foundid = driver.findElement(By.xpath(findPath)).getText().trim();
          System.out.println(foundid);
          if (foundid.equals(id_)) {
              clickId = "(//div[@id='accordion']/div[" + h + "]/div/h4/div/span)";
              System.out.println(clickId);
              driver.findElement(By.xpath(clickId)).click();
              break;
          }
      }
      if (clickId == null) {
          System.out.println("Searched Application Module Does Not Exists in the System !");
          return;
      }

    driver.findElement(By.cssSelector("#edit-app-module-block > div.modal-dialog > form > div.modal-content > div.modal-body > #new-app-module-block > div.form-group.required > input[name=\"appModuleName\"]")).clear();
    driver.findElement(By.cssSelector("#edit-app-module-block > div.modal-dialog > form > div.modal-content > div.modal-body > #new-app-module-block > div.form-group.required > input[name=\"appModuleName\"]")).sendKeys("MODIFIED_APP_" + CurrentDateTime.getTodayDate());
    driver.findElement(By.cssSelector("#edit-app-module-block > div.modal-dialog > form > div.modal-content > div.modal-body > #new-app-module-block > div.form-group > input[name=\"appModuleVersion\"]")).clear();
    driver.findElement(By.cssSelector("#edit-app-module-block > div.modal-dialog > form > div.modal-content > div.modal-body > #new-app-module-block > div.form-group > input[name=\"appModuleVersion\"]")).sendKeys("V2.0.0.0");
    driver.findElement(By.cssSelector("#edit-app-module-block > div.modal-dialog > form > div.modal-content > div.modal-body > #new-app-module-block > div.form-group > textarea[name=\"appModuleDescription\"]")).clear();
    driver.findElement(By.cssSelector("#edit-app-module-block > div.modal-dialog > form > div.modal-content > div.modal-body > #new-app-module-block > div.form-group > textarea[name=\"appModuleDescription\"]")).sendKeys("TEST APPLICATION MODULE MODIFY");
    driver.findElement(By.cssSelector("#edit-app-module-block > div.modal-dialog > form > div.modal-content > div.modal-footer > div.form-group > input.btn.btn-primary")).click();
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
