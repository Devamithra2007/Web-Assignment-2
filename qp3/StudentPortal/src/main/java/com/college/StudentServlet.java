package com.college;

import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;

@WebServlet("/students")
public class StudentServlet extends HttpServlet {

    protected void doGet(HttpServletRequest request,
            HttpServletResponse response)
            throws ServletException, IOException {

        // Allow access only to logged-in users
        HttpSession session = request.getSession(false);

        if (session == null ||
            !Boolean.TRUE.equals(session.getAttribute("loggedIn"))) {
            response.sendRedirect(
                request.getContextPath() + "/login.jsp");
            return;
        }

        // Use a map to group each student's files under one student
        Map<Integer, Student> studentMap = new LinkedHashMap<>();

        String sql = "SELECT s.id, s.name, s.roll_number, s.course, " +
            "f.id AS file_id, f.original_name, f.file_type " +
            "FROM students s LEFT JOIN student_files f " +
            "ON s.id = f.student_id ORDER BY s.id, f.id";

        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {

            // Loop through every student and their associated files
            while (rs.next()) {
                int id = rs.getInt("id");

                // Create each student object only once
                if (!studentMap.containsKey(id)) {
                    studentMap.put(id, new Student(
                        id,
                        rs.getString("name"),
                        rs.getString("roll_number"),
                        rs.getString("course")
                    ));
                }

                // Add file metadata if the student has uploaded a file
                int fileId = rs.getInt("file_id");

                if (!rs.wasNull()) {
                    studentMap.get(id).addFile(new StudentFile(
                        fileId,
                        rs.getString("original_name"),
                        rs.getString("file_type")
                    ));
                }
            }

            // Send the grouped student list to the JSP page
            List<Student> students =
                new ArrayList<>(studentMap.values());

            request.setAttribute("students", students);
            request.getRequestDispatcher("/students.jsp")
                   .forward(request, response);

        } catch (Exception e) {
            // Log the actual error on the server
            getServletContext().log("Unable to retrieve student records", e);
            response.sendError(
                HttpServletResponse.SC_INTERNAL_SERVER_ERROR,
                "Unable to load student records.");
        }
    }
}