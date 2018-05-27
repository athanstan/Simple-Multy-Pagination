<?php 
    include "db_connect.php";
    //Check whether we are connected to the database.
    if ($connection) {
        //if connected do things
        $query = "SELECT COUNT(*) FROM posts";
        $getRowCountQuery = mysqli_query($connection, $query);

        /*  mysqli_fetch_row returns the count of the rows in the posts table and stores them
            in the $row variable creating an array $row[] where in the first place ($row[0])
            it is stored the count that we want :)
        */
        $row = mysqli_fetch_row($getRowCountQuery);

        //echo "<h1>The rows in the posts table are : ".$row[0]."</h1>";
        $rowCount = $row[0];

        //define the number of posts you want to be displayed in a single page
        $postsPerPage = 5;

        //Create tha last number of pagination
        $last = ceil($rowCount/$postsPerPage); // ceil() return the next whole number from the result of the division ex: ceil(5.6) = 6
        
        //$last can never be less than one
        if($last < 1){
            $last = 1;
        }

        //establish the pagenum variable
        $pagenum = 1;

        //Check if there is a $_GET superglobal set for the pagenum variable
        if (isset($_GET['page'])) {
            $pagenum = preg_replace('#[^0-9]#', '', $_GET['page']); //filter the variable in order to only accept numbers and nothing else
        }

        /*  
            Make sure the page number is not less than 1 or larger than the $last page that we
            generated above
        */
        if ($pagenum < 1) {
            $pagenum = 1;
        } 
        elseif ($pagenum > $last) {
            $pagenum = $last;
        }

        /* 
            Now it's time to pull some results from our table

            The SQL SELECT LIMIT statement is used to retrieve records from one or more tables in a database 
            and limit the number of records returned based on a limit value.

            We are going to use the LIMIT statement in order to retrieve the rows that should be shown in each page for the 
            chosen $pagenum
        */
        $limit = 'LIMIT '.($pagenum - 1) * $postsPerPage. ','. $postsPerPage;
        $query = "SELECT post_id, post_title, post_date FROM posts ORDER BY post_id DESC $limit";

        $finalQuery = mysqli_query($connection, $query);

        //Some simple code to let the user know in which page he is at.
        $textline1 = "Posts : <b>$rowCount</b>";
        $textline2 = "Page <b>$pagenum</b> of <b>$last</b>";
        

        // Establish the pagination controls
        $paginationCtrls = '';

        //If there is more than one page worth of controls
        if($last != 1){
        /*
            First we check if we are on page one. If we are then we don't need a link to 
            the previous page or the first page so we do nothing. If we aren't then we
            generate links to the first page, and to the previous page. 
        */
            if ($pagenum > 1) {
                $previous = $pagenum - 1;
                $paginationCtrls .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$previous.'">Previous</a></li> &nbsp; &nbsp; ';
                //Render Clickable number links that should appear on the left of the target page
                for ($i=$pagenum-3; $i < $pagenum; $i++) { 
                    
                    if($i >= 1){
                        $paginationCtrls .= '<li class="page-item"><a class="page-link number-page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$i.'">'.$i.'</a></li> &nbsp;';
                    }
                }
            }
            //Render the target page number but without it being a link
            $paginationCtrls .= '<li class="page-item"><span class="page-link number-page-link active-page">'.$pagenum.'</span></li> &nbsp; ';

            //Render Clickable number links that should appear on the right of the target page
            for ($i=$pagenum+1; $i<=$last; $i++) { 
                $paginationCtrls .= '<li class="page-item"><a class="page-link number-page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$i.'">'.$i.'</a></li> &nbsp; ';
                if ($i > $pagenum+2) {
                    break; //Stop at the 4th loop. in order to have a really nice looking pagination
                }
            }
            //Generate the NEXT Button at the right of the last page link
            //Which is being generated only if we are not at the last pagelink, obviously. 
            if ($pagenum != $last) {
                $next = $pagenum + 1;
                $paginationCtrls .= ' &nbsp; <li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$next.'">Next</a></li> ';
            }
        }

    }
    else{
        echo "You are not currently connected to a Database"; // Just for error handling
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Google Pagination</title>
    <!-- Custom Css -->
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    
</head>
<body>
    <div class="container">

        <h2 class='mt-5' ><?php echo $textline1; ?></h2>
        <p><?php echo $textline2; ?></p>
        <table class="table table-striped table-dark table-hover">
            <thead>
                <tr>
                <th scope="col">#post-id</th>
                <th scope="col">post-title</th>
                <th scope="col">post-date</th>
                </tr>
            </thead>
            <tbody>
            <?php
        //get results from database
        while ($row = mysqli_fetch_assoc($finalQuery)) {
                $post_id = $row['post_id'];
                $post_title = $row['post_title'];
                $post_date = $row['post_date'];
        ?>
                <tr>
                    <th scope="row"><?php echo $post_id; ?></th>
                    <td><?php echo $post_title; ?></td>
                    <td><?php echo $post_date; ?></td>
                </tr>
                <?php } //End the Loop Generating Table Rows?> 
            </tbody>
        
        
        </table>
        <div id="pagination_controls" class="mx-auto">

        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php echo $paginationCtrls; ?>
            </ul>
        </nav>
        </div>
    </div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
</body>
</html>