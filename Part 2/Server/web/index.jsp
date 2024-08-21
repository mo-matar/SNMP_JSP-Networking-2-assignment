<%-- 
    Document   : index
    Created on : Feb 23, 2018, 1:04:10 PM
    Author     : raed
--%>

<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE html>
<%! int fontSize;%>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JSP Page</title>
    </head>
    <body>
        <h1>Hello World...!</h1>
        <h1>Entry Form</h1>
        <form name="Name Input Form" action="response.jsp">
            Enter Your Name:
            <input type="text" name="name" value="----" />
            <input type="submit" value="Ok" />
        </form>
    <body>
        <% while (fontSize <= 10) {%>
        <font color = "green" size = "<%= fontSize%>">
        JSP Tutorial
        </font><br />
        <% fontSize++; %> 

        <% }%>     
    </body>
</html>
