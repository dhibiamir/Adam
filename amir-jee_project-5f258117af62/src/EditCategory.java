import javax.servlet.RequestDispatcher;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;

/**
 * Created by Mokh on 11/12/2016.
 */
@WebServlet(name = "EditCategory", urlPatterns = "/Category/*")
public class EditCategory extends HttpServlet {
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        String ID = request.getPathInfo();
        if (ID.split("/")[2].equals("Edit")){
            int Category_ID = Integer.parseInt(ID.split("/")[1]);
            category category_to_edit = new category().find(Category_ID);
            System.out.println(Category_ID);
            System.out.println(request.getParameter("nameUpdate"));
            System.out.println(request.getParameter("descriptionUpdate"));
            System.out.println(request.getParameter("shortDescUpdate"));
            category_to_edit.updateCategory(Category_ID,
                    request.getParameter("nameUpdate"),
                    request.getParameter("descriptionUpdate"),
                    request.getParameter("shortDescUpdate")
            );

        } else if (ID.split("/")[2].equals("Delete")){
            int Category_ID = Integer.parseInt(ID.split("/")[1]);
            category category_to_delete = new category().find(Category_ID);
            category_to_delete.deleteCategory(Category_ID);
        }
    }

    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        String ID = request.getPathInfo();
        if (ID.split("/")[2].equals("Edit")){
            int Category_ID = Integer.parseInt(ID.split("/")[1]);
            category category_to_edit = new category().find(Category_ID);
            String Category_JSON = "{\"id\":"+category_to_edit.getId()+
                    ",\"description\":\""+category_to_edit.getDescription()+
                    "\",\"shortDesc\":\""+category_to_edit.getShortDescription()+
                    "\",\"name\":\""+category_to_edit.getName()+"\"}";

            response.setContentType("text/plain");
            response.setCharacterEncoding("UTF-8");
            response.getWriter().write(Category_JSON);
        }
    }
}
