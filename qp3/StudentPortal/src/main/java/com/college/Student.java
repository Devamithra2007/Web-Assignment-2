package com.college;

import java.util.ArrayList;
import java.util.List;

public class Student {

    private int id;
    private String name;
    private String rollNumber;
    private String course;
    private List<StudentFile> files = new ArrayList<>();

    // Store student details
    public Student(int id, String name, String rollNumber, String course) {
        this.id = id;
        this.name = name;
        this.rollNumber = rollNumber;
        this.course = course;
    }

    public int getId() { return id; }
    public String getName() { return name; }
    public String getRollNumber() { return rollNumber; }
    public String getCourse() { return course; }
    public List<StudentFile> getFiles() { return files; }

    // Associate an uploaded file with this student
    public void addFile(StudentFile file) {
        files.add(file);
    }
}