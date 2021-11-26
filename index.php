<style type="text/css">
.diff td{
  vertical-align : top;
  white-space    : pre;
  white-space    : pre-wrap;
  font-family    : monospace;
}

.diff .diffDeleted {
    color: red;
}

.diff .diffInserted {
    color: green;
}

.diff .diffUnmodified {
    color: orange;
}
</style>

<?php
set_time_limit(0);
require_once './libs/DigiHunt.php';

$hunter = new DigiHunt();
$products = $hunter->fetchProducts($pages = null, $allImages = true, $verbose = true);
$hunter->downloadImages($products)->updateProducts($products, 'newss.json');
// var_dump($products);

// $hunter->downloadImages($images = $products);
// $hunter->updateProducts($data = $products, $as = 'new.json');
// $hunter->compare($update = true, $pages = null);
?>