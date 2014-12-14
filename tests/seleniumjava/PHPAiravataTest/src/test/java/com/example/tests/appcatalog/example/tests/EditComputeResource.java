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

public class EditComputeResource extends UserLogin {
    private WebDriver driver;
    private String baseUrl;
    private String subUrl;
    private String computeResourceID;
    private boolean acceptNextAlert = true;
    private StringBuffer verificationErrors = new StringBuffer();

    @Before
    public void setUp() throws Exception {
        driver = new FirefoxDriver();
        baseUrl = ExpFileReadUtils.readProperty("base.url");
        subUrl = ExpFileReadUtils.readProperty("sub.url");
        computeResourceID = ExpFileReadUtils.readProperty("computeResource.ID");
        driver.manage().timeouts().implicitlyWait(30, TimeUnit.SECONDS);
    }

  @Test
  public void testEditComputeResource() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("Compute Resource")).click();
    driver.findElement(By.id("browse")).click();
    assertEquals(computeResourceID, driver.findElement(By.xpath("(//tr[@id='crDetails']/td[2])[81]")).getText());
      //assertEquals("TEST_COMP_RES_2014-12-10T15:34:56_e842c9dd-03c6-473d-9395-3425fb2ee27d", driver.findElement(By.xpath("(//tr[@id='crDetails']/td[2])[29]")).getText());
      //System.out.println("here1 .....");
      String id_ = computeResourceID;
      for(int h = 1; h < 100; ++h) {
          String findPath = "(//tr[@id='crDetails']/td[2])[" + h + "]";
          String foundid = driver.findElement(By.xpath(findPath)).getText();
          //System.out.println(foundid);
          if (foundid.equals(id_)) {
              String clickId = "(//tr[@id='crDetails']/td[3]/a/span)[" + h + "]";
              //System.out.println(clickId);
              driver.findElement(By.xpath(clickId)).click();
              break;
          }
      }
    //driver.findElement(By.xpath("(//tr[@id='crDetails']/td[3]/a/span)[81]")).click();
    driver.findElement(By.name("hostname")).clear();
    driver.findElement(By.name("hostname")).sendKeys("TEST_COMP_RES_MODIFIED" + CurrentDateTime.getTodayDate());
    driver.findElement(By.name("hostaliases[]")).clear();
    driver.findElement(By.name("hostaliases[]")).sendKeys("ALIAS_1_MODIFIED");
    driver.findElement(By.xpath("(//input[@name='hostaliases[]'])[2]")).clear();
    driver.findElement(By.xpath("(//input[@name='hostaliases[]'])[2]")).sendKeys("ALIAS_2_MODIFIED");
    driver.findElement(By.name("ips[]")).clear();
    driver.findElement(By.name("ips[]")).sendKeys("100.125.225.195");
    driver.findElement(By.xpath("(//input[@name='ips[]'])[2]")).clear();
    driver.findElement(By.xpath("(//input[@name='ips[]'])[2]")).sendKeys("2.5.6.200");
    driver.findElement(By.name("step1")).click();
    driver.findElement(By.name("description")).clear();
    driver.findElement(By.name("description")).sendKeys("ADD_COMPUTE_RESOURCE_DESCRIPTION_MODIFIED");
    driver.findElement(By.name("step1")).click();
    driver.findElement(By.linkText("Queues")).click();
    try {
      assertEquals("TEST-NORMAL", driver.findElement(By.linkText("TEST-NORMAL")).getText());
    } catch (Error e) {
      verificationErrors.append(e.toString());
    }
    driver.findElement(By.xpath("//div[@id='accordion']/div/div/h4/div/span")).click();
    driver.findElement(By.cssSelector("#delete-queue > div.modal-dialog > div.modal-content > form > div.modal-footer > button.btn.btn-danger")).click();
    try {
      assertEquals("TEST-VIP", driver.findElement(By.linkText("TEST-VIP")).getText());
    } catch (Error e) {
      verificationErrors.append(e.toString());
    }
    driver.findElement(By.linkText("TEST-VIP")).click();
    driver.findElement(By.name("qdesc")).clear();
    driver.findElement(By.name("qdesc")).sendKeys("TEST QUEUE DESCRIPTION MODIFIED");
    driver.findElement(By.name("qmaxruntime")).clear();
    driver.findElement(By.name("qmaxruntime")).sendKeys("900");
    driver.findElement(By.name("qmaxnodes")).clear();
    driver.findElement(By.name("qmaxnodes")).sendKeys("3500");
    driver.findElement(By.name("qmaxprocessors")).clear();
    driver.findElement(By.name("qmaxprocessors")).sendKeys("640");
    driver.findElement(By.name("qmaxjobsinqueue")).clear();
    driver.findElement(By.name("qmaxjobsinqueue")).sendKeys("9990");
    driver.findElement(By.cssSelector("div.queue > div.form-group > input[name=\"step1\"]")).click();
    driver.findElement(By.xpath("(//button[@type='button'])[4]")).click();
    driver.findElement(By.xpath("(//input[@name='qname'])[3]")).clear();
    driver.findElement(By.xpath("(//input[@name='qname'])[3]")).sendKeys("TEST-NORMAL");
    driver.findElement(By.xpath("(//textarea[@name='qdesc'])[3]")).clear();
    driver.findElement(By.xpath("(//textarea[@name='qdesc'])[3]")).sendKeys("TEST QUEUE DESCRIPTION");
    driver.findElement(By.xpath("(//input[@name='qmaxruntime'])[3]")).clear();
    driver.findElement(By.xpath("(//input[@name='qmaxruntime'])[3]")).sendKeys("25");
    driver.findElement(By.xpath("(//input[@name='qmaxnodes'])[3]")).clear();
    driver.findElement(By.xpath("(//input[@name='qmaxnodes'])[3]")).sendKeys("64");
    driver.findElement(By.xpath("(//input[@name='qmaxprocessors'])[3]")).clear();
    driver.findElement(By.xpath("(//input[@name='qmaxprocessors'])[3]")).sendKeys("100");
    driver.findElement(By.xpath("(//input[@name='qmaxjobsinqueue'])[3]")).clear();
    driver.findElement(By.xpath("(//input[@name='qmaxjobsinqueue'])[3]")).sendKeys("2500");
    driver.findElement(By.xpath("(//input[@name='step1'])[4]")).click();
    driver.findElement(By.linkText("FileSystem")).click();
    driver.findElement(By.name("fileSystems[0]")).clear();
    driver.findElement(By.name("fileSystems[0]")).sendKeys("TEST/LOCAL/CURRENT/HOME/MODIFIED");
    driver.findElement(By.name("fileSystems[1]")).clear();
    driver.findElement(By.name("fileSystems[1]")).sendKeys("TEST/LOCAL/CURRENT/WORK/MODIFIED");
    driver.findElement(By.name("fileSystems[2]")).clear();
    driver.findElement(By.name("fileSystems[2]")).sendKeys("TEST/LOCAL/CURRENT/LOCALTMP/MODIFIED");
    driver.findElement(By.name("fileSystems[3]")).clear();
    driver.findElement(By.name("fileSystems[3]")).sendKeys("TEST/LOCAL/CURRENT/SCRATCH/MODIFIED");
    driver.findElement(By.name("fileSystems[4]")).clear();
    driver.findElement(By.name("fileSystems[4]")).sendKeys("TEST/LOCAL/CURRENT/ARCHIVE/MODIFIED");
    driver.findElement(By.cssSelector("button.btn.btn-prim")).click();
    driver.findElement(By.linkText("Job Submission Interfaces")).click();
    try {
      assertEquals("Job Submission Protocol : LOCAL", driver.findElement(By.cssSelector("form > h4")).getText());
    } catch (Error e) {
      verificationErrors.append(e.toString());
    }
    driver.findElement(By.cssSelector("button.close.delete-jsi")).click();
    driver.findElement(By.xpath("(//button[@type='submit'])[7]")).click();
    new Select(driver.findElement(By.name("securityProtocol"))).selectByVisibleText("USERNAME_PASSWORD");
    driver.findElement(By.name("alternativeSSHHostName")).clear();
    driver.findElement(By.name("alternativeSSHHostName")).sendKeys("HOST4_MODIFIED");
    driver.findElement(By.name("sshPort")).clear();
    driver.findElement(By.name("sshPort")).sendKeys("8888");
    new Select(driver.findElement(By.name("resourceJobManagerType"))).selectByVisibleText("PBS");
    driver.findElement(By.name("pushMonitoringEndpoint")).clear();
    driver.findElement(By.name("pushMonitoringEndpoint")).sendKeys("TEST_END_POINT_MODIFIED");
    driver.findElement(By.name("jobManagerBinPath")).clear();
    driver.findElement(By.name("jobManagerBinPath")).sendKeys("TEST/PATH/HOME/CURRENT/LOCAL/BIN/MODIFIED");
    driver.findElement(By.name("jobManagerCommands[0]")).clear();
    driver.findElement(By.name("jobManagerCommands[0]")).sendKeys("TEST_SUBMISSION_MOD");
    driver.findElement(By.name("jobManagerCommands[1]")).clear();
    driver.findElement(By.name("jobManagerCommands[1]")).sendKeys("TEST_MONITORING_MOD");
    driver.findElement(By.name("jobManagerCommands[2]")).clear();
    driver.findElement(By.name("jobManagerCommands[2]")).sendKeys("TEST_DELETION_MOD");
    driver.findElement(By.name("jobManagerCommands[3]")).clear();
    driver.findElement(By.name("jobManagerCommands[3]")).sendKeys("TEST_CHECK_JOB_MOD");
    driver.findElement(By.name("jobManagerCommands[4]")).clear();
    driver.findElement(By.name("jobManagerCommands[4]")).sendKeys("TEST_SHOW_QUEUE_MOD");
    driver.findElement(By.name("jobManagerCommands[5]")).clear();
    driver.findElement(By.name("jobManagerCommands[5]")).sendKeys("TEST_SHOW_RESERVATION_MOD");
    driver.findElement(By.name("jobManagerCommands[6]")).clear();
    driver.findElement(By.name("jobManagerCommands[6]")).sendKeys("TEST_SHOW_START_MOD");
    driver.findElement(By.cssSelector("div.job-protocol-block > form > div.form-group > button.btn")).click();
    driver.findElement(By.xpath("(//button[@type='button'])[5]")).click();
    new Select(driver.findElement(By.cssSelector("div.job-protocol-block.col-md-12 > form > div.form-group > select[name=\"jobSubmissionProtocol\"]"))).selectByVisibleText("LOCAL");
    new Select(driver.findElement(By.cssSelector("div.resourcemanager-local > div.select-resource-manager-type > div.form-group.required > select[name=\"resourceJobManagerType\"]"))).selectByVisibleText("UGE");
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"pushMonitoringEndpoint\"]")).clear();
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"pushMonitoringEndpoint\"]")).sendKeys("TEST_END_POINT`");
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerBinPath\"]")).clear();
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerBinPath\"]")).sendKeys("TEST_E PATH/HOME/BIN/CURRENT/LOCAL");
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[0]\"]")).clear();
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[0]\"]")).sendKeys("TEST_QSUBM");
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[1]\"]")).clear();
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[1]\"]")).sendKeys("TEST_QMONITOR");
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[2]\"]")).clear();
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[2]\"]")).sendKeys("TEST_DELETION");
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[3]\"]")).clear();
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[3]\"]")).sendKeys("TEST_QCHKJB");
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[4]\"]")).clear();
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[4]\"]")).sendKeys("TEST_QSHWQU");
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[5]\"]")).clear();
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[5]\"]")).sendKeys("TEST_E_QRESERVATION");
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[6]\"]")).clear();
    driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[6]\"]")).sendKeys("TEST_E_QSTART");
    driver.findElement(By.xpath("(//button[@type='submit'])[8]")).click();
    driver.findElement(By.xpath("(//button[@type='button'])[6]")).click();
    driver.findElement(By.name("jsi-priority[]")).clear();
    driver.findElement(By.name("jsi-priority[]")).sendKeys("1");
    driver.findElement(By.xpath("(//input[@name='jsi-priority[]'])[2]")).clear();
    driver.findElement(By.xpath("(//input[@name='jsi-priority[]'])[2]")).sendKeys("2");
    driver.findElement(By.cssSelector("button.btn.btn-update")).click();
    driver.findElement(By.linkText("Data Movement Interfaces")).click();
    try {
      assertEquals("Data Movement Protocol : LOCAL", driver.findElement(By.cssSelector("div.data-movement-block > form > h4")).getText());
    } catch (Error e) {
      verificationErrors.append(e.toString());
    }
    driver.findElement(By.cssSelector("button.close.delete-dmi")).click();
    driver.findElement(By.xpath("(//button[@type='submit'])[8]")).click();
    driver.findElement(By.xpath("(//button[@type='button'])[9]")).click();
    new Select(driver.findElement(By.cssSelector("div.data-movement-block.col-md-12 > form > select[name=\"dataMovementProtocol\"]"))).selectByVisibleText("LOCAL");
    driver.findElement(By.xpath("(//button[@type='submit'])[10]")).click();
    new Select(driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > select[name=\"securityProtocol\"]"))).selectByVisibleText("SSH_KEYS");
    driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > input[name=\"alternativeSSHHostName\"]")).clear();
    driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > input[name=\"alternativeSSHHostName\"]")).sendKeys("HOST5_MODIFIED");
    driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > input[name=\"sshPort\"]")).clear();
    driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > input[name=\"sshPort\"]")).sendKeys("9999");
    driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > button.btn")).click();
    new Select(driver.findElement(By.xpath("(//select[@name='securityProtocol'])[3]"))).selectByVisibleText("KERBEROS");
    driver.findElement(By.name("gridFTPEndPoints[]")).clear();
    driver.findElement(By.name("gridFTPEndPoints[]")).sendKeys("TEST_E-END1_MODIFIED");
    driver.findElement(By.xpath("(//input[@name='gridFTPEndPoints[]'])[2]")).clear();
    driver.findElement(By.xpath("(//input[@name='gridFTPEndPoints[]'])[2]")).sendKeys("TEST_E-END2_MODIFIED");
    driver.findElement(By.xpath("(//button[@type='button'])[14]")).click();
    driver.findElement(By.xpath("(//input[@name='gridFTPEndPoints[]'])[3]")).clear();
    driver.findElement(By.xpath("(//input[@name='gridFTPEndPoints[]'])[3]")).sendKeys("TEST_E-END3_ADD");
    driver.findElement(By.cssSelector("div.form-group > div.form-group > button.btn")).click();
    driver.findElement(By.xpath("(//button[@type='button'])[10]")).click();
    driver.findElement(By.name("dmi-priority[]")).clear();
    driver.findElement(By.name("dmi-priority[]")).sendKeys("1");
    driver.findElement(By.cssSelector("#dmi-priority-form > button.btn.btn-update")).click();
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
