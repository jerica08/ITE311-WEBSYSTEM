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
        .button:hover{
            background-color: #D2B55B;
        }
        .blog-box{
            height: auto;
            align-items:center;
        }
       
        .vision{
            margin-top: 100px;
            margin-left:100px;
        }
        .vision-title{
            font-size:50px;
        }
        .mission-content{
            padding:20px;
        }
        .mission{
            margin-top: 100px;
            margin-left:100px;
        }
        h1{
            text-align:center;
        }
        .coreValues-title{
            font-size:50px;
        }
        .coreValues{
            margin-top: 100px;
            margin-left:100px;
        }
        h1{
            text-align:center;
        }
        
        
        
        
    </style>
    <body>
        
        <nav class="navigationbar">
            <nav class="text d-flex align-items-center" style="background-color:#000000;padding: 10px;">
                <p><h4 style="color: white;text-align:left;margin-bottom:none;font-family: 'Times New Roman', serif;">Kawas National High School</h4></p>
            </nav>       
                <nav class="btm-navbar" style="background-color:#DAA520;font-family: 'Times New Roman', serif;">
                    <div class="container-fluid d-flex justify-content-between align-items-center">
                        <a class="navbar-brand text-white" href="#"><h2>Learning Management System</h2></a>
                        <ul class="nav d-flex align-items-center gap-3" style="">
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('/') ?>"><button class="button"> Home</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/register') ?>"><button class="button"> Sign Up</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/login') ?>"><button class="button"> Log-In</button></a></li>                           
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('about') ?>"><button class="button"> About Us</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('contact') ?>"><button class="button"> Contact Us</button></a></li>
                        </ul>
                    </div>       
               </nav>
           </nav>
           <nav class="blog-box">
                <nav class="blog" style="font-family: 'Times New Roman', serif;">
                    <h1 class="blog-title" style="font-weight: 900;justify-content:left;font-family: 'Times New Roman', serif;">ABOUT US</h1>
                     <img src="<?= base_url('img/kawas_logo.jpg') ?>" alt="Kawas National High School Logo" style="max-width: 200px; height: auto; justify-content:center;margin-left:50px;">
                     <img src="<?= base_url('img/kawas.jpg') ?>" alt="Kawas National High School" style="max-width: 500px; height: auto; justify-content:center;margin-left:50px;filter: drop-shadow(8px 8px 16px rgba(0, 0, 0, 0.5));">
                    <div class="vision">
                        <h2>DepEd VISION</h2>   
                        <div class="vision-content">
                            <h5>We dream of Filipinos who passionately love their country and whose values and competencies enable them to realize their full potential and contribute meaningfully to building the nation.</h5><br>
                            <h5>As a learner-centered public institution, the Department of Education continuously improves itself to better serve its stakeholders.</h5>
                        </div>
                    </div>
                      <div class="mission">
                        <h2>DepEd MISSION</h2>
                        <div class="mission-content">
                            <h5>To protect and promote the right of every Filipino to quality, equitable, culture-based, and complete basic education where:</h5><br>
                            <h5>Students learn in a child-friendly, gender-sensitive, safe, and motivating environment.</h5>
                            <h5>Teachers facilitate learning and constantly nurture every learner.</h5>
                            <h5>Administrators and staff, as stewards of the institution, ensure an enabling and supportive environment for effective learning to happen.</h5>
                            <h5>Family, community, and other stakeholders are actively engaged and share responsibility for developing life-long learners.</h5><br>
                            <h5>As a learner-centered public institution, the Department of Education continuously improves itself to better serve its stakeholders.</h5>
                        </div>
                    </div>
                     <div class="coreValues">
                        <h2>CORE VALUES</h2>   
                        <div class="coreValues-content">
                            <h5>Maka-Diyos</h5>
                            <h5>Maka-Tao</h5>
                            <h5>Makakalikasan</h5>
                            <h5>Makabansa</h5>
                        </div>
                    </div>
  
                </nav>
           </nav>
    
     
    </body>
   
</html>
