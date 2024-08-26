<?php
namespace App\Models;

class Styles {
    public function putStyle() {
        echo '<style>
            body {
                background: url("../../../public/image_and_video/gif/anim_background2.gif");
                font-family: Arial, sans-serif;
                color: #333;
                margin: 0;
                padding: 0;
            }
            .profile-header {
                text-align: center;
                margin-bottom: 30px;
            }
            .profile-header img {
                border-radius: 50%;
                width: 150px;
                height: 150px;
                object-fit: cover;
            }
            .profile-header h1 {
                font-size: 2rem;
                margin-top: 10px;
            }
            .profile-header p {
                color: #555;
            }
            .left-sidebar, .right-sidebar, .main-content {
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                padding: 20px;
                margin-bottom: 20px;
            }
            .sidebar-item h3, .content-item h3 {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }
            .list-group-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
                margin-bottom: 10px;
                background-color: #f8f9fa;
            }
            .navbar {
                background-color: #343a40;
                padding: 10px 0;
            }
            .navbar a {
                color: #ffffff;
                text-decoration: none;
                font-weight: bold;
                margin: 0 15px;
            }
            .navbar a:hover {
                text-decoration: underline;
            }
            .container {
                margin-top: 50px;
            }
            h1 {
                text-align: center;
                margin-bottom: 40px;
                font-size: 2.5rem;
                font-weight: bold;
                color: white;
            }
            .table-responsive {
                margin-bottom: 50px;
            }
            .table {
                background-color: #ffffff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .table th {
                background-color: #343a40;
                color: #ffffff;
                padding: 15px;
                font-weight: bold;
                text-align: center;
            }
            .table td {
                padding: 15px;
                text-align: center;
                vertical-align: middle;
            }
            .btn {
                font-size: 14px;
                padding: 10px 20px;
                border-radius: 4px;
                transition: background-color 0.3s ease;
            }
            .btn-primary {
                background-color: #007bff;
                border-color: #007bff;
            }
            .btn-primary:hover {
                background-color: #0056b3;
                border-color: #0056b3;
            }
            .btn-success {
                background-color: #28a745;
                border-color: #28a745;
            }
            .btn-success:hover {
                background-color: #218838;
                border-color: #218838;
            }
            .btn-secondary {
                background-color: #6c757d;
                border-color: #6c757d;
            }
            .btn-secondary:hover {
                background-color: #5a6268;
                border-color: #5a6268;
            }
            .btn-warning {
                background-color: #ffc107;
                border-color: #ffc107;
            }
            .btn-warning:hover {
                background-color: #e0a800;
                border-color: #d39e00;
            }
            .modal-content {
                border-radius: 8px;
            }
            .form-control {
                border-radius: 4px;
            }
            .form-group label {
                font-weight: 600;
            }
            footer {
                background-color: #343a40;
                color: white;
                padding: 20px 0;
                text-align: center;
                margin-top: 50px;
            }
            footer a {
                color: #adb5bd;
                text-decoration: none;
            }
            footer a:hover {
                text-decoration: underline;
            }
            .modal-header, .modal-footer {
                background-color: #f0f2f5;
            }
            .modal-title {
                font-weight: bold;
                color: #333;
            }
            .hero {
                background: url("../../../../public/image_and_video/webp/background_image_index.webp") no-repeat center center;
                background-size: cover;
                color: white;
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
                border-radius: 10px;
            }
            .hero h1 {
                font-size: 3.5rem;
                font-weight: bold;
                margin-bottom: 20px;
            }
            .hero p {
                font-size: 1.25rem;
            }
            .navbar-toggler {
                background-color: #fff;
                border: none;
                outline: none;
            }
            .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3Csvg viewBox=\'0 0 30 30\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath stroke=\'rgba%280, 0, 0, 0.5%29\' stroke-width=\'2\' linecap=\'round\' linejoin=\'round\' d=\'M4 7h22M4 15h22M4 23h22\'/%3E%3C/svg%3E");
            }
            .navbar-toggler:focus {
                outline: none;
            }
            .navbar-toggler-icon {
                width: 25px;
                height: 25px;
            }
            .bio {
                background-color: white;
                color: white;
                padding: 20px 0;
                text-align: center;
                margin-top: 50px;
                opacity: 75%;
                border-radius: 12px;
            }
        </style>';
    }
}