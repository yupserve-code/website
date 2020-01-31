<?php
include_once 'page_initialization.php';
include_once (dirname(__FILE__) . "/header.php");
/*
 * 404 error page
 * 404.php
 */
?>
<!DOCTYPE html>
<html class="no-js">
    <head>
        <title>404 Page Not Found</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
        <link href="css/owl.carousel.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/chosen.min.css" rel="stylesheet" type="text/css"/>
        <?php include_once 'include/header-scripts-style.php'; ?>
        <style>
            /* 
shivaram 
style for 404 page image
            */
            /*************************
            *******404 CSS******
            **************************/

            .logo-404 {
                margin-top: 60px;
            }
            .content-404 {
                width: 100%;
                float: left;
                position: relative;
            }
            .content-404 h1 {
                color: #363432;
                font-family: 'Roboto', sans-serif;
                font-size: 41px;
                font-weight: 300;
                margin-top: 50px;
                line-height: 50px;
            }

            .content-404 img {
                margin:0 auto;
                width: 70%; 
            }

            .content-404 p {
                color: #363432;
                font-family: 'Roboto', sans-serif;
                font-size: 18px;
                margin-top: 20px;
                line-height: 25px;
            }

            .content-404  h2 {
                margin-top:50px;
            }

            .content-404 h2 a {
                background:#045A9C;
                color: #FFFFFF;
                font-family: 'Roboto', sans-serif;
                font-size: 22px;  
                padding: 8px 40px;
            }
            .ct-button {
                color: #ffffff !important;
                background: #e9403a !important;
                border: 2px solid #e9403a !important;
                position: relative;
                font-weight: 400;
                padding: 0px 38px;
                /* display: block; */
            }
            .ct-btn-big {
                width: 285px;
                border-radius: 50px !important;
                line-height: 46px !important;
                /* height: 50px !important; */
                font-size: 21px !important;
                margin: 27px auto !important;
                display: block;
            }
            a:hover {
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <?php include_once 'include/header.php'; ?>
        <!-- 404 content -->
        <section class="section 404_page" style="background: none !important;">
            <div class="container text-center">
                <div class="content-404">
                    <img src="<?php echo $base_url; ?>assets/images/404.png" class="img-responsive" alt="404"/>
                    <h1><b>OPPS!</b> We couldn't find this page for you</h1>
                    <p>Looks like you were looking for something we could not find for you.</p>
                    <div class="clearfix"></div>
                    <a href="<?php echo $base_url; ?>" class="ct-button ct-btn-big ct_remove_id">Bring me back Home</a>
<!--                    <h2><a href="<?php echo $base_url; ?>">Bring me back Home</a></h2>-->
                </div>
            </div>
        </section>
        <!-- 404 content -->
        <?php include_once 'include/footer.php'; ?>
        <div class="clearfix"></div>
        <?php include_once 'include/script.php'; ?>
    </body>
</html>