import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;

/**
 * Created by Mokh on 11/12/2016.
 */
@WebServlet(name = "CreateCategory", urlPatterns = "/createCategory")
public class CreateCategory extends HttpServlet {
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        category new_category = new category();
        int Category_ID = new_category.addCategory(
                request.getParameter("name"),
                request.getParameter("description"),
                request.getParameter("shortDesc")
        );

    }

    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

    }
}
