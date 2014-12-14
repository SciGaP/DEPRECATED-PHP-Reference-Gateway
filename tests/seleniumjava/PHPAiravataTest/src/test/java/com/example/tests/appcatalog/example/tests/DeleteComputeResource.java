package com.example.tests;

import java.util.regex.Pattern;
import java.util.concurrent.TimeUnit;

import com.example.tests.utils.ExpFileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;
import static org.hamcrest.CoreMatchers.*;
import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.support.ui.Select;
/*
 **********Modify an Application Module**********
 * Created by Eroma on 12/09/14.
 * The script modifies an existing Application Module in Airavata Application Catalog
 * base URL, sub URL, existing application Name and modified name are read from the exp.properties file
*/

public class DeleteComputeResource extends UserLogin {
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
  public void testDeleteComputeResource() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("Compute Resource")).click();
    driver.findElement(By.id("browse")).click();
//    assertEquals("TEST_COMP_RES_2014-12-10T15:34:56_e842c9dd-03c6-473d-9395-3425fb2ee27d", driver.findElement(By.xpath("(//tr[@id='crDetails']/td[2])[11]")).getText());
      String id_ = "TEST_COMP_RES_2014-12-10T15:34:56_e842c9dd-03c6-473d-9395-3425fb2ee27d";
      for(int h = 1; h < 100; ++h) {
          String findPath = "(//tr[@id='crDetails']/td[2])[" + h + "]";
          String foundid = driver.findElement(By.xpath(findPath)).getText();
          //System.out.println(foundid);
          if (foundid.equals(id_)) {
              String clickId = "(//tr[@id='crDetails']/td[5]/a/span)[" + h + "]";
              //System.out.println(clickId);
              driver.findElement(By.xpath(clickId)).click();
              break;
          }
      }
    //driver.findElement(By.xpath("(//tr[@id='crDetails']/td[5]/a/span)[11]")).click();
    driver.findElement(By.cssSelector("input.btn.btn-danger")).click();
   //TEST_COMP_RES_2014-12-10T15:34:56_e842c9dd-03c6-473d-9395-3425fb2ee27d_MODIFIED
    //driver.findElement(By.linkText("Log out")).click();
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
