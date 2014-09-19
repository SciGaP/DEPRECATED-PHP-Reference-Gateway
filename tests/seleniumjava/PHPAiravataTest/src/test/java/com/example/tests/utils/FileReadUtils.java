package com.example.tests.utils;

import java.io.InputStream;
import java.util.Properties;

/*
 **********Reading Utilities File**********
 * Created by Airavata on 9/15/14.
*/

public class FileReadUtils {
    public static String readProperty (String propertyName) throws Exception{
        Properties prop = new Properties();
        InputStream input = null;
        try{
            String filename = "config.properties";
            input = FileReadUtils.class.getClassLoader().getResourceAsStream(filename);
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
