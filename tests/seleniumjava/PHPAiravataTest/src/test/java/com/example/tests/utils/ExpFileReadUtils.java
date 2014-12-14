package com.example.tests.utils;

import java.io.InputStream;
import java.util.Enumeration;
import java.util.Properties;

/*
 **********Reading Utilities File**********
 * Created by Airavata on 9/15/14.
*/

public class ExpFileReadUtils {
    public static String readProperty (String propertyName) throws Exception{
        Properties prop = new Properties();
        InputStream input = null;
        try{
            String filename = "exp.properties";
            input = ExpFileReadUtils.class.getClassLoader().getResourceAsStream(filename);
            if(input==null){
                throw new Exception("Unable to read the file..");
            }
            //load a properties file from class path, inside static method
            prop.load(input);

            Enumeration e = prop.propertyNames();

            while (e.hasMoreElements()) {
                String key = (String) e.nextElement();
                System.out.println(key + " -- " + prop.getProperty(key));
            }

            return prop.getProperty(propertyName);
        }catch (Exception e){
            throw new Exception("Error while reading file..", e);
        }
    }
    public static String AMBER_INPUT1 = getLocalPath() + "/AMBER_FILES/02_Heat.rst";
    public static String AMBER_INPUT2 = getLocalPath() + "/AMBER_FILES/prmtop";
    public static String AMBER_INPUT3 = getLocalPath() + "/AMBER_FILES/03_Prod.in";
    public static String ESPRESSO_INPUT1 = getLocalPath() + "/ESPRESSO_FILES/Al.sample.in";
    public static String ESPRESSO_INPUT2 = getLocalPath() + "/ESPRESSO_FILES/Al.pz-vbc.UPF";
    public static String GROMACS_INPUT1 = getLocalPath() + "/GROMMACS_FILES/pdb1y6l.gro";
    public static String GROMACS_INPUT2 = getLocalPath() + "/GROMMACS_FILES/pdb1y6l-EM-vacuum.tpr";
    public static String LAMMPS_INPUT1 = getLocalPath() + "/LAMMPS_FILES/in.friction";
    public static String NWCHEM_INPUT1 = getLocalPath() + "/NWCHEM_FILES/water.nw";
    public static String PHASTA_INPUT1 = getLocalPath() + "/PHASTA_FILES/geom.sms";
    public static String PHASTA_INPUT2 = getLocalPath() + "/PHASTA_FILES/geom.xmt_txt";
    public static String PHASTA_INPUT3 = getLocalPath() + "/PHASTA_FILES/geom.smd";
    public static String PHASTA_INPUT4 = getLocalPath() + "/PHASTA_FILES/solver.inp";
    public static String TRINITY_INPUT1 = getLocalPath() + "/TRINITY_FILES/reads.left.fq";
    public static String TRINITY_INPUT2 = getLocalPath() + "/TRINITY_FILES/reads.right.fq";
    public static String WRF_INPUT1 = getLocalPath() + "/WRF_FILES/namelist.input";
    public static String WRF_INPUT2 = getLocalPath() + "/WRF_FILES/wrfbdy_d01";
    public static String WRF_INPUT3 = getLocalPath() + "/WRF_FILES/wrfinput_d01";

    public static String getLocalPath() {
        try {
            return ExpFileReadUtils.readProperty("local.path");
        }catch(Exception e) {
            e.printStackTrace();
            throw new RuntimeException(e);
        }
    }
}

