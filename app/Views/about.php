<!DOCTYPE html>
<html lang="en">
    <head>
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
        .button:hover{
            background-color: #D2B55B;
        }
        .blog-box{
            height: auto;
            align-items:center;
        }

    </style>
    <body>
        
            <nav class="navigationbar">
                <nav class="top-navbar" style="background-color:#000000;padding: 10px;">
                    <p style="color: white;"> Purok 3, Kawas,Alabel, Sarangani Province, 9501, Philippines</p>
                </nav>       
                <nav class="btm-navbar" style="background-color:#DAA520;">
                    <div class="container-fluid d-flex justify-content-between align-items-center">
                        <a class="navbar-brand text-white" href="#"><h2>Learning Management System</h2></a>
                        <ul class="nav d-flex align-items-center gap-3">
                            <li class="nav-item"><a class="nav-link text-white" href="#"><button class="button"> Sign Up</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="#"><button class="button"> Log-In</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('/') ?>"><button class="button"> Home</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('contact') ?>"><button class="button"> Contact Us</button></a></li>
                        </ul>
                    </div>       
               </nav>
           </nav>
           <nav class="blog-box" style>
                <nav class="blog" >
                    <p class="blog-title"><h1>ABOUT US</h1></p>
                    <img style="height: 500px; width: 1000px;" src="<?= base_url('http://localhost/ITE311-MARQUEZ/public/img/kawas.jpg') ?>">
                
                </nav>
           </nav>
    
     
    </body>
   
</html>
