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
import org.openqa.selenium.JavascriptExecutor;
/*
 **********Add an Application Interface**********
 * Created by Eroma on 12/09/14.
 * The script creates an Application Interface in Airavata Application Catalog
 * base URL and sub URL are read from the exp.properties file
*/

public class AddApplicationInterface extends UserLogin {
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
  public void testAddApplicationInterface() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("App Catalog")).click();
    driver.findElement(By.id("interface")).click();
    driver.findElement(By.xpath("//div/div/div/button")).click();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group.required > input[name=\"applicationName\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group.required > input[name=\"applicationName\"]")).sendKeys("APP_INT200");
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > input[name=\"applicationDescription\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > input[name=\"applicationDescription\"]")).sendKeys("TEST_APPLICATION_INTERFACE");
      WebElement element = driver.findElement(By.xpath("(//input[@value='Add Application Module'])[27]"));
      System.out.println(element.getTagName());
      System.out.println("last here ....");
    JavascriptExecutor js=(JavascriptExecutor) driver;
    //js.executeScript("document.getElementsByClassName('hide btn btn-default add-app-module')[0].click();");
    js.executeScript("arguments[0].click();", element);

    new Select(driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-modules > div.input-group > select[name=\"applicationModules[]\"]"))).selectByVisibleText("TEST_APP2014-12-11T03:08:10");
    driver.findElement(By.xpath("(//input[@value='Add Application Input'])[27]")).click();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group.required > input[name=\"inputName[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group.required > input[name=\"inputName[]\"]")).sendKeys("TEST_INPUT1");
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > input[name=\"inputValue[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > input[name=\"inputValue[]\"]")).sendKeys("100.10");
    new Select(driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > select[name=\"inputType[]\"]"))).selectByVisibleText("FLOAT");
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > input[name=\"applicationArgument[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > input[name=\"applicationArgument[]\"]")).sendKeys("NONE");
    new Select(driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > select[name=\"standardInput[]\"]"))).selectByVisibleText("True");
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > textarea[name=\"userFriendlyDescription[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > textarea[name=\"userFriendlyDescription[]\"]")).sendKeys("NONE");
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > textarea[name=\"metaData[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > textarea[name=\"metaData[]\"]")).sendKeys("NONE");
    driver.findElement(By.xpath("(//input[@value='Add Application Output'])[27]")).click();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-outputs > div > div.well > div.form-group.required > input[name=\"outputName[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-outputs > div > div.well > div.form-group.required > input[name=\"outputName[]\"]")).sendKeys("TEST_OUTPUT1");
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-outputs > div > div.well > div.form-group > input[name=\"outputValue[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-interface-block > div.appInterfaceInputs > div.form-group > div.app-outputs > div > div.well > div.form-group > input[name=\"outputValue[]\"]")).sendKeys("TEST_OUTPUT");
    driver.findElement(By.xpath("//input[@value='Create']")).click();
    driver.findElement(By.xpath("(//input[@value=''])[186]")).click();
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
