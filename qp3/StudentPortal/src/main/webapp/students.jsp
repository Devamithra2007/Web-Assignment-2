<%@ page contentType="text/html;charset=UTF-8" %>
<%@ taglib prefix="c" uri="jakarta.tags.core" %>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profiles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f5fa;
            padding: 20px;
        }

        .student {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ccc;
        }

        img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }

        a {
            color: #2457b2;
        }
    </style>
</head>
<body>

<h1>Student Profiles</h1>

<!-- Loop through the student list provided by StudentServlet -->
<c:forEach var="student" items="${students}">
    <div class="student">

        <!-- Display each student's details safely -->
        <h2><c:out value="${student.name}"/></h2>
        <p>Roll Number: <c:out value="${student.rollNumber}"/></p>
        <p>Course: <c:out value="${student.course}"/></p>

        <h3>Uploaded Files</h3>

        <!-- Check whether this student has uploaded any files -->
        <c:choose>
            <c:when test="${not empty student.files}">

                <!-- Loop through all files associated with this student -->
                <c:forEach var="file" items="${student.files}">

                    <!-- Display image files through FileServlet -->
                    <c:if test="${file.fileType eq 'image/jpeg'
                              or file.fileType eq 'image/png'}">
                        <img
                            src="${pageContext.request.contextPath}/file?id=${file.id}"
                            alt="Student profile image">
                    </c:if>

                    <!-- Display a link for documents such as PDF certificates -->
                    <p>
                        <a href="${pageContext.request.contextPath}/file?id=${file.id}"
                           target="_blank" rel="noopener">
                            <c:out value="${file.originalName}"/>
                        </a>
                    </p>

                </c:forEach>
            </c:when>

            <!-- Show a message when no files are available -->
            <c:otherwise>
                <p>No files uploaded.</p>
            </c:otherwise>
        </c:choose>

    </div>
</c:forEach>

</body>
</html>