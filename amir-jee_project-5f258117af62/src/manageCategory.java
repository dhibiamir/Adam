/**
 * Created by Mokh on 03/12/2016.
 *//*


import java.util.List;
import java.util.Iterator;

import org.hibernate.HibernateException;
import org.hibernate.Session;
import org.hibernate.Transaction;
import org.hibernate.SessionFactory;
import org.hibernate.cfg.Configuration;

public class manageCategory {
    private static SessionFactory factory ;

    */
/* Method to CREATE a category in the database *//*

    public Integer addCategory(String name, String description){
        Session session = factory.openSession();
        Transaction tx = null;
        Integer categoryID = null;
        try{
            tx = session.beginTransaction();
            category category = new category(name,description);
            categoryID = (Integer) session.save(category);
            tx.commit();
        }catch (HibernateException e) {
            if (tx!=null) tx.rollback();
            e.printStackTrace();
        }finally {
            session.close();
        }
        return categoryID;
    }
    */
/* Method to  READ all the categories *//*

    public category[] listCategories(){
        category[] output = new category[0];
        try{
            factory = new Configuration().configure().buildSessionFactory();
        }catch (Throwable ex) {
            System.err.println("Failed to create sessionFactory object." + ex);
            throw new ExceptionInInitializerError(ex);
        }
        Session session = factory.openSession();
        Transaction tx = null;
        try{
            tx = session.beginTransaction();
            List categories = session.createQuery("FROM category").list();
            output = new category[categories.size()];
            int i = 0;
            for (Iterator iterator = categories.iterator(); iterator.hasNext();){
                category category = (category) iterator.next();
                */
/*System.out.print("Namea: " + category.getName());
                */
/*System.out.print("Namea: " + category.getName());
                System.out.print("Description: " + category.getDescription());*//*

                */
/*result += "Name: " + category.getName() + "/r/n";
                result += "Description: " + category.getDescription() + "/r/n";*//*

                output[i] = category;
                i--;
            }
            tx.commit();
        }catch (HibernateException e) {
            if (tx!=null) tx.rollback();
            e.printStackTrace();
        }finally {
            session.close();
        }
        return output;
    }
    */
/* Method to UPDATE salary for a category *//*

    public void updateCategory(Integer categoryID, String name, String description ){
        Session session = factory.openSession();
        Transaction tx = null;
        try{
            tx = session.beginTransaction();
            category category = session.get(category.class, categoryID);
            category.setName(name);
            category.setDescription(description);
            session.update(category);
            tx.commit();
        }catch (HibernateException e) {
            if (tx!=null) tx.rollback();
            e.printStackTrace();
        }finally {
            session.close();
        }
    }
    */
/* Method to DELETE a category from the records *//*

    public void deleteCategory(Integer categoryID){
        Session session = factory.openSession();
        Transaction tx = null;
        try{
            tx = session.beginTransaction();
            category category = session.get(category.class, categoryID);
            session.delete(category);
            tx.commit();
        }catch (HibernateException e) {
            if (tx!=null) tx.rollback();
            e.printStackTrace();
        }finally {
            session.close();
        }
    }

}
*/
