<?php
/*************************
 * Proje: Empatisoft @ 2017
 * Developer: Onur KAYA
 * Telefon: 0537 493 10 20
 * E-posta: empatisoft@gmail.com
 * Web: www.empatisoft.com
 * PHP Sürümü: 7.0.9
 * MySQL Sürümü: 5.0.12 (InnoDB, MariaDB)
 * Oluşturma Tarihi: 6.06.2017 13:16
 */

define('ROOT', $_SERVER['DOCUMENT_ROOT']);

require ROOT . 'api/app/Model.php';

$db = new Model();

$comment_id = isset($_GET['comment_id']) ? $db->IntKontrol($_GET['comment_id']) : null;
$cat_id = isset($_GET['cat_id']) ? $db->IntKontrol($_GET['cat_id']) : 1;
$lang_id = isset($_GET['lang_id']) ? $db->IntKontrol($_GET['lang_id']) : 0;
$status = 1;

// Dil Türkçe ise
if ($lang_id == 0)
{
    if($comment_id == null)
    {
        $comment_sql = 'SELECT comment_id, patient_name, country, comment, image FROM comment WHERE status = :status AND cat_id = :cat_id ORDER BY comment_id DESC';
        $where = array(
            'cat_id' => $cat_id,
            'status' => $status
        );
    }
    else
    {
        $comment_sql = 'SELECT comment_id, patient_name, country, comment, image FROM comment WHERE status = :status AND cat_id = :cat_id AND comment_id = :comment_id';
        $where = array(
            'cat_id' => $cat_id,
            'status' => $status,
            'comment_id' => $comment_id
        );
    }

    $comment_sql_next = 'SELECT comment_id as next FROM comment WHERE status = :status AND cat_id = :cat_id AND comment_id = (SELECT MIN(comment_id) FROM comment WHERE comment_id > :comment_id)';

    $comment_sql_prev = 'SELECT comment_id as prev FROM comment WHERE status = :status AND cat_id = :cat_id AND comment_id = (SELECT MAX(comment_id) FROM comment WHERE comment_id < :comment_id)';
}
// Dil Türkçe değilse
else
{
    if($comment_id == null)
    {
        $comment_sql = 'SELECT c.comment_id, c.patient_name, c.image, t.country, t.comment FROM comment AS c INNER JOIN comment_translate AS t ON t.comment_id = c.comment_id WHERE c.status = :status AND c.cat_id = :cat_id AND t.lang_id = :lang_id ORDER BY c.comment_id DESC';
        $where = array(
            'cat_id' => $cat_id,
            'status' => $status,
            'lang_id' => $lang_id
        );
    }
    else
    {
        $comment_sql = 'SELECT c.comment_id, c.patient_name, c.image, t.country, t.comment FROM comment AS c INNER JOIN comment_translate AS t ON t.comment_id = c.comment_id WHERE c.status = :status AND c.cat_id = :cat_id AND t.lang_id = :lang_id AND c.comment_id = :comment_id';
        $where = array(
            'cat_id' => $cat_id,
            'status' => $status,
            'lang_id' => $lang_id,
            'comment_id' => $comment_id
        );
    }

    $comment_sql_next = 'SELECT c.comment_id as next FROM comment AS c INNER JOIN comment_translate AS t ON t.comment_id = c.comment_id WHERE c.status = :status AND c.cat_id = :cat_id AND c.comment_id = (SELECT MIN(comment_id) FROM comment WHERE comment_id > :comment_id AND t.lang_id = :lang_id) AND t.lang_id = :lang_id';

    $comment_sql_prev = 'SELECT c.comment_id as prev FROM comment AS c INNER JOIN comment_translate AS t ON t.comment_id = c.comment_id WHERE c.status = :status AND c.cat_id = :cat_id AND c.comment_id = (SELECT MAX(comment_id) FROM comment WHERE comment_id < :comment_id AND t.lang_id = :lang_id) AND t.lang_id = :lang_id';
}

try {
    $query = $db->db_connect()->prepare($comment_sql);
    $query->execute($where);
    $comment = $query->fetch(PDO::FETCH_ASSOC);

    if(is_null($comment))
    {
        $json_data = null;
    }
    else
    {
        if ($lang_id == 0)
        {
            $where_prev_next = array(
                'status' => $status,
                'cat_id' => $cat_id,
                'comment_id' => $comment['comment_id']
            );
        }
        else
        {
            $where_prev_next = array(
                'status' => $status,
                'cat_id' => $cat_id,
                'lang_id' => $lang_id,
                'comment_id' => $comment['comment_id']
            );
        }

        $query_next = $db->db_connect()->prepare($comment_sql_next);
        $query_next->execute($where_prev_next);
        $next = $query_next->fetch(PDO::FETCH_ASSOC);

        $query_prev = $db->db_connect()->prepare($comment_sql_prev);
        $query_prev->execute($where_prev_next);
        $prev = $query_prev->fetch(PDO::FETCH_ASSOC);

        /**
         * JSON olarak çıktı isteniyorsa aşağıdaki şekilde kullanılabilir.
         */
        /*$json_data = array(
            'comment' => $comment,
            'prev' => $prev,
            'next' => $next
        );
        $myJSON = json_encode($json_data);
        echo $myJSON;*/
        echo '<div class="animated fadeIn">';
        echo '<img src="/api/images/'.$comment['image'].'" class="img-responsive img-testimonial" alt="'.$comment['patient_name'].'">';
        echo '<div class="testmonial-content">';

        echo '    <div class="testmonial-title">"';
        echo        $comment['comment'];
        echo '    "</div>';
        echo '    <div class="testmonial-text">';
        echo        $comment['patient_name'].', '.$comment['country'];
        echo '    </div>';
        echo '</div>';

        if($prev != NULL)
        {
            echo '<div class="btn-prev">';
            echo '    <a href="#prev" class="btn-testimonial-load" data-id="'.$prev['prev'].'">';
            echo '        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
            echo '    </a>';
            echo '</div>';
        }

        if($next != NULL)
        {
            echo '<div class="btn-next">';
            echo '    <a href="#next" class="btn-testimonial-load" data-id="'.$next['next'].'">';
            echo '        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
            echo '    </a>';
            echo '</div>';
        }
        echo '</div>';
        ?>
        <script>
            $(document).ready(function() {
                $('.btn-testimonial-load').on('click', function(){
                    var comment_id = $(this).data('id');
                    //$("#loader").fadeIn("fast");
                    var result = $("#testimonial-ajax-response");
                    $.ajax({
                        type: "GET",
                        url: "/api/comments.php",
                        cache: false,
                        dataType: 'html',
                        data: 'comment_id='+comment_id+'&cat_id=1&lang_id=0',
                        success: function(view) {
                            result.html(view).fadeIn("fast");
                            //$("#loader").fadeOut("fast");
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            result.html(xhr.responseText).fadeIn("fast");
                            //$("#loader").fadeOut("fast");
                        }
                    });
                });
            });
        </script>
<?php
    }
} catch(PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}
