package com.example.tests.appcatalog.example.tests;

import java.util.concurrent.TimeUnit;

import com.example.tests.UserLogin;
import com.example.tests.utils.CurrentDateTime;
import com.example.tests.utils.ExpFileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;
import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.support.ui.Select;

/*
 **********Create a Compute Resource**********
 * Created by Eroma on 11/12/14.
 * The script creates a new Compute Resource with Description, Queues, File system, Job and Data MMovement Interfaces
 * Some input parameters are read from the exp.properties file fore ease of use
 * Modified by Eroma on 12/08/14.
*/

public class AddComputeResource extends UserLogin {
  private WebDriver driver;
  private String subUrl;
  private String baseUrl;
  private String securityProtocol;
  private String resourceJobManagerType;
  private boolean acceptNextAlert = true;
  private StringBuffer verificationErrors = new StringBuffer();

  @Before
  public void setUp() throws Exception {
    driver = new FirefoxDriver();
    baseUrl = ExpFileReadUtils.readProperty("base.url");
    subUrl = ExpFileReadUtils.readProperty("sub.url");
    securityProtocol = ExpFileReadUtils.readProperty("securityProtocol.value");
    resourceJobManagerType = ExpFileReadUtils.readProperty("resourceJobManagerType.value");
    driver.manage().timeouts().implicitlyWait(30, TimeUnit.SECONDS);

  }

