
<!DOCTYPE html>
<html>
<head>
    <title>Student Portal Login</title>
</head>
<body>
    <h2>Student Portal Login</h2>

    <!-- Display login errors when credentials are invalid -->
    <p style="color:red">${error}</p>

    <!-- Send login credentials to LoginServlet -->
    <form action="${pageContext.request.contextPath}/login"
          method="post">
        Username:
        <input type="text" name="username" required>
        <br><br>

        Password:
        <input type="password" name="password" required>
        <br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>