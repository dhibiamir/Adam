<%@ page import="java.util.ArrayList" %><%--
  Created by IntelliJ IDEA.
  User: Mokh
  Date: 01/12/2016
  Time: 21:38
  To change this template use File | Settings | File Templates.
--%>
<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<html>
  <head>
    <title>$Title$</title>
  </head>
  <body>
  $END$
  <%String values[][] = (String[][])request.getAttribute("data");
    for (int i = 0 ; i<values.length;i++){
      out.print(values[i][0]);
      out.print(values[i][1]);
      out.println(values[i][2]);
    }
  %>

  </body>
</html>
