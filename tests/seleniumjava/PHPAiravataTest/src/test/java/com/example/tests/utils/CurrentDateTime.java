package com.example.tests.utils;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

/**
 * Created by airavata on 11/24/14.
 */
public class CurrentDateTime {
    private static final String DATE_PATTERN = "yyyy-MM-dd'T'HH:mm:ss";

    public static String getTodayDate() {
        Calendar calendar = Calendar.getInstance();
        Date date = calendar.getTime();
        SimpleDateFormat simpleDateFormat = new SimpleDateFormat(DATE_PATTERN);
        return simpleDateFormat.format(date);
    }
}
