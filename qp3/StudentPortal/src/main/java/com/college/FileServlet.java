package com.college;

import java.io.IOException;
import java.io.InputStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;

@WebServlet("/file")
public class FileServlet extends HttpServlet {

    // Configure this as the actual directory containing uploaded files
    private static final Path UPLOAD_DIR =
        Paths.get("C:/studentportal-uploads").toAbsolutePath().normalize();

    protected void doGet(HttpServletRequest request,
            HttpServletResponse response)
            throws ServletException, IOException {

        // Protect uploaded files with session authentication
        HttpSession session = request.getSession(false);

        if (session == null ||
            !Boolean.TRUE.equals(session.getAttribute("loggedIn"))) {
            response.sendError(HttpServletResponse.SC_UNAUTHORIZED);
            return;
        }

        // Validate the requested file ID
        int fileId;

        try {
            fileId = Integer.parseInt(request.getParameter("id"));
            if (fileId <= 0) throw new NumberFormatException();
        } catch (Exception e) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST,
                "Invalid file ID.");
            return;
        }

        String filename;
        String fileType;

        // Retrieve the stored filename and MIME type from MySQL
        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(
                 "SELECT file_path, file_type FROM student_files WHERE id = ?")) {

            ps.setInt(1, fileId);

            try (ResultSet rs = ps.executeQuery()) {
                if (!rs.next()) {
                    response.sendError(HttpServletResponse.SC_NOT_FOUND);
                    return;
                }

                // Accept only a simple filename, not a directory path
                filename = rs.getString("file_path");
                fileType = rs.getString("file_type");

                if (filename == null ||
                    !filename.equals(Paths.get(filename).getFileName().toString())) {
                    response.sendError(HttpServletResponse.SC_FORBIDDEN);
                    return;
                }
            }
        } catch (Exception e) {
            getServletContext().log("Unable to retrieve file metadata", e);
            response.sendError(
                HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            return;
        }

        // Resolve the path and ensure it stays inside the upload directory
        Path file = UPLOAD_DIR.resolve(filename).normalize();

        if (!file.startsWith(UPLOAD_DIR) || !Files.isRegularFile(file)) {
            response.sendError(HttpServletResponse.SC_NOT_FOUND);
            return;
        }

        // Permit only the file types supported by this portal
        if (!"image/jpeg".equals(fileType) &&
            !"image/png".equals(fileType) &&
            !"application/pdf".equals(fileType)) {
            response.sendError(HttpServletResponse.SC_FORBIDDEN);
            return;
        }

        // Prevent browsers from interpreting the response as another type
        response.setHeader("X-Content-Type-Options", "nosniff");
        response.setContentType(fileType);
        response.setContentLengthLong(Files.size(file));

        // Stream the file contents directly to the browser
        try (InputStream in = Files.newInputStream(file)) {
            in.transferTo(response.getOutputStream());
        }
    }
}