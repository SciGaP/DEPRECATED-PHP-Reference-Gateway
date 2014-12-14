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

public class AddAppDeployment extends UserLogin {
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
  public void testAddAppDeployment() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("App Catalog")).click();
    driver.findElement(By.id("deployment")).click();
    driver.findElement(By.xpath("//div/div/div/button")).click();
    new Select(driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group.required > select[name=\"appModuleId\"]"))).selectByVisibleText("TEST_APP2014-12-11T03:08:10");
    new Select(driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group.required > select[name=\"computeHostId\"]"))).selectByVisibleText("TEST_COMP_RES_2014-12-09T15:17:38");
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group.required > input[name=\"executablePath\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group.required > input[name=\"executablePath\"]")).sendKeys("TEST/LOCAL/BIN/");
    new Select(driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group.required > select[name=\"parallelism\"]"))).selectByVisibleText("MPI");
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > textarea[name=\"appDeploymentDescription\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > textarea[name=\"appDeploymentDescription\"]")).sendKeys("TEST_APPLICATION_DEPLOYMENT");
    driver.findElement(By.xpath("(//button[@type='add-load-cmd'])[338]")).click();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-load-cmds > input[name=\"moduleLoadCmds[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-load-cmds > input[name=\"moduleLoadCmds[]\"]")).sendKeys("LOAD_CMD");
    driver.findElement(By.xpath("(//button[@type='button'])[339]")).click();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-lib-prepend-paths > div.col-md-12.well > input[name=\"libraryPrependPathName[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-lib-prepend-paths > div.col-md-12.well > input[name=\"libraryPrependPathName[]\"]")).sendKeys("APP");
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-lib-prepend-paths > div.col-md-12.well > input[name=\"libraryPrependPathValue[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-lib-prepend-paths > div.col-md-12.well > input[name=\"libraryPrependPathValue[]\"]")).sendKeys("MAIN");
    driver.findElement(By.xpath("(//button[@type='button'])[340]")).click();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-lib-append-paths > div.col-md-12.well > input[name=\"libraryAppendPathName[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-lib-append-paths > div.col-md-12.well > input[name=\"libraryAppendPathName[]\"]")).sendKeys("PREP");
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-lib-append-paths > div.col-md-12.well > input[name=\"libraryAppendPathValue[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-lib-append-paths > div.col-md-12.well > input[name=\"libraryAppendPathValue[]\"]")).sendKeys("SECOND");
    driver.findElement(By.xpath("(//button[@type='button'])[341]")).click();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-environments > div.col-md-12.well > input[name=\"environmentName[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-environments > div.col-md-12.well > input[name=\"environmentName[]\"]")).sendKeys("TEST_ENV");
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-environments > div.col-md-12.well > input[name=\"environmentValue[]\"]")).clear();
    driver.findElement(By.cssSelector("div.create-app-deployment-block > div.form-group > div.show-environments > div.col-md-12.well > input[name=\"environmentValue[]\"]")).sendKeys("TEST_ENV");
    driver.findElement(By.cssSelector("#create-app-deployment-block > div.modal-dialog > form > div.modal-content > div.modal-footer > div.form-group > input.btn.btn-primary")).click();
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
