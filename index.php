<?php
/*************************
 * Proje: Empatisoft @ 2017
 * Developer: Onur KAYA
 * Telefon: 0537 493 10 20
 * E-posta: empatisoft@gmail.com
 * Web: www.empatisoft.com
 * PHP Sürümü: 7.0.9
 * MySQL Sürümü: 5.0.12 (InnoDB, MariaDB)
 * Oluşturma Tarihi: 7.06.2017 11:06
 */
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>asd</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="/api/css/css.css">
    <link rel="stylesheet" href="/api/css/animate.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="testimonials-container">
                <!--<div id="loader" class="loader">
                    <img src="/api/images/preloader.gif"/>
                </div>-->
                <div id="testimonial-ajax-response"></div>
            </div>
        </div>
    </div>
</div>
<script>
    function loadTestimonial(comment_id) {
        //$("#loader").fadeIn("fast");
        var data;
        if(comment_id == 0)
        {
            data = 'cat_id=1&lang_id=0';
        }
        else
        {
            data = 'comment_id='+comment_id+'&cat_id=1&lang_id=0';
        }
        var result = $("#testimonial-ajax-response");
        $.ajax({
            type: "GET",
            url: "/api/comments.php",
            cache: false,
            dataType: 'html',
            data: data,
            success: function(view) {
                result.html(view).fadeIn("fast");
                //$("#loader").fadeOut("fast");
            },
            error: function(xhr, ajaxOptions, thrownError) {
                result.html(xhr.responseText).fadeIn("fast");
                //$("#loader").fadeOut("fast");
            }
        });
    }
    $(document).ready(function() {
        loadTestimonial(0);

        $('.btn-testimonial-load').on('click', function(){
            var comment_id = $(this).data('id');
            loadTestimonial(comment_id);
        });
    });
</script>
</body>
</html>

