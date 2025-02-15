# **E-commerce Website**

A fully responsive, dynamic e-commerce website that allows users to browse, purchase products, and manage their accounts. It also features an admin dashboard to manage products, orders, and users. Built with **HTML**, **CSS**, **JavaScript**, **PHP**, and **MySQL**, this project offers a real-world solution for building an online store with user and admin functionalities.

---

## **Key Features**

### **For Customers**
- **User Authentication:**  
  Customers can create an account, log in, and manage their profile details.
  
- **Product Catalog:**  
  View a variety of products with images, descriptions, prices, and availability status.
  
- **Shopping Cart:**  
  Add products to the cart, update quantities, and view the total cost before proceeding to checkout.

- **Checkout & Payment:**  
  Secure checkout process allowing users to enter shipping details and complete the purchase.

- **Order History:**  
  Customers can view their previous orders and track the status of ongoing orders.

- **Responsive Design:**  
  Fully responsive, ensuring the site is functional and visually appealing on all devices (desktop, tablet, and mobile).

---

### **For Admin**
- **Admin Login:**  
  Secure login page for administrators to access the backend.

- **Product Management:**  
  Admins can add new products, update existing ones, or delete them. Each product includes attributes like name, description, price, and product images.

- **Order Management:**  
  Admins can view all customer orders, update the status (e.g., shipped, pending), and manage order details.

- **User Management:**  
  View customer information, track their orders, and modify user accounts.

---

## **Tech Stack**
- **Frontend:**
  - HTML, CSS, JavaScript
  - Responsive design using Flexbox and Media Queries for mobile-first design
  - Product images and static content

- **Backend:**
  - PHP for server-side scripting
  - MySQL database for storing user data, products, and orders
  - XAMPP for local server management (Apache & MySQL)

- **Admin Panel:**  
  Custom-built admin interface using PHP for data management and control.

---

## **Project Structure**

---

## **Installation & Setup**

### **Prerequisites**
- Install **XAMPP** to run Apache and MySQL locally.

### **Setup Instructions**
1. **Clone the Repository:**
   - Clone this repository to your local machine using Git:
     ```bash
     git clone https://github.com/JPOORNA/Ecommerce-website-Responsive.git
     ```

2. **Start XAMPP:**
   - Launch XAMPP and start **Apache** and **MySQL**.

3. **Create the Database:**
   - Open **phpMyAdmin** in your browser (http://localhost/phpmyadmin).
   - Create a new database (e.g., `ecommerce_db`).
   - Import the `database.sql` file (located in the repository) into your newly created database.

4. **Configure Database Connection:**
   - Open the `includes/db_config.php` file and update the database connection settings (username, password, database name).

5. **Run the Website:**
   - Move the cloned project folder into the `htdocs` directory of your XAMPP installation.
   - Access the project in your browser by navigating to: `http://localhost/ecommerce`.

---

## **Contributing**

If you’d like to contribute to this project:
1. Fork the repository to your own GitHub account.
2. Create a new branch to work on your feature or bug fix.
3. Make your changes and commit them.
4. Submit a pull request with a clear explanation of your changes.

---

## **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

### **Additional Notes:**
- The website is designed to be easily extensible. You can add features like product search, advanced payment gateway integration, and more.
- Ensure your server is configured to handle email notifications and other required services for production use.

---


