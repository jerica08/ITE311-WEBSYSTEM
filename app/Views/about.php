<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width", initial-scale="1.0">
        <title>About Page</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    </head>
    <style>
        .button{
            background-color:#DAA520;
            color : black;
            border:none;
            padding: 10px 20px;
            cursor: pointer;
            transition: background 0.3s;
        }  
        .button-active {
            background-color: #B8860B;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .button:hover{
            background-color: #D2B55B;
        }
        .blog-box{
            height: auto;
            align-items:center;
        }
       
        .about{
            margin-top: 100px;
            margin-left:100px;
        }
        .vision-content{
            font-size:50px;
        }   
        h1{
            text-align:center;
        }
         
    </style>
    <body>
        
        <nav class="navigationbar">
            <nav class="text d-flex align-items-center" style="background-color:#000000;padding: 10px;">
                <p><h4 style="color: white;text-align:left;margin-bottom:none;font-family: 'Times New Roman', serif;">Kawas National University</h4></p>
            </nav>       
                <nav class="btm-navbar" style="background-color:#DAA520;font-family: 'Times New Roman', serif;">
                    <div class="container-fluid d-flex justify-content-between align-items-center">
                        <a class="navbar-brand text-white" href="#"><h2>Learning Management System</h2></a>
                        <ul class="nav d-flex align-items-center gap-3" style="">
                            <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('/') ?>"><button class="button"> Home</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('/register') ?>"><button class="button"> Sign Up</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('/login') ?>"><button class="button"> Log-In</button></a></li>                           
                            <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('/about') ?>"><button class="button-active"> About Us</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('/contact') ?>"><button class="button"> Contact Us</button></a></li>
                        </ul>
                    </div>       
               </nav>
           </nav>
           <nav class="blog-box">
                <nav class="blog" style="font-family: 'Times New Roman', serif;">
                    <h1 class="blog-title" style="font-weight: 900;justify-content:left;font-family: 'Times New Roman', serif;margin:50px;">ABOUT US</h1>
                     <img src="<?= base_url('./img/KNU_logo.png') ?>" alt="Kawas National University" style="max-width: 200px; height: auto; justify-content:center;margin-left:50px;">
                    <div class="about">
                        <div class="about-content">
                            <h5>Welcome to the Kawas National University Learning Management System, an innovative platform designed to transform the way our students and faculty connect, learn, and grow.</h5><br>
                    </div>
                </nav>
           </nav>
    
     
    </body>
   
</html>
