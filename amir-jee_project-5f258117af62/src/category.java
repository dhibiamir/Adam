import org.hibernate.*;
import org.hibernate.cfg.Configuration;

import java.util.Date;
import java.util.Iterator;
import java.util.List;

/**
 * Created by Mokh on 03/12/2016.
 */
public class category {
    private int id;
    private String name;
    private String description;
    private String ShortDescription;
    private Date created_at;
    private static SessionFactory factory ;

    public category(){}

    public category(String name,String description,String ShortDescription){
        this.name             = name;
        this.description      = description;
        this.ShortDescription = ShortDescription;
        this.created_at       = new Date();
    }

    public void setId(int id){
        this.id = id;
    }

    public int getId(){
        return id;
    }

    public void setName(String name){
        this.name = name;
    }

    public String getName(){
        return name;
    }

    public void setDescription(String description){
        this.description = description;
    }

    public String getDescription(){
        return description;
    }

    public void setShortDescription(String ShortDescription){
        this.ShortDescription = ShortDescription;
    }

    public String getShortDescription(){
        return ShortDescription;
    }

    public void setCreated_at(Date created_at){
        this.created_at = created_at;
    }

    public Date getCreated_at(){
        return created_at;
    }

    /* Method to CREATE a category in the database */
    public Integer addCategory(String name, String description, String ShortDescription){
        try{
            factory = new Configuration().configure().buildSessionFactory();
        }catch (Throwable ex) {
            System.err.println("Failed to create sessionFactory object." + ex);
            throw new ExceptionInInitializerError(ex);
        }
        Session session = factory.openSession();
        Transaction tx = null;
        Integer categoryID = null;
        try{
            tx = session.beginTransaction();
            category category = new category(name,description,ShortDescription);
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
    /* Method to  READ all the categories */
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
                /*System.out.print("Name: " + category.getName());
                System.out.print("Description: " + category.getDescription());*/
                /*result += "Name: " + category.getName() + "/r/n";
                result += "Description: " + category.getDescription() + "/r/n";*/
                output[i] = category;
                i++;
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
    /* Method to UPDATE salary for a category */
    public void updateCategory(Integer categoryID, String name, String description, String shortDescription ){
        Session session = factory.openSession();
        Transaction tx = null;
        try{
            tx = session.beginTransaction();
            category category = session.get(category.class, categoryID);
            category.setName(name);
            category.setDescription(description);
            category.setShortDescription(shortDescription);
            session.update(category);
            tx.commit();
        }catch (HibernateException e) {
            if (tx!=null) tx.rollback();
            e.printStackTrace();
        }finally {
            session.close();
        }
    }
    /* Method to DELETE a category from the records */
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
    /* Method to FIND a category from the records */
    public category find(int ID) {
        category category = new category();
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
            category = session.get(category.getClass(),ID);
            Hibernate.initialize(category);
            tx.commit();
        }catch (HibernateException e) {
            if (tx!=null) tx.rollback();
            e.printStackTrace();
        }finally {
            session.close();
        }
        return category;

    }

    public static void main (String[] args){
        String Path = "/0/Edit";
        String[] data = Path.split("/");
        System.out.print(data[2]);

        category category_to_edit = new category().find(2);

/*        String Category_JSON = "{\"id\":"+category_to_edit.getId()+
                ",\"description\":"+category_to_edit.getDescription()+
                ",\"shortDesc\":"+category_to_edit.getShortDescription()+
                ",\"name\":"+category_to_edit.getName()+"}";
        System.out.print(Category_JSON);*/

        category_to_edit.updateCategory(1,"updatedagain","updated","updated");


    }

}
