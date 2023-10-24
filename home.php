<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'admin/db_connect.php'; 
?>
<style>
    #cat-list li{
        cursor: pointer;
    }
       #cat-list li:hover {
        color: white;
        background: #007bff8f;
    }
    .prod-item p{
        margin: unset;
    }
    .bid-tag {
    position: absolute;
    right: .5em;
}
</style>
<?php 
$cid = isset($_GET['category_id']) ? $_GET['category_id'] : 'all';
?>
<div class="contain-fluid">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-3" style="background-color:#141414">
                <div class="card" style="background-color:#131A27; color:white;">
                    <div class="card-header" style="text-align:center; background-color:#E50914;">Categories</div>
                    <div class="card-body">
                        <ul class='list-group' id='cat-list'>
                            <li class='list-group-item' style="background-color:#131A27;color:white;" data-id='0' data-href="index.php?page=home&category_id=all">All</li>
                            <?php
                                $cat = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                                if ($conn->error) {
                                    die("Categories query failed: " . $conn->error);
                                }
                                $where = "";
                                if ($cid > 0) {
                                    $where = " AND category_id = $cid ";
                                }
                            
                                while($row=$cat->fetch_assoc()):
                                    $cat_arr[$row['id']] = $row['name'];
                             ?>
                            <li class='list-group-item' style="background-color:#131A27;color:white;" data-id='<?php echo $row['id'] ?>' data-href="index.php?page=home&category_id=<?php echo $row['id'] ?>"><?php echo ucwords($row['name']) ?></li>

                            <?php endwhile; ?>
                        </ul>

                    </div>
                </div>
            </div>
            <div class="col-md-9" style="background-color:#141414">
                <div class="card" style="background-color:#141414">
                    <div class="card-body">
                        <div class="row" style="background-color:#141414">
                            <?php
                            $where = "";
                            if ($cid !== 'all' && $cid !== '0') {
                                     $where = " WHERE category_id = $cid AND bid_end_datetime >= DATE_ADD(NOW(), INTERVAL 24 HOUR)";
                            } elseif ($cid === 'all') {
                              $where = " WHERE bid_end_datetime >= DATE_ADD(NOW(), INTERVAL 24 HOUR)";
                             }

                             $cat = $conn->query("SELECT * FROM products" . $where);




                             
                               if ($conn->error) {
                                   die("Products query failed: " . $conn->error);
                               }
                               if($cat->num_rows == 0){
                                    #echo "<center><h4><i>No Available is Product.</i></h4></center>";
                               }
                               while($row=$cat->fetch_assoc()):
                             ?>
                            <div class="col-sm-4" style="padding-top: 30px;">
    <div class="card" style="border:0px;">
        <div class="float-right align-top bid-tag">
            <span class="badge badge-pill badge-primary text-white"><i class="fa fa-tag"></i> <?php echo number_format($row['start_bid']) ?></span>
        </div>
        <img class="card-img-top" style="height:150px;object-fit:cover;" src="admin/assets/uploads/<?php echo $row['img_fname'] ?>" alt="Card image cap">
        <div class="float-right align-top d-flex">
            <span class="badge badge-pill badge-warning text-white"><i class="fa fa-hourglass-half"></i> <?php echo date("M d,Y h:i A",strtotime($row['bid_end_datetime'])) ?></span>
        </div>
        <div class="card-body prod-item" style="background-color:#131A27; color:white;border:none!important;">
            <p><?php echo $row['name'] ?></p>
            <p style="padding-bottom:5px;"><small><?php echo $cat_arr[$row['category_id']] ?></small></p>
            
            <!-- Give Review Button -->
            <button class="btn btn-sm btn-success mr-1" type="button" data-toggle="modal" data-target="#review_modal" data-product-id="<?php echo $row['id']; ?>">Give Review</button>

            
            <button class="btn btn-sm view_prod" style="background-color:#E50914;color:white;" type="button" data-id="<?php echo $row['id'] ?>"> View</button>
        </div>
        
        <!-- Review Stars -->
        <div class="card-footer" style="background-color: #131A27;">
          <div class="text-left">
        <span class="fa fa-star text-warning"></span>
        <span class="fa fa-star text-warning"></span>
        <span class="fa fa-star text-warning"></span>
        <span class="fa fa-star text-warning"></span>
        <span class="fa fa-star text-warning"></span>
    </div>
</div>
    </div>
</div>
                            <?php endwhile; ?>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->

<div class="modal fade" id="review_modal" tabindex="-1" role="dialog" aria-labelledby="review_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="review_modal_label">Give Review</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="review_form" action="submit_review.php" method="POST">
                    <input type="hidden" id="product_id_input" name="product_id">
                    <div class="form-group">
                        <label for="rating">Rating (out of 5)</label>
                        <input type="number" name="rating" class="form-control" min="1" max="5" required>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea name="comment" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</div>

       
<script>
       $('#cat-list li').click(function(){
    var categoryID = $(this).attr('data-id');
    var href = "index.php?page=home";
    if (categoryID !== 'all') {
        href += "&category_id=" + categoryID;
    }
    location.href = href;
});

$('#cat-list li').each(function(){
    var id = '<?php echo $cid ?>';
    if (id === $(this).attr('data-id') || (id === 'all' && $(this).attr('data-id') === '0')) {
        $(this).addClass('active');
    }
});


     $('.view_prod').click(function(){
        uni_modal_right('View Product','view_prod.php?id='+$(this).attr('data-id'))
     })
     
    $(document).ready(function() {
        // When the "Give Review" button is clicked
        $('.btn-success[data-toggle="modal"]').on('click', function() {
            var productId = $(this).data('product-id'); // Get the product ID from the clicked button
            $('#product_id_input').val(productId); // Set the product ID in the hidden input field
        });

        // Handle form submission
        $('#review_form').submit(function(e) {
            e.preventDefault();
            
            // Now you can use $('#product_id_input').val() to access the product ID in your JavaScript
            var productId = $('#product_id_input').val(); // Get the product ID
            
            // Perform any necessary AJAX request or other actions here
            // You can also use this value to send to the server using the form submission
            
            // Submit the form (optional, depending on your needs)
            // this.submit(); 
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.btn-success[data-toggle="modal"]').on('click', function() {
            var productId = $(this).data('product-id');
            $('#product_id_input').val(productId);
        });

        $('#review_form').submit(function(e) {
            e.preventDefault();
            var productId = $('#product_id_input').val();
            var rating = $('input[name="rating"]').val();
            var comment = $('textarea[name="comment"]').val();

            // Perform AJAX request to submit the review data
            $.ajax({
                url: 'submit_review.php',
                method: 'POST',
                data: {
                    product_id: productId,
                    rating: rating,
                    comment: comment
                },
                success: function(response) {
                    // Handle success response (if needed)
                    console.log('Review submitted successfully.');
                    $('#review_modal').modal('hide'); // Hide the modal
                },
                error: function(error) {
                    // Handle error (if needed)
                    console.error('Error submitting review:', error);
                }
            });
        });
    });
</script>








