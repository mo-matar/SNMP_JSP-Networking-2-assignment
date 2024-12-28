# Project Title:  
**SNMP Manager and Authentication System**

## Description  
This project involves the development of a system using **HTML**, **PHP**, **Java**, **Servlet**, **JSP**, and **SNMP**. It consists of two main parts:

### Part 1: SNMP Manager in PHP  
- Develop an SNMP manager to communicate with an SNMP agent (PC).  
- Retrieve and display SNMP data such as **System Group**, **UDP Table**, **ARP Table**, and **SNMP Statistics**.  
- Provide functionalities to modify specific SNMP values (e.g., `sysContact`, `sysName`, `sysLocation`).  
- Implement a navigation system for easy switching between pages.

### Part 2: Java Client with Servlet and JSP Authentication  
- Create a Java Client application to authenticate users and interact with the PHP-based SNMP manager.  
- Authentication includes a Servlet and JSP that verify user credentials using a file or database.  
- After successful authentication, the Java client can fetch and display SNMP data from the PHP server.

---

## Project Features  

### Part 1: PHP SNMP Manager  
#### **Page 1:**  
- Display all System Group information (except `sysServices`).  
- Allow editing of `sysContact`, `sysName`, and `sysLocation`.  

#### **Page 2:**  
- Display the contents of the UDP table.  

#### **Page 3:**  
- Display the ARP table contents.  

#### **Page 4:**  
- Display SNMP statistics using two methods:  
  - `snmp2_get()`: Fetch data using individual OIDs and display in a styled table.  
  - `snmp2_walk()`: Fetch data in a bulk walk operation and display in another table.  
- Place the two tables side by side for comparison.

### Part 2: Java Client Application  
#### **Login System:**  
- Accepts Name, ID, and Password.  
- Verifies credentials using:  
  - **Servlet (Verify1)**  
  - **JSP (Verify2)**  

#### **SNMP Data Retrieval:**  
- Fetch SNMP data from the PHP server upon successful authentication.  
- Provides buttons on each page for retrieving specific SNMP data.  

#### **Editable Fields:**  
- Allows editing of the last three elements in the System Group (`sysContact`, `sysName`, and `sysLocation`).  

---

## Installation and Setup  

### Part 1: PHP Setup  
1. Install **XAMPP** or **WAMP** to run the PHP server.  
2. Enable the SNMP extension in PHP by modifying the `php.ini` file. Uncomment the line:  
   ```makefile
   extension=snmp
   ```
3. Place the PHP files in the `htdocs` directory of your server.  
4. Start the Apache server in XAMPP.  

### Part 2: Java and Tomcat Setup  
1. Install **Apache Tomcat** for deploying Servlet and JSP.  
2. Set up the project in an IDE (e.g., **Eclipse** or **IntelliJ IDEA**) with the required libraries for HTTP connection and servlet functionality.  
3. Configure the Tomcat server in the IDE and deploy the Servlet and JSP files.  
4. Create a file or database for storing user credentials for authentication.  

---

### Dependencies  
- **PHP SNMP Extension**  
- **XAMPP Server**  
- **Apache Tomcat**  
- **Java Development Kit (JDK)**  
- **Eclipse/IntelliJ IDEA**  

---

### Usage Instructions  

#### Part 1: Accessing the SNMP Manager  
1. Open a web browser and navigate to `http://localhost/SNMPManager`.  
2. Use the navigation menu to explore the four pages:  
   - **Page 1**: View and edit System Group information.  
   - **Page 2**: View the UDP table.  
   - **Page 3**: View the ARP table.  
   - **Page 4**: View SNMP statistics (Table by Get and Table by Walk).  

#### Part 2: Java Client Application  
1. Run the Java application from your IDE or executable JAR file.  
2. Enter your **Name**, **ID**, and **Password**.  
3. Click **Verify1** to authenticate with the Servlet.  
4. Click **Verify2** to authenticate with the JSP page.  
5. Upon successful authentication, use the application buttons to fetch SNMP data for each page.  

---

### Developer Notes  
- Use pure PHP for backend processing to ensure separation of concerns.  
- Implement client-side JavaScript for fetching and displaying data dynamically.  
- Ensure Tomcat and PHP servers are running simultaneously for full functionality.  
- The editable fields (`sysContact`, `sysName`, `sysLocation`) require read-write SNMP community configuration on the agent side.  

---
