package com.example.tests.utils;

import java.io.InputStream;
import java.util.Properties;


/*
 **********Reading Utilities File**********
 * Created by Eroma on 12/11/14.
*/

public class AppCatFileReadUtils {


    public static String AMBER_INPUT1 = getLocalPath() + "/AMBER_FILES/02_Heat.rst";
    public static String AMBER_INPUT2 = getLocalPath() + "/AMBER_FILES/prmtop";
    public static String AMBER_INPUT3 = getLocalPath() + "/AMBER_FILES/03_Prod.in";
    public static String INPUT_PATH2 = getLocalPath() + "/Stampede/Trinity/reads.left.fq";

    public static String getLocalPath() {
        try {
            return AppCatFileReadUtils.readProperty("local.path");
        }catch(Exception e) {
            e.printStackTrace();
            throw new RuntimeException(e);
        }
    }

    public static String readProperty (String propertyName) throws Exception{
        Properties prop = new Properties();
        InputStream input = null;
        try{
            String filename = "app.catalog.properties";
            input = ExpFileReadUtils.class.getClassLoader().getResourceAsStream(filename);
            if(input==null){
                throw new Exception("Unable to read the file..");
            }
            //load a properties file from class path, inside static method
            prop.load(input);
            return prop.getProperty(propertyName);
        }catch (Exception e){
            throw new Exception("Error while reading file..", e);
        }
    }
}