  @Test
  public void testAddComputeResource() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("Compute Resource")).click();
    driver.findElement(By.id("register")).click();
    driver.findElement(By.name("hostname")).clear();
      waitTime (500);
   driver.findElement(By.name("hostname")).sendKeys("TEST_COMP_RES_" + CurrentDateTime.getTodayDate());
      waitTime (500);
    driver.findElement(By.name("hostaliases[]")).sendKeys("ALIAS_1");
      waitTime (500);
    driver.findElement(By.xpath("(//button[@type='button'])[2]")).click();
    driver.findElement(By.xpath("(//input[@name='hostaliases[]'])[2]")).sendKeys("ALIAS_2");
      waitTime (500);
    driver.findElement(By.name("ips[]")).sendKeys("100.125.225.95");
      waitTime (500);
    driver.findElement(By.xpath("(//button[@type='button'])[3]")).click();
      waitTime (500);
    driver.findElement(By.xpath("(//input[@name='ips[]'])[2]")).sendKeys("2.5.6.100");
      waitTime (500);
    driver.findElement(By.name("description")).sendKeys("ADD_COMPUTE_RESOURCE_DESCRIPTION");
      waitTime (500);
    driver.findElement(By.name("step1")).click();
      waitTime (500);
    driver.findElement(By.linkText("Queues")).click();
      waitTime (500);
    driver.findElement(By.xpath("(//button[@type='button'])[4]")).click();
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group.required > input[name=\"qname\"]")).sendKeys("TEST-NORMAL");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > textarea[name=\"qdesc\"]")).sendKeys("ADD_QUEUE_DESCRIPTION");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxruntime\"]")).sendKeys("120");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxnodes\"]")).sendKeys("500");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxprocessors\"]")).sendKeys("32");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxjobsinqueue\"]")).sendKeys("1000");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.form-group > input[name=\"step1\"]")).click();
    driver.findElement(By.xpath("(//button[@type='button'])[4]")).click();
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group.required > input[name=\"qname\"]")).sendKeys("TEST-VIP");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > textarea[name=\"qdesc\"]")).sendKeys("TEST_DESCRIPTION");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxruntime\"]")).sendKeys("90");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxnodes\"]")).sendKeys("350");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxprocessors\"]")).sendKeys("64");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxjobsinqueue\"]")).sendKeys("999");
      waitTime (500);
    driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.form-group > input[name=\"step1\"]")).click();
      waitTime (500);
    driver.findElement(By.linkText("FileSystem")).click();
    driver.findElement(By.name("fileSystems[0]")).sendKeys("TEST/LOCAL/CURRENT/HOME");
      waitTime(500);
    driver.findElement(By.name("fileSystems[1]")).sendKeys("TEST/LOCAL/CURRENT/WORK");
      waitTime(500);
    driver.findElement(By.name("fileSystems[2]")).sendKeys("TEST/LOCAL/CURRENT/LOCALTMP");
      waitTime(500);
    driver.findElement(By.name("fileSystems[3]")).sendKeys("TEST/LOCAL/CURRENT/SCRATCH");
      waitTime(500);
    driver.findElement(By.name("fileSystems[4]")).sendKeys("TEST/LOCAL/CURRENT/ARCHIVE");
      waitTime(500);
    driver.findElement(By.cssSelector("button.btn.btn-prim")).click();
    driver.findElement(By.linkText("Job Submission Interfaces")).click();
    driver.findElement(By.xpath("(//button[@type='button'])[5]")).click();
    new Select(driver.findElement(By.cssSelector("div.job-protocol-block.col-md-12 > form > div.form-group > select[name=\"jobSubmissionProtocol\"]"))).selectByVisibleText("LOCAL");
      waitTime (500);
    new Select(driver.findElement(By.cssSelector("div.resourcemanager-local > div.select-resource-manager-type > div.form-group.required > select[name=\"resourceJobManagerType\"]"))).selectByVisibleText(resourceJobManagerType);
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"pushMonitoringEndpoint\"]")).sendKeys("TEST_END_POINT");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerBinPath\"]")).sendKeys("TEST/PATH/HOME/CURRENT/BIN");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[0]\"]")).sendKeys("TEST_QSUB");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[1]\"]")).sendKeys("TEST_QMON");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[2]\"]")).sendKeys("TEST_QDEL");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[3]\"]")).sendKeys("TEST_QCHK");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[4]\"]")).sendKeys("TEST_QSHW");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[5]\"]")).sendKeys("TEST_QRESV");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[6]\"]")).sendKeys("TEST_QSTRT");
      waitTime (500);
    driver.findElement(By.xpath("(//button[@type='submit'])[5]")).click();
      //waitTime (500);
    driver.findElement(By.xpath("(//button[@type='button'])[5]")).click();
    new Select(driver.findElement(By.cssSelector("div.job-protocol-block.col-md-12 > form > div.form-group > select[name=\"jobSubmissionProtocol\"]"))).selectByVisibleText("SSH");
      waitTime (500);
    new Select(driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group.required > select[name=\"securityProtocol\"]"))).selectByVisibleText(securityProtocol);
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group.addedScpValue > input[name=\"alternativeSSHHostName\"]")).sendKeys("HOST4");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group.addedScpValue > input[name=\"sshPort\"]")).sendKeys("7777");
      waitTime (500);
    new Select(driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.select-resource-manager-type > div.form-group.required > select[name=\"resourceJobManagerType\"]"))).selectByVisibleText(resourceJobManagerType);
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group > input[name=\"pushMonitoringEndpoint\"]")).sendKeys("TEST_END_POINT");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group > input[name=\"jobManagerBinPath\"]")).sendKeys("TEST/PATH/HOME/CURRENT/LOCAL/BIN");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group > input[name=\"jobManagerCommands[0]\"]")).sendKeys("TEST_SUBMISSION");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group > input[name=\"jobManagerCommands[1]\"]")).sendKeys("TEST_MONITORING");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group > input[name=\"jobManagerCommands[2]\"]")).sendKeys("TEST_DELETION");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group > input[name=\"jobManagerCommands[3]\"]")).sendKeys("TEST_CHECK_JOB");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group > input[name=\"jobManagerCommands[4]\"]")).sendKeys("TEST_SHOW_QUEUE");
      waitTime (500);
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group > input[name=\"jobManagerCommands[5]\"]")).sendKeys("TEST_SHOW_RESERVATION");
    driver.findElement(By.cssSelector("div.resourcemanager-ssh > div.form-group > input[name=\"jobManagerCommands[6]\"]")).sendKeys("TEST_SHOW_START");
      waitTime (500);
    driver.findElement(By.xpath("(//button[@type='submit'])[6]")).click();
      waitTime (500);
    /*driver.findElement(By.xpath("(//button[@type='button'])[6]")).click();
    //driver.findElement(By.xpath("(//input[@name='jsi-priority[]'])[1]")).clear();
   // driver.findElement(By.xpath("(//input[@name='jsi-priority[]'])[1]")).sendKeys("0");
    //  waitTime (500);
   // driver.findElement(By.xpath("(//input[@name='jsi-priority[]'])[2]")).clear();
   // driver.findElement(By.xpath("(//input[@name='jsi-priority[]'])[2]")).sendKeys("1");
    //  waitTime (500);
   // driver.findElement(By.cssSelector("button.btn.btn-update")).click();
      waitTime (500);
    */driver.findElement(By.linkText("Data Movement Interfaces")).click();
      waitTime (500);
    driver.findElement(By.xpath("(//button[@type='button'])[9]")).click();
    new Select(driver.findElement(By.cssSelector("div.data-movement-block.col-md-12 > form > select[name=\"dataMovementProtocol\"]"))).selectByVisibleText("LOCAL");
      waitTime (500);
    driver.findElement(By.xpath("(//button[@type='submit'])[8]")).click();
      waitTime (500);
    driver.findElement(By.xpath("(//button[@type='button'])[9]")).click();
    new Select(driver.findElement(By.cssSelector("div.data-movement-block.col-md-12 > form > select[name=\"dataMovementProtocol\"]"))).selectByVisibleText("SCP");
      waitTime (500);
    new Select(driver.findElement(By.cssSelector("div.dataprotocol-scp > div.form-group.required > select[name=\"securityProtocol\"]"))).selectByVisibleText(securityProtocol);
      waitTime (500);
    driver.findElement(By.cssSelector("div.dataprotocol-scp > div.form-group.addedScpValue > input[name=\"alternativeSSHHostName\"]")).sendKeys("HOST5");
      waitTime (500);
    driver.findElement(By.cssSelector("div.dataprotocol-scp > div.form-group.addedScpValue > input[name=\"sshPort\"]")).sendKeys("8888");
      waitTime (500);
    driver.findElement(By.xpath("(//button[@type='submit'])[8]")).click();
      waitTime (500);
      driver.findElement(By.xpath("(//button[@type='button'])[9]")).click();
      waitTime(500);
      new Select(driver.findElement(By.cssSelector("div.data-movement-block.col-md-12 > form > select[name=\"dataMovementProtocol\"]"))).selectByVisibleText("GridFTP");
      waitTime (500);
     // driver.findElement(By.cssSelector("div.dataprotocol-gridftp > div.form-group.required > input[name=\"gridFTPEndPoints[]\"]")).clear();
      waitTime (500);
      driver.findElement(By.cssSelector("div.dataprotocol-gridftp > div.form-group.required > input[name=\"gridFTPEndPoints[]\"]")).sendKeys("TEST_E-END1");
      waitTime (500);
      driver.findElement(By.xpath("(//button[@type='button'])[16]")).click();
      waitTime(500);
      driver.findElement(By.xpath("(//input[@name='gridFTPEndPoints[]'])[3]")).sendKeys("TEST_E-END2");
      waitTime (500);
      driver.findElement(By.xpath("(//button[@type='submit'])[9]")).click();
      waitTime (500);
      driver.findElement(By.xpath("(//button[@type='button'])[10]")).click();
      waitTime (500);
      driver.findElement(By.name("dmi-priority[]")).clear();
      driver.findElement(By.name("dmi-priority[]")).sendKeys("1");
      driver.findElement(By.xpath("(//input[@name='dmi-priority[]'])[2]")).clear();
      driver.findElement(By.xpath("(//input[@name='dmi-priority[]'])[2]")).sendKeys("2");
      driver.findElement(By.xpath("(//input[@name='dmi-priority[]'])[3]")).clear();
      driver.findElement(By.xpath("(//input[@name='dmi-priority[]'])[3]")).sendKeys("3");
      driver.findElement(By.cssSelector("#dmi-priority-form > button.btn.btn-update")).click();
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
