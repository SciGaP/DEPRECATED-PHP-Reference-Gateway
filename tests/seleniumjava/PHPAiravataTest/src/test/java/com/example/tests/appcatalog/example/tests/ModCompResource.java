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

public class ModCompResource extends UserLogin {
  private WebDriver driver;
  private String baseUrl;
  private String subUrl;
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
  public void testModCompResource() throws Exception {
    driver.get(baseUrl + subUrl);
      authenticate(driver);
    driver.findElement(By.linkText("Compute Resource")).click();
    driver.findElement(By.id("browse")).click();
      //System.out.println("here1 .....");
    String id_ = "TEST_COMP_RES_2014-12-09T16:07:31_06a36d00-97f9-4e54-8c91-394e1817c08b";
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

    driver.findElement(By.name("hostname")).clear();
    driver.findElement(By.name("hostname")).sendKeys("MODIFIED_COMP_" + CurrentDateTime.getTodayDate());
    driver.findElement(By.name("hostaliases[]")).clear();
    driver.findElement(By.name("hostaliases[]")).sendKeys("CH8");
    driver.findElement(By.xpath("(//input[@name='hostaliases[]'])[2]")).clear();
    driver.findElement(By.xpath("(//input[@name='hostaliases[]'])[2]")).sendKeys("CMPHOST8");
    driver.findElement(By.name("ips[]")).clear();
    driver.findElement(By.name("ips[]")).sendKeys("1.2.3.4.5");
    driver.findElement(By.xpath("(//input[@name='ips[]'])[2]")).clear();
    driver.findElement(By.xpath("(//input[@name='ips[]'])[2]")).sendKeys("100.100.100.100.5");
    driver.findElement(By.name("description")).clear();
    driver.findElement(By.name("description")).sendKeys("TEST_E-Compute_Resource_Description 7.5");
    driver.findElement(By.name("step1")).click();
      driver.findElement(By.linkText("Queues")).click();
      driver.findElement(By.linkText("Queue : TEST-NORMAL")).click();
      driver.findElement(By.name("qdesc")).clear();
      driver.findElement(By.name("qdesc")).sendKeys("TEST_E-DESCRIPTION MOD");
      driver.findElement(By.name("qmaxruntime")).clear();
      driver.findElement(By.name("qmaxruntime")).sendKeys("303");
      driver.findElement(By.name("qmaxnodes")).clear();
      driver.findElement(By.name("qmaxnodes")).sendKeys("1001");
      driver.findElement(By.name("qmaxprocessors")).clear();
      driver.findElement(By.name("qmaxprocessors")).sendKeys("323");
      driver.findElement(By.name("qmaxjobsinqueue")).clear();
      driver.findElement(By.name("qmaxjobsinqueue")).sendKeys("2002");
      driver.findElement(By.cssSelector("div.queue > div.form-group > input[name=\"step1\"]")).click();
     // driver.findElement(By.xpath("//div[@id='accordion']/div[2]/div/h4/div/span")).click();
    //  driver.findElement(By.cssSelector("form > div.modal-footer > button.btn.btn-danger")).click();
      driver.findElement(By.xpath("(//button[@type='button'])[4]")).click();
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group.required > input[name=\"qname\"]")).clear();
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group.required > input[name=\"qname\"]")).sendKeys("TEST-VVIP");
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > textarea[name=\"qdesc\"]")).clear();
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > textarea[name=\"qdesc\"]")).sendKeys("VIP User Queue");
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxruntime\"]")).clear();
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxruntime\"]")).sendKeys("360");
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxnodes\"]")).clear();
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxnodes\"]")).sendKeys("1000");
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxprocessors\"]")).clear();
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxprocessors\"]")).sendKeys("320");
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxjobsinqueue\"]")).clear();
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group > input[name=\"qmaxjobsinqueue\"]")).sendKeys("550");
      driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.form-group > input[name=\"step1\"]")).click();
      //driver.findElement(By.linkText("Queues")).click();
   // driver.findElement(By.xpath("(//button[@type='button'])[4]")).click();
   // driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group.required > input[name=\"qname\"]")).clear();
   // driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.queue > div.form-group.required > input[name=\"qname\"]")).sendKeys("NORMAL");
  //  driver.findElement(By.cssSelector("div.form-group.well > form > div.queue > div.form-group > input[name=\"step1\"]")).click();
    //driver.findElement(By.linkText("Description")).click();
   // driver.findElement(By.name("hostaliases[]")).clear();
  //  driver.findElement(By.name("hostaliases[]")).sendKeys("CH4_MOD_1.234");
   // driver.findElement(By.linkText("FileSystem")).click();
  //  driver.findElement(By.name("fileSystems[0]")).clear();
  //  driver.findElement(By.name("fileSystems[0]")).sendKeys("TEST/LOCAL/CURRENT/HOME/MOD/MOD");
  //  driver.findElement(By.cssSelector("button.btn.btn-prim")).click();
  //  driver.findElement(By.linkText("Description")).click();
  //  driver.findElement(By.name("hostaliases[]")).clear();
  //  driver.findElement(By.name("hostaliases[]")).sendKeys("CH4_MOD_1.234");
  //  driver.findElement(By.name("step1")).click();
      driver.findElement(By.linkText("FileSystem")).click();
      driver.findElement(By.name("fileSystems[0]")).clear();
      driver.findElement(By.name("fileSystems[0]")).sendKeys("TEST_E/LOCAL/CURRENT/VIP/HOME");
      driver.findElement(By.name("fileSystems[1]")).clear();
      driver.findElement(By.name("fileSystems[1]")).sendKeys("TEST_E/LOCAL/CURRENT/VIP/WORK");
      driver.findElement(By.name("fileSystems[2]")).clear();
      driver.findElement(By.name("fileSystems[2]")).sendKeys("TEST_E/LOCAL/CURRENT/VIP/LOCALTMP");
      driver.findElement(By.name("fileSystems[3]")).clear();
      driver.findElement(By.name("fileSystems[3]")).sendKeys("TEST_E/LOCAL/CURRENT/VIP/SCRATCH");
      driver.findElement(By.name("fileSystems[4]")).clear();
      driver.findElement(By.name("fileSystems[4]")).sendKeys("TEST_E/LOCAL/CURRENT/VIP/ARCHIVE");
      driver.findElement(By.cssSelector("button.btn.btn-prim")).click();
      driver.findElement(By.linkText("Job Submission Interfaces")).click();
