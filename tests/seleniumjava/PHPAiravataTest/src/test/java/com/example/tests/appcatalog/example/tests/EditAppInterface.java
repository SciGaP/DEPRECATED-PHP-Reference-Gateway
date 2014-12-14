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

public class EditAppInterface extends UserLogin {
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
  public void testEditAppInterface() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("App Catalog")).click();
    driver.findElement(By.id("interface")).click();
    assertEquals("TEST_APP_INT_MODIFIED", driver.findElement(By.linkText("TEST_APP_INT_MODIFIED")).getText());
    driver.findElement(By.xpath("//div[@id='accordion']/div[15]/div/h4/div/span")).click();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group.required > input[name=\"applicationName\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group.required > input[name=\"applicationName\"]")).sendKeys("TEST_APP_INT_MODIFIED");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > input[name=\"applicationDescription\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > input[name=\"applicationDescription\"]")).sendKeys("TEST_APP_INT_MODIFIED");
    new Select(driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-modules > div.input-group > select[name=\"applicationModules[]\"]"))).selectByVisibleText("TEST_APP2014-12-11T03:08:10");
    new Select(driver.findElement(By.xpath("(//select[@name='applicationModules[]'])[24]"))).selectByVisibleText("TEST_APP_MOD");
    driver.findElement(By.xpath("(//button[@type='button'])[129]")).click();
    new Select(driver.findElement(By.xpath("(//select[@name='applicationModules[]'])[25]"))).selectByVisibleText("HooraY");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group.required > input[name=\"inputName[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group.required > input[name=\"inputName[]\"]")).sendKeys("TEST_INPUT1_MODIFIED");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > input[name=\"inputValue[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > input[name=\"inputValue[]\"]")).sendKeys("1000");
    new Select(driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > select[name=\"inputType[]\"]"))).selectByVisibleText("FLOAT");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > input[name=\"applicationArgument[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > input[name=\"applicationArgument[]\"]")).sendKeys("TEST ARGUMENT MODIFIED");
    new Select(driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > select[name=\"standardInput[]\"]"))).selectByVisibleText("False");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > textarea[name=\"userFriendlyDescription[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > textarea[name=\"userFriendlyDescription[]\"]")).sendKeys("TETST MODIFIED");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > textarea[name=\"metaData[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > textarea[name=\"metaData[]\"]")).sendKeys("TEST MODIFIED");
    driver.findElement(By.xpath("(//input[@name='inputName[]'])[37]")).clear();
    driver.findElement(By.xpath("(//input[@name='inputName[]'])[37]")).sendKeys("TEST_INPUT2_MODIFIED");
    driver.findElement(By.xpath("(//input[@name='inputValue[]'])[37]")).clear();
    driver.findElement(By.xpath("(//input[@name='inputValue[]'])[37]")).sendKeys("TEST_MODIFIED");
    new Select(driver.findElement(By.xpath("(//select[@name='inputType[]'])[37]"))).selectByVisibleText("STDOUT");
    driver.findElement(By.xpath("(//input[@name='applicationArgument[]'])[37]")).clear();
    driver.findElement(By.xpath("(//input[@name='applicationArgument[]'])[37]")).sendKeys("TEST_ARGUMENT_MODIFIED");
    new Select(driver.findElement(By.xpath("(//select[@name='standardInput[]'])[37]"))).selectByVisibleText("False");
    driver.findElement(By.xpath("(//textarea[@name='userFriendlyDescription[]'])[37]")).clear();
    driver.findElement(By.xpath("(//textarea[@name='userFriendlyDescription[]'])[37]")).sendKeys("TEST MODIFIED");
    driver.findElement(By.xpath("(//textarea[@name='metaData[]'])[37]")).clear();
    driver.findElement(By.xpath("(//textarea[@name='metaData[]'])[37]")).sendKeys("TEST TEST MODIFIED");
    driver.findElement(By.xpath("(//button[@type='button'])[132]")).click();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group.required > input[name=\"inputName[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group.required > input[name=\"inputName[]\"]")).sendKeys("TEST_INPUT3");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > input[name=\"inputValue[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > input[name=\"inputValue[]\"]")).sendKeys("NONE/TEST/MMP");
    new Select(driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > select[name=\"inputType[]\"]"))).selectByVisibleText("URI");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > input[name=\"applicationArgument[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > input[name=\"applicationArgument[]\"]")).sendKeys("NO ARGUMENTS");
    new Select(driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > select[name=\"standardInput[]\"]"))).selectByVisibleText("True");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > textarea[name=\"userFriendlyDescription[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > textarea[name=\"userFriendlyDescription[]\"]")).sendKeys("JUST TEST APP");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > textarea[name=\"metaData[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-inputs > div > div.well > div.form-group > textarea[name=\"metaData[]\"]")).sendKeys("NO MET DATA");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group.required > input[name=\"outputName[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group.required > input[name=\"outputName[]\"]")).sendKeys("TEST_OUTPUT1_MODIFIED");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > input[name=\"outputValue[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > input[name=\"outputValue[]\"]")).sendKeys("TEST_MODIFIED");
    new Select(driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.well > div.form-group > select[name=\"outputType[]\"]"))).selectByVisibleText("STDERR");
    driver.findElement(By.xpath("(//input[@name='outputName[]'])[34]")).clear();
    driver.findElement(By.xpath("(//input[@name='outputName[]'])[34]")).sendKeys("TEST_OUTPUT2_MODIFIED");
    driver.findElement(By.xpath("(//input[@name='outputValue[]'])[34]")).clear();
    driver.findElement(By.xpath("(//input[@name='outputValue[]'])[34]")).sendKeys("1000");
    new Select(driver.findElement(By.xpath("(//select[@name='outputType[]'])[34]"))).selectByVisibleText("FLOAT");
    driver.findElement(By.xpath("(//button[@type='button'])[156]")).click();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-outputs > div > div.well > div.form-group.required > input[name=\"outputName[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-outputs > div > div.well > div.form-group.required > input[name=\"outputName[]\"]")).sendKeys("TEST_OUTPUT3");
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-outputs > div > div.well > div.form-group > input[name=\"outputValue[]\"]")).clear();
    driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-outputs > div > div.well > div.form-group > input[name=\"outputValue[]\"]")).sendKeys("N/A");
    new Select(driver.findElement(By.cssSelector("div.app-interface-form-content.col-md-12 > div.appInterfaceInputs > div.form-group > div.app-outputs > div > div.well > div.form-group > select[name=\"outputType[]\"]"))).selectByVisibleText("STDOUT");
    driver.findElement(By.xpath("//input[@value='Update']")).click();
    driver.findElement(By.xpath("(//input[@value=''])[153]")).click();
    try {
      assertEquals("TEST_APP_INT_MODIFIED", driver.findElement(By.linkText("TEST_APP_INT_MODIFIED")).getText());
    } catch (Error e) {
      verificationErrors.append(e.toString());
    }
    driver.findElement(By.linkText("TEST_APP_INT_MODIFIED")).click();
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
