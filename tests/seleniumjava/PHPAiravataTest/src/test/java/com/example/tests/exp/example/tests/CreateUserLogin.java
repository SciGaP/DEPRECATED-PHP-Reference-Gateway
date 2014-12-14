package com.example.tests.exp.example.tests;

import java.util.concurrent.TimeUnit;

import com.example.tests.utils.ExpFileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;

import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;

public class CreateUserLogin {
  private WebDriver driver;
  private String baseUrl;
  private boolean acceptNextAlert = true;
  private StringBuffer verificationErrors = new StringBuffer();

  @Before
  public void setUp() throws Exception {
    driver = new FirefoxDriver();
    baseUrl = ExpFileReadUtils.readProperty("base.url");
    driver.manage().timeouts().implicitlyWait(30, TimeUnit.SECONDS);
  }

  @Test
  public void testCreateUserLogin() throws Exception {
    driver.get(baseUrl);
    driver.findElement(By.linkText("Create account")).click();
    driver.findElement(By.id("username")).sendKeys("Henry56");
      waitTime(500);
    driver.findElement(By.id("password")).sendKeys("hen123");
      waitTime(500);
    driver.findElement(By.id("confirm_password")).sendKeys("hen123");
      waitTime(500);
    driver.findElement(By.id("email")).sendKeys("henry56@gmail.com");
      waitTime(500);
    driver.findElement(By.id("first_name")).sendKeys("Henry");
      waitTime(500);
    driver.findElement(By.id("last_name")).sendKeys("Pens");
      waitTime(500);
    driver.findElement(By.id("organization")).sendKeys("IU");
      waitTime(500);
    driver.findElement(By.id("address")).sendKeys("IU, CIB Building Bloomington");
      waitTime(500);
    driver.findElement(By.id("country")).sendKeys("USA");
      waitTime(500);
    driver.findElement(By.id("telephone")).sendKeys("812 400 5000");
      waitTime(500);
    driver.findElement(By.id("mobile")).sendKeys("812 333 9999");
      waitTime(500);
    driver.findElement(By.id("im")).sendKeys("-");
      waitTime(500);
    driver.findElement(By.id("url")).sendKeys("-");
      waitTime(500);
    driver.findElement(By.name("Submit")).click();
      waitTime(5000);
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
