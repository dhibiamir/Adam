import javax.servlet.RequestDispatcher;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.lang.reflect.Array;
import java.text.Format;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

/**
 * Created by Mokh on 02/12/2016.
 */
@WebServlet(name = "Categories" , urlPatterns = "/Categories")
public class Categories extends HttpServlet {
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

    }

    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        category category = new category();

        String[][] data  = new String[category.listCategories().length][5];
        Format formatter = new SimpleDateFormat("dd-MM-yyyy");
        for (int i = 0; i<category.listCategories().length;i++){
            String string_date = formatter.format(category.listCategories()[i].getCreated_at());
            data[i][0] = String.valueOf(category.listCategories()[i].getId());
            data[i][1] = category.listCategories()[i].getName();
            data[i][2] = category.listCategories()[i].getDescription();
            data[i][3] = category.listCategories()[i].getShortDescription();
            data[i][4] = string_date;
        }
        request.setAttribute("data",data);
        request.getRequestDispatcher("/Master.jsp").forward(request,response);




        /*int var = 3;
        request.setAttribute("myname",var);
        Object [][] data = {{"ok","notok"},{"same","same"}};//use map also
        request.setAttribute("data",data);
        request.getRequestDispatcher("/Master.jsp").forward(request,response);

       *//* RequestDispatcher view = request.getRequestDispatcher("WEB-INF/new.html");
        view.forward(request, response);*/
    }
}
