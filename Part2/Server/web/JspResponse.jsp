<%@page import="java.io.IOException"%>
<%@page import="java.io.BufferedReader"%>
<%@page import="java.io.InputStreamReader"%>
<%@page import="java.io.FileInputStream"%>
<%
        String id = request.getParameter("id");
        String password = request.getParameter("password");
        boolean isVerified = false;
        
        String filePath = "C://xampp//htdocs//credentials.txt";
        try {
            try (FileInputStream fileInputStream = new FileInputStream(filePath);
            InputStreamReader inputStreamReader = new InputStreamReader(fileInputStream);
            BufferedReader bufferedReader = new BufferedReader(inputStreamReader)) {
                String line;
                while ((line = bufferedReader.readLine()) != null) {
                    String[] columns = line.split(" ");
                    String idFromFile = columns[0];
                    String passwordFromFile = columns[2];
                    if (idFromFile.equals(id) && passwordFromFile.equals(password)) {
                        isVerified = true;
                    }
                }
                bufferedReader.close();
                inputStreamReader.close();
            }
        } catch (IOException e) {
            e.printStackTrace();
        }

        if (isVerified) {
            out.println("OK, ID and password are verified");
        } else {
            out.println("NO, Please check ID and pasword");
        }
   
%>