//      driver.findElement(By.cssSelector("button.close.delete-jsi")).click();
//      driver.findElement(By.xpath("(//button[@type='button'])[23]")).click();
      try {
          assertEquals("Job Submission Protocol : SSH", driver.findElement(By.cssSelector("form > h4")).getText());
      } catch (Error e) {
          verificationErrors.append(e.toString());
      }
      new Select(driver.findElement(By.name("securityProtocol"))).selectByVisibleText("USERNAME_PASSWORD");
      driver.findElement(By.name("alternativeSSHHostName")).clear();
      driver.findElement(By.name("alternativeSSHHostName")).sendKeys("TEST_E-SSH_HOST");
      driver.findElement(By.name("sshPort")).clear();
      driver.findElement(By.name("sshPort")).sendKeys("5555");
      new Select(driver.findElement(By.name("resourceJobManagerType"))).selectByVisibleText("UGE");
      driver.findElement(By.name("pushMonitoringEndpoint")).clear();
      driver.findElement(By.name("pushMonitoringEndpoint")).sendKeys("TEST_E-END_POINT_NULL");
      driver.findElement(By.name("jobManagerBinPath")).clear();
      driver.findElement(By.name("jobManagerBinPath")).sendKeys("TEST_E-PATH/HOME/CURRENT/LOCAL/BIN/VIP");
      driver.findElement(By.name("jobManagerCommands[0]")).clear();
      driver.findElement(By.name("jobManagerCommands[0]")).sendKeys("TEST_E-QSUBMISSION");
      driver.findElement(By.name("jobManagerCommands[1]")).clear();
      driver.findElement(By.name("jobManagerCommands[1]")).sendKeys("TEST_E-QMONITORING");
      driver.findElement(By.name("jobManagerCommands[2]")).clear();
      driver.findElement(By.name("jobManagerCommands[2]")).sendKeys("TEST_E-QDELETION");
      driver.findElement(By.name("jobManagerCommands[3]")).clear();
      driver.findElement(By.name("jobManagerCommands[3]")).sendKeys("TEST_E-QCHECK");
      driver.findElement(By.name("jobManagerCommands[4]")).clear();
      driver.findElement(By.name("jobManagerCommands[4]")).sendKeys("TEST_E-QSHOW");
      driver.findElement(By.name("jobManagerCommands[5]")).clear();
      driver.findElement(By.name("jobManagerCommands[5]")).sendKeys("TEST_E-QRESERVATION");
      driver.findElement(By.name("jobManagerCommands[6]")).clear();
      driver.findElement(By.name("jobManagerCommands[6]")).sendKeys("TEST_E-QSTART");
      driver.findElement(By.cssSelector("div.job-protocol-block > form > div.form-group > button.btn")).click();
      driver.findElement(By.xpath("(//button[@type='button'])[5]")).click();
      new Select(driver.findElement(By.cssSelector("div.job-protocol-block.col-md-12 > form > div.form-group > select[name=\"jobSubmissionProtocol\"]"))).selectByVisibleText("LOCAL");
      new Select(driver.findElement(By.cssSelector("div.resourcemanager-local > div.select-resource-manager-type > div.form-group.required > select[name=\"resourceJobManagerType\"]"))).selectByVisibleText("PBS");
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"pushMonitoringEndpoint\"]")).clear();
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"pushMonitoringEndpoint\"]")).sendKeys("PUSH_END_POINT");
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerBinPath\"]")).clear();
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerBinPath\"]")).sendKeys("HOME/BIN");
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[0]\"]")).clear();
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[0]\"]")).sendKeys("TEST_E QSUB");
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[1]\"]")).clear();
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[1]\"]")).sendKeys("TEST_E QMON");
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[2]\"]")).clear();
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[2]\"]")).sendKeys("TEST_E QDEL");
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[3]\"]")).clear();
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[3]\"]")).sendKeys("TEST_E QCHK");
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[4]\"]")).clear();
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[4]\"]")).sendKeys("TEST_E QSHW");
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[5]\"]")).clear();
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[5]\"]")).sendKeys("TEST_E QRESV");
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[6]\"]")).clear();
      driver.findElement(By.cssSelector("div.resourcemanager-local > div.form-group > input[name=\"jobManagerCommands[6]\"]")).sendKeys("TEST_E-QSTART");
      driver.findElement(By.xpath("(//button[@type='submit'])[7]")).click();
      try {
          assertEquals("Job Submission Protocol : LOCAL", driver.findElement(By.cssSelector("form > h4")).getText());
      } catch (Error e) {
          verificationErrors.append(e.toString());
      }
      driver.findElement(By.cssSelector("button.close.delete-jsi")).click();
      driver.findElement(By.xpath("(//button[@type='submit'])[7]")).click();
      driver.findElement(By.linkText("Data Movement Interfaces")).click();
      try {
          assertEquals("Data Movement Protocol : LOCAL", driver.findElement(By.cssSelector("div.data-movement-block > form > h4")).getText());
      } catch (Error e) {
          verificationErrors.append(e.toString());
      }
      try {
          assertEquals("Data Movement Protocol : SCP", driver.findElement(By.xpath("//div[@id='tab-dataMovement']/div[2]/div[2]/form/h4")).getText());
      } catch (Error e) {
          verificationErrors.append(e.toString());
      }
      driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > input[name=\"alternativeSSHHostName\"]")).clear();
      driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > input[name=\"alternativeSSHHostName\"]")).sendKeys("HOST5");
      driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > input[name=\"sshPort\"]")).clear();
      driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > input[name=\"sshPort\"]")).sendKeys("7777");
      driver.findElement(By.cssSelector("div.data-movement-block > form > div.form-group > button.btn")).click();
      try {
          assertEquals("Data Movement Protocol : GridFTP", driver.findElement(By.xpath("//div[@id='tab-dataMovement']/div[2]/div[3]/form/h4")).getText());
      } catch (Error e) {
          verificationErrors.append(e.toString());
      }
      driver.findElement(By.xpath("(//button[@type='button'])[13]")).click();
      driver.findElement(By.xpath("(//button[@type='button'])[19]")).click();
      //driver.findElement(By.cssSelector("div.data-movement-block > form > h4 > button.close")).click();
      //driver.findElement(By.name("gridFTPEndPoints[]")).clear();
      //driver.findElement(By.name("gridFTPEndPoints[]")).sendKeys("END_POINT1_7.5");
      //driver.findElement(By.xpath("(//input[@name='gridFTPEndPoints[]'])[2]")).clear();
     // driver.findElement(By.xpath("(//input[@name='gridFTPEndPoints[]'])[2]")).sendKeys("END_POINT2 7.5");
      //driver.findElement(By.xpath("(//button[@type='button'])[13]")).click();
      //driver.findElement(By.xpath("(//input[@name='gridFTPEndPoints[]'])[3]")).clear();
      //driver.findElement(By.xpath("(//input[@name='gridFTPEndPoints[]'])[3]")).sendKeys("END_POINT3 7.5");
     // driver.findElement(By.name("gridFTPEndPoints[]")).clear();
      //driver.findElement(By.name("gridFTPEndPoints[]")).sendKeys("");
     // driver.findElement(By.cssSelector("div.form-group > div.form-group > button.btn")).click();
      //driver.findElement(By.cssSelector("div.form-group > div.form-group > button.btn")).click();
     // driver.findElement(By.cssSelector("div.form-group > div.form-group > button.btn")).click();
     // driver.findElement(By.name("gridFTPEndPoints[]")).clear();
     // driver.findElement(By.name("gridFTPEndPoints[]")).sendKeys("END_POINT1 7.5");
     // driver.findElement(By.cssSelector("div.form-group > div.form-group > button.btn")).click();
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
