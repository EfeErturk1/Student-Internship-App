import java.sql.*;

public class Database {
    public static void main(String[] args) {
        final String url = "jdbc:mysql://dijkstra.ug.bcc.bilkent.edu.tr/efe_erturk";
        final String username = "XXX";
        final String password = "XXX";

        // Open a connection
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
            Connection connection = DriverManager.getConnection(url, username, password);
            System.out.println("Database connected!");
            Statement stmt = connection.createStatement();

            System.out.println("Dropping previous tables");
            stmt.execute("DROP TABLE IF EXISTS apply,student,company");
            System.out.println("Successfully dropped previous tables");

            String studentTable = "CREATE TABLE student(" +
                    "sid CHAR(12), " +
                    "sname VARCHAR(50), " +
                    "bdate DATE, " +
                    "address VARCHAR(50), " +
                    "scity VARCHAR(20), " +
                    "year CHAR(20), " +
                    "gpa FLOAT, " +
                    "nationality VARCHAR(20)," +
                    "PRIMARY KEY(sid))"+
                    "ENGINE = INNODB;";

            String companyTable = "CREATE TABLE company("+
                    "cid CHAR(8)," +
                    "cname VARCHAR(20),"+
                    "quota INT,"+
                    "gpathreshold FLOAT,"+
                    "PRIMARY KEY(cid))"+
                    "ENGINE = INNODB;";

            String applyTable = "CREATE TABLE apply("+
                    "sid CHAR(12),"+
                    "cid CHAR(8)," +
                    "FOREIGN KEY (sid) REFERENCES student(sid)," +
                    "FOREIGN KEY (cid) REFERENCES company(cid))"+
                    "ENGINE = INNODB;";

            stmt.execute(studentTable);
            stmt.execute(companyTable);
            stmt.execute(applyTable);
            System.out.println("Tables are created");

            String studentValues = "INSERT INTO student VALUES" +
                    "('21000001', 'Marco', '1998-05-31', 'Strobelallee', 'Dortmund', 'senior', 2.64, 'DE')," +
                    "('21000002', 'Arif', '2001-11-17', 'Nisantasi', 'Istanbul', 'junior', 3.86, 'TC')," +
                    "('21000003', 'Veli', '2003-02-19', 'Cayyolu', 'Ankara', 'freshman', 2.21, 'TC')," +
                    "('21000004', 'Ayse', '2003-05-01', 'Tunali', 'Ankara', 'freshman', 2.52, 'TC');";

            String companyValues = "INSERT INTO company VALUES" +
                    "('C101', 'milsoft', 3, 2.50),"+
                    "('C102', 'merkez bankasi', 10, 2.45),"+
                    "('C103', 'tubitak' ,2 ,3.00)," +
                    "('C104', 'havelsan' ,5 ,2.00)," +
                    "('C105', 'aselsan', 4, 2.50)," +
                    "('C106', 'tai', 2, 2.20)," +
                    "('C107', 'amazon' ,1 ,3.85);";

            String applyValues = "INSERT INTO apply VALUES" +
                    "('21000001', 'C101')," +
                    "('21000001', 'C102')," +
                    "('21000001', 'C104')," +
                    "('21000002', 'C107')," +
                    "('21000003', 'C104')," +
                    "('21000003', 'C106')," +
                    "('21000004', 'C102')," +
                    "('21000004', 'C106');";

            stmt.execute(studentValues);
            stmt.execute(companyValues);
            stmt.execute(applyValues);
            System.out.println("Values are inserted to the tables\n");

            System.out.println("Executing queries");
            System.out.println("Query 1:");
            // now queries
            String query1 = "select S.sname "+
                    "from student S natural join apply A natural join company C "+
                    "group by S.sid "+
                    "having count(C.cid) = 3;";
            System.out.println(query1);
            ResultSet res = stmt.executeQuery(query1);
            System.out.println("sname");
            System.out.println("--------");
            while (res.next()) {
                System.out.println(res.getString("sname"));
            }
            System.out.println("--------\n");


            System.out.println("Query 2:");
            String query2 ="select sum(quota) " +
                    "from (select sid,max(cnt) " +
                    "from (select S.sid, count(C.cid) as cnt  " +
                    "from student S natural join apply A natural join company C " +
                    "group by S.sid) as Q1) as Q2 natural join apply natural join company;";

            System.out.println(query2);
            res = stmt.executeQuery(query2);
            System.out.println("sum of quotas");
            System.out.println("--------");
            while (res.next()) {
                System.out.println(res.getString("sum(quota)"));
            }
            System.out.println("--------\n");

            System.out.println("Query 3:");
            String query3 ="select Q1.nationality, Q1.cnt1 / Q2.cnt2 as average "+
            "from ((select S.nationality, count(C.cid) as cnt1  from student S natural join apply A natural join company C group by S.nationality) as Q1) " +
                    "natural join ((select nationality,count(sid) as cnt2 from student group by nationality) as Q2);";
            System.out.println(query3);
            res = stmt.executeQuery(query3);
            System.out.println("nationality |\t average");
            System.out.println("-----------------------");
            while (res.next()) {
                System.out.println(res.getString("nationality") + " \t\t\t\t " + res.getString("average"));
            }
            System.out.println("-------------------\n");

            System.out.println("Query 4:");
            String query4 ="select C.cname " +
                    "from (select * from student where year = 'freshman') F natural join apply A natural join company C " +
                    "group by C.cname " +
                    "having count(*) = count(F.Sid);";

            System.out.println(query4);
            res = stmt.executeQuery(query4);
            System.out.println("cname");
            System.out.println("--------");
            while (res.next()) {
                System.out.println(res.getString("cname"));
            }
            System.out.println("--------\n");

            System.out.println("Query 5:");
            String query5 ="select C.cname, avg(S.gpa) as average " +
                    "from student S natural join apply A natural join company C " +
                    "group by C.cname;";

            System.out.println(query5);
            res = stmt.executeQuery(query5);
            System.out.println("cname |\t\t average");
            System.out.println("-----------------------");
            while (res.next()) {
                System.out.println(res.getString("cname") + " \t\t\t " + res.getString("average"));
            }
            System.out.println("-------------------\n");

        } catch (SQLException | ClassNotFoundException e) {
            e.printStackTrace();
        }
    }
}
