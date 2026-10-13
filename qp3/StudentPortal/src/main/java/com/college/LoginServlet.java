package com.college;

import java.io.IOException;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;

@WebServlet("/login")
public class LoginServlet extends HttpServlet {

    protected void doPost(HttpServletRequest request,
            HttpServletResponse response)
            throws ServletException, IOException {

        // Read login credentials from the form
        String username = request.getParameter("username");
        String password = request.getParameter("password");

        // Demo credentials; use a database and hashed passwords in production
        if ("admin".equals(username) && "admin123".equals(password)) {

            // Create a session after successful login
            HttpSession session = request.getSession(true);
            session.setAttribute("loggedIn", true);

            // Prevent reuse of the pre-login session ID
            request.changeSessionId();

            response.sendRedirect(
                request.getContextPath() + "/students");
        } else {
            // Display an error for invalid credentials
            request.setAttribute("error", "Invalid username or password");
            request.getRequestDispatcher("/login.jsp")
                   .forward(request, response);
        }
    }
}