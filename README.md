# Online Store Web Application  
**Final Project – Web Application Development (WAD)**

---

## 📌 Project Overview
This project is a final project for the **Web Application Development (WAD)** course.  
The application is a **PHP & MySQL-based online store web application** that supports both **user-side** and **admin-side** functionalities.

The system simulates a real e-commerce workflow, where users can browse products, add them to a cart, and place orders, while administrators manage users, products, categories, and orders.

---

## 👨‍🏫 Lecturer
**Mr. Ahmad Fadhil N**

---

## 👥 Group Members
- **Rieka Zalsa Nabila** (012202400020)  
- **Inayah Nur A’ini** (012202400111)

---

## 🛠️ Technologies Used
- **Front-End:** HTML, CSS, JavaScript  
- **Back-End:** PHP (Native)  
- **Database:** MySQL  
- **Web Server:** Apache (XAMPP)  
- **Tools:**
  - Visual Studio Code  
  - XAMPP  
  - phpMyAdmin  

---

## 🎯 Application Features

### 👤 User Features
- User login
- Dashboard view
- View product list
- Search products
- Add products to cart
- Update or remove cart items
- Checkout and place orders

### 🛠️ Admin Features
- Admin login
- View all users
- Manage products (Create, Read, Update, Delete)
- Manage product categories
- View all user orders

---

## 🗄️ Database Structure

### Main Tables
| Table Name | Description |
|-----------|-------------|
| users | Stores user and admin data |
| categories | Stores product categories |
| subcategories | Stores product data |
| cart | Stores temporary cart items |
| orders | Stores order transactions |

### Table Relationships
- One user → many orders  
- One category → many products  
- One user → many cart items  
- One product → many cart records  

---

## 🔄 CRUD Implementation

### Products (Admin)
- **Create:** Add new products  
- **Read:** Display product list with categories  
- **Update:** Edit product details  
- **Delete:** Remove products  

### Cart (User)
- **Create:** Add products to cart  
- **Read:** View cart items  
- **Update:** Modify product quantity  
- **Delete:** Remove items from cart  

---

## 🔐 Security Features
- Password hashing using `password_hash()`
- Session-based authentication
- Role-based access control (Admin & User)
- Input validation and sanitization
- Protection against SQL Injection

---

## 🧪 Testing
- Login validation testing
- Product CRUD testing
- Cart and checkout testing
- Admin access control testing

---

## 🚀 How to Run the Project
1. Install **XAMPP**
2. Start **Apache** and **MySQL**
3. Place the project folder inside:

##Output:
<img width="941" height="429" alt="image" src="https://github.com/user-attachments/assets/be7c9d43-9578-4fe4-9a8f-47c9973ae664" />
<img width="941" height="431" alt="image" src="https://github.com/user-attachments/assets/416b7fe0-f0a1-4831-b37d-4a1bf8c9f818" />
<img width="941" height="429" alt="image" src="https://github.com/user-attachments/assets/0f755943-c21d-4ddd-ad11-26ac2c3d968d" />


<img width="941" height="438" alt="image" src="https://github.com/user-attachments/assets/10490e3c-f9d8-446b-875e-8b3ccd2f7c32" />
<img width="941" height="437" alt="image" src="https://github.com/user-attachments/assets/7cb8c814-0b26-42e0-855f-59d3ae07934b" />
<img width="941" height="433" alt="image" src="https://github.com/user-attachments/assets/dee43d0f-8b64-4bf6-90a1-cae98c561793" />
<img width="941" height="428" alt="image" src="https://github.com/user-attachments/assets/4de02b40-485d-4d98-be6f-de41e63b5b72" />
<img width="941" height="431" alt="image" src="https://github.com/user-attachments/assets/f99b255d-6079-4db9-9052-711a1a589b67" />
<img width="941" height="423" alt="image" src="https://github.com/user-attachments/assets/b3eaf619-1b9d-4eda-afd5-77f30ee8d080" />
<img width="941" height="356" alt="image" src="https://github.com/user-attachments/assets/d1e31645-af5c-4bb9-8e3f-9d0efdc4164a" />
<img width="941" height="428" alt="image" src="https://github.com/user-attachments/assets/d28df7a7-b058-4434-9c26-c300110fa274" />
<img width="941" height="431" alt="image" src="https://github.com/user-attachments/assets/61f56ed9-6985-4a64-bf7e-877db4c425bd" />
