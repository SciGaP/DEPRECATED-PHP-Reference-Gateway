package com.example.tests.appcatalog.example.tests;

import java.util.regex.Pattern;
import java.util.concurrent.TimeUnit;

import com.example.tests.UserLogin;
import com.example.tests.utils.ExpFileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;
import static org.hamcrest.CoreMatchers.*;
import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.support.ui.Select;

/*
 **********Add an Application Module**********
 * Created by Eroma on 12/09/14.
 * The script creates an Application Module in Airavata Application Catalog
 * base URL, sub URL and Application Name are read from the exp.properties file
*/

public class DeleteAppInterface extends UserLogin {
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
  public void testDeleteAppInterface() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("App Catalog")).click();
    driver.findElement(By.id("interface")).click();
    try {
      assertEquals(ExpFileReadUtils.readProperty("appInterface.name"), driver.findElement(By.linkText(ExpFileReadUtils.readProperty("appInterface.name"))).getText());
    } catch (Error e) {
      verificationErrors.append(e.toString());
    }

      String id_ = ExpFileReadUtils.readProperty("appInterface.name");
      String clickId = null;
      for(int h = 1; h < 100; ++h) {
          String findPath = "(//div[@id='accordion']/div[" + h + "]/div/h4)";
          String foundid = driver.findElement(By.xpath(findPath)).getText().trim();
          System.out.println(foundid);
          if (foundid.equals(id_)) {
              clickId = "(//div[@id='accordion']/div[" + h + "]/div/h4/div/span[2])";
              System.out.println(clickId);
              driver.findElement(By.xpath(clickId)).click();
              break;
          }
      }
      if (clickId == null) {
          System.out.println("Searched Application Interface Does Not Exists in the System !");
          return;
      }

     // driver.findElement(By.xpath("//div[@id='accordion']/div[15]/div/h4/div/span[2]")).click();
    driver.findElement(By.cssSelector("input.btn.btn-danger")).click();
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
